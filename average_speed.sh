#!/bin/bash
if [ ! "$1" ]||[ ! -f "$1" ]
then
	echo "param need or param is not a logfile"
	exit 
fi

timestamp=`date +%s`
start_time=`head -n1 $1`
duration=$(($timestamp - $start_time))
line_count=$((`cat $1 | wc -l` - 1))
echo "当前时间："$timestamp
echo "开始时间："$start_time
echo "持续秒数："$duration
echo "当前总请求数："$line_count
echo -n "平均每秒请求数："
echo "scale=3;$line_count/$duration" | bc
