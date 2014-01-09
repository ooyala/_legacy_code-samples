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
      $("#video-title").text(video_title);
      if( video_description ) {
        $("#description").text(video_description)
      } else {
        $("#description").text("There is no description");
      }
    },

    onMetadataFetched: function (eventName, metadata) {
      var jsonMetadata = metadata.base;
      $(".meta").each(function() {
        var value = jsonMetadata[this.id];
        if( value ) {
          $(this).text(value);
        } else {
          $(this).text("There is no metadata for: " + this.id);
        }
      });
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
  var releatedVideoContainer = $('.related-video-container');
  var trendingVideoContainer = $('.trending-video-container');

  for (var i = 0; i < related_videos.length; i++) {
      try {
          // Set the image src to one of the related/trending videos
          var queryRelated = "#related-" + i;
          var captionRelated = "#related-caption-" + i;
          releatedVideoContainer.find(queryRelated).attr("src", related_videos[i].preview_image_url);
          releatedVideoContainer.find(captionRelated).text(related_videos[i].name);

          var queryTrending = "#trending-" + i;
          var captionTrending = "#trending-caption-" + i;
          trendingVideoContainer.find(queryTrending).attr("src", trending_videos[i].preview_image_url);
          trendingVideoContainer.find(captionTrending).text(trending_videos[i].name);
      } catch(e) {
          // do nothing
      }
  }

  // When user clicks a related or trending video change the current video.
  $(".related-video").click( function(event) {
      var callingContainer = event.target.id;
      var id = callingContainer.charAt(callingContainer.length - 1);
      var embedCode = related_videos[id].embed_code;
      videoPlayer.setCurrentItemEmbedCode(embedCode);
  });
  $(".trending-video").click( function(event) {
       var callingContainer = event.target.id;
       var id = callingContainer.charAt(callingContainer.length - 1);
       var embedCode = trending_videos[id].embed_code;
       videoPlayer.setCurrentItemEmbedCode(embedCode);
  });
});
