<?php
/**
 * @filename 100.cfg.php
 * @desc 新浪微博的配置
 * @author leo(suwi.bin)
 * @date 2012-06-27
 */

$oauth = array();

# 基本配置
$oauth['config'] = array
(
    'fashion' => array
    (
        'id' => '1282313098',
        'key' => '7384bc63926fc4fc95e5f6f19d81e0c2',
    )
);

# 名称
$oauth['name']  = '新浪微博';
# 通过js中转一下。。php获取不到#号后的参数，而google返回的数据都是在#中的，这个浏览器就是为js服务的
$oauth['js']    = true;
# 授权地址
$oauth['auth']   = 'https://api.weibo.com/oauth2/authorize';
# 获取token信息
$oauth['token']   = 'https://api.weibo.com/oauth2/access_token';


# 以下兼容数据均以_com结尾
$oauth['param'] = array
(
    'request' => array
    (
		# 授权地址
        'scope'             => 'https://api.weibo.com/oauth2/authorize',
        'response_type'     => 'token',
        'state'             => '',
        'redirect_uri_com'  => 'redirect_uri',
        'client_id_com'     => 'client_id',
    ),
    #返回的数据，标准值
    'callback' => array
    (
        'access_token_com'  => 'access_token',
        'token_type_com'    => 'token_type',
        'expires_in_com'    => 'expires_in',
        'token_id_com'    	=> 'uid',
        
        # 下边是返回错误的处理，error是错误的参数名
        'error_com'         => 'error',
    ),
    /*
    # 获取access token
    'access' => array
    (
        'grant_type'  		=> 'authorization_code',
        'code'    			=> 'code',
        'client_id'    		=> 'client_id',
        'client_secret'    	=> 'redirect_uri',
        'client_id'    		=> 'client_id',
    ),
    */
);

# api接口 以下兼容数据均以_com结尾
$oauth['api'] = array
(
	# 用户信息
    'user' => array
    (
		'url'  => 'https://api.weibo.com/2/users/show.json',
		# 请求的字段
		'request' => array
		(	
			'access_token_com' => 'access_token',
			'uid_com' => 'uid',
			//'screen_name_com' => 'screen_name',
			'key_com' => 'oauth_consumer_key',
		),
		# 返回的字段
		'return' => array
		(	
			'username_com' => 'name',
			'uid_com' => 'id',
			'gender_com' => 'gender',
			'avatar_com' => 'avatar_large',
		),
    ),
);

