<?php
/*
 * Copyright 2014 Yukino Hayakawa<tennencoll@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>

<?php

$sid = $_GET['sid'];

if(!moe_isSessionValid($sid))
{
    exit('Invalid session.');
}

if(moe_getSessionPair($sid) != -1)
{
    exit('Trying to replicate session.');
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="sola.moe waiting room">
    <meta name="author" content="Yukino Hayakawa">
    <link rel="icon" href="content/theme/favicon.jpg">

    <title>Pairing...</title>
  </head>

  <body>
    <script src="thirdparties/jquery/jquery.min.js"></script>

    <form id="room-redirect" action="room.php" method="post">
      <input type="hidden" name="sid" value="<?php echo($_GET['sid']); ?>"/>
    </form>

    <script>
        var paired = false;
        function seekPair(){
            $.post('ajax_seekPair.php', { "sid" : <?php echo($sid); ?> },
            function(data) {
                var response = $.parseJSON(data);
                if(response['paired'] == true)
                {
                    paired = true;
                    $('#room-redirect').submit();
                }
                else
                {
                    setTimeout(seekPair, 5000);
                }
            });
        }
        $(document).ready(function() {
            seekPair();
            $(window).on('beforeunload', function(){
                if(!paired)
                {
                    return 'Are you sure you want to leave?';
                }
            });
            $(window).on('unload', function(){
                if(!paired)
                {
                    $.post('ajax_closeSession.php', { "sid" : <?php echo($sid); ?> });
                }
            });
        });
    </script>
  </body>
</html>
