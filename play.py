# -*- coding: utf-8 -*-

__author__ = 'tyruschin'

# Thanks for http://cuiqingcai.com/968.html

import os
import urllib
import urllib2
import cookielib
import config
import json
import re
import time

if not os.path.exists('./cookie_log'):
    os.mkdir('cookie_log')
if not os.path.exists('./course_log'):
    os.mkdir('course_log')
if not os.path.exists('./log'):
    os.mkdir('log')

log_file = 'log/'+ config.studentInfo['USERNAME'] + str(int(time.time())) +'.txt'
cookie_file = 'cookie_log/'+ config.studentInfo['USERNAME'] +'.txt'
output_file = 'course_log/'+ config.studentInfo['USERNAME'] +'_dump.txt'

cookie = cookielib.MozillaCookieJar(cookie_file)
handler = urllib2.HTTPCookieProcessor(cookie)
opener = urllib2.build_opener(handler)
postData = urllib.urlencode(config.studentInfo)
loginUrl = 'http://jxgl.gdufs.edu.cn/jsxsd/xk/LoginToXkLdap'
result = opener.open(loginUrl,postData)
cookie.save(ignore_discard=True, ignore_expires=True)
grabUrl = 'http://jxgl.gdufs.edu.cn/jsxsd/xsxk/xsxk_index?'+config.courseCode
opener.open(grabUrl)
grabUrl = 'http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xsxkXxxk'  # 通选课好像不是这个
courseList = opener.open(grabUrl)
courseList =  str(courseList.read())
courseList.replace('\r','')
courseContent = ""
json_str = courseList
# print json_str
dict1 = json.loads(json_str)
i = 1
for row in dict1['aaData']:
    if row['skls'] is None:
        row['skls'] = "未知"
    content = '%s %s %s %s %s %s' % (str(i)+u"、",str(row['jx0404id']),row['kcmc'],row['skls'],row['sksj'],row['ctsm'])
    courseContent = courseContent + content + "\n"
    i = i + 1

courseContentWithoutCode = re.sub("201[0-9]*","",courseContent)
print courseContentWithoutCode
courseCode = re.findall("201[0-9]*",courseContent)  # 这里返回的是从0开始的数组list
courseSelect = raw_input("请选择需要选的课程编号，以空格隔开(eg:1 2 3)：")
courseSelect = courseSelect.split()
f = open(log_file,'a+')
while True:
    for num in courseSelect:
        cc_temp = courseCode[(int(num)-1)]
        grabUrl = 'http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xxxkOper?jx0404id='+cc_temp
        res = opener.open(grabUrl)
        res_json = str(res.read())
        # f.write(res_json)    # log
        print cc_temp,res_json

