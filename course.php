<?php
@$username=$_COOKIE['username'];
@$name=$_COOKIE['name'];
if(!@$username){
    header("Location:index.html");
    exit;
}
include "Course.class.php";
$grab = new Course($username);
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
$course_list=$grab->showCourse($url,$field);

$url='http://jxgl.gdufs.edu.cn/jsxsd/xsxkjg/comeXkjglb';
$myCourse=$grab->getMYCourse($url);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>抢课系统</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
    <style>
        .status-box{
            position: fixed;
            width: 250px;
            right: 100px;
        }
        #info{
            background: #fff;
            height:500px;
            overflow: auto;
         }
    </style>
    <script type="text/javascript" src="js/zepto.min.js"></script>
    <script type="text/javascript" src="js/index.js"></script>
</head>
<body>
<div class="container">
    <div class="well well-sm text-center" id="user" data-uid="<?php echo $username; ?>"><?php echo $name; ?></div>
    <div class="row">
        <div class="col-sm-9">
            <table class="table table-bordered table-hover table-striped" id="course-list" width="100%">
                <tr><th colspan="8" class="text-center">可选课</th></tr>
                <tr align="center">
                    <td></td>
                    <td>课程名称</td>
                    <td>老师</td>
                    <td>时间</td>
                    <td>地点</td>
                    <td>学分</td>
                    <td>人数</td>
                    <td>余量</td>
                </tr>
                <?php foreach($course_list['aaData'] as $index => $value): ?>
                <tr align="center">
                    <td>
                        <?php if($value['ctsm']==''){ ?>
                        <input type="checkbox" value="<?php echo $value['jx0404id'];?>" name="kcid">
                        <?php }else{ ?>
                        冲突
                        <?php } ?>
                    </td>
                    <td><?php echo $value['kcmc']; ?></td>
                    <td><?php echo $value['skls']; ?></td>
                    <td><?php echo $value['sksj']; ?></td>
                    <td><?php echo $value['skdd']; ?></td>
                    <td><?php echo $value['xf']; ?></td>
                    <td><?php echo $value['pkrs']; ?></td>
                    <td><?php echo $value['syrs']; ?></td>
                </tr>
                <?php endforeach; ?>
                <tr align="center">
                    <td colspan="8">
                        <a class=" btn btn-sm btn-default" id="btn-grab">抢课</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a class="btn btn-sm btn-default" id="btn-stop">停止</a>
                    </td>
                </tr>
            </table>
            <br/>
            <table class="tableed table table-bordered table-hover table-striped" id="course-my" width="100%">
                <?php echo $myCourse; ?>
            </table>
        </div>
        <div class="col-sm-3 status-box">
            <div class="status-box panel panel-default">
                <div class="panel-heading text-center">请求状态区</div>
                <div class="panel-body" id="info">

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>