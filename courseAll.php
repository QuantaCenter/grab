<?php
@$username=$_COOKIE['username'];
@$name=$_COOKIE['name'];
if(!@$username){
    header("Location:index.html");
    exit;
}
include "Course.class.php";
$grab = new Course($username);

$grab->intoCourse();
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
            font-size: 12px;
        }
        #info{
            background: #fff;
            max-height:300px;
            font-size: 10px;
            overflow: auto;
         }
        .delcon{
            cursor: pointer;
        }
        #bg{
            background-image: url("img/bg0.5.png");
            width: 100%;
            height: 100%;
            position: fixed;
            z-index: 1000;
            text-align: center;
            overflow: hidden;
            display: none;
        }
        #bg img{
            margin-top: 200px;
        }
    </style>
    <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="js/courseAll.js"></script>
</head>
<body>
<div id="bg">
    <img src="img/loading.gif">
</div>
<div class="container">
    <div class="well well-sm text-center" id="user" data-uid="<?php echo $username; ?>"><?php echo $name; ?> <a class="btn btn-sm btn-primary" href="index.html">退出</a></div>
    <div class="row">
        <div class="well well-sm" style="font-size: 13px;">
            类别：
            <select id="szjylb" name="szjylb">
                <option value="">--所有课程--</option>

                <option value="02">英语课</option>

                <option value="05"> 统计类公选课</option>

                <option value="06">通识课</option>

                <option value="07">计算机类公选课</option>

                <option value="04">数学类公选课</option>

                <option value="01">体育课</option>

                <option value="03">思政类公选课</option>

            </select>
            <select id="szkclb" name="szkclb" style="display: none;">

                <option value="11">人文科学</option>

                <option value="12">社会科学</option>

                <option value="13">自然科学</option>

                <option value="14">文化与文学</option>

                <option value="15">哲学与历史</option>

                <option value="16">艺术与审美</option>

                <option value="17">其他人文社会科学</option>

                <option value="18">其他自然科学</option>

                <option value="19">人文社科类通选课</option>

                <option value="20">艺术类通选课</option>

                <option value="21">中国语言文学类通选课</option>

                <option value="22">自然科学类通选课</option>

                <option value="23">计算机课程</option>

                <option value="24">计算机类通选课</option>

            </select>
            &nbsp;&nbsp;
            课程：<input type="text" id="kcxx" name="kcxx" size="15">
            &nbsp;&nbsp;
            上课老师：<input type="text" id="skls" name="skls" size="15">
            &nbsp;&nbsp;
            星期：
            <select id="skxq" name="skxq">
                <option value="">--请选择--</option>

                <option value="1">星期一</option>

                <option value="2">星期二</option>

                <option value="3">星期三</option>

                <option value="4">星期四</option>

                <option value="5">星期五</option>

                <option value="6">星期六</option>

                <option value="7">星期日</option>

            </select>
            &nbsp;&nbsp;
            节次：
            <select id="skjc" name="skjc">
                <option value="">--请选择--</option>

                <option value="1-2-">1,2节</option>

                <option value="3-4-5">3,4,5节</option>

                <option value="6-7-">6,7节</option>

                <option value="8-9-">8,9节</option>

                <option value="10-11-">10,11节</option>

                <option value="12-13-">12,13节</option>

                <option value="14-15-">14,15节</option>

            </select>
            &nbsp;&nbsp;

            &nbsp;&nbsp;
            <input type="checkbox" id="sfym" name="sfym">&nbsp;过滤已满课程

            &nbsp;&nbsp;
            <input type="checkbox" id="sfct" name="sfct">&nbsp;过滤冲突课程
            &nbsp;&nbsp;
            <input type="button" value="查询" id="query">
        </div>
        <div class="col-sm-9">
            <table class="table table-bordered table-hover table-striped" id="course-list" width="100%">

            </table>
            <br/>
            <table class="tableed table table-bordered table-hover table-striped" id="course-my" width="100%">
                <?php echo $myCourse; ?>
            </table>
        </div>
        <div class="col-sm-3 status-box">
            <div class="status-box panel panel-default">
                <div class="panel-heading text-center">抢课列表</div>
                <div class="panel-body" id="course-list">
                    <ul class="list-group">
                    </ul>
                    <div class="text-center">
                        <a class=" btn btn-sm btn-default" id="btn-grab">抢课</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a class="btn btn-sm btn-default" id="btn-stop">停止</a>
                    </div>
                </div>
                <div class="panel-heading text-center">请求状态</div>
                <div class="panel-body" id="info">

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>