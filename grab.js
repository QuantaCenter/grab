/**
 * Created by zjy on 2016/1/16.
 */
var Gwxk=require("./gwxk.lib.js");
var grab=new Gwxk();
var fs = require('fs');
if (process.argv.length<4){
    return console.log('use this file as "node file username password"');
}
var username = process.argv[2];
var password = process.argv[3];


grab.login({'username':username,'password':password}, function (data) {
    process.stdin.pause();
    console.log(data.info);
    console.log("***************************");
    console.log(data.data.name);
    console.log("***************************");
    if(data.status){
        //var field={
        //    "kksj":'2015-2016-1'
        //};
        //grab.getScore(field,function(data){
        //    console.log(data);
        //})
        var field = {
            sEcho:1,
            iColumns:8,
            'sColumns':'',
            'iDisplayStart':0,
            'iDisplayLength':0,
            'mDataProp_0':'kch',//课程号
            'mDataProp_1':'kcmc',//课程名
            'mDataProp_2':'xf',//学分
            'mDataProp_3':'skls',//上课老师
            'mDataProp_4':'sksj',//上课时间
            'mDataProp_5':'skdd',//上课地点
            'mDataProp_6':'ctsm',//时间冲突
            'mDataProp_7':'czOper'//操作
        };
        grab.showCourse('xxk', field, function (req, data) {
            var cid=[];
            JSON.parse(data).aaData.forEach(function (value, index) {
                cid[index]=value['jx0404id'];
                if(value['ctsm']==''){
                    console.log(`${index}\t${value['kcmc']}\t${value['skls']}\t${value['sksj']}`);
                }
                else{
                    console.log(`冲突\t${value['kcmc']}\t${value['skls']}\t${value['sksj']}`);
                }
            });

            console.log("input your choose as 1,2,3,4:");

            process.stdin.resume();
            process.stdin.setEncoding('utf-8');
            process.stdin.on('data', function(chunk) {
                process.stdin.pause();
                chunk = chunk.split(',');
                chunk[chunk.length-1]=Number(chunk[chunk.length-1]);
                chunk.map(function (value, index, arr) {
                    value = Number(value);
                    return value;
                });
                console.log('start:');
                var sdate = Date.parse(new Date());
                var i=0;
                setInterval(function () {
                    chunk.forEach(function (value, index) {
                        grab.sendCourse(cid[value], function (req, data) {
                            var edate = Date.parse(new Date());
                            i++;
                            var msg ;
                            if(edate - sdate >1000){
                                sdate = Date.parse(new Date());
                                msg = "请求速度 "+ i + '/s' + "\n";
                                fs.writeFile(__dirname + '/log/' + username+'.txt',msg,{flag:"a+"}, function () {

                                });
                                i=0;
                            }

                            if(JSON.parse(data).success){
                                chunk.splice(index,1);
                                msg = "成功" + value +' '+ data+"\n";
                                fs.writeFile(__dirname + '/log/' + username+'success.txt',msg,{flag:"a+"}, function () {

                                });
                            }
                        })
                    })
                },300);
            });
        })
    }
});