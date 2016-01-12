<?php
header("Content-type: text/html; charset=gb2312");
include ("Course.class.php");
switch($_GET['action']){
	case 'grab':
		grab();
		break;
	case 'myCourse':
		getMyCourse();
		break;
	case 'del':
		del();
		break;
}
function grab(){//提交表单
	$grab = new Course($_POST['username']);
	$course=explode(',',$_POST['course']);
	$arr=array();
	if(is_array($course)){
		foreach($course as $index=>$value){
			$url = 'http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xxxkOper?jx0404id='.$value;
			$res = $grab->submitForm($url);
			$arr[]=json_decode($res,true);
		}
		echo json_encode($arr);
	}
	else{
		$url = 'http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xxxkOper?jx0404id='.$course;
		$res = $grab->submitForm($url);
		echo $res;
	}
}
function getMyCourse(){
	$grab = new Course($_POST['username']);
	$url='http://jxgl.gdufs.edu.cn/jsxsd/xsxkjg/comeXkjglb';
	$myCourse=$grab->getMYCourse($url);
	echo $myCourse;
}
function del(){
	$grab = new Course($_POST['username']);
	$url='http://jxgl.gdufs.edu.cn/jsxsd/xsxkjg/xstkOper?jx0404id='.$_POST['course'];
	$re=$grab->delCourse($url);
	echo $re;
}
function my_iconv($data){
	foreach ($data as $key=>$value) {
		$data[$key] = iconv("GB2312", "UTF-8", $value);
	}
	return $data;
}
?>