this.videoPlayer = window.videoPlayer;

var playerIsFlash = false;

// This flags are to avoid duplicating
// milestones events. Once the user has
// reached 25 percent we won't trigger
// that event again, unless replay
var videoStarted = false;
var _25per = false;
var _50per = false;
var _75per = false;
var videoEnded = false;

var videoLength = 0;
var _25milestone;
var _50milestone;
var _75milestone;


this.videoPlayer.subscribe(OO.EVENTS.CONTENT_TREE_FETCHED, "func1", function (eventName, arg1, arg2) {
  playerIsFlash = isFlash();
  if (playerIsFlash){
    // Flash reports events in seconds
    videoLength = arg1.time;
  }
  else{
    // HTML reports video in miliseconds
    videoLength = arg1.duration;
    videoLength = videoLength / 1000;
  }
  _25milestone = 1 * (videoLength / 4);
  _50milestone = 2 * (videoLength / 4);
  _75milestone = 3 * (videoLength / 4);
});

this.videoPlayer.subscribe(OO.EVENTS.PLAYING, "func2", function (eventName, arg1, arg2) {
  if (videoEnded){
    // Reset all values
    // We are asuming we hit replay on the same video
    videoStarted = false;
    _25per = false;
    _50per = false;
    _75per = false;
    videoEnded = false;
    write("Replaying video");
  }

  if (!videoStarted){
    videoStarted = true;
    write("Video began playback");
  }
});

this.videoPlayer.subscribe(OO.EVENTS.PLAYHEAD_TIME_CHANGED, "func3", function (eventName, currentTime) {
  // We check from first to last to account for scrubbing
  if (currentTime > _75milestone && !_75per){
    _25per = true;
    _50per = true;
    _75per = true;
    write("We hit the 75% milestone");
  }
  else if (currentTime > _50milestone && !_50per) {
    _25per = true;
    _50per = true;
    write("We hit the 50% milestone");
  }
  else if (currentTime > _25milestone && !_25per){
    _25per = true;
    write("We hit the 25% milestone");
  }
});

this.videoPlayer.subscribe(OO.EVENTS.PLAYED, "func4", function (eventName){
  videoEnded = true;
  write("Video endeded");
});

function isFlash(){
  try{
    return OO.__internal.requiredInEnvironment('flash-playback');
  }
  catch(e){
    // Fallback, asume true
    return true;
  }
}

function write(text) {
    var textLog = document.getElementById("textLog");
    textLog.innerHTML = textLog.value+new Date()+":"+text+"\n";
}