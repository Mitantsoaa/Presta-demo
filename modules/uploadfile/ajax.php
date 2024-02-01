<?php
require_once ('../../config/config.inc.php');
require_once ('../../init.php');
$obj_mp = Module::getInstanceByName('uploadfile');
if($_FILES){
    echo $obj_mp->uploadAjax($_FILES['file']);
}
