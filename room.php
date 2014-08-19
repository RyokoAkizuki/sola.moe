<?
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

<? require_once('common.php'); ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="sola.moe chatroom">
    <meta name="author" content="Yukino Hayakawa">
    <link rel="icon" href="content/theme/favicon.jpg">

    <title>Homepage</title>

    <link rel="stylesheet" href="content/theme/room.css">
    
    <style type="text/css">
    #layer-room-bg {
      background-image: url(<? echo(randomFile('content/backgrounds')); ?>);
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
        <!--div id="layer-room-chatbox-text-wrap" class="layer-inner">
          <div id="layer-room-chatbox-text" class="layer-inner">
            <span class="chattext">早上好~</span>
          </div>
        </div-->
        <div id="chat-text-wrapper" class="layer-inner vertical-middle">
          <div id="chat-text-matched">嗷嗷嗷嗷</div>
          <div id="chat-text-spliter"></div>
          <form action="/" method="post">
            <input type="text" name="input-player" id="chat-text-player"/>
            <input type="submit" class="hidden"/>
          </form>
        </div>
      </div>
    </div>

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="thirdparties/jquery/jquery.min.js"></script>
  </body>
</html>
