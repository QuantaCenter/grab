#!/bin/bash
read -p "student number:" username
stty -echo
read -p "password:" password
stty echo

file_name=$username"_"`date +%s`

# mkdir if not found
if [ ! -d "./cookie_file" ];then
    mkdir cookie_file
    chmod 777 cookie_file
fi
if [ ! -d "./course_json" ];then
    mkdir course_json
    chmod 777 course_json
fi

curl -c "./cookie_file/"$file_name".txt" -d "USERNAME=$username&PASSWORD=$password" http://jxgl.gdufs.edu.cn/jsxsd/xk/LoginToXkLdap
curl -b "./cookie_file/"$file_name".txt" "http://jxgl.gdufs.edu.cn/jsxsd/xsxk/xsxk_index?jx0502zbid=425DF1EBE9644E6297C4D54B3EAD7A93"
curl -b "./cookie_file/"$file_name".txt" http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xsxkXxxk > "./course_json/"$file_name".txt"

sed -i 's/\r//g' "./course_json/"$file_name".txt" # 过滤^M

python JsonProcess.py -i "./course_json/"$file_name".txt" -o "./course_json/"$file_name"_dump.txt"

echo "请选择需要选的课程编号："

sed 's/20152016[0-9]*//g' "./course_json/"$file_name"_dump.txt"

read line
declare -a cn

t=0
for i in $line
do
temp=`awk '$1 ~ "^'$i'、" {print $2}' "./course_json/"$file_name"_dump.txt"`
cn[$t]=$temp
t=$(($t+1))
done

while true ; do
for item in ${cn[@]};do
    echo $item
    curl -b "./cookie_file/"$file_name".txt" "http://jxgl.gdufs.edu.cn/jsxsd/xsxkkc/xxxkOper?jx0404id="$item
done
python -c "import time;time.sleep(0.05)"  # 限速
done
