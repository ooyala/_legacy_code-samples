// This example leverages what is already done in web-sample.js
// This code only cares about resizing and content-deliver
// on document.ready
// This is meant as an example of what you can achieve
// with mobile versions of your website

$(document).ready(function() {
    var availableWidth = $(window).width();
    var availableHeight = $(window).height();
    if (availableWidth < 700) {
        $(".related_video_container").hide();
        $("#playerwrapper").width("100%");
        // Use aspect ratio 4:3
        var computedWidth = $("#playerwrapper").width();
        var playerHeight = computedWidth * (3/4);
        $("#playerwrapper").height(playerHeight);
    } else {
        $(".related_video_container").show();
    }
});