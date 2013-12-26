<!DOCTYPE html>
<html>
 <head>
   <script src="http://player.ooyala.com/v3/<?php echo $player_id; ?>"></script>
  </head>
  <body>
     <h1>Cross resume example</h1>
     <div id='playerwrapper' style='width:920px;height:400px;'></div>
     <script>
         var playheadTime = <?php echo $playhead_time;?>;
         var videoPlayer =
            OO.Player.create('playerwrapper','<?php echo $embed_code; ?>', {
            embedToken: '<?php echo $embed_token_url; ?>',
            initialTime: playheadTime
        });
      </script>
  </body>
</html>