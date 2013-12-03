<!DOCTYPE HTML>
<html>
<head>
   <meta http-equiv="Content-type" content="text/html; charset=utf-8">
   <title>PlayerV3 Demo</title>
   <meta name="viewport" content="width=device-width" />
   <script src='http://player.ooyala.com/v3/<?php echo $player_id; ?>?platform=flash&debug=true'></script>
</head>

<body>
    <div id="playerV3Container" style="background:black;width:640px;
    height:480px;"></div><br/>

    <script type="text/javascript" charset="utf-8">
    OO.ready(function() {
      window.pp = OO.Player.create('playerV3Container',
          '<?php echo $embed_code; ?>', {
              embedToken : '<?php echo $embed_token_url;?>'
          });
  });

    </script>

</body>
</html>