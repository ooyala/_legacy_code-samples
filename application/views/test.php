<!DOCTYPE html>
<html>
  <head>
  </head>
  <body>
    <p>It works!</p>
    <p>Printing all assets labeled with "Funny dogs"</p>
     <?php
         echo "Printing assets in the 'Funny dogs' label...";
         foreach($assets as $asset) {
             echo $asset->embed_code . " - " . $asset->name . "\n";
         }

      ?>
  </body>
</html>