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
        'id' => '101045021',
        'key' => '43ae93771f553c147a90887670256ce2',
    )
);

# 名称
$oauth['name']  = '腾讯';
# 通过js中转一下。。php获取不到#号后的参数，而google返回的数据都是在#中的，这个浏览器就是为js服务的
$oauth['js']    = true;
# 授权地址
$oauth['auth']   = 'https://graph.qq.com/oauth2.0/authorize';
# 获取token信息
$oauth['token']   = 'https://api.weibo.com/oauth2/access_token';


# 以下兼容数据均以_com结尾
$oauth['param'] = array
(
    'request' => array
    (
		# 授权地址
        'scope'             => 'https://graph.qq.com/oauth2.0/authorize',
        'response_type'     => 'token',
        'state'             => 'yes',
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
    'open' => array
    (
		'url'  => 'https://graph.qq.com/oauth2.0/me',
		# 请求的字段
		'request' => array
		(	
			'access_token_com' => 'access_token',
		),
		# 返回的字段
		'return' => array
		(	
			'uid_com' => 'openid',
		),
    ),
	# 用户信息
    'user' => array
    (
		'url'  => 'https://graph.qq.com/user/get_user_info',
		# 请求的字段
		'request' => array
		(	
			'access_token_com' => 'access_token',
			'uid_com' => 'openid',
			//'screen_name_com' => 'oauth_consumer_key',
			'key_com' => 'oauth_consumer_key',
		),
		# 返回的字段
		'return' => array
		(	
			'username_com' => 'nickname',
			'gender_com' => 'gender',
			'avatar_com' => 'figureurl_2',
		),
    ),
);

