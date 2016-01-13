<?php
/**
 * Created by PhpStorm.
 * User: zjy
 * Date: 2016/1/13
 * Time: 14:04
 */
include "Course.class.php";
$action=$_GET['action'];
switch($action){
    case 'into':
        into();
        break;
    case 'curl':
        curlto();
        break;
}
function into(){
    $username=$_POST['username'];
    $grab = new Course($username);
    $grab->intoCourse();
    echo 'success';
}

function curlto(){
    $username=$_POST['username'];
    $grab = new Course($username);
    $url='http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/ggxxkxkOper?jx0404id=20152016200000000000000020067&xkzy=&trjf=';
    $c=$grab->submitForm($url);
    echo $c;
}