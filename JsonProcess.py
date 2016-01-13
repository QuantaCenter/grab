#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys , getopt     #用于接收命令行参数和选项
import json

reload(sys)
sys.setdefaultencoding('utf8')


def usage_this():
    print "eg: python JsonProcess.py -i inputfile -o outputfile\ndefault directory is ./course_json/"

# sys.argv[0] 表示脚本名，1之后表示多个参数
# 当一个选项不带参数，例如python a.py -h ，则这是一个开关选项，后面的字符只用字母；如果带参数则需要带一个:
opts,args = getopt.getopt(sys.argv[1:],"hi:o:")

input_file = ""
output_file = ""
# default_dir = "./course_json/"
for opt,value in opts:
    if opt == "-h":
        usage_this()
        sys.exit()
    elif opt == "-i":
        input_file = value
    elif opt == "-o":
        output_file = value

try:
    f = open(input_file,"r")
    df = open(output_file,'a+')
    df.truncate()   # make file empty
    json_str = f.read()
    # print json_str
    dict1 = json.loads(json_str)
    i = 1
    for row in dict1['aaData']:
        if row['skls'] is None:
            row['skls'] = "未知"
        content = '%s %s %s %s %s %s' % (str(i)+"、",str(row['jx0404id']),row['kcmc'],row['skls'],row['sksj'],row['ctsm'])
        df.write("%s\n" % content)
        i = i + 1
except:
    print "file not found or other unknown error"
finally:
    if f:
        f.close()
    if df:
        df.close()


