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

# 基本配置
*/

ini_set('session.gc_maxlifetime', 24 * 3600); //设置时间
session_start(); 
setcookie(session_name(), session_id(), SIS_TIME + 24 * 3600, "/");

$config = array
(
	# 数据库配置
	'mysql' => array
	(
		"host" => "192.168.1.205",
		"port" => "3306",
		"user" => "root",
		//"pass" => "fwGO@)!$",
		"pass" => "123456",
		"dbname" => "siscon",
		"charset" => "utf8",
		"create" => 1,
	),

    # 数据库配置
	'mysql' => array
	(
		"host" => "localhost",
		"port" => "3306",
		"user" => "root",
		"pass" => "123456",
		"dbname" => "siscon",
		"charset" => "utf8",
		"create" => 1,
	),
    # 站点名
    'title' => '快乐福利 - happy well',
    'titles' => '快乐福利官方网站，快乐生活，福利你我。',
    'description' => '快乐福利是中国第一家以...',
    'keywords' => '快乐、福利、等等',
    # 定义前台模版目录，相对于public来说
    'template' => 'fashion',
	# 是否开启rewrite
	'rewrite' => true,
    # 配置水印图片
    'water' => array
    (
		# 位置
		'position' => array
		(
            -1 => '不选择',
            1 => '左上',
            2 => '左下',
            3 => '右上',
            4 => '右下',
            5 => '居中',
		),
		# 水印图
		'pic' => array
			(
				# 第二个参数为水印图所在位置，一般为write目录下的water
				1 => array('水印图', SIS_ROOT . 'public/fashion/images/water_1.png'),
				//2 => array('水印图2', ''),
			),
    ),
    # 图片上传地址
    'upload' => 'http://siscon.paladinly.com/upload',
    'ueditor' => 'http://siscon.paladinly.com/ueditor',
    # 图片浏览地址
    'pic' => SIS_IMG_HOST . 'data/',
    # 系统头像地址
    'photo' => SIS_IMG_HOST . 'data/photo/',
    # 列表页对应的配置，样式
    'cate' => array
    (
        # 样式
		'style' => array
		(
			-1 => array
            (
                'name' => '默认样式',
                'value' => '',
                'link' => '',
                'pic' => array('s_180_360','180*360'),
                'default' => true,
                'template' => 'default',
            ),
            /*
            6 => array
            (
                'name' => '秀场',
                'link' => '',
                'pic' => array('s_180_360','180*360'),
                'len' => array(10),
                'template' => 'show',
            ),
            8 => array
            (
                'name' => '风格搭配',
                'link' => '',
                'pic' => array('s_400_500','400*500'),
                'len' => array(10,100,15),
                'template' => 'fashion',
            ),
            13 => array
            (
                'name' => '杂志',
                'link' => '',
                'pic' => array('s_400_500','400*500'),
                'len' => array(10,100,15),
                'template' => 'mag',
            ),
            */
            20 => array
            (
                'name' => '多样式混合',
                'link' => '',
                'template' => 'all',
            ),
		),
    ),
    # 文章的列表样式
    'article' => array
    (
        # 样式
		'style' => array
		(
		
			-1 => array
            (
                'name' => '默认样式',
                'value' => '',
                'link' => '',
                'pic' => array('news_pic','620*620,不能超过1000*1000'),
                'default' => true,
                'template' => 'default',
            ),
            /*
            2 => array
            (
                'name' => '三块内容推荐',
                'link' => '',
                'pic' => array('news_pic','620*620,不能超过1000*1000'),
                # 文字长度提醒，分别对应标题、内容、父级标题
                'len' => array(30, 0, 10),
                'template' => 'third',
            ),
            */
			2 => array
            (
                'name' => '正方图一行三列',//原三块内容推荐 读取规则有变
                'link' => '',
                'pic' => array('news_pic','620*620,不能超过1000*1000'),
                # 文字长度提醒，分别对应标题、内容、父级标题
                'len' => array(30, 0, 10),
                'template' => 'third',
            ),
            15 => array
            (
                'name' => '街拍图一行三列',
                'link' => '',
                'pic' => array('s_310_388','图片尺寸310*388，4：5，（3张）'),
                'len' => array(10,100,15),
                'template' => 'jp',
            ),
            3 => array
            (
                'name' => '左图右文',
                'link' => '',
                'pic' => array('news_pic','图像尺寸为345*345（1张）'),
                'len' => array(15, 200),
                'template' => 'leftpic',
            ),
            4 => array
            (
                'name' => '左文右图',
                'link' => '',
                'pic' => array('news_pic','图像尺寸为345*345（1张）'),
                'len' => array(15, 200),
                'template' => 'rightpic',
            ),
            6 => array
            (
                'name' => '单品牌秀场',
                'link' => '',
                'pic' => array('s_180_360','图像尺寸为180*360（5张）'),
                'len' => array(10),
                'template' => 'show',
            ),
            /*
            7 => array
            (
                'name' => '单张焦点图',
                'link' => '',
                'pic' => array('focus','图像尺寸为1000*500（1张）'),
                'len' => array(100),
                'template' => 'focus',
            ),
            */
            8 => array
            (
                'name' => '搭配一分三',
                'link' => '',
                'pic' => array('s_400_500','图像尺寸为400*500（4张）'),
                'len' => array(10,100,15),
                'template' => 'fashion',
            ),
            9 => array
            (
                'name' => '视频推荐',
                'link' => '',
                'pic' => array('s_1000_400','1000*400（1张）'),
                'len' => array(100),
                'template' => 'video',
            ),
            /*
            13 => array
            (
                'name' => '杂志',
                'link' => '',
                'pic' => array('s_420_563','图像尺寸420*563（4张）'),
                'len' => array(10,100,15),
                'template' => 'mag',
            ),
            */
            16 => array
            (
                'name' => '自由大图',
                'link' => '',
                'pic' => array('bigpic_1','图片尺寸宽度1000，高度不限（1张）'),
                'template' => 'bigpic',
            ),
            19 => array
            (
                'name' => '每日一搭（shopping）',
                'link' => '',
                'pic' => array('s_640_320','图片尺寸宽度640*320（1张）'),
                'template' => 'shop',
            ),
            
		),
    ),
    # 模块调用的项目及其业务，一般不用更改
    'model' => array
    (
		# 项目
		'project' => array
		(
			1 => array
			(
				'value' => 'common',
				'name' => '公共',
				'service' => array
				(
					'null' => '暂无',
				),
			),
			2 => array
			(
				'value' => 'article',
				'name' => '文章管理系统',
				'service' => array
				(
					'article' => '文章',
					'cate' => '栏目',
				),
			),
		),
        # 样式
		'style' => array
		(
			-1 => array
            (
                'name' => '默认样式',
                'value' => '',
                'link' => '',
                'default' => true,
            ),
            100 => array
            (
                'name' => '1000*300首页广告位1',
                'link' => '',
                'pic' => array('banner','图片尺寸1000*300'),
                'align' => 4,
            ),
            
            101 => array
            (
                'name' => '1000*300首页广告位2',
                'link' => '',
                'pic' => array('banner','图片尺寸1000*300'),
                 'align' => 2,
            ),
            
            18 => array
            (
                'name' => '风尚卡',
                'link' => '',
                # 文字长度提醒，分别对应标题、内容、父级标题
                //'len' => array(100),
                'align' => 4,
            ),
            
            1 => array
            (
                'name' => '多张切换焦点图',
                'pic' => array('focus','1000*500'),
                'link' => '',
                'align' => 1,
            ),
            
            
            2 => array
            (
                'name' => '正方图一行三列',//原三块内容推荐 读取规则有变
                'link' => '',
                'pic' => array('news_pic','620*620,不能超过1000*1000'),
                # 文字长度提醒，分别对应标题、内容、父级标题
                'len' => array(30, 0, 10),
            ),
            
            5 => array
            (
                'name' => '多品牌秀场',
                'link' => '',
                'pic' => array('s_180_360','180*360'),
                'len' => array(10),
            ),
            6 => array
            (
                'name' => '单品牌秀场',
                'link' => '',
                'pic' => array('s_180_360','180*360'),
                'len' => array(10),
            ),
            
            
            3 => array
            (
                'name' => '左图右文',
                'link' => '',
                'pic' => array('news_pic','620*620,不能超过1000*1000'),
                'len' => array(15, 200),
            ),
            4 => array
            (
                'name' => '左文右图',
                'link' => '',
                'pic' => array('news_pic','620*620,不能超过1000*1000'),
                'len' => array(15, 200),
            ),
            
            /*
            7 => array
            (
                'name' => '自由大图模块',
                'link' => '',
                'pic' => array('focus','1000*500'),
                'len' => array(100),
            ),
            */
            
            16 => array
            (
                'name' => '自由大图',
                'link' => '',
                'pic' => array('bigpic_1','图片尺寸宽度1000，高度不限'),
            ),
            
            8 => array
            (
                'name' => '搭配一分三',
                'link' => '',
                'pic' => array('s_400_500','400*500'),
                'len' => array(10,100,15),
            ),
            
            19 => array
            (
                'name' => '每日一搭（shopping）',
                'link' => '',
                'pic' => array('s_640_320','图片尺寸宽度640*320（1张）'),
            ),
            
            9 => array
            (
                'name' => '视频推荐',
                'link' => '',
                'pic' => array('s_1000_400','1000*400'),
                'len' => array(100),
            ),
            13 => array
            (
                'name' => '多视频推荐',
                'link' => '',
                'pic' => array('s_570_320','大图尺寸为570*320，小图为200*113'),
                'len' => array(100),
            ),
            
            10 => array
            (
                'name' => '专栏作家',
                'link' => '',
                'pic' => array('s_180_180','180*180（等比例）'),
                'len' => array(10),
            ),
            
            15 => array
            (
                'name' => '街拍图一行三列',
                'link' => '',
                'pic' => array('s_310_388','图片尺寸310*388，4：5'),
                'len' => array(30, 0, 10),
            ),
            
            /*
            11 => array
            (
                'name' => '一行三列内容推荐',
                'link' => '',
                'pic' => array('news_pic','620*620,不能超过1000*1000'),
            ),
            
            17 => array
            (
                'name' => 'banner',
                'link' => '',
                'pic' => array('banner','图片尺寸1000*300'),
            ),
            */

            /*
            12 => array
            (
                'name' => '杂志订阅',
                'link' => '',
                'pic' => array('s_228_330','228*330'),
                'align' =>3,
            ),
            */
            
		),
		# 位置
		'align' => array
		(

            1 => '左侧/上部',
            2 => '中间',
            3 => '右侧/下部',
            4 => '头部',
		),
    ),
    # 邮件设置
    'email' => array
	(
		'host' => 'smtp.qq.com',
		'port' => '465',	
		'user' => '2934170',
		'pass' => 'yubinilyxddfe521',
		//'from' => 'service@fashionweekly.com.cn',
		'from' => '2934170@qq.com',
		'name' => '风尚志',
		'title' => '风尚志注册激活确认邮件',
		'body' => '您好，<br /><br />《风尚志》杂志官网在 {time} 收到了邮箱 {email} 的注册申请。<br />请点击以下链接完成注册：<br /><br />{link}<br /><br />如果邮箱不能打开链接,您可以将它复制到浏览器地址栏打开。',
		'password_title' => '风尚志密码重置申请',
		'password_body' => '您好，<br /><br />《风尚志》杂志官网在 {time} 收到了邮箱 {email} 的密码重置申请。<br />请点击以下链接完成新密码的修改：<br /><br />{link}<br /><br />如果邮箱不能打开链接,您可以将它复制到浏览器地址栏打开。',
	),
    # 刊物
    'mag'=> array
    (
        1 => '《风尚志》',
        2 => '《精品购物指南》',
        3 => '《其他公司内部杂志》',
    ),
    # 品牌配置
    'brand' => array
    (
        'etype' => array('选择品牌首字母', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '其他'),
        'price' => array('￥','$','€','￡','HK$','円','₩'),
    ),
    # 专题配置
    'feature' => array
    (
        'modeltype' => array('左图右文','纯文章','纯图片（每行两张）','图文混排（每行一条）'),
    ),
    # 后台菜单
    'menu' => array
    (
		1 => array
		(
			'name' => '内容管理',
			'child' => array
			(
				1 => array
				(
					'name' => '数据列表',
					'link' => 'happy/manage/data_list',
				),
				
				2 => array
				(
					'name' => '栏目列表',
					'link' => 'happy/manage/cate_list',
				),
				
				/*
				array
				(
					'name' => '文章发布',
					'link' => 'article/manage/update',
				),
				array
				(
					'name' => '文章列表',
					'link' => 'article/manage/list',
				),
				array
				(
					'name' => '栏目管理',
					'link' => 'article/manage/cate_list',
				),
				array
				(
					'name' => '自定义页面设置',
					'link' => 'page/manage/list',
				),
				array
				(
					'name' => '模块数据管理',
					'link' => 'page/manage/model_data_list',
				),
				array
				(
					'name' => '内容来源管理',
					'link' => 'article/manage/source_list',
				),
				/*
				array
				(
					'name' => '图片内容管理',
					'link' => 'brand/manage/pic_list',
				),
				
				array
				(
					'name' => '移动专题转化',
					'link' => 'mfeature/manage/list',
				),
				
				//array
				//(
					//'name' => '快速专题生成器',
					//'link' => 'page/manage/fast',
				//),
				*/
			),
		),
		/*
		2 => array
		(
			'name' => '网站用户数据管理',
			'child' => array
			(
				array
				(
					'name' => '用户管理',
					'link' => 'user/manage/list',
				),
				array
				(
					'name' => '评论管理',
					'link' => 'comment/manage/list',
				),
				array
				(
					'name' => '问题反馈列表',
					'link' => 'feedback/manage/list',
				),
				array
				(
					'name' => '订阅列表',
					'link' => 'user/manage/ding_list',
				),
				array
				(
					'name' => '分享日志',
					'link' => 'share/manage/list',
				),
				
				array
				(
					'name' => '消息管理',
					'link' => 'message/manage/list',
				),
				
				
			),
		),
		array
		(
			'name' => '品牌单品管理',
			'child' => array
			(
				array
				(
					'name' => '品牌列表',
					'link' => 'brand/manage/list',
				),
				array
				(
					'name' => '新增品牌',
					'link' => 'brand/manage/update',
				),
				array
				(
					'name' => '单品列表',
					'link' => 'brand/manage/product_list',
				),
				array
				(
					'name' => '新增单品',
					'link' => 'brand/manage/product_update',
				),
				
				array
				(
					//'name' => '单品图片关联',
					//'link' => 'brand/manage/pic_list',
				),
				
			),
		),
		*/
		5 => array
		(
			'name' => '后台权限管理',
			'child' => array
			(
				1 => array
				(
					'name' => '管理员列表',
					'link' => 'admin/manage/list',
				),
				2 => array
				(
					'name' => '角色管理',
					'link' => 'admin/manage/role_list',
				),
				
			),
		),
        6 => array
        (
            'name' => '站点设置',
            'child' => array
            (
				/*
                array
                (
                    'name' => '文章模板管理',
                    'link' => 'article/manage/template_list',
                ),
                array
                (
                    'name' => '站点图片配置',
                    'link' => 'pic/manage/list',
                ),
                */
                1 => array
                (
                    'name' => '远程抓取配置',
                    'link' => 'happy/manage/list',
                ),
                
            ),
        ),
        /*
        array
        (
            'name' => '广告位管理',
            'child' => array
            (
                array
                (
                    'name' => '广告位列表',
                    'link' => 'page/manage/model_data_list/pid=17&mid=50',
                ),
            ),
        ),
        */
    ),
);

