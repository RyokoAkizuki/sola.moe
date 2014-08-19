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

<?php

$conn = new MongoClient();
$db = $conn->solamoe;

$col_user = $db->user;
$col_session = $db->session;
$col_chattext = $db->chattext;

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
        return 'content/roles/' . $this->role . '/' . $suit . '.png';
    }
}

class Session
{
    public $id;
    public $role; 
    public $sex;
    public $seek;
    public $lastping;

    function __construct($_id)
    {
        $data = $col_session->findOne(array('_id' => new MongoId($_id)));
        if(!isset($data))
        {
            exit('Invalid session.');
        }
        $this->id = (string)$data->_id;
        $this->role = (string)$data->role;
        $this->sex = (string)$data->sex;
        $this->seek = (string)$data->seek;
        $this->lastping = (int)$data->lastping;
    }

    public function ping()
    {
        $time = time();
        $col_session->update(array('_id' => new MongoId($this->id)), array('lastping' => $time));
        $this->lastping = $time;
    }

    public function checkOnline()
    {
        return (time()- $this->lastping <= 30);
    }

    public function destroy()
    {
        $col_session->remove(array('_id' => new MongoId($this->id)));
    }
}

function createSession(Role $role, $seekfor)
{
    $sessionid = new MongoId();
    global $col_session;
    $col_session->insert(array(
        '_id' => $sessionid,
        'role' => $role->role,
        'sex' => $role->sex,
        'seek' => $seekfor,
        'lastping' => time(),
        'paired' => false,
        'pairedsid' => new MongoId('000000000000000000000000')
        ));
    return $sessionid;
}
