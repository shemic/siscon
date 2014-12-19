<?php
/*
type的值：ttable、mysql、sqlite、redis、tt等
*/

$mysql = siscon::$global['define']['mysql'];

$store = array
(
    //建立一个mysql表 图片配置表
    'pic_config' => array
    (
        'type'      => 'mysql',
        'host'      => $mysql['host'],
        'port'      => $mysql['port'],
        'username'  => $mysql['user'],
        'password'  => $mysql['pass'],
        'dbname'    => $mysql['dbname'],
        'charset'   => $mysql['charset'],
        'create'   => $mysql['create'],
        'col' => array
        (
            'id' => 'int-11',
            'name' => 'varchar-24 配置名',
            'key' => 'varchar-24 配置key',
            'width' => 'int-11 宽度设置',
            'height' => 'int-11 高度设置',
            'size' => 'int-11 图片大小',
            't_width' => 'int-11 缩放图宽度设置',
            't_height' => 'int-11 缩放图高度设置',
            't_type' => 'int-11 缩放图类型1为等比2为居中3为上4为下',
            'c_width' => 'int-11 裁图宽度设置',
            'c_height' => 'int-11 裁图高度设置',
            'c_type' => 'int-11 裁图类型1为等比2为居中3为上4为下',
            'w_type' => 'int-11 水印类型1左上2为左下3为右上4为右下5为居中',
            'w_pic' => 'int-11 水印图片，从配置里读取',
            'filename' => 'int-1 是否生成文件名1不生成2生成',
            'quality' => 'int-11 清晰度默认为0',
            'content' => 'varchar-255 备注描述',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'status' => 'int-1 1为开放2为关闭',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
	# 图片表
    'pic' => array
    (
        'type'      => 'mysql',
        'host'      => $mysql['host'],
        'port'      => $mysql['port'],
        'username'  => $mysql['user'],
        'password'  => $mysql['pass'],
        'dbname'    => $mysql['dbname'],
        'charset'   => $mysql['charset'],
        'create'   => $mysql['create'],
        'col' => array
        (
            'id' => 'int-11',
            'config_id' => 'int-11 图片配置id',
            'did' => 'int-11 关联id',
            'file' => 'varchar-255 图片地址',
            'source' => 'varchar-255 原图片地址',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
);
