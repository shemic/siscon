<?php
/**
 *@filename oauth_interface.class.php
 *@desc oauth系统 接口类
 *
 *@author leo(suwi.bin)
 *@date 2012-08-27
 */

class Oauth
{
    /**
     * @desc id
     * @var string
     */
    protected $_id = 100;
    
    /**
     * @desc system
     * @var string
     */
    protected $_system = 'fashion';
    
    /**
     * @desc callback
     * @var string
     */
    protected $_callback = false;
    
    /**
     * @desc js
     * @var string
     */
    protected $_js = false;
    
    /**
     * @desc name
     * @var string
     */
    protected $_name = '';
    
    /**
     * @desc config
     * @var array
     */
    protected $_config = array();
    
    /**
     * @desc param
     * @var array
     */
    protected $_param = array();
    
    /**
     * @desc api
     * @var array
     */
    protected $_api = array();
    
    /**
     * @desc get
     * @var array
     */
    protected $_get = array();
    
    /**
     * @desc url
     * @var array
     */
    protected $_url = array();
    
    /**
     * @desc save
     * @var object
     */
    protected $_save = false;
    
    /**
     * @desc db
     * @var DB
     */
    protected $_db = false;
    
    /**
     * @desc user
     * @var state 是否获取用户信息
     */
    protected $_user = true;
    
    /**
     * @desc uid
     * @var int
     */
    protected $_uid = -1;
    
    /**
     * @desc path
     * @var string
     */
    protected $_path = 'module/oauth';
    
    
    /**
     * @desc 构造函数 定义配置
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    public function __construct()
    {
        $this->_save        = siscon::core('save')->init();
        $id                 = $this->_save->get('auth_id');
        $system             = $this->_save->get('auth_system_' . $id);
        $callback           = $this->_save->get('auth_callback_' . $id);
        
        $this->_get 		= $_GET;
        
        $this->_id          = (isset($this->_get['id']) && $this->_get['id']) ? $this->_get['id'] : ($id ? $id : $this->_id);
        $this->_system      = (isset($this->_get['system']) && $this->_get['system']) ? $this->_get['system'] : ($system ? $system : $this->_system);
        $this->_callback    = (isset($this->_get['callback']) && $this->_get['callback']) ? $this->_get['callback'] : ($callback ? $callback : $this->_callback);
        
        $this->_save->add('auth_id', $this->_id);
        $this->_save->add('auth_system_' . $this->_id, $this->_system);
        $this->_save->add('auth_callback_' . $this->_id, $this->_callback);
        
        $this->_config();
    }
    
    /**
     * @desc 构造函数 定义配置
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _config()
    {
        $file = SIS_ROOT . '/' . $this->_path . '/config/' . $this->_id . '.config.php';
        if(file_exists($file))
        {
            include_once($file);
            if(isset($oauth['config'][$this->_system]))
            {
                $this->_config          = $oauth['config'][$this->_system];
                $this->_param           = $oauth['param'];
                $this->_api           	= $oauth['api'];
                $this->_url['auth']     = $oauth['auth'];
                $this->_url['token']    = isset($oauth['token']) ? $oauth['token'] : $oauth['auth'];
                $this->_js              = isset($oauth['js']) ? $oauth['js'] : $this->_js;
                $this->_name            = $oauth['name'];
            }
            else
            {
                siscon::error('错误的oauth配置信息');
            }
        }
        else
        {
            siscon::error('错误的oauth配置信息');
        }
    }
    
    
    /**
     * @desc oauth请求
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    protected function _request($callback)
    {
		$refer = siscon::link(str_replace('.', '/', siscon::input('refer', '')));
        if(!siscon::input('refer'))
        {
            $refer = ($_SERVER['HTTP_REFERER'] && !strstr($_SERVER['HTTP_REFERER'], 'reg')) ? $_SERVER['HTTP_REFERER'] : $refer;
        }
        $this->_save->add('refer', $refer);
        
        $this->_paramCom('request', 'client_id', $this->_config['id']);
        $this->_paramCom('request', 'redirect_uri', $callback);
        
        $url = $this->_url['auth'] . '?' . http_build_query($this->_param['request']);

        siscon::location($url);
    }
    
    /**
     * @desc oauth请求
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    protected function _callback($url)
    {
        if((isset($this->_get['js']) && $this->_get['js']))
        {
            $this->_js = false;
        }
        if($this->_js == true)
        {
            return $this->_output($url);
        }
        else
        {
            if($this->_paramCom('callback', 'error'))
            {
                siscon::error('参数错误');
            }
            
            $data['token_code']     = $this->_paramCom('callback', 'access_token');
            if(isset($this->_param['access']))
            {
                if(!$this->_save->get('token_refresh'))
                {
                    $this->_paramCom('access', 'code',          $data['token_code']);
                    $this->_paramCom('access', 'client_id',     $this->_config['id']);
                    $this->_paramCom('access', 'client_secret', $this->_config['key']);
                    $this->_paramCom('access', 'redirect_uri',  $url);
                    $this->_paramCom('access', 'grant_type',  $this->_param['access']['grant_type']);
                    $return = $this->_curl('post', $this->_param['access']);
                    if(isset($return['error']))
                    {
                        siscon::error('参数错误');
                    }
                    else
                    {
                        $return = http_build_query($return);
                        parse_str($return, $this->_get);
                    }
                }
                else
                {
                    # 由于refresh token是长期有效的，所以这里无需再次获取了。之后通过这个refresh获取access token就行了
                    return;
                }
            }
            
            $data['token_code']     = $this->_paramCom('callback', 'access_token');
            $data['token_refresh']  = $this->_paramCom('callback', 'refresh_token');
            $data['token_type']     = $this->_paramCom('callback', 'token_type');
            $data['token_time']     = $this->_paramCom('callback', 'expires_in');
            $data['token_id']     	= $this->_paramCom('callback', 'token_id');
            //echo '流程审核中';die;
            //print_r($data);die;
            # 进入绑定流程吧
            $this->_bind($data);
        }
    }
    
    /**
     * @desc 检测token信息
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    protected function _checkToken($url)
    {
        //$this->_curl();
    }
    
    /**
     * @desc 重新获取token
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    protected function _refreshToken()
    {
        $data = $this->_get();
        $state = false;
        if(isset($data['token_refresh']) && $data['token_refresh'])
        {
            $this->_paramCom('refresh', 'refresh_token',         $data['token_refresh']);
            $this->_paramCom('refresh', 'client_id',             $this->_config['id']);
            $this->_paramCom('refresh', 'client_secret',         $this->_config['key']);
            $return = $this->_curl('post', $this->_param['refresh']);
            if(isset($return['error']))
            {
                siscon::error('参数错误');
            }
            if(isset($return['access_token']) && $return['access_token'])
            {
                $update['token_code'] = $return['access_token'];
                $update['token_type'] = $return['token_type'];
                $update['token_time'] = $return['expires_in'];
                $state = $this->_update($update, $data['id']);
            }
        }
        return $state;
    }
    
    /**
     * @desc 获取db
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _db()
    {
        if(!$this->_db)
        {
            $this->_db = siscon::model('oauth');
        }
    }
    
    /**
     * @desc 获取已绑定的数据
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _get()
    {
        $this->_db();
        
        return $this->_db->info($this->_where());
    }
    
    private function _where()
    {
        $where = array();
        
        $where['sid']       = $this->_id;
        $where['system']    = $this->_system;
        $where['uid']       = $this->_uid;
        $where['oid']       = 1;
        $where['name']      = $this->_name;
        $where['state']     = 1;
        
        return $where;
    }
    
    /**
     * @desc 更新数据
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _update($data, $id = false)
    {
        $this->_db();
        if($id <= 0)
        {
            $data += $this->_where();
            $data['cdate'] = SIS_TIME;
            $data['mdate'] = SIS_TIME;
            $state = $this->_db->insert($data);
        }
        else
        {
            $where['id'] = $id;
            $data['mdate'] = SIS_TIME;
            $state = $this->_db->update($data, $where);
        }
        
        return $state;
    }
    
    /**
     * @desc 绑定数据
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _bind($data = false)
    {
        if($data)
        {
            $this->_save->add('token_code', $data['token_code']);
            $this->_save->add('token_refresh', $data['token_refresh']);
            $this->_save->add('token_type', $data['token_type']);
            $this->_save->add('token_time', $data['token_time']);
            $this->_save->add('token_id', $data['token_id']);
        }
        
        # 生成用户信息
        $this->_user($data);

        $get = $this->_get();
        
        $id = false;
        
        if(isset($get['id']) && $get['id'] > 0)
        {
            $id = $get['id'];
        }
        
        $return = $this->_update($data, $id);
        
        if($this->_user == true && $this->_api['user']['return'])
        {
			$template['token'] = $token;
			$template['user'] = $this->_api['user']['return'];
			$template['name'] = $this->_name;
			//print_r($template);die;
			$template['refer'] = $this->_save->get('refer');
			siscon::template('bind', 'oauth', $template);
			die;
		}
        
        # 跳转吧，从哪来去哪吧
        if($this->_callback)
        {
            $callback = base64_decode($this->_callback);
            siscon::location($callback);
        }
        else
        {
			siscon::location($this->_save->get('refer'));
		}
    }
    
    private function _user($data)
    {
		# 获取用户信息
		if($this->_user == true)
		{
			$this->_apiCom('user', 'request', 'access_token',	$data['token_code']);
			$this->_apiCom('user', 'request', 'key',     $this->_config['id']);
			if($this->_api['open']['url'])
			{
				$this->_apiCom('open', 'request', 'access_token',	$data['token_code']);
				$this->_get = $this->_curl('get', $this->_api['open']['request'], $this->_api['open']['url']);
				//print_r($this->_get);
				$this->_apiCom('open', 'return', 'uid', false, true);
				$this->_api['user']['return']['uid'] = $this->_api['open']['return']['uid'];
				$this->_apiCom('user', 'request', 'uid',     		$this->_api['user']['return']['uid']);
			}
			else
			{
				$this->_apiCom('user', 'request', 'uid',     		$data['token_id']);
			}
			//$this->_apiCom('user', 'request', 'screen_name',     '');
			$this->_get = $this->_curl('get', $this->_api['user']['request'], $this->_api['user']['url']);
			$this->_apiCom('user', 'return', 'username', false, true);
			$this->_apiCom('user', 'return', 'uid', $this->_api['user']['return']['uid'], true);
			$this->_apiCom('user', 'return', 'gender', false, true);
			$this->_apiCom('user', 'return', 'avatar', false, true);
			
			if($this->_api['user']['return']['gender'] == '男')
			{
				$this->_api['user']['return']['gender'] = 'm';
			}
			elseif($this->_api['user']['return']['gender'] == '女')
			{
				$this->_api['user']['return']['gender'] = 'f';
			}
			elseif($this->_api['open']['url'])
			{
				$this->_api['user']['return']['gender'] = 'n';
			}
			
			
			/*
			$this->_api['user']['return']['uid'] = '1404376560';
			$this->_api['user']['return']['username'] = 'zaku';
			$this->_api['user']['return']['gender'] = 'm';
			$this->_api['user']['return']['avatar'] = 'http://tp1.sinaimg.cn/1404376560/180/0/1';
			*/

		}
		
		$user['token_type'] = $this->_id;
		$user['token_system'] = $this->_system;
		$user['token_id'] = $data['token_id'];
		$user['token_uid'] = $this->_api['user']['return']['uid'] ? $this->_api['user']['return']['uid'] : -1;
		$user['status'] = 1;
		//print_r($user);die;
		$return = siscon::model('user', 'user')->exists($user);
		if($return)
		{
			if(!$return['username'])
			{
				$this->_user = true;
			}
			else
			{
				$this->_user = false;
			}
			$this->_uid = $return['id'];
			siscon::model('user', 'user')->update($user, array('id' => $return['id']));
		}
		else
		{
			$this->_uid = siscon::model('user', 'user')->insert($user);
		}
		
		$info = siscon::model('user', 'user')->one($this->_uid);
		$info['username'] = $this->_api['user']['return']['username'] ? $this->_api['user']['return']['username'] : md5(SIS_TIME);
        siscon::core('save')->init()->add('user', $info);
	}
    
    /**
     * @desc 输出内容
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _output($url)
    {
        $html = 
        '<script>   
            var params = {}, queryString = location.hash.substring(1),
            regex = /([^&=]+)=([^&]*)/g, m;
            while (m = regex.exec(queryString))
            {
                params[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
            }

            location.href="'.$url.'?js=false&" + queryString;
        </script>';
        echo $html;
    }
    
    /**
     * @desc 对数据的兼容
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _paramCom($type, $key, $value = false)
    {
        if(isset($this->_param[$type][$key . '_com']))
        {
            $com = $this->_param[$type][$key . '_com'];
            
            unset($this->_param[$type][$key . '_com']);
            
            return $this->_param[$type][$com] = ($value ? $value : (isset($this->_get[$com]) ? $this->_get[$com] : false));
        }
        else
        {
            unset($this->_param[$type][$key]);
            return $this->_param[$type][$key] = ($value ? $value : (isset($this->_get[$key]) ? $this->_get[$key] : false));
        }
    }
    
    /**
     * @desc 对数据的兼容
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _apiCom($method, $type, $key, $value = false, $state = false)
    {
        if(isset($this->_api[$method][$type][$key . '_com']))
        {
            $com = $this->_api[$method][$type][$key . '_com'];
            
            unset($this->_api[$method][$type][$key . '_com']);
            
            if($state == false)
            {
				$key = $com;
			}
            
            return $this->_api[$method][$type][$key] = ($value ? $value : (isset($this->_get[$com]) ? $this->_get[$com] : false));
        }
        else
        {
            unset($this->_api[$method][$type][$key]);
            return $this->_api[$method][$type][$key] = ($value ? $value : (isset($this->_get[$key]) ? $this->_get[$key] : false));
        }
    }
    
    /**
     * @desc http
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _curl($method, $param, $url = false)
    {
		$http = siscon::core('http');
		$url = $url ? $url : $this->_url['token'];
		$return = $http->$method($url, $param);
		if(strstr($return, 'callback('))
		{
			$return = str_replace(array('callback(',' );'), '', $return);
		}
        return json_decode($return,true);
    }
}
