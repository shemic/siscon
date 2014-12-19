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
 * @NAME interface 接口类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Share_Front extends Module
{
	public function _platform()
	{
		$platform = array(
            '1'         => array('tqq',     "http://v.t.qq.com/share/share.php?"),
            '2'         => array('tsina',   "http://service.weibo.com/share/share.php?"),
            '3'         => array('qzone',   "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?"),
            '4'         => array('renren',  "http://widget.renren.com/dialog/share?"),
            '5'         => array('douban',  "http://www.douban.com/share/service?"),
            '6'         => array('weixin',  ""),
            '7'         => array('mtsina',   "http://service.weibo.com/share/share.php?"),
        );
        
        return $platform;
	}
	//分享数据收集
    public function collect_get_ajax()
    {
		$this->collect_get();
	}
    //分享数据收集
    public function collect_get()
    {   
		$callback = siscon::input('callback');
        $platform = $this->_platform();
        $sharedata['platform']	= siscon::input('platform');
        //获取站点信息
        foreach($platform as $pk => $pv)
        {
            if( $pv[0] == $sharedata['platform'] )
            {
                //$sharedata['platform']  = $pk;
            }
        }
        $sharedata['site']      = siscon::input('site', 1);
        $sharedata['cate']      = siscon::input('cat', 0);
        $sharedata['article']      = siscon::input('article', 0);
        $sharedata['title']     = siscon::input('title');
        
        
        //网站原链接
        if($sharedata['platform'] == 'renren' )
        {
            $sharedata['url']       = siscon::input('resourceUrl');
        }
        else
        {
            $sharedata['url']       = siscon::input('url');
        }
        //分享图片(后续这块可以优化)
        if($sharedata['platform'] == 'qzone' )
        {
            $sharedata['pic']       = siscon::input('pics');
        }
        elseif($sharedata['platform'] == 'douban' )
        {
            $sharedata['pic']       = siscon::input('image');
        }
        else
        {
            $sharedata['pic']       = siscon::input('pic');
        }
        
        $user= siscon::core('save')->get('user');
        $sharedata['uid'] = $user && $user['id'] ? $user['id'] : 0;
        
        //获取用户ip
        $sharedata['ip']        = siscon::ip();
        //实例化用户ip归属地类
        $qip = siscon::core('qip');
        //用户ip归属地
        $IpAddress      = $qip->getaddress($sharedata['ip']);
        $sharedata['city']      = $IpAddress['area1'] . '#' . $IpAddress['area2'];
        //获取浏览器信息
        $sharedata['browser']       = $_SERVER['HTTP_USER_AGENT'];
        
        if( !empty($_SERVER['REQUEST_URI']) )
        {
            if( strstr($_SERVER['REQUEST_URI'],'?') == true )
            {
                $uri = explode('?',$_SERVER['REQUEST_URI']);
                
                //这里的shareurl需要重组(做回流用)
                if( strstr($uri[1], '&') == true )
                {
                    $parms = explode('&', $uri[1]);
                    foreach($parms as $k => $v)
                    {
                        if( strstr($v, 'url=') == true || strstr($v, 'resourceUrl') == true )
                        {
                            $suffix = ''; 
                            //请求参数url  (目前新浪不支持动态参数的分享，同时也不支持带有@、url参数中包含&符号的分享，所以现在把url中的动态参数去掉,如果是有#则加在回流参数后)
                            if( strstr(urldecode($v), '?')==true )
                            {
                                $list = explode('?', urldecode($v));
                            }else{
                                $list[0] = $v;
                            }
                            //url中包含#
                            if( strstr(urldecode($v), '#')==true )
                            {
                                $list = explode('#', urldecode($v));
                            }else{
                                $list[0] = $v;
                                $suffix = '#' . $list[1];
                            }
                            
                            $parms[$k] = $list[0] . '?' . $sharedata['platform'] . '-' . $sharedata['cate'] . '-' . $sharedata['article'] . '-' . time() . '-' . $sharedata['site'] . $suffix;
                            //记录数据库surl
                            $list[0] = str_replace("url=",'',$list[0]);
                            $list[0] = str_replace("resourceUrl=",'',$list[0]);
                            $surl = $list[0] . '?' . $sharedata['platform'] . '-' . $sharedata['cate'] . '-' . $sharedata['article'] . '-' . time() . '-' . $sharedata['site'] . $suffix;
                            //分享后的链接
                            if( !empty($parms[$k]) )
                            {
                                $sharedata['surl']  = urldecode($surl);
                            }
                        }
                    }
                }                
                //合并分享参数
                $uri[1] = implode('&', $parms);
                //第三方媒体平台的分享链接
                foreach($platform as $pkUrl => $pvUrl)
                {
                    if( $pvUrl[0] == $sharedata['platform'] )
                    {
                        $shareurl = $pvUrl[1] . $uri[1];
                    }
                }
                
                //插入分享数据
                $id = siscon::model('share_log')->eupdate($sharedata, $sharedata, true);
                
                //点击页面分享按钮时
                $UrlHash = siscon::input('hash', '');
                if( !empty($sharedata['url']) && !empty($UrlHash) )
                {
					$totalwhere['url'] = $sharedata['url'];
					$totalwhere['url_hash'] = $UrlHash;
				
                
					$totalinfo = siscon::model('share_total')->info($totalwhere);
					
					if($totalinfo)
					{
						$totaldata['article'] =  $sharedata['article'];
						$totaldata['cate']   = $sharedata['cate'];
						$totaldata['total'] = $totalinfo['total'] + 1;                    
						siscon::model('share_total')->update($totaldata, array('id' => $totalinfo['id']));
                    }
                    else
                    {
						$totaldata = $totalwhere;
						$totaldata['article'] =  $sharedata['article'];
						$totaldata['cate']   = $sharedata['cate'];
						$totaldata['total'] = 1;
						siscon::model('share_total')->insert($totaldata);
					}
                }
                
                /**
                 * @desc 分享到微信及其它媒体平台
                 */
                if( $sharedata['platform'] == 'weixin' )
                {
                    $erweima = $this->erweima($sharedata['surl'],3,10);
                    echo $callback . '(' . json_encode(array('erweima' => $erweima)) . ')';
                }else{
                    echo '正在加载……'; 
                    //分享成功(以进入第三方页面)
                    echo '<script language=javascript>';
                    echo 'location.href=\''.$shareurl.'\';';
        			echo '</script>';
        			exit;
                }
            }
        }
    }
    public function reflux_get()
    {
		$this->reflux_get_ajax();
	}
    //回流数据收集
    public function reflux_get_ajax()
    {
        //ajax请求返回值
        $data = array(
            'msg' => 'ok',
        );
        $callback = siscon::input('callback');
		echo $callback . '(' . json_encode($data) . ')';
        
        //回流信息汇总
        $param = siscon::input('param');
        if( strstr($param, '?') == true )
        {
            $params = explode('?', $param);
            
            if( strstr($params[1],'&')==true )
            {
                $a = explode('&',$params[1]);
                $list = explode('-',$a[1]);
            }else{
                $list = explode('-',$params[1]);
            }
            
            $platform = $this->_platform();
            foreach($platform as $pk => $pv)
            {
                if( $pv[0] == $list[0] )
                {
                    $recode['platform'] = $pk;
                }
            }
            $recode['cate']      = $list[1];
            $recode['article']      = $list[2];
            
            if( $list[0] == 'weixin' )
            {
                $wx = explode('&', $list[3]);
                $recode['site']     = $wx[0];
            }else{
                $recode['site']     = $list[3];
            }
        }
        
        $user= siscon::core('save')->get('user');
        $recode['uid'] = $user && $user['id'] ? $user['id'] : 0;
        
        $recode['url']    = siscon::input('url');
        $recode['browser']     = siscon::input('ua');
        $recode['ip']     = $this->ip();
        //实例化用户ip归属地类
        $qip = siscon::core('qip');
        //用户ip归属地
        $IpAddress      = $qip->getaddress($recode['ip']);
        $recode['city']      = $IpAddress['area1'] . '#' . $IpAddress['area2'];
        
        //回流插入数据库(如果媒体平台不在配置中不插入)
        if( !empty($recode['platform']) && array_key_exists($recode['platform'], $platform) )
        {
            $refluxId = siscon::model('share_reflux')->eupdate($recode, $recode, true);
        }
    }
    public function shareTotal_get()
    {
		$this->shareTotal_get_ajax();
	}
    //累计分享次数更新
    public function shareTotal_get_ajax()
    {
		$callback = siscon::input('callback');
        $url = siscon::input('url');
        $UrlHash = siscon::input('hash', '');
        //初次访问或重新刷新页面时执行
        if( !empty($url) && empty($UrlHash) )
        {
            //验证是否记录
            $url = explode('?', $url);
            $url = $url[0];
            $where['url'] = $url;
            $data = siscon::model('share_total')->info($where);
            //只有初次访问才执行
            if( empty($data) ) //插入
            {
				
                $info['url']    = $url;
                //生成url相对应的hash码并验证是否冲突
                $info['url_hash'] = $this->check_code();
                $info['total'] = 0;
                $info['article']      = siscon::input('article', 0);
                $info['cate']      = siscon::input('cate', 0);
                $insertId = siscon::model('share_total')->insert($info);
                //js初始化执行
                $data['total']      = 0;
                $data['url_hash']   = $info['url_hash'];
            }else{
                //$data['total'] = $data['totle'];
            }

            echo $callback . '(' . json_encode($data) . ')';
        }
        //点击页面分享按钮时执行
        /*if( !empty($url) && !empty($UrlHash) )
        {
            $UpdateData = $this->_model->SelectTotle($url, $UrlHash);
            $info['totle'] = $UpdateData['totle'] + 1;
            $where = ' url_hash = \'' . $UrlHash . '\'';
            $this->_model->TotleUpdate($info, $where); 
        }*/
    }
    
    
    //获取ip
    public function ip() {
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
    
    //生成二维码
    public function erweima($url,$size,$margin)
    {
        $size = !empty($size) ? $size : 5;
        $margin = !empty($margin) ? $margin : 5;
        //$a = CondeQrcode::get($url,$size,$margin);
        
        $webDir = SIS_WRITE_ROOT . '/qrcode/';
		$filepath = date('Y',time()).'/'.date('m',time()).'/'.date('d',time()).'/';
		$this->createDir($webDir, $filepath);

		$filepath .= uniqid(time()).'.png';
		$filename = $webDir.$filepath;

		$size = (int)$size;
		$margin = (int)$margin;
		$level = 'H';
		$class = siscon::core('qrcode',false);
		Qrcode::png($url, $filename, $level, $size, $margin, TRUE);
		if(file_exists($filename)) {
			return SIS_IMG_HOST.'write/qrcode/'.$filepath;
		}
		
        return false;
    }
    
    /**
	* 根据目录路径判断是否存在此目录，如果不存在则生成
	* @param $webDir 文件的根目录地址路径
	* @param $fileDir 文件的目录路径
	**/
	private function CreateDir($webDir, $fileDir){

		$array = explode('/', $fileDir);

		foreach($array as $k => $v)
		{
			if(!strstr($v, '.'))
			{
				$webDir .= $v . '/';
				
				if(!is_dir($webDir)) 
				{
					mkdir($webDir,0777);//如果不是目录则生成新的目录
				}
			}
		}
	}
    
    /**
     * 生成短urlcode
     * @param num 生成的位数
     */ 
    public function generate_code($num) {
        $codes = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $code="";
        for($i=1;$i<=$num;$i++)
        {
            $code.=$codes{rand(0,61)};
        }
        return $code;
    }
    
    /**
     * 检证code是否冲突并生成新的code码
     * @return code
     */
     public function check_code()
     {
         $code = $this->generate_code(10);
         //验证url_hash是否冲突
         $iscode = siscon::model('share_total')->info(array('url_hash' => $code));
         if( empty($iscode) )
         {
             return $code;
         }else{
            $this->check_code();
         }
     }
     
     //线上测试
     public function online_test_get()
     {   
        $test = !empty($_GET['type']) ? $_GET['type'] : '';
        if( $test )
        {
            $this->_smarty->display('share_test.html');
        }else{
            $this->_smarty->display('share.html');
        }
        
     }
     
     
     //微信二维码接口
     public function wxInterface_get()
     {
        $url = siscon::input('url');
        $callback = siscon::input('callback');
        $size = siscon::input('size');
        $margin = siscon::input('margin');
        $json = siscon::input('json');
        if( !empty($url) )
        {
            $size   = !empty($size) ? $size : 5;
            $margin = !empty($margin) ? $margin : 5;
            $erweima = $this->erweima($url,$size,$margin);
            $erweima = isset($json) && $json ? json_encode($erweima) : $erweima;
            echo $callback . '(' . $erweima . ')';
        }else{
            echo $callback . '(' . '请输入URl，谢谢！'. ')';
        }
     }
}
