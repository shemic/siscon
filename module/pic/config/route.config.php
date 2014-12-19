<?php
# 设置前台路由，支持正则
$route = array
(
	'upload' =>  "pic/interface/upload",
	'ueditor-([a-zA-Z_]+)' =>  "pic/interface/ueditor/key=$1",
);
