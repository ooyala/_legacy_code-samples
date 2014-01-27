<!DOCTYPE html>
<html>
  <body>
    <script src="http://player.ooyala.com/v3/<?php echo $player_id; ?>"></script>
    <div id='playerContainer'></div>
    <script>
OO.ready( function(OO) {
    var videoPlayer = OO.Player.create('playerContainer', '<?php echo $embed_code; ?>', { width: 640, height: 360 });
    // Preserve the mb to acces it within simple_milestones
    var mb = videoPlayer.mb
});

    </script>
      <div id="logName"><textarea id="textLog" style="width:640px; height:300px;">Log file:</textarea></div>

    <script src="<?php echo base_url();?>/application/assets/javascript/simple_milestones.js"> </script>
  </body>
</html>