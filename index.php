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
 * @NAME siscon 核心类，所有功能都在这里载入，由于特殊并且经常用到，没有遵循命名规范，类名小写。
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

include(dirname(__FILE__) . '/siscon.php');

siscon::core('route')->load();
