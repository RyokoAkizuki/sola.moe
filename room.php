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

require_once('common.php');

$sid = (int)$_POST['sid'];

if(!moe_isSessionValid($sid))
{
    exit('Invalid session.');
}

$role = new Role(moe_getPairRole($sid));

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="sola.moe chatroom">
    <meta name="author" content="Yukino Hayakawa">
    <link rel="icon" href="content/theme/favicon.jpg">

    <title>Chat</title>

    <link rel="stylesheet" href="content/theme/room.css">
    <link rel="stylesheet" href="thirdparties/notifybar/jquery.notifyBar.css">

    <style type="text/css">
    #layer-room-bg {
      background-image: url(<?php echo(randomFile('content/backgrounds')); ?>);
    }
    </style>
  </head>

  <body>

    <div id="container-page">
      <div id="layer-room-bg" class="layer-inner bgimage"></div>
      <div id="layer-room-logo" class="layer-inner layer-page-logo"></div>
      <div id="layer-room-role-matched" class="layer-inner role-half-big-female-0 drop-shadow"></div>
      <div id="layer-room-chatbox" class="layer-inner vertical-middle-container">
        <div id="layer-room-chatbox-bg" class="layer-inner blur"></div>
        <div id="chat-text-wrapper" class="layer-inner vertical-middle">
          <div id="chat-text-matched"></div>
          <div id="chat-text-spliter"></div>
          <form id="chat-text-submit" action="ajax_sentMessage.php" method="post">
            <input type="text" name="message" id="chat-text-player"/>
            <input type="submit" class="hidden"/>
          </form>
        </div>
      </div>
    </div>

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="thirdparties/jquery/jquery.min.js"></script>
    <script src="thirdparties/notifybar/jquery.notifyBar.js"></script>

    <script>
      var valid = true;

      function peekMessage(){
          $.post('ajax_peekMessage.php', { "sid" : <?php echo($sid); ?> },
          function(data) {
              var response = $.parseJSON(data);
              if(response['pair'] == -1)
              {
                $.notifyBar({
                  cssClass: "warning",
                  html: "会话已失效. 对方可能已经退出.<a href=\"index.php\">返回主页</a>",
                  close: true,
                  closeOnClick: false
                });
                valid = false;
                return;
              }
              if(response['message'])
              {
                  $('#chat-text-matched').text(response['message']);
              }
              setTimeout(peekMessage, 3000);
          });
      }

      $(document).ready(function() {

        // CAUTION: DO NOT USE 'background' attribute. It will overwrite other background styles.
        $('#layer-room-role-matched').css('background-image', 'url(<?php echo($role->getSuitPath()); ?>)');

        // Send message.
        $("#chat-text-submit").submit(function(e) {

          $.post('ajax_sendMessage.php', {
            "sid" : <?php echo($sid); ?>,
            "message": $("#chat-text-player").val()
          })
          .done(function() {
            $("#chat-text-player").val('');
          })
          .fail(function() {
            alert( "Message sending failed." );
          });

          return false;

        });

        // Clear session.
        $(window).on('beforeunload', function(){
          if(valid)
          {
            return 'Are you sure you want to leave?';
          }
        });
        $(window).on('unload', function(){
          $.post('ajax_closeSession.php', { "sid" : <?php echo($sid); ?> });
        });

        peekMessage();

      });
    </script>
  </body>
</html>
