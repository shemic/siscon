<?php
# 设置前台路由，支持正则
$route = array
(
	'comment-([0-9]+)-([0-9]+)-([0-9]+)-([0-9]+)' =>  "comment/front/list/type=$1&did=$2&pageturn=$3&state=$4",
	'comment-([0-9]+)-([0-9]+)-([0-9]+)' =>  "comment/front/list/type=$1&did=$2&pageturn=$3",
	'comment-([0-9]+)-([0-9]+)' =>  "comment/front/update/type=$1&did=$2",
);
