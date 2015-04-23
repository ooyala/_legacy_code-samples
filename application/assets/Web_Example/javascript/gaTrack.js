// Each custom module must be defined using OO.plugin method
// The first parameter is the module name
// The second parameter is a factory function that will be called by
// the player to create an instance of the module. This function must
// return a constructor for the module class
OO.plugin("GaTrackModule", function (OO, _, $, W) {

    // As this is defined as a plugin, each player will have their own
    // definition of this module, hence avoiding message bus or other
    // variables conflict
    GaTrackModule = function (mb, id) {
        this.gaMechanism = 'events';
        this.gaPageviewFormat = 'ooyala-event/:event/:title';
        this.gaEventCategory = 'Ooyala';
        this.verboseLogging = false;
        this.playbackMilestones = [[0.25, 'playProgressQuarter'], [0.5, 'playProgressHalf'], [0.75, 'playProgressThreeQuarters'], [0.97, 'playProgressEnd']];

        // Create a unique identifier so we can refer to each module
        // by this id
        this.identifier = Math.floor(Math.random()*10000000000);
        this.mb = mb;
        this.id = id;
        this.playing = false;
        this.duration = NaN;
        this.metaData = NaN;
        this.playerRoot = NaN;
        this.gaMethod = NaN;
        this.content = NaN;
        this.currentPlaybackType = 'content';
        this.lastEventReported = NaN;
        this.lastReportedPlaybackMilestone = 0;
        this.gaFormat = NaN;

        if(!window.ooyalaGaTrackModule) {
            window.ooyalaGaTrackModule = {};
        }

        // Create a global reference to each module
        window.ooyalaGaTrackModule[this.identifier] = this;

        this.init(); // subscribe to relevant events
    };

    // public functions of the module object
    GaTrackModule.prototype = {
        init: function () {
            // subscribe to relevant player events-
            // see http://underscorejs.org/#bind
            this.mb.subscribe(OO.EVENTS.PLAYER_CREATED,
                'GaTrack', _.bind(this.onPlayerCreate, this));
            this.mb.subscribe(OO.EVENTS.PLAYHEAD_TIME_CHANGED,
                'GaTrack', _.bind(this.onTimeUpdate, this));
            this.mb.subscribe(OO.EVENTS.CONTENT_TREE_FETCHED,
                'GaTrack', _.bind(this.onContentReady, this));
            this.mb.subscribe(OO.EVENTS.PLAYING,
                'GaTrack', _.bind(this.onPlay, this));
            this.mb.subscribe(OO.EVENTS.PLAYED,
                'GaTrack', _.bind(this.onEnd, this));
            this.mb.subscribe(OO.EVENTS.PLAY_FAILED,
                'GaTrack', _.bind(this.onFail, this));
            this.mb.subscribe(OO.EVENTS.METADATA_FETCHED,
                'GaTrack', _.bind(this.onMetadataFetched, this));
            this.mb.subscribe(OO.EVENTS.WILL_PLAY_ADS,
                'GaTrack', _.bind(this.onWillPlayAds, this));
            this.mb.subscribe(OO.EVENTS.ADS_PLAYED,
                'GaTrack', _.bind(this.onAdsPlayed, this));
            this.mb.subscribe(OO.EVENTS.PAUSED,
                'GaTrack', _.bind(this.onPaused, this));
        },

        // Custom logger
        consoleLog: function (what) {
            if(this.verboseLogging) {
                if(typeof console != 'undefined') {
                    console.log(what);
                }
            }
        },


        // Event handlers
        // All events receive as first parameter the event name

        // Handles the PLAYER_CREATED event
        // First parameter is the event name
        // Second parameter is the elementId of player container
        // Third parameter is the list of parameters which were passed into
        // player upon creation.
        onPlayerCreate: function (eventName, elementId, params) {
            this.playerRoot = $("#" + elementId);
            this.importUserSettings();
            this.initGa();
            this.consoleLog("onPlayerCreate");
        },

        // Handles CONTENT_TREE_FETCHED event
        // Second parameter is a content object with details about the
        // content that was loaded into the player
        onContentReady: function (eventName, content) {
            this.content = content;
            this.reportToGa('contentReady');
            this.consoleLog("onContentReady");
        },

        onTimeUpdate: function (eventName, time, duration, buffer) {
            // We don't save the duration of ads, just of content
            if(this.currentPlaybackType != 'content') {
                return;
            }

            // The duration is 0 when we are on live streaming, and since
            // we don't know the duration of it, we don't record it
            if (duration > 0) {
                this.duration = duration;
            }

            this.currentPlayheadPosition = time;

            // Report each of the milestones
            var gaTrack = this;
            $.each(gaTrack.playbackMilestones, function() {
                if((gaTrack.currentPlayheadPosition / gaTrack.duration) > this[0] && gaTrack.lastReportedPlaybackMilestone != this[0] && this[0] > gaTrack.lastReportedPlaybackMilestone) {
                    gaTrack.reportToGa(this[1]);
                    gaTrack.lastReportedPlaybackMilestone = this[0];
                    gaTrack.consoleLog("onTimeUpdate (" + time + ", " + this[1] + ")");
                }
            });
        },

        onPlay: function () {
            this.playing = true;
            this.clearErrors();
            if(this.currentPlaybackType == 'content') {
                this.reportToGa('playbackStarted');
            } else {
                this.reportToGa('adPlaybackStarted');
            }
            this.consoleLog("onPlay");
        },

        onEnd: function () {
            this.reportToGa('playbackFinished');
            this.consoleLog("onEnd");
        },

        onPaused: function () {
            // Don't report pause events for ads, because Ooyala doesn't properly report playback resuming for ads
            if(this.currentPlaybackType != 'content') {
                return false;
            }

            this.playing = false;

            // The Ooyala event subscription triggers an "onpause" on playback; we'll filter it here
            // It also triggers an "onpause" when playback finishes; we'll filter that, too
            if(typeof this.currentPlayheadPosition == 'undefined' || this.currentPlayheadPosition > (this.duration -2)) {
                return false;
            }

            this.reportToGa('playbackPaused');
            this.consoleLog("onPaused");
        },

        onFail: function () {
            this.playing = false;
            this.reportToGa('playbackFailed');
            this.consoleLog("onFail");
        },

        onMetadataFetched: function (eventName, metadata) {
            this.metaData = metadata;
        },

        onWillPlayAds: function() {
            this.currentPlaybackType = 'ad';
            this.reportToGa('adPlaybackStarted');
            this.consoleLog("onWillPlayAds");
        },

        onAdsPlayed: function() {
            this.currentPlaybackType = 'content';
            this.reportToGa('adPlaybackFinished');
            this.reportToGa('playbackStarted');
            this.consoleLog("onAdsPlayed");
        },

        readCookies: function () {},

        initGa: function() {
            // Track as pageviews?
            if(this.gaMechanism == 'pageviews') {
                // Legacy GA code block support
                if(typeof _gaq != 'undefined') {
                    this.gaMethod = "_gaq.push(['_trackPageview', '" + this.gaPageviewFormat + "'])";
                // Current GA code block support
                } else if(typeof ga != 'undefined') {
                    this.gaMethod = "ga('send', 'pageview', '" + this.gaPageviewFormat + "')";
                } else {
                    this.displayError();
                }
            // Track as events?
            } else {
                // Legacy GA code block support
                if(typeof _gaq != 'undefined') {
                    this.gaMethod = "_gaq.push(['_trackEvent', '" + this.gaEventCategory + "', ':event', ':title']);";
                // Current GA code block support
                } else if(typeof ga != 'undefined') {
                    this.gaMethod = "ga('send', 'event', '" + this.gaEventCategory + "', ':event', ':title');";
                } else {
                    this.displayError();
                }
            }
        },

        reportToGa: function (event) {
            if(this.gaMethod && this.lastEventReported != event) {
                // Ooyala event subscriptions result in duplicate triggers; we'll filter them out here
                this.lastEventReported = event;

                eval(this.gaMethod.replace(/:hostname/g, document.location.host).replace(/:event/g, event).replace(/:title/g, this.content.title));
                this.consoleLog('REPORTED TO GA:' + this.gaMethod.replace(/:hostname/g, document.location.host).replace(/:event/g, event).replace(/:title/g, this.content.title));
            }
        },

        displayError: function() {
            this.gaMethod = false;
            this.playerRoot.find('.innerWrapper').prepend('<div class="gatrack-error" style="color: red; padding: 10px; text-align: center; margin: auto; font-family: Arial; font-size: 13px; height: 18px; z-index: 1000; position: absolute; width: ' + this.playerRoot.find('.innerWrapper').width() + 'px;">The Ooyala Google Analytics Tracking module is installed, but no valid Google Analytics code block is detected.</div>');
        },

        clearErrors: function() {
            this.playerRoot.find('.innerWrapper .gatrack-error').remove();
        },

        importUserSettings: function() {
            if(typeof window.ooyalaGaTrackSettings != 'undefined') {
                var gaTrack = this;
                $.each(window.ooyalaGaTrackSettings, function(index, value) {
                    eval('gaTrack.' + index + '=window.ooyalaGaTrackSettings["' + index + '"]');
                });
            }
        },

        __end_marker: true
    };

    // Return the constructor of the module class.
    // This is required so that Ooyala’s player can instantiate the custom
    // module correctly.
    return GaTrackModule;
});
