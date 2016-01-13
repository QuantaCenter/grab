<?php
/**
 * Created by PhpStorm.
 * User: zjy
 * Date: 2016/1/11
 * Time: 23:38
 */
header("Content-type:text/html;charset=utf-8");
include "Course.class.php";
$username = $_POST['username'];
$password = $_POST['password'];
$type=$_POST['type'];
$grab = new Course($username);
$url = "http://jxgl.gdufs.edu.cn/jsxsd/xk/LoginToXkLdap";
$field = array('USERNAME'=>$username,'PASSWORD'=>$password);
if($grab->login($url,$field)){
    $res = $grab->loginResult();
    setcookie('username',$res['username']);
    setcookie('name',$res['name']);

    switch($type){
        case 1:
            header("Location:course.php");
            break;
        case 2:
            header("Location:courseAll.php");
            break;
        default:
            header("Location:course.php");
    }
}
else{
	exit("登陆失败");
}