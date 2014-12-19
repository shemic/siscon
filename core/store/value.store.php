<?php
/**
 * @sourcename store_value.class.php
 * @desc 数据通用存储 默认值类
 * @author leo(suwi.bin) - bin.yu@condenast.com.cn
 * @date 2012-05-8
 */

class Value
{
    /**
     * @desc 获取默认值
     * @param method(string) 本类中的方法
     * @param data(string) 原始数据
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    public function get($method, $data)
    {
        $method = '_' . $method . 'Data';
        if(!method_exists($this,$method))
        {
            return false;
        }
        return $this->$method();

    }

    /**
     * @desc 获取ip
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _ipData()
    {
        $ip = false;
        if(!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if($ip)
            {
                array_unshift($ips, $ip);
                $ip = false;
            }
            for($i = 0; $i < count($ips); $i++)
            {
                if(!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i]))
                {
                    if(version_compare(phpversion(), "5.0.0", ">="))
                    {
                        if(ip2long($ips[$i]) != false)
                        {
                            $ip = $ips[$i];
                            break;
                        }
                    }
                    else
                    {
                        if(ip2long($ips[$i]) != - 1)
                        {
                            $ip = $ips[$i];
                            break;
                        }
                    }
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    /**
     * @desc 获取uri
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _uriData()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @desc 获取refer
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _referData()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @desc 获取agent
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _agentData()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * @desc 获取lang
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _langData()
    {
        return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }

    /**
     * @desc 获取file
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _fileData()
    {
        return $_SERVER['SCRIPT_NAME'];
    }

    /**
     * @desc 获取method
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _requestData()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @desc 获取request data
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _reqdataData()
    {
        return serialize($_GET + $_POST);
    }

    /**
     * @desc 获取cdate 时间
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _cdateData()
    {
        return $_SERVER['REQUEST_TIME'];
    }
    
    /**
     * @desc 获取cdate 时间
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _stateData()
    {
        return 1;
    }

    /**
     * @desc 获取cdate 时间
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _mdateData()
    {
        return $_SERVER['REQUEST_TIME'];
    }

    /**
     * @desc 获取vtime
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _ctimeData()
    {
        return date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
    }

    /**
     * @desc 获取操作系统
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _osData()
    {
        $agent = $this->_agentData();
        $os = 'unknown';

        if(preg_match('win', $agent) && strpos($agent, '95'))
        {
            $os = 'Windows 95';
        }
        elseif(preg_match('win 9x', $agent) && strpos($agent, '4.90'))
        {
            $os = 'Windows ME';
        }
        elseif(preg_match('win', $agent) && ereg('98', $agent))
        {
            $os = 'Windows 98';
        }
        elseif(preg_match('win', $agent) && preg_match('nt 5.1', $agent))
        {
            $os = 'Windows XP';
        }
        elseif(preg_match('win', $agent) && preg_match('nt 5', $agent))
        {
            $os = 'Windows 2000';
        }
        elseif(preg_match('win', $agent) && preg_match('nt 6', $agent))
        {
            $os = 'Windows 7';
        }
        elseif(preg_match('win', $agent) && preg_match('nt', $agent))
        {
            $os = 'Windows NT';
        }
        elseif(preg_match('win', $agent) && ereg('32', $agent))
        {
            $os = 'Windows 32';
        }
        elseif(preg_match('linux', $agent))
        {
            $os = 'Linux';
        }
        elseif(preg_match('unix', $agent))
        {
            $os = 'Unix';
        }
        elseif(preg_match('sun', $agent) && preg_match('os', $agent))
        {
            $os = 'SunOS';
        }
        elseif(preg_match('ibm', $agent) && preg_match('os', $agent))
        {
            $os = 'IBM OS/2';
        }
        elseif(preg_match('Mac', $agent) && preg_match('PC', $agent))
        {
            $os = 'Macintosh';
        }
        elseif(preg_match('PowerPC', $agent))
        {
            $os = 'PowerPC';
        }
        elseif(preg_match('AIX', $agent))
        {
            $os = 'AIX';
        }
        elseif(preg_match('HPUX', $agent))
        {
            $os = 'HPUX';
        }
        elseif(preg_match('NetBSD', $agent))
        {
            $os = 'NetBSD';
        }
        elseif(preg_match('BSD', $agent))
        {
            $os = 'BSD';
        }
        elseif(ereg('OSF1', $agent))
        {
            $os = 'OSF1';
        }
        elseif(ereg('IRIX', $agent))
        {
            $os = 'IRIX';
        }
        elseif(preg_match('FreeBSD', $agent))
        {
            $os = 'FreeBSD';
        }
        elseif(preg_match('teleport', $agent))
        {
            $os = 'teleport';
        }
        elseif(preg_match('flashget', $agent))
        {
            $os = 'flashget';
        }
        elseif(preg_match('webzip', $agent))
        {
            $os = 'webzip';
        }
        elseif(preg_match('offline', $agent))
        {
            $os = 'offline';
        }
        else
        {
            $os = 'unknown';
        }

        return $os;
    }

    /**
     * @desc 获取浏览器
     * @author leo(suwi.bin)
     * @date 2012-05-9
     */
    private function _browserData()
    {
        $agent = $this->_agentData();
        $brower = 'unknown';
        if(strpos($agent, 'MSIE 9.0'))
        {
            $brower = 'Internet Explorer 9.0';
        }
        if(strpos($agent, 'MSIE 8.0'))
        {
            $brower = 'Internet Explorer 8.0';
        }
        elseif(strpos($agent, 'MSIE 7.0'))
        {
            $brower = 'Internet Explorer 7.0';
        }
        elseif(strpos($agent, 'MSIE 6.0'))
        {
            $brower = 'Internet Explorer 6.0';
        }
        elseif(strpos($agent, 'MSIE 5.5'))
        {
            $brower = 'Internet Explorer 5.5';
        }
        elseif(strpos($agent, 'MSIE 5.0'))
        {
            $brower = 'Internet Explorer 5.0';
        }
        elseif(strpos($agent, 'MSIE 4.01'))
        {
            $brower = 'Internet Explorer 4.01';
        }
        elseif(strpos($agent, 'NetCaptor'))
        {
            $brower = 'NetCaptor';
        }
        elseif(strpos($agent, 'Netscape'))
        {
            $brower = 'Netscape';
        }
        elseif(strpos($agent, 'Lynx'))
        {
            $brower = 'Lynx';
        }
        elseif(strpos($agent, 'Opera'))
        {
            $brower = 'Opera';
        }
        elseif(strpos($agent, 'Konqueror'))
        {
            $brower = 'Konqueror';
        }
        elseif(strpos($agent, 'Mozilla/5.0'))
        {
            $brower = 'Mozilla';
        }
        else
        {
            $brower = 'unknown';
        }
        return $brower;
    }
}
