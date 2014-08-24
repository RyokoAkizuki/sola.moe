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

function getFileList($dir)
{
    $predir = __DIR__;
    chdir($dir);
    $files = glob('*');
    chdir($predir);
    return $files;
}

function randomFile($dir)
{
    $files = glob($dir . '/*.*');
    $index = array_rand($files);
    return $files[$index];
}

function getRoleList()
{
    return getFileList('content/roles');
}

class Role
{
    public $role;
    public $sex;

    function __construct($_role)
    {
        $this->role = $_role;
        if(strpos($_role, 'female') == 0)
        {
            $this->sex = 'female';
        }
        else if(strpos($_role, 'male') == 0)
        {
            $this->sex = 'male';
        }
        else
        {
            $this->sex = 'unknown';
        }
    }

    function isValid()
    {
        return file_exists('content/roles/' . $this->role);
    }

    /*
    public function echoImgAvatar($size, array $attrs)
    {
        $classname = '';

        switch($size)
        {
            case 'small': $classname = 'role-avatar-small'; break;
            case 'middle' : $classname = 'role-avatar-middle'; break;
            default: echo('Warning: Unexpected avatar size.'); return;
        }

        $src = 'content/roles/' . $this->role . '/avatar.png';

        echo('<img src="' . $src . '" class="' . $classname . '" role="' . $this->role . '"');
        foreach ($attrs as $attr => $value) {
            echo(' ' . $attr . '=' . $value . '"');
        }
        echo("/>\n");
    }
    */
    public function getAvatarPath()
    {
        return 'content/roles/' . $this->role . '/avatar.png';
    }

    public function getSuitPath($suit = 'normal')
    {
        return 'content/roles/' . $this->role . '/suits/' . $suit . '.png';
    }
}
