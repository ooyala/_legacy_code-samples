this.mb = window.mb;

$ = OO.$;
_ = OO._;
console.log("related videos", related_videos);
console.log("trending videos", trending_videos);

this.mb.subscribe(OO.EVENTS.CONTENT_TREE_FETCHED, "func1", function (eventName, arg1, arg2) {
    var video_title = arg1.title;
    var video_description = arg1.description;

    $("#video_title").text(video_title);
    $("#description").text(video_description);
});

this.mb.subscribe(OO.EVENTS.METADATA_FETCHED, "func2", function (eventName, arg1, arg2){
    var metadata = arg1.base;
    var metadata_as_string = JSON.stringify(metadata);
    console.log(metadata);
    console.log(JSON.stringify(metadata));
    $("#metadata").text(metadata_as_string);
});

// Assign related and trending videos
var releatedVideoContainer = $('#related_video_container');
var trendingVideoContainer = $('.related_video_container');
for (var i = 0; i < related_videos.length; i++){
    try{
        var queryRelated = "#related_" + i;
        var queryTrending = "#trending_" + i;
        releatedVideoContainer.find(queryRelated).attr("src", related_videos[i].preview_image_url);
        trendingVideoContainer.find(queryTrending).attr("src", trending_videos[i].preview_image_url);
    } catch(e) {
        // do nothing
    }
}

$(".related_video").click( function(event){
    var callingContainer = event.target.id;
    var id = callingContainer.charAt(callingContainer.length - 1);
    var embedCode = related_videos[id].embed_code;
    videoPlayer.setCurrentItemEmbedCode(embedCode);
});

$(".trending_video").click( function(event){
     var callingContainer = event.target.id;
     var id = callingContainer.charAt(callingContainer.length - 1);
     var embedCode = trending_videos[id].embed_code;
     videoPlayer.setCurrentItemEmbedCode(embedCode);
});