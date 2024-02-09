<?php
require_once ('../../config/config.inc.php');
require_once ('../../init.php');
$obj_mp = Module::getInstanceByName('uploadfile');
if($_FILES){
    header('Content-Type: application/json');
    echo $obj_mp->uploadAjax($_FILES['file']);
}
