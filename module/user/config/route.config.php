<?php
# 设置前台路由，支持正则
$route = array
(
	'reg' =>  "user/front/reg",
	'reg-([a-zA-Z]+)' =>  "user/front/reg/refer=$1",
	'password-(.*?)' =>  "user/front/find_password_set/code=$1",
	'password' =>  "user/front/find_password",
    'login' => 'user/front/login',
    'out' => 'user/front/out',
    'check' => 'user/front/check',
    'user/front/ding' => 'user/front/ding',
    'code' => 'user/front/code',
    'code/(.*?).html' => 'user/front/code/time=$1',
    'status' => 'user/front/status',
    'profile' => 'user/front/profile',
    'user-photo' => 'user/front/photo',
    'user-password' => 'user/front/password',
    'reg-success' => 'user/front/reg_suc',
    'reg-activate-(.*?)' => 'user/front/reg_activate/code=$1',
    
);
