<!DOCTYPE html>
<html>
 <head>
   <script src="http://player.ooyala.com/v3/<?php echo $player_id; ?>"></script>
  </head>
  <body>
     <div id='playerwrapper' style='width:920px;height:400px;'></div>
     <script>
         var videoPlayer =
            OO.Player.create('playerwrapper', '<?php echo $embed_code; ?>', {
        });
      </script>
  </body>
</html>