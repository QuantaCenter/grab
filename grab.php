<?php
header("Content-type: text/html; charset=gb2312");
include ("Course.class.php");
switch($_GET['action']){
	case 'login':
		login();
		break;
	case 'choose':
		choose();
		break;
	case 'grab':
		grab();
		break;
}
function login(){//登录
	$grab = new Course($_POST['username']);
	$username = $_POST['username'];
	$password = $_POST['password'];
	$url = "http://jw.gdufs.edu.cn/pkmslogin.form";
	$field = array('username'=>$username,'password'=>$password,'login-form-type'=>'pwd');
	if($grab->login($url,$field)){
		$res = my_iconv($grab->loginResult());
		echo json_encode($res);
	}
	else{
		echo 0;
	}
}
function choose(){//查看课程列表
	$grab = new Course($_POST['username']);
	$url = "http://jw.gdufs.edu.cn/".$_POST['type']."&xh=".$_POST['username']."&xm=".urlencode($_POST['name']);
	$res = my_iconv($grab->showCourse($url));
	echo json_encode($res);
}
function grab(){//提交表单
	$grab = new Course($_POST['username']);
	$url = "http://jw.gdufs.edu.cn/".$_POST['type']."&xh=".$_POST['username']."&xm=".urlencode($_POST['name']);
	$course = split(",", $_POST['course']);
	$input = $_POST['input'];
	$arr = split("=>|&", $input);
	$from = array();
	for($i=0;$i<count($arr);$i+=2){
		$form[$arr[$i]] = $arr[$i+1];
	}
	$form['ddl_ywyl'] = "";//有无余量
	$form['ddl_kcgs'] = "";//课程归属
	$form['ddl_sksj'] = "";//上课时间
	$form['ddl_xqbs'] = 2;//1:北校区,2:南校区
	for($i=0;$i<count($course);$i++){
		$form[$course[$i]] = "on";
	}
	echo json_encode($grab->submitForm($url,$form));
}
function my_iconv($data){
	foreach ($data as $key=>$value) {
	    $data[$key] = iconv("GB2312", "UTF-8", $value);
	}
	return $data;
}
?>