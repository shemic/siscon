<?php
/*~:SISCON:~
.---------------------------------------------------------------------------.
|  Software: SISCON - 一款用于php敏捷开发的架构程序                         		|
|   Version: 1.0.0                                                          |
|   Contact: 暂无                                                           	|
|      Info: 暂无                                                           	|
|   Support: 暂无                                                           	|
| --------------------------------------------------------------------------|
|    Author: Leo (suwibin.yu)                                               |
| Copyright (c) 2013-2018, Leo. All Rights Reserved.                        |
'---------------------------------------------------------------------------'

/**
 * @TYPE class
 * @NAME route 路由类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Route
{
    /**
     * @TYPE function
     * @NAME load 获取
     * @PARAM uri(string) 传入的uri
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function load($uri = false)
    {
		siscon::$global['uri'] = $uri;
		# xss

		# 获取当前访问方式：get/post/delete/
        siscon::$global['request'] = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';

		# 获取uri
        if(siscon::$global['uri'] === false)
        {
			if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest")
			{
				# ajax请求
				siscon::$global['type'] = 'ajax';
			}
			else
			{
				# 正常请求
				siscon::$global['type'] = '';
			};
            

            //$_SERVER['QUERY_STRING']PATH_INFO

            # 获取请求 URI
            siscon::$global['uri'] = (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : ((isset($_SERVER['ORIG_SCRIPT_FILENAME'])) ? str_replace(SIS_ROOT . SIS_ENTRY . '/', '', $_SERVER['ORIG_SCRIPT_FILENAME']) : '');
            siscon::$global['uri'] = trim(siscon::$global['uri'], '/');

            # 默认首页
            empty(siscon::$global['uri']) && siscon::$global['uri'] = 'home';
            
        }
        else
        {
			# 接口请求
            //siscon::$global['type'] = 'interface';
        }


        $file = SIS_WRITE_ROOT . 'front.route.php';
        siscon::$global['template'] = 'front';
        if(siscon::$global['uri'] == 'manage')
        {
            siscon::$global['uri'] = 'main/manage/home';
        }
		# manage 为后台管理关键词 不允许使用
		if(strstr(siscon::$global['uri'], SIS_MANAGE))
		{
			siscon::$global['template'] = 'manage';

			$param = siscon::resolve(siscon::$global['uri']);
			if(isset(siscon::$global['project']))
			{
				$route = siscon::config('route');
                if(!strstr(siscon::$global['method'], 'login'))
                {
					$array = explode('/', siscon::$global['uri']);
					siscon::$global['uri_method'] = $array[0] . '/' . $array[1] . '/' . $array[2];
					if(siscon::$global['param'])
					{
						$i = 0;
						foreach(siscon::$global['param'] as $k => $v)
						{
							
							if(strstr($k, 'search_'))
							{
								$l = '&';
								if($i == 0)
								{
									$l = '/';
								}
								siscon::$global['uri_method'] .= $l . $k . '=' . $v;
								
								$i++;
							}
							
						}
					}
				    siscon::$global['auth'] = siscon::core('auth')->init(siscon::$global['uri_method']);
				}
				$state = true;

				include($file);

				if(isset($module)) siscon::$global['load_module'] = $module;
				
				if(isset($time[siscon::$global['project']]) && $time[siscon::$global['project']] >= $route['filetime'])
				{
					 $state = false;
				}

				if($state == true && $route)
				{
					$time[siscon::$global['project']] = $route['filetime'];
					$module[siscon::$global['project']] = siscon::$global['project'];
					unset($route['filetime']);
					foreach($route as $k => $v)
					{
						$front[$k] = $v;
					}
					$content = '<?php $front = ' . var_export($front, true) . ';$time = ' . var_export($time, true) . ';' . ';$module = ' . var_export($module, true) . ';';
					file_put_contents($file, $content);
				}
						
				return siscon::service($param);
			}
			siscon::error('路由错误', 'route');
		}
		else
		{
			if(!file_exists($file))
			{
				siscon::error('前台路由不存在', 'route');
			}
			
			$user = siscon::core('save')->init(false, 'cookie')->get('user');
			if($user)
			{
				siscon::core('save')->init(false, 'session')->add('user', $user);
				if($user['admin'])
				{
					siscon::core('save')->init(false, 'session')->add('admin', $user['admin']);
				}
			}
			
			include($file);

			#完全匹配
			if(isset($front[siscon::$global['uri']]) && $front[siscon::$global['uri']])
			{
				siscon::$global['uri'] = $front[siscon::$global['uri']];
			}
			else
			{
				#正则匹配
				foreach($front as $key => $val)
				{
					$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

					if(preg_match('#^'.$key.'$#', siscon::$global['uri']))
					{
						if(strstr($val, '$') && strstr($key, '('))
						{
							siscon::$global['uri'] = preg_replace('#^'.$key.'$#', $val, siscon::$global['uri']);
							break;
						}

					}
				}
			}
			
			//echo siscon::$global['uri'];die;
			return siscon::service(siscon::$global['uri']);
		}
    }
}
