OO.plugin("MilestoneWithAds", function (OO, _, $, W) {
  MilestoneWithAds = function(mb, id) {
    this.mb = mb;
    this.id = id;
    var playerIsFlash = false;

    // This are the milestones to track. The way we are tracking them
    // is setting a boolean each time the video passes a milestone. For
    // a different approach, check gaTrack.js
    var videoStarted = false;
    var _25per = false;
    var _50per = false;
    var _75per = false;
    var videoEnded = false;

    var videoLength = 0;
    var _25milestone;
    var _50milestone;
    var _75milestone;

    var isAdPlaying = false;
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
      playerIsFlash = isFlash();
    },

    onWillPlayAds: function() {
      this.write("An ad will be played");
      isAdPlaying = true;
    },

    onAdsPlayed: function() {
      this.write("Ads finished");
      isAdPlaying = false;
    },

    onContentTreeFetched: function (eventName, content) {
      // Flash reports the duration as "time" in seconds
      if (playerIsFlash) {
        videoLength = content.time;
      } else {
        // HTML reports video in miliseconds and as "duration"
        videoLength = content.duration;
        videoLength = videoLength / 1000;
      }
      // Define the duration of the milestones
      _25milestone = 1 * (videoLength / 4);
      _50milestone = 2 * (videoLength / 4);
      _75milestone = 3 * (videoLength / 4);
    },

    onPlaying: function() {
      if (isAdPlaying) {
        // Ignore the event if it's not the main video
        return;
      }
      if (videoEnded) {
        // Reset all values
        // We are asuming we hit replay on the same video
        videoStarted = false;
        _25per = false;
        _50per = false;
        _75per = false;
        videoEnded = false;
        this.write("Replaying video");
      }

      if (!videoStarted){
        videoStarted = true;
        this.write("Video began playback");
      }
    },

    onPlayheadTimeChanged: function (eventName, currentTime) {
      // Ignore the event if it's not the main video
      if (isAdPlaying) {
        return;
      }

      // We check from first to last to account for scrubbing
      if (currentTime > _75milestone && !_75per) {
        _25per = true;
        _50per = true;
        _75per = true;
        this.write("We hit the 75% milestone");
      } else if (currentTime > _50milestone && !_50per) {
        _25per = true;
        _50per = true;
        this.write("We hit the 50% milestone");
      } else if (currentTime > _25milestone && !_25per) {
        _25per = true;
        this.write("We hit the 25% milestone");
      }
    },

    onPlayed: function() {
      videoEnded = true;
      this.write("Video endeded");
    }
  });

  return MilestoneWithAds;
});