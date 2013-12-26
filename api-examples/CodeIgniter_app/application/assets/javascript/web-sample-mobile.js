// This example leverages what is already done in web-sample.js
// This code only cares about resizing and content-deliver
// on document.ready
// This is meant as an example of what you can achieve
// with mobile versions of your website
OO.ready(function (OO) {
  if (!$) {
    $ = OO.$;
  }
  $(document).ready(function() {
      var windowWidth = $(window).width();
      if (windowWidth < 700) {
          console.log(windowWidth);
          $(".related_video_container").hide();
          // Use the full width of the player to display the player
          $("#playerwrapper").width("100%");
          // Use aspect ratio 4:3
          var computedWidth = $("#playerwrapper").width();
          var playerHeight = computedWidth * (3/4);
          $("#playerwrapper").height(playerHeight);
      } else {
          $(".related_video_container").show();
      }
  });
});
