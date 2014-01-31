<!DOCTYPE html>
<!--
This example sets up a simple desktop page that shows a main video, along with related videos
and videos that are trending right now. For more information on how to get to those, please see
the controller web_example.php
-->
<html>
<head>
 <script src="http://player.ooyala.com/v3/<?php echo $player_id; ?>"></script>
 <script src="<?php echo base_url();?>/application/assets/Web_Example/javascript/gaTrack.js"></script>
 <script>
    ooyalaGaTrackSettings = {
      verboseLogging: true
    }
  </script>
 <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>application/assets/Web_Example/css/web-sample.css">
</head>

<body>
  <div class="main-wrap">
    <ul class="nav skrollable skrollable-between" data-0="position:absolute;" data-630="position:fixed;">
      <li><a href="#"> <img class="logo" src="http://www.ooyala.com/careers-mx/2014/assets/images/logo.png"></img> </a></li>
      <li>
        <a href="#"> Web example </a>
        <ul>
          <li>
            <a href="#"> Sublink </a>
            <ul>
              <li><a href="#">Sub-Sublink</a></li>
              <li><a href="#">Sub-Sublink 2</a></li>
            </ul>
          </li>
          <li><a href="#"> Sublink 2 </a></li>
          <li><a href="#"> Sublink 3 </a></li>
        </ul>
      </li>
      <li><a href="#">Options</a></li>
      <!-- These will be shown from right to left in the nav
                starting from firstone to lastone -->
      <li class="right"><a href="#">Log In</a></li>
      <li class="right"><a href="#">Try it now</a></li>
    </ul>

    <div class="main">
      <div id='playerwrapper' class="player"></div>
      <div class="player-description">
        <h1 id="video-title"> Title </h1>
        <p id="description"> Description </p>
        <!-- To add metadata info you just need to add the tag
                 with a class="meta" and id="key"-->
        <p id="releaseDate" class="meta"> </p>
        <p id="director" class="meta"> </p>
      </div>
    </div>

    <div class="extra-video-containter">
      <div class="related-video-container">
        <h2>Related</h2>
        <div class="video-container">
          <a href="#">
            <img id="related-1" class="video related-video" src=""/>
            <p id="related-caption-1" class="caption">Caption x</p>
          </a>
        </div>
        <div class="video-container">
          <a href="#">
            <img id="related-2" class="video related-video" src=""/>
            <p id="related-caption-2" class="caption">Caption x</p>
          </a>
        </div>
        <div class="video-container">
          <a href="#">
            <img id="related-3" class="video related-video" src=""/>
            <p id="related-caption-3"class="caption">Caption x</p>
          </a>
        </div>
        <div class="video-container">
          <a href="#">
            <img id="related-4" class="video related-video" src=""/>
            <p id="related-caption-4"class="caption">Caption x</p>
          </a>
        </div>
      </div>

      <div class="trending-video-container">
        <h2>Trending</h2>
        <div class='video-container'>
          <a href="#">
            <img id="trending-1" class="video trending-video" src=""/>
            <p id="trending-caption-1" class="caption">Trending</p>
          </a>
        </div>
        <div class='video-container'>
          <a href="#">
            <img id="trending-2" class="video trending-video" src=""/>
            <p id="trending-caption-2" class="caption">Trending</p>
          </a>
        </div>
        <div class="video-container">
          <a href="#">
            <img id="trending-3" class="video related-video" src=""/>
            <p id="trending-caption-3"class="caption">Caption x</p>
          </a>
        </div>
        <div class="video-container">
          <a href="#">
            <img id="trending-4" class="video related-video" src=""/>
            <p id="trending-caption-4"class="caption">Caption x</p>
          </a>
        </div>
      </div>
      <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    </div>
  </div>

  <script>
    OO.ready(function (){
      window.videoPlayer = OO.Player.create('playerwrapper', '<?php echo $embed_code; ?>', {});
    });

    var related_videos = <?php echo $related_videos;?>;
    var trending_videos = <?php echo $trending_videos;?>;
  </script>
  <script src="<?php echo base_url();?>/application/assets/Web_Example/javascript/web-sample.js"> </script>

  <!-- Library used to move the nav through the page -->
  <script type="text/javascript" src="http://prinzhorn.github.io/skrollr/dist/skrollr.min.js"></script>
  <script type="text/javascript">
    var s = skrollr.init();
  </script>

  <script>
   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
   })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

   ga('create', 'UA-304811-17', 'localdevelopment.com');
   ga('send', 'pageview');
  </script>
</body>
</html>