<?php
include ("Course.class.php");
switch($_GET['action']){
	case 'login':
		login();
		break;
	case 'grab':
		grab();
		break;
	case 'grabTX':
		grabTX();
		break;
	case 'showTX':
		showTX();
		break;
	case 'myCourse':
		getMyCourse();
		break;
	case 'del':
		del();
		break;
}
function login(){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$grab = new Course($username);
	$url = "http://jxgl.gdufs.edu.cn/jsxsd/xk/LoginToXkLdap";
	$field = array('USERNAME'=>$username,'PASSWORD'=>$password);
	if($grab->login($url,$field)) {
		$res = $grab->loginResult();
		echo json_encode($res);
	}
	else{
		echo false;
	}
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
	$data['course']=$grab->getMYCourse($url);
	echo json_encode($data);
}
function showTX(){
	$field = array(
		'sEcho'=>1,
		'iColumns'=>11,
		'sColumns'=>'',
		'iDisplayStart'=>0,
		'iDisplayLength'=>100,
		'mDataProp_0'=>'kch',//课程号
		'mDataProp_1'=>'kcmc',//课程名
		'mDataProp_2'=>'xf',//学分
		'mDataProp_3'=>'skls',//上课老师
		'mDataProp_4'=>'sksj',//上课时间
		'mDataProp_5'=>'skdd',//上课地点
		'mDataProp_6'=>'xkrs',//学科人数
		'mDataProp_7'=>'syrs',//剩余人数
		'mDataProp_9'=>'ctsm',//时间冲突
		'mDataProp_10'=>'szkcflmc',//类别
		'mDataProp_11'=>'czOper',//操作
	);
	$query['kcxx']=urlencode($_GET['kcxx']);//课程搜索
	$query['skls']=urlencode($_GET['skls']);//老师
	$query['skxq']=urlencode($_GET['skxq']);//星期
	$query['skjc']=urlencode($_GET['skjc']);//节次
	$query['sfym']=urlencode($_GET['sfym']);//过滤已满
	$query['sfct']=urlencode($_GET['sfct']);//过滤冲突
	$query['szjylb']=urlencode($_GET['szjylb']);//类别
	$query['szkclb']=urlencode($_GET['szkclb']);//老类别，没用了

	$url="http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xsxkGgxxkxk";
	$url.="?".http_build_query($query);
	$grab = new Course($_GET['username']);
	$arr=$grab->showCourse($url,$field);
	echo $arr;
}

function grabTX(){
	$grab = new Course($_POST['username']);
	$course=explode(',',$_POST['course']);
	$arr=array();
	if(is_array($course)){
		foreach($course as $index=>$value){
			$url = 'http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/ggxxkxkOper?xkzy=&trjf=&jx0404id='.$value;
			$res = $grab->submitForm($url);
			$arr[]=json_decode($res,true);
		}
		echo json_encode($arr);
	}
	else{
		$url = 'http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/ggxxkxkOper?xkzy=&trjf=&jx0404id='.$course;
		$res = $grab->submitForm($url);
		echo $res;
	}

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