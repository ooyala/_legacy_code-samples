<!DOCTYPE html>
<html>
 <head>
   <script src="http://player.ooyala.com/v3/<?php echo $player_id; ?>"></script>
  </head>
  <body>
     <h1>Example one</h1>
     <p>Google IMA, playerToken and cross-resume</p>
     <div id='playerwrapper' style='width:920px;height:400px;'></div>
     <script>
         var playheadTime = <?php if($playhead_time) {echo $playhead_time;} else echo '0';?>;
         var videoPlayer =
            OO.Player.create('playerwrapper','<?php echo $embed_code; ?>', {

            <?php
            if($embed_token_url){
              echo "embedToken : " . "\"" . $embed_token_url . "\"" . ",";
            }
            ?>
            <?php
            if($uses_google_ima){
              $output = "\"google-ima-ads-manager\"" . ":" . "{";
              $output .= "\"adTagUrl\"" . ":" . "\"" . $adTagUrl . "\"";
              $output .= "}" . ",";
              echo $output;
            }
            ?>

            initialTime: playheadTime
        });
      </script>
  </body>
</html>