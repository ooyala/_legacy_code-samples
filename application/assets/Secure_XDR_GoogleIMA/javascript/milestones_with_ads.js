OO.plugin("MilestoneWithAds", function (OO, _, $, W) {
  MilestoneWithAds = function(mb, id) {
    this.mb = mb;
    this.id = id;
    this.playerIsFlash = false;

    // This are the milestones to track. The way we are tracking them
    // is setting a boolean each time the video passes a milestone. For
    // a different approach, check gaTrack.js
    this.videoStarted = false;
    this._25per = false;
    this._50per = false;
    this._75per = false;
    this.videoEnded = false;
    this.videoLength = 0;
    this._25milestone = 0;
    this._50milestone = 0;
    this._75milestone = 0;
    this.isAdPlaying = false;
    this.init();
  };

  _.extend(MilestoneWithAds.prototype, {
    init: function() {
      // Subscribe to relevant events
      this.mb.subscribe(OO.EVENTS.PLAYBACK_READY,
        "MilestoneWithAds", _.bind(this.onPlaybackReady, this));
      this.mb.subscribe(OO.EVENTS.WILL_PLAY_ADS,
        "MilestoneWithAds", _.bind(this.onWillPlayAds, this));
      this.mb.subscribe(OO.EVENTS.ADS_PLAYED,
        "MilestoneWithAds", _.bind(this.onAdsPlayed, this));
      this.mb.subscribe(OO.EVENTS.CONTENT_TREE_FETCHED,
        "MilestoneWithAds", _.bind(this.onContentTreeFetched, this));
      this.mb.subscribe(OO.EVENTS.PLAYING,
        "MilestoneWithAds", _.bind(this.onPlaying, this));
      this.mb.subscribe(OO.EVENTS.PLAYHEAD_TIME_CHANGED,
        "MilestoneWithAds", _.bind(this.onPlayheadTimeChanged, this));
      this.mb.subscribe(OO.EVENTS.PLAYED,
        "MilestoneWithAds", _.bind(this.onPlayed, this));
      this.write("Log here");
    },

    // Helper functions
    isFlash: function() {
      try {
        return OO.requiredInEnvironment('flash-playback');
      }
      catch(e) {
        // Fallback, asume is Flash
        return true;
      }
    },

    write: function(text) {
        var textLog = document.getElementById("textLog");
        textLog.innerHTML = textLog.value+new Date()+":"+text+"\n";
    },

    // Event functions
    onPlaybackReady: function() {
      this.playerIsFlash = isFlash();
    },

    onWillPlayAds: function() {
      this.write("An ad will be played");
      this.isAdPlaying = true;
    },

    onAdsPlayed: function() {
      this.write("Ads finished");
      this.isAdPlaying = false;
    },

    onContentTreeFetched: function (eventName, content) {
      // Flash reports the duration as "time" in seconds
      if (this.playerIsFlash) {
        this.videoLength = content.time;
      } else {
        // HTML reports video in miliseconds and as "duration"
        this.videoLength = content.duration;
        this.videoLength = this.videoLength / 1000;
      }
      // Define the duration of the milestones
      this._25milestone = 1 * (this.videoLength / 4);
      this._50milestone = 2 * (this.videoLength / 4);
      this._75milestone = 3 * (this.videoLength / 4);
    },

    onPlaying: function() {
      if (this.isAdPlaying) {
        // Ignore the event if it's not the main video
        return;
      }
      if (this.videoEnded) {
        // Reset all values
        // We are asuming we hit replay on the same video
        this.videoStarted = false;
        this._25per = false;
        this._50per = false;
        this._75per = false;
        this.videoEnded = false;
        this.write("Replaying video");
      }

      if (!this.videoStarted){
        this.videoStarted = true;
        this.write("Video began playback");
      }
    },

    onPlayheadTimeChanged: function (eventName, currentTime) {
      // Ignore the event if it's not the main video
      if (this.isAdPlaying) {
        return;
      }

      // We check from first to last to account for scrubbing
      if (currentTime > this._75milestone && !this._75per) {
        this._25per = true;
        this._50per = true;
        this._75per = true;
        this.write("We hit the 75% milestone");
      } else if (currentTime > this._50milestone && !this._50per) {
        this._25per = true;
        this._50per = true;
        this.write("We hit the 50% milestone");
      } else if (currentTime > this._25milestone && !this._25per) {
        this._25per = true;
        this.write("We hit the 25% milestone");
      }
    },

    onPlayed: function() {
      this.videoEnded = true;
      this.write("Video endeded");
    }
  });

  return MilestoneWithAds;
});