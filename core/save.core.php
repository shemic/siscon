<?php
/**
 *@filename save.class.php
 *@desc session存储类（也可以选择存储到cookie中）
 *
 *@author yubin@condenast
 *@date 2012-08-27
 */

class Save
{
    /**
     * @desc 设置key
     * @var string
     */
    private $_key = '';
    
    /**
     * @desc 前缀
     * @var string
     */
    private $_prefix = 'siscon';

    /**
     * @desc 调用的方法
     * @var string
     */
    private $_method = 'session';
    
    /**
     * @desc 构造函数
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    public function init($key = false, $method = 'session')
    {
		siscon::core('security',false);
		$this->_key 	= $key ? $key : $this->_key;
		$this->_method 	= $method ? $method : $this->_method;
		$this->_method 	= ucwords($this->_method);
		return $this;
	}

    /**
     * @desc 加入数据
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    public function add($key, $value)
    {
        $this->_key($key);
        $value = base64_encode(serialize($value));
        $v = Security::encrypt($value, $this->_key);
        $method = '_set' . $this->_method;
        $this->$method($key, $v);
        return $v;
    }

    /**
     * @desc 获取数据
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    public function get($key, $type = false)
    {
        $this->_key($key);
        $method = '_get' . $this->_method;
        $value = $this->$method($key);
        $type == false && $value = Security::decrypt($value, $this->_key);
		$value = unserialize(base64_decode($value));
        return $value;
    }

    /**
     * @desc 清理数据
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    public function un($key)
    {
        $method = '_unset' . $this->_method;
        return $this->$method($key);
    }

    /**
     * @desc 设置配置数据
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _key($key)
    {
        $this->_key = $this->_prefix . '_' . $this->_method . '_' . $key;
    }

    /**
     * @desc cookie
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _setCookie($key, $value)
    {
        return setCookie($this->_prefix . $key, $value, SIS_TIME + 3600);
    }

    /**
     * @desc cookie
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _getCookie($key)
    {
        return $_COOKIE[$this->_prefix . $key];
    }

    /**
     * @desc cookie
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _unsetCookie($key)
    {
        return setCookie($this->_prefix . $key, false, SIS_TIME - 3600);
    }

    /**
     * @desc session
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _setSession($key, $value)
    {
        return $_SESSION[$this->_prefix . $key] = $value;
    }

    /**
     * @desc session
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _getSession($key)
    {
        return (isset($_SESSION[$this->_prefix . $key]) && $_SESSION[$this->_prefix . $key]) ? $_SESSION[$this->_prefix . $key] : false;
    }

    /**
     * @desc session
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    private function _unsetSession($key)
    {
        unset($_SESSION[$this->_prefix . $key]);
        return true;
    }
}
