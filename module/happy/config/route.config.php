<?php
# 设置前台路由，支持正则
$route = array
(
	'home' =>  "happy/front/all",
	'home/p-([0-9]+).html' =>  "happy/front/all/pageturn=$1",
	'interface/list' =>  "happy/front/home",
	'interface/list/([0-9]+)' =>  "happy/front/home/cate=$1",
	'interface/list/([0-9]+)/([0-9]+)' =>  "happy/front/home/cate=$1&pageturn=$2",
	'interface/info/([0-9]+)/([0-9]+)' =>  "happy/front/info/id=$1&type=$2",
);
