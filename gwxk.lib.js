/**
 * Created by zjy on 2016/1/16.
 */
var http=require('http');
//var request=require('request');
var fs=require('fs');
var querystring=require('querystring');
var urls=require('url');
var iconvLite = require('iconv-lite');

var cheerio = require("cheerio");
function Gwxk(){
    _cookie=null;
    _username=null;
    _name=null;
}

Gwxk.prototype={
    constructor:Gwxk,
    login: function ($data,$callback) {
        var self = this;

        //先尝试用旧cookie看是否有效
        fs.readFile("./cookie/" + $data.username + ".txt", 'utf8', function (err, data) {
            if (err)
                self.reLogin($data,$callback);
            else {
                self._cookie = data;
                var $url = self.getUrl('index');
                var result={};
                self.sendGet($url, function (res, data) {
                    var $pattern = /<div id="Top1_divLoginName" class="Nsb_top_menu_nc" style=".+?">(.+?)\([0-9]+?\)<\/div>/;
                    var match = $pattern.exec(data);
                    if (match) {
                        self._name = match[1];
                        result.status = 1;
                        result.info = 'use last login';
                        result.data = {
                            username: $data.username,
                            name: self._name
                        };
                        $callback(result);
                    }
                    else {
                        self.reLogin($data,$callback);
                    }
                })
            }
        });
    },
    reLogin: function ($data,$callback) {
        var self = this;
        var $files={
            "USERNAME":$data.username,
            "PASSWORD":$data.password
        };
        var result={
            status:null,
            data:null,
            info:null
        };
        var $url=this.getUrl('login');
        this.sendPost($url,$files, function (res,data) {
            if(res.statusCode==302){
                self._cookie=res.headers['set-cookie'].join("; ");
                fs.writeFile("./cookie/"+$data.username+".txt",self._cookie, function (err) {
                    if(err)
                    throw  err;
                });
                self._username=$data.username;
                self.sendGet(res.headers['location'], function (res, data) {
                    //console.log(data);
                    var $pattern=/<div id="Top1_divLoginName" class="Nsb_top_menu_nc" style=".+?">(.+?)\([0-9]+?\)<\/div>/;
                    var match=$pattern.exec(data);
                    if(match){
                        self._name=match[1];
                        result.status=1;
                        result.info='login success';
                        result.data={
                            username:self._username,
                            name:self._name
                        }
                    }
                    else{
                        result.status=0;
                        result.info='login error';
                    }
                    $callback(result);
                });
            }
            else{
                result.status=0;
                result.info='login error';
                $callback(result);
            }
        });
    },
    getScore: function ($filed,$callback) {
        var self=this;
        var $url=self.getUrl('score');
        var field={
            "kksj":$filed.kksj,
            "kcxz":$filed.kcxz,
            "kcmc":$filed.kcmc,
            "xsfs":$filed.xsfs || 'all'
        };
        self.sendPost($url,field, function (res, data) {
            data=data.replace(/[\r\n\t]/g,'');
            var $pattern=/<table id="dataList" width="100%" border="0" cellspacing="0" cellpadding="0" class="Nsb_r_list Nsb_table">(.+?)<\/table>/g;
            var matches=$pattern.exec(data);
            if(matches){
                var $pattern1=/<tr>(.+?)<\/tr>/g;
                var title=$pattern1.exec(matches[1]);
                var $pattern2=/<th.*?>(.*?)<\/th>/g;
                var $pattern3=/<td.*?>(.*?)<\/td>/g;
                var $pattern4=/<a[^>]+>(.*?)<\/a>/g;
                var key;
                var key2;
                var echo='';
                var i=0;
                while(key=$pattern2.exec(title[1])){
                    if(i==0 || i==3 || i==4 || i==5)
                        echo+=key[1]+"\t";
                    i++;
                }
                echo+="\n";
                var value;
                while(value=$pattern1.exec(matches[1])){
                    i=0;
                    while(key=$pattern3.exec(value[1])){
                        if(i==0 || i==3 || i==4 || i==5){
                            if(key2=$pattern4.exec(key[1]))
                                echo+=key2[1]+"\t";
                            else
                                echo+=key[1].substring(0,7)+"\t";
                        }
                        i++;
                    }
                    echo+="\n";
                }
                $callback(echo);
            }
            else{
                $callback('match fail');
            }
        })
    },
    sendPost: function ($url, $filed , $callback) {
        var self=this;
        var $param=querystring.stringify($filed),
            url=urls.parse($url),
            $origin='http://'+url.host,
            $header={
                'Proxy-Connection': 'keep-alive',
                'Content-Length':$param.length,
                'Cache-Control': 'max-age=0',
                'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Origin': $origin,
                'User-Agent': 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.12 Safari/537.31',
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept-Encoding': 'gzip,deflate,sdch',
                'Accept-Language': 'zh-CN,zh;q=0.8',
                'Accept-Charset': 'GBK,utf-8;q=0.7,*;q=0.3'
            };
        if(self._cookie){
            $header.Cookie=self._cookie;
        }
        var $options={
            host:url.host,
            path:url.path,
            method:"POST",
            headers:$header
        };

        var req=http.request($options, function (res) {
            var buffers = [];
            res.on('data', function (chunk) {
                buffers.push(chunk);
            });
            res.on("end",function(){
                var data = iconvLite.decode(Buffer.concat(buffers), 'utf-8');
                $callback(res,data);
            });
        });
        req.end($param);
    },
    sendGet: function ($url, $filed , $callback) {
        var self=this;
        if($filed){
            if(typeof $filed == 'function'){
                $callback=$filed;
            }
            else
                $url+="?"+querystring.stringify($filed);
        }
        var url=urls.parse($url);
        var $header = {
            'Cookie':self._cookie
        };
        var options={
            host:url.host,
            path:url.path,
            method:'GET',
            headers:$header
        };
        var req=http.request(options,function(res){
            var buffers = [];
            res.on('data', function (chunk) {
                buffers.push(chunk);
            });
            res.on("end",function(){
                var data = iconvLite.decode(Buffer.concat(buffers), 'utf-8');
                $callback(res,data);
            });
        });
        req.end();
    },
    getUrl:function(type){
        var url=null;
        switch (type){
            case 'login':
                url='http://jxgl.gdufs.edu.cn/jsxsd/xk/LoginToXkLdap';
                break;
            case 'score':
                url='http://jxgl.gdufs.edu.cn/jsxsd/kscj/cjcx_list';
                break;
            case 'index':
                url='http://jxgl.gdufs.edu.cn/jsxsd/framework/main.jsp';
                break;
            case 'xklist':
                url='http://jxgl.gdufs.edu.cn/jsxsd/xsxk/xklc_list';
                break;
            case 'xxk':
                url='http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xsxkXxxk';
                break;
            case 'sc':
                url='http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xxxkOper';
                break;
        }
        return url;
    },
    intoCourse: function (cb) {
        var self = this;
        this.sendGet(this.getUrl('xklist'), function (res, data) {
            var $ = cheerio.load(data);
            var href = $("#tbKxkc td a[target='blank']").attr('href');
            self.sendGet('http://jxgl.gdufs.edu.cn' + href, cb);
        });
    },
    showCourse: function (type, field, cb) {
        var self=this;
        this.intoCourse(function () {
            self.sendGet(self.getUrl(type),field, cb);
        });
    },
    sendCourse: function(id, cb){
        var self = this;
        var field = {
            jx0404id: id
        };
        self.sendGet(self.getUrl('sc'), field, cb);
    }
};
module.exports=Gwxk;