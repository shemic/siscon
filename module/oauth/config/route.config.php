<?php
# 设置前台路由，支持正则
$route = array
(
	'at/get' =>  "oauth/front/get",
    'at/callback' => 'oauth/front/callback',
    'at/refresh' => 'oauth/front/refresh',
);
