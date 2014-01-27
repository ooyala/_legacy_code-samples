OO.plugin("SimpleMilestones", function (OO, _, $, W) {
    SimpleMilestones = function(mb, id) {
        this.mb = mb;
        this.id = id;
        var playerIsFlash = false;

        // This flags are to avoid duplicating
        // milestones events. Once the user has
        // reached 25 percent we won't trigger
        // that event again, unless replay
        this.videoStarted = false;
        this._25per = false;
        this._50per = false;
        this._75per = false;
        this.videoEnded = false;

        this.videoLength = 0;
        this._25milestone = 0;
        this._50milestone = 0;
        this._75milestone = 0;
        this.init();
    };

    _.extend(SimpleMilestones.prototype, {
        init: function() {

            this.mb.subscribe(OO.EVENTS.CONTENT_TREE_FETCHED,
              "SimpleMilestones", _.bind(this.onContentTreeFetched, this));
            this.mb.subscribe(OO.EVENTS.PLAYING,
              "SimpleMilestones", _.bind(this.onPlaying, this));
            this.mb.subscribe(OO.EVENTS.PLAYHEAD_TIME_CHANGED,
              "SimpleMilestones", _.bind(this.onPlayheadTimeChanged, this));
            this.mb.subscribe(OO.EVENTS.PLAYED,
              "SimpleMilestones", _.bind(this.onPlayed, this));
        },

        // Helper functions
        isFlash: function() {
          try {
            return OO.requiredInEnvironment('flash-playback');
          } catch(e) {
            // Fallback, asume true
            return true;
          }
        },

        write: function(text) {
            var textLog = document.getElementById("textLog");
            textLog.innerHTML = textLog.value + new Date() + ":" + text + "\n";
        },

        onContentTreeFetched: function(eventName, content) {
          playerIsFlash = this.isFlash();
          if (playerIsFlash){
            // Flash reports events in seconds
            this.videoLength = content.time;
          }
          else{
            // HTML reports video in miliseconds
            this.videoLength = content.duration;
            this.videoLength = this.videoLength / 1000;
          }
          this._25milestone = 1 * (this.videoLength / 4);
          this._50milestone = 2 * (this.videoLength / 4);
          this._75milestone = 3 * (this.videoLength / 4);
        },
        onPlaying: function() {
          if (this.videoEnded){
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
        onPlayheadTimeChanged: function(eventName, currentTime) {
          // We check from first to last to account for scrubbing
          if (currentTime > this._75milestone && !this._75per){
            this._25per = true;
            this._50per = true;
            this._75per = true;
            this.write("We hit the 75% milestone");
          }
          else if (currentTime > this._50milestone && !this._50per) {
            this._25per = true;
            this._50per = true;
            this.write("We hit the 50% milestone");
          }
          else if (currentTime > this._25milestone && !this._25per){
            this._25per = true;
            this.write("We hit the 25% milestone");
          }
        },
        onPlayed: function() {
          this.videoEnded = true;
          this.write("Video endeded");
        }

    });

    return SimpleMilestones;
});