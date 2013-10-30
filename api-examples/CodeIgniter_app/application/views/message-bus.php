<!DOCTYPE html>
<html>
 <head>
   <script src="http://player.ooyala.com/v3/<?php echo $player_id; ?>"></script>
  </head>
  <body>
    <h1>Message bus sample</h1>
    <h3>You can see the events and their arguments on the console too!</h3>
     <div id='playerwrapper' style='width:920px;height:400px;'></div>

    <div id="logName"><textarea id="textLog" style="width:640px; height:300px;"></textarea></div>

     <script>
         var videoPlayer =
            OO.Player.create('playerwrapper', '<?php echo $embed_code; ?>', {
        });
      </script>

      <script>
        videoPlayer.mb.subscribe('*', 'anyName', function (eventName, arg1, arg2) {
          var text = eventName + " " + arg1 + " " + arg2;
          write(text);
          console.log(eventName, arg1, arg2);
        });

        function write(text) {
            var textLog = document.getElementById("textLog");
            textLog.innerHTML = textLog.value+new Date()+":"+text+"\n";
        }
      </script>

  </body>
</html>