// Define a plugin to interact with the player.
// It helps us create a namepsace
// and hold references to helper libraries
OO.plugin("WebSample", function (OO, _, $, W) {
  WebExample = function (mb, id) {
      this.mb = mb;
      this.id = id;
      this.init();
  };

  _.extend(WebExample.prototype, {
    init: function(){
      // subscribe to relevant events
      this.mb.subscribe(OO.EVENTS.CONTENT_TREE_FETCHED,
        'WebSample', _.bind(this.onContentFetched, this));
      this.mb.subscribe(OO.EVENTS.METADATA_FETCHED,
        'WebSample', _.bind(this.onMetadataFetched, this));
    },

    onContentFetched: function (eventName, content) {
      // Set the title and description in the page
      var video_title = content.title;
      var video_description = content.description;
      $("#video_title").text(video_title);
      $("#description").text(video_description);
    },

    onMetadataFetched: function (eventName, metadata) {
      var metadata_as_string = JSON.stringify(metadata.base);
      $("#metadata").text(metadata_as_string);
    },

  });
  return WebExample;
});

// Non-player related

// Wait until the player is ready for playback
OO.ready(function (OO){
  // If jQuery is not defined, use the jQuery version from the player
  if (!$) {
    $ = OO.$;
  }

  // Reference to the related and trending video containers to avoid
  // having to search the entire DOM when looking for specific elements
  var releatedVideoContainer = $('.related_video_container');
  var trendingVideoContainer = $('.trending_video_container');

  for (var i = 0; i < related_videos.length; i++) {
      try {
          // Set the image src to one of the related/trending videos
          var queryRelated = "#related_" + i;
          var queryTrending = "#trending_" + i;
          releatedVideoContainer.find(queryRelated).attr("src", related_videos[i].preview_image_url);
          trendingVideoContainer.find(queryTrending).attr("src", trending_videos[i].preview_image_url);
      } catch(e) {
          // do nothing
      }
  }

  // When user clicks a related or trending video change the current video.
  $(".related_video").click( function(event) {
      var callingContainer = event.target.id;
      var id = callingContainer.charAt(callingContainer.length - 1);
      var embedCode = related_videos[id].embed_code;
      videoPlayer.setCurrentItemEmbedCode(embedCode);
  });
  $(".trending_video").click( function(event) {
       var callingContainer = event.target.id;
       var id = callingContainer.charAt(callingContainer.length - 1);
       var embedCode = trending_videos[id].embed_code;
       videoPlayer.setCurrentItemEmbedCode(embedCode);
  });
});
