<!DOCTYPE html>
<html>
 <head>
    <!-- Player Id is taken from view-source:http://se.ooyala.com/ts/fchavez/ad_demo/google_ima_preroll_companion.html -->
   <script src="http://player.ooyala.com/v3/<?php echo $player_id; ?>"></script>
  </head>
  <body>
    <h1>Google IMA Sample</h1>
     <div id='playerwrapper' style='width:920px;height:400px;'></div>
     <script>
         var videoPlayer =
            OO.Player.create('playerwrapper', '<?php echo $embed_code; ?>', {
            'google-ima-ads-manager':{
                'adTagUrl': '<?php echo $adTagUrl ?>'
            }
        });

        videoPlayer.mb.subscribe("*", "myPage", function(eventName, arg1, arg2){
             console.log(eventName, arg1, arg2);
        });
      </script>
  </body>
</html>