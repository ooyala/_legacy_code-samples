<!DOCTYPE html>
<html>
 <head>
   <script src="http://player.ooyala.com/v3/<?php echo $player_id; ?>"></script>
  </head>
  <body>
     <h1>Example one</h1>
     <p>Google IMA, playerToken and cross-resume</p>
     <div id='playerwrapper' style='width:920px;height:400px;'></div>
     <div id="logName"><textarea id="textLog" style="width:640px; height:300px;">Log file:</textarea></div>
     <script>
         var playheadTime = <?php if($playhead_time) {echo $playhead_time;} else echo '0';?>;
         OO.ready ( function () {
            window.videoPlayer =
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
         });
      </script>
      <script src="<?php echo base_url();?>/application/assets/javascript/milestones_with_ads.js"></script>
  </body>
</html>