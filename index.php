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

$roles = getRoleList();

if(empty($roles))
{
  exit('Got an empty role list.');
}

for($i = 0; $i < sizeof($roles); $i++)
{
  $roles[$i] = new Role($roles[$i]);
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="sola.moe">
    <meta name="author" content="Yukino Hayakawa">
    <link rel="icon" href="content/theme/favicon.jpg">

    <title>Homepage</title>

    <link rel="stylesheet" href="thirdparties/tooltipster/tooltipster.css">
    <link rel="stylesheet" href="thirdparties/tooltipster/themes/tooltipster-shadow.css">
    <link rel="stylesheet" href="content/theme/home.css">
  </head>

  <body>

    <div id="container-page" class="container-bgimage bgimage vertical-middle-container">
      <div id="layer-page-logo" class="layer-inner layer-page-logo"></div>
      <div id="container-login" class="layer-inner vertical-middle">
        <div id="login">
          <div id="layer-login-background" class="layer-inner blur container-bgimage bgimage"></div>
          <div id="layer-login-content" class="layer-inner">
            <div id="role-container">
              <img class="role-avatar-middle" id="role-select-player"/>
              <img class="role-avatar-middle" id="role-select-match"/> 
            </div>
            <div id="role-select-submit" class="button">CONTINUE</div>
          </div>
        </div>
      </div>
    </div>

    <div id="role-select-player-list" class="hidden">
      <div class="role-select-title">选择我的形象</div>
      <?php
        foreach ($roles as $role)
        {
          echo('<img src="' . $role->getAvatarPath() . '" class="role-avatar-small role-select-player" role="' . $role->role . '"/>'. "\n");
        }
      ?>
    </div>

    <div id="role-select-match-list" class="hidden">
      <div class="role-select-title">选择寻找目标</div>
      <img src="content/theme/female.png" class="role-avatar-small role-select-match" role="female"/>
      <img src="content/theme/male.png" class="role-avatar-small role-select-match" role="male"/>
    </div>

    <form id="role-select-form" action="createSession.php" method="post">
      <input type="hidden" id="selected-role" name="role" value="<?php echo($roles[0]->role); ?>"/>
      <input type="hidden" id="selected-match" name="match" value="female"/>
    </form>

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="thirdparties/jquery/jquery.min.js"></script>
    <script src="thirdparties/tooltipster/jquery.tooltipster.min.js"></script>
    <script src="scripts/home.js"></script>

  </body>
</html>
