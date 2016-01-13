<?php
/**
 * Created by PhpStorm.
 * User: zjy
 * Date: 2016/1/13
 * Time: 15:05
 */
include "Course.class.php";

print("username: ");
$username=trim(fgets(STDIN));
print("password:");
$password=trim(fgets(STDIN));

$grab = new Course($username);
$url = "http://jxgl.gdufs.edu.cn/jsxsd/xk/LoginToXkLdap";
$field = array('USERNAME'=>$username,'PASSWORD'=>$password);
if($grab->login($url,$field)){
    print("login success! \n");

    $url = "http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xsxkXxxk";
    $field = array(
        'sEcho'=>1,
        'iColumns'=>8,
        'sColumns'=>'',
        'iDisplayStart'=>0,
        'iDisplayLength'=>0,
        'mDataProp_0'=>'kch',//课程号
        'mDataProp_1'=>'kcmc',//课程名
        'mDataProp_2'=>'xf',//学分
        'mDataProp_3'=>'skls',//上课老师
        'mDataProp_4'=>'sksj',//上课时间
        'mDataProp_5'=>'skdd',//上课地点
        'mDataProp_6'=>'ctsm',//时间冲突
        'mDataProp_7'=>'czOper',//操作
    );
	$content=$grab->showCourse($url,$field);
	$course_list=json_decode(trim($content,chr(239).chr(187).chr(191)),true);

    $cid=array();
    foreach($course_list['aaData'] as $index => $value){
        $cid[$index]=$value['jx0404id'];
        if($value['ctsm']==''){
            print("{$index}\t{$value['kcmc']}\t{$value['skls']}\t{$value['sksj']}\r\n");
        }
        else{
            print("冲突\t{$value['kcmc']}\t{$value['skls']}\t{$value['sksj']}\r\n");
        }
    }

    $course_list=null;

    print("input your choose as 1,2,3,4:\n");
    $choose=trim(fgets(STDIN));
    $arr_choose=explode(',',$choose);

    $fp=fopen($username."console.txt",'w+');
    $i=0;
    $j=1;
    while(true){
        $msg="{$i} grabbing··· \r\n";
        fwrite($fp,$msg);
        foreach($arr_choose as $in=>$va){
            $url = 'http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xxxkOper?jx0404id='.$cid[$va];
            $res = $grab->submitForm($url);
            $msg=date("Y-m-d H:i:s")."\t ".$res."\r\n";
            fwrite($fp,$msg);
        }
        $i++;
        if($i/1000>$j){
        	$j++;
        	fclose($fp);
        	$fp=fopen($username."console.txt",'w+');
        }
        usleep(50000);
    }
}