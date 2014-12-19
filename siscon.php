<?php
/*~:SISCON:~
.---------------------------------------------------------------------------.
|  Software: SISCON - 一款用于php敏捷开发的架构程序                         |
|   Version: 1.0.0                                                          |
|   Contact: 暂无                                                           |
|      Info: 暂无                                                           |
|   Support: 暂无                                                           |
| ------------------------------------------------------------------------- |
|    Author: Leo (suwibin.yu)                                               |
| Copyright (c) 2013-2018, Leo. All Rights Reserved.                        |
'---------------------------------------------------------------------------'

/**
 * @TYPE class
 * @NAME siscon 1核心类，所有功能都在这里载入，由于特殊并且经常用到，没有遵循命名规范，类名小写。
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */
error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('PRC'); 
# 定义项目路径
define('SIS_ROOT'           ,   dirname(__FILE__) . '/');
# 定义核心类库路径
define('SIS_CORE_ROOT'      ,   SIS_ROOT . 'core/');
# 定义模块类库路径
define('SIS_MODULE_ROOT'    ,   SIS_ROOT . 'module/');
# 定义模块可写路径
define('SIS_WRITE_ROOT'    ,   SIS_ROOT . 'write/');
# 定义域名
define('SIS_DOMAIN'               	,   (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
# 定义系统时间
define('SIS_TIME'                 	,   $_SERVER['REQUEST_TIME']);
!defined('SIS_ENTRY') && define('SIS_ENTRY', 'entry.php');
# 定义当前入口文件
define('SIS_SELF'                 	,   substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], SIS_ENTRY)));
# 获取域名
define('SIS_HOST'              ,   'http://' . SIS_DOMAIN . (SIS_SELF ? SIS_SELF : '/'));
# 定义全局静态域名
define('SIS_PUBLIC_HOST'              ,   SIS_HOST . 'public/');
# 定义前台静态域名
define('SIS_IMG_HOST'              ,    'http://img.fashionweekly.com.cn/');
//define('SIS_IMG_HOST'              ,    SIS_HOST);
# 定义前台静态域名
define('SIS_FRONT_HOST'              ,    SIS_IMG_HOST . 'public/');
# 定义后台路径
define('SIS_MANAGE'    ,   '/manage/');
$lifeTime = 24 * 3600; 
ini_set('session.gc_maxlifetime', $lifeTime); //设置时间
session_start(); 
setcookie(session_name(), session_id(), SIS_TIME + $lifeTime, "/");
include(SIS_ROOT . 'config.php');
siscon::$global['define'] = $config;
class siscon
{
    /**
     * @TYPE var
     * @NAME global 核心全局变量
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public $global;

    /**
     * @TYPE function
     * @NAME core 载入核心类
     * @PARAM name(string) 核心类名
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function core($name, $new = true, $send = array())
    {
		$class = ucwords($name);
        if(isset(self::$global[$class]) && self::$global[$class])
        {
            return self::$global[$class];
        }
        include_once(SIS_CORE_ROOT . $name . '.core.php');
        if($new == true)
        {
			if($send)
			{
				self::$global[$class] = new $class($send);
			}
			else
			{
				self::$global[$class] = new $class;
			}
			return self::$global[$class];
		}
    }

    /**
     * @TYPE function
     * @NAME service 载入模块类下的方法
     * @PARAM param(string|array) 模块类名 'article/page/index'|array('article','page','index')
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function service($param, $send = false)
    {
		$temp = is_array($param) ? $param : self::resolve($param);
        $key = ucwords($temp[0]) . '_' . ucwords($temp[1]);
        $file = SIS_MODULE_ROOT . $temp[0] . '/' . $temp[1] . '.module.php';
        if(empty(self::$global['module'][$key]) && file_exists($file))
        {
			self::core('module', false);
			$config = self::config('config');
			if($config) siscon::$global['define'] += $config;
            include_once($file);
            self::$global['module'][$key] = new $key;
        }
        
        $temp_1 = $temp[2].'_ajax';
        if(method_exists(self::$global['module'][$key], $temp[2]))
        {
			$method = $temp[2];
            if($send)
            {
                return self::$global['module'][$key]->$method($send);
            }
            else
            {
                return self::$global['module'][$key]->$method();
            }
            
        }
        elseif(method_exists(self::$global['module'][$key], $temp_1))
        {
			$method = $temp_1;
            if($send)
            {
                return self::$global['module'][$key]->$method($send);
            }
            else
            {
                return self::$global['module'][$key]->$method();
            }
            
        }
        return isset(self::$global['module'][$key]) ? self::$global['module'][$key] : false;
    }

    /**
     * @TYPE function
     * @NAME service 载入模块类下的方法
     * @PARAM param(string|array) 模块类名 'article/page/index'|array('article','page','index')
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function inf($project, $name, $send = false, $file = 'interface')
    {
        $param = array($project, $file, $name);
        return self::service($param, $send);
    }

    /**
     * @TYPE function
     * @NAME model 载入数据模型
     * @PARAM name(string) 配置名
     * @PARAM project(string) 项目名
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function model($name, $project = false)
    {
		$project 	= $project ? $project : self::$global['project'];
		$key 		= ucwords($project) . '_' . ucwords($name);
        $file 		= SIS_MODULE_ROOT . $project . '/model/' . $name . '.model.php';
		if(empty(self::$global['model'][$key]) && file_exists($file))
		{
			self::core('model', false, $project);
			include_once($file);
			self::$global['model'][$key] = new $key;
		}
        else
        {
            $model = self::core('model', true, $project);
            $model->setName($name, $project);
            return $model;
        }
		return isset(self::$global['model'][$key]) ? self::$global['model'][$key] : false;
    }

    /**
     * @TYPE function
     * @NAME config 获取一个项目下的配置信息
     * @PARAM name(string) 配置名
     * @PARAM project(string) 项目名
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function config($name, $project = false)
    {
		$project = $project ? $project : self::$global['project'];
        $key = $project . '_' . $name;
        $file = SIS_MODULE_ROOT . $project . '/config/' . $name . '.config.php';
		if(empty(self::$global['config'][$key]) && file_exists($file))
		{
			include_once($file);
			self::$global['config'][$key] = $$name;
			self::$global['config'][$key]['filetime'] = filemtime($file);
		}
		return isset(self::$global['config'][$key]) ? self::$global['config'][$key] : false;
    }

    /**
     * @TYPE function
     * @NAME resolve 对形如“article/page/index”字符串进行解析
     * @PARAM string(string) 字符串
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function resolve($string)
    {
        self::$global['param'] = $_REQUEST;
		/*
		if(strpos($string, SIS_MANAGE) === true)
		{
			$string = str_replace(SIS_MANAGE, '/', $string);
		}
		*/
        $param = self::explode(array('/', '.'), $string);
        if(empty($param[0]))
        {
            self::error('项目不存在', 'siscon');
        }
        elseif(empty($param[1]))
        {
            self::error('项目文件不存在', 'siscon');
        }
        elseif(empty($param[2]))
        {
            $param[2] = 'default';
        }
        self::$global['project'] 	= $param[0];
		self::$global['file'] 	= $param[1];
		self::$global['method'] 	= $param[2];
        $param[2] .= '_' . self::$global['request'];
        if(siscon::$global['type'])
        {
			$param[2] .= '_' . siscon::$global['type'];
		}
        
        if(isset($param[3]) && $param[3])
		{
			parse_str($param[3],$temp_param);
            self::$global['param'] += $temp_param;
            $_GET += $temp_param;
		}

		return $param;
    }

    /**
     * @TYPE function
     * @NAME explode 分割字符串变为数组
     * @PARAM separator(string) 分隔符
     * @PARAM string(string) 字符串
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function explode($separator, $string)
    {
        $array = array();
        if(is_array($separator))
        {
            foreach($separator as $k => $v)
            {
                if($array = self::explode($v, $string))
                {
                    break;
                }
            }
        }
        else
        {
            if(strpos($string, $separator))
            {
                $array = explode($separator, $string);
            }
        }
        return $array;
    }

    /**
     * @TYPE function
     * @NAME error 错误信息提醒
     * @PARAM msg(string) 提醒信息
     * @PARAM project(string) 项目名
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function error($msg, $project = 'system')
    {
        self::out($msg, $project);
        die;
    }

    /**
     * @TYPE function
     * @NAME out 信息提醒
     * @PARAM msg(string) 提醒信息
     * @PARAM project(string) 项目名
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function out($msg, $project = 'system')
    {
        $json = siscon::input('json');
        $callback = siscon::input('callback');
        $function = siscon::input('function');

        if($json == 1 || is_array($msg))
        {
            $msg = json_encode($msg);
        }
        elseif(is_numeric($msg))
        {
			
		}
        elseif(siscon::$global['template'] == 'manage' || siscon::$global['template'] == 'front')
        {
			$msg = '"'.$msg.'"';
		}

        if($callback)
        {
            $msg = $callback . '('.$msg.')';
        }

        if($function)
        {
            $msg = '<script>parent.'.$function.'('.$msg.')</script>';
        }

        echo $msg;
    }

    /**
     * @TYPE function
     * @NAME link 生成链接
     * @PARAM string(string) uri
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function link($string = '')
    {
        $entry = self::$global['define']['rewrite'] == false ? SIS_ENTRY . '?' : '';

        $link = SIS_HOST . $entry . $string;

        return $link;
    }

    /**
     * @TYPE function
     * @NAME input 获取input数据
     * @PARAM name(string) 名称
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function input($name = false, $default = false)
    {
        $data = isset(self::$global['param'][$name]) && self::$global['param'][$name] ? self::$global['param'][$name] : $default;
        return $data;
    }

    /**
     * @TYPE function
     * @NAME data 数组
     * @PARAM key(string) 名称
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function str($data, $key = 'id')
    {
        $return = $result = array();
        $key = explode(',', $key);
        foreach($data as $k => $v)
        {
            foreach($key as $i => $j)
            {
                if(isset($v[$j]) && $v[$j])
                {
                    $return[$j][] = $v[$j];
                }
            }
        }
        foreach($return as $k => $v)
        {
            $result[$k] = implode(',', $v);
        }

        return $result;
    }
    
    /**
     * @TYPE function
     * @NAME data 数组
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function path($path)
    {
        if(!is_dir($path))
        {
            mkdir($path,0777);
            exec("chmod 777 ".$path);
        }
        return $path;
    }
    
    /**
     * @TYPE function
     * @NAME data 数组
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function pic($pic)
    {
		if(!strstr($pic, 'http://'))
		{
			$pic = siscon::$global['define']['pic'] . 'view/' . $pic;
		}
        $pic = $pic;
        return $pic;
    }
    
    static public function location($url, $js = false)
    {
        if($js == true)
		{
			common_output('<script>location.href="'.$url.'"</script>');
		}
		else
		{
			header('Location: ' . $url);
		}
		exit;
    }
    
    static function maketime($v)
	{
		if(strstr($v, ' '))
		{
			$t = explode(' ', $v);
			$k = explode('-', $t[0]);
			$v = explode(':', $t[1]);
			$v = mktime($v[0], $v[1], $v[2], $k[1], $k[2], $k[0]);
		}
		elseif(strstr($v, '-'))
		{
			$t = explode('-', $v);
			$v = mktime($t[3], $t[4], $t[5], $t[1], $t[2], $t[0]);
		}
		return $v;
	}
	
	/**
     * @TYPE function
     * @NAME data 数组
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    static public function template($file, $project = false, $data = array())
    {
		$project 	= $project ? $project : self::$global['project'];
        if($file == '404')
        {
            $project = 'main';
        }
        include(SIS_MODULE_ROOT . $project . '/'.siscon::$global['template'].'/' . $file . '.html');
        if($file == '404')
        {
            die;
        }
    }
    
    static public function array_sort($arr,$keys,$type='asc')
	{
		$keysvalue = $new_array = array();
		foreach ($arr as $k=>$v){
			$keysvalue[$k] = $v[$keys];
		}
		if($type == 'asc'){
			asort($keysvalue);
		}else{
			arsort($keysvalue);
		}
		reset($keysvalue);
		foreach ($keysvalue as $k=>$v){
			$new_array[$k] = $arr[$k];
		}
		return $new_array;
	}
    static public function user($uid)
    {
        if($uid <= 0)
        {
            return false;
        }
        $user = self::model('user', 'user')->one($uid);
        return $user;
    }
    static public function userPhoto($pic, $size)
    {
        if(strstr($pic, '/photo/'))
        {
            return $pic;
        }
        elseif(strstr($pic, '/face/'))
        {
            return $pic;
        }
        else
        {
			$pic .= '.' . $size . '.jpg';
			return $pic;
		}
    }
    
    static public function face($sex = 'bm')
    {
		$file = SIS_FRONT_HOST . siscon::$global['define']['template'] . '/face/'.$sex.'.png';
		return $file;
	}
    static public function email($email, $subject, $body, $ishtml = true)
    {
        $phpmailer = self::core('email');

        $mailconfig = self::$global['define']['email'];

        /// 设置发送信息
        $phpmailer->CharSet = "utf-8";

        $phpmailer->IsSMTP();

        $phpmailer->Host = $mailconfig['host'];

        $phpmailer->SMTPAuth = true;

        $phpmailer->Username = $mailconfig['user'];

        $phpmailer->Password = $mailconfig['pass'];

        $phpmailer->From = $mailconfig['from'];

        $phpmailer->FromName = $mailconfig['name'];
        
        $phpmailer->PluginDir = SIS_CORE_ROOT . 'email/';

        /// 设置接收者信息

        $phpmailer->AddAddress($email,"");

        $phpmailer->Subject = $subject;

        $phpmailer->Body = $body;

        $phpmailer->IsHTML($ishtml);

        /// 发送邮件

        $ret = $phpmailer->Send();
        return $ret;
    }
    
    static public function cut($string, $length = 80, $etc = '...')
    {
		$result = '';
		$string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'utf-8');
		for($i = 0, $j = 0; $i < strlen($string); $i++)
		{
			if($j >= $length)
			{
				for($x = 0, $y = 0; $x < strlen($etc); $x++)
				{
					if($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0'))
					{
						$x += $number - 1;
						$y++;
					}
					else
					{
						$y += 0.5;
					}
				}
				$length -= $y;
				break;
			}
			if($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0'))
			{
				$i += $number - 1;
				$j++;
			}
			else
			{
				$j += 0.5;
			}
		}
		for($i = 0; (($i < strlen($string)) && ($length > 0)); $i++)
		{
			if($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0'))
			{
				if($length < 1.0)
				{
					break;
				}
				$result .= substr($string, $i, $number);
				$length -= 1.0;
				$i += $number - 1;
			}
			else
			{
				$result .= substr($string, $i, 1);
				$length -= 0.5;
			}
		}
		$result = htmlentities($result, ENT_QUOTES, 'utf-8');
		if($i < strlen($string))
		{
			$result .= $etc;
		}
		return $result;

	}
	
	//获取ip
    static public function ip() 
    {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    }
    
    //将textarea的数据进行编码
    static public function text_encode($string)
    {
		return base64_encode(str_replace('&nbsp;', '{nbsp}', $string));
	}
	//将textarea的数据进行编码
    static public function text_decode($string)
    {
		return str_replace('{nbsp}', '&nbsp;', base64_decode($string));
	}
}


