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
siscon::core('oauth', false);
class Oauth_Front extends Oauth
{
    /**
     * @desc oauth请求
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */

    function get_get()
    {
        //授权步骤1 申请url
        $result = $this->_request(siscon::link('at/callback'));
    }

    /**
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    function callback_get()
    {
        //授权步骤2 申请callback
        $this->_callback(siscon::link('at/callback'));
    }

    /**
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    function check_get()
    {
        print_r($_REQUEST);die;
    }

    /**
     * @desc 重新获取token
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    function refresh_get()
    {
        //授权步骤2 申请callback
        $result = $this->_refreshToken();
    }
}
