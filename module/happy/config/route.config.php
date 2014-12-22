<?php
# 设置前台路由，支持正则
$route = array
(
	'home' =>  "happy/front/all",
	'home/p-([0-9]+).html' =>  "happy/front/all/pageturn=$1",
);
