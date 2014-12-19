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
 * @NAME model 图片处理类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Img
{
    /**
     * @desc 使用的图片转换类型,默认是gd库，也可以用imagemagick,值为im
     * @var string
     */
    private $_type = 'im';//另一个值是im

    /**
     * @desc 进行缩略图 值为'600_100,100_200'
     * @var string
     */
    private $_thumb = '';

    /**
     * @desc 进行裁切图 值为'600_100,100_200'
     * @var string
     */
    private $_crop = '';

    /**
     * @desc 如果文件存在，值为true则强制再次操作，默认为false
     * @var string
     */
    private $_setup = false;

    /**
     * @desc 图片水平位置 0=>100 1=>100
     * @var string
     */
    private $_position = array();

    /**
     * @desc 源文件
     * @var string
     */
    private $_source;
    
    /**
     * @desc 裁切图片时候需要抛弃的大小
     * @var string
     */
    private $_dropSize = array();

    /**
     * @desc 目标文件
     * @var string
     */
    private $_dest = array();

    /**
     * @desc 添加水印图片
     * @var array
     */
    private $_mark = array();

    /**
     * @desc 添加文字
     * @var array
     */
    private $_txt = array();

    /**
     * @desc image的源信息
     * @var array
     */
    private $_image = null;

    /**
     * @desc image的类型
     * @var array
     */
    private $_imageType = null;
    
    /**
     * @desc 图片压缩的清晰度
     * @var int
     */
    private $_quality = 20;

    /**
     * @desc 生成的图片名
     * @var int
     */
    private $_name = null;

    /**
     * @desc 设置图片库类型
     * @param type(string) 类型
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }
    
    /**
     * @desc 设置清晰度
     * @param quality(int) 清晰度
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setQuality($quality)
    {
        $this->_quality = $quality;
        return $this;
    }

    /**
     * @desc 设置强制功能
     * @param setup(bool) 是否强制再次生成重复的文件
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setSetup($setup)
    {
        $this->_setup = $setup;
        return $this;
    }

    /**
     * @desc 设置缩略图
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setThumb($thumb)
    {
        $this->_thumb = $thumb;
        
        return $this;
    }

    /**
     * @desc 设置裁切图
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setCrop($crop)
    {
        $this->_crop = $crop;
        return $this;
    }

    /**
     * @desc 设置生成的文件名
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setName($name)
    {
		if(is_string($name)) $name = explode(',', $name);
        $this->_name = $name;
        return $this;
    }

    /**
     * @desc 设置位置
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }

    /**
     * @desc 设置水印
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setMark($mark)
    {
        $this->_mark = $mark;
        $this->_check('mark', 'water');
        $this->_check('mark', 'position');
        return $this;
    }

    /**
     * @desc 设置文字
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setTxt($txt)
    {
        $this->_txt = $txt;
        
        return $this;
    }

    /**
     * @desc 设置源文件
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function setSource($source)
    {
        $this->_source = $source;
        $this->_check('source');
        $this->_image();
        return $this;
    }

    /**
     * @desc 获取生成的图片名字
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function getName($name = false, $key = false)
    {
		if($this->_name)
		{
			if($key)
			{
				return $this->_name[$key];
			}
			return $this->_name[0];
		}
		else
		{
			return $this->_source . $name;
		}
    }

    /**
     * @desc 获取目标文件
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function getDest($key = false)
    {
        return ($key && isset($this->_dest[$key])) ? $this->_dest[$key] : $this->_desc;
    }
    
    /**
     * @desc 获取宽高
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function getSize($source)
    {
		$this->setSource($source);
		$this->_image();
		return array($this->_image->getImageWidth(), $this->_image->getImageHeight());
	}

    /**
     * @desc 添加水印图
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function mark($source, $water, $setup = false, $name = false)
    {
        if($setup == true)
        {
            $this->setSetup($setup);
        }
        $this->setSource($source);
        if($name) $this->setName($name);
        $this->setMark($water);
        //if($position) $this->setPosition($position);
        $this->loadMethod('mark');
        return $this->getDest('mark');
    }
    
    /**
     * @desc 对图片进行压缩处理
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function compress($source, $quality = 20, $name = false)
    {
        $this->setSource($source);
        if($name) $this->setName($name);
        $this->setQuality($quality);
        $this->loadMethod('compress');
        return $this->getDest('compress');
    }

    /**
     * @desc 构造函数 可批量建立
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function __construct($config = array())
    {
        if($config)
        {
            $this->init($config);
        }
    }

    /**
     * @desc 批量建立
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function init($config)
    {
        $this->setSource($config['source']);
        if(isset($config['setup']) && $config['setup'] == true)
        {
            $this->setSource($config['setup']);
        }
        unset($config['source']);
        foreach($config as $k => $v)
        {
            $this->$k($config['source'], $v);
        }
        return $this->getDest();
    }

    /**
     * @desc 建立缩略图（原比例缩略）
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function thumb($source, $thumb, $setup = false, $name = false)
    {
        if($setup == true)
        {
            $this->setSetup($setup);
        }
        $this->setSource($source);
        if($name) $this->setName($name);
        $this->setThumb($thumb);
        $this->loadMethod('thumb');
        $this->_name = false;
        return $this->getDest('thumb');
    }

    /**
     * @desc 建立剪切图（从图中剪切）
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function crop($source, $crop, $position = false, $setup = false, $name = false)
    {
        if($setup == true)
        {
            $this->setSetup($setup);
        }
        $this->setSource($source);
        if($position) $this->setPosition($position);
        if($name) $this->setName($name);
        $this->setCrop($crop);
        $this->loadMethod('crop');
        $this->_name = false;
        return $this->getDest('crop');
    }
    
    /**
     * @desc 建立剪切图（从图中剪切）
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function thumbAndCrop($source, $size, $dropSize = array(), $position = false, $setup = true, $name = false)
    {
        if($setup == true)
        {
            $this->setSetup($setup);
        }
        $this->setSource($source);
        $this->setThumb($size);
        if($position) $this->setPosition($position);
        if($name) $this->setName($name);
        $this->setCrop($size);
        $this->loadMethod('thumb');
        $this->_dropSize = $dropSize;
        $this->loadMethod('crop');
        $this->_name = false;
        return $this->getDest('crop');
    }

    /**
     * @desc 加入文字
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function txt($source, $txt, $setup = false, $name = false)
    {
        if($setup == true)
        {
            $this->setSetup($setup);
        }
        $this->setSource($source);
        if($name) $this->setName($name);
        $this->setTxt($txt);
        $this->loadMethod('txt');
        return $this->getDest('txt');
    }

    /**
     * @desc 载入方法
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function loadMethod($method)
    {
        if($this->_type == 'im' && !class_exists('Imagick')) $this->_type = 'gd';
        $method = '_' . $this->_type . '_create_' . $method;
        $this->$method();
    }

    /**
     * @desc 对变量进行检测
     * @param name(string) 变量名称
     * @author leo(suwi.bin)
     * @date 2012-04-5
     */
    private function _check($name, $key = false)
    {
        $name = '_' . $name;
        if($name == '_mark')
        {
			return $this->_mark[$key];
		}
        if(isset($this->$name) && $this->$name)
        {
			if($key == false)
			{
				return $this->$name;
			}
			else
			{
				return $this->$name[$key];
			}
        }
        else
        {
            $this->_error($name . ' error');
        }
    }

    /**
     * @desc 匹配错误
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _error($string, $type = 1)
    {
        $errstr = '' ;
        $errstr .= "Img Tool Error:" . $string . "\n";
        return $errstr;
    }

    /**
     * @desc 获取文件源信息
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _image()
    {
        $this->_check('source');
        switch($this->_type)
        {
            case 'gd' :
                $this->_image = $this->_gd_get($this->_source);
                break;
            case 'im' :
                $this->_image = new Imagick($this->_source);
                $this->_imageType = strtolower($this->_image->getImageFormat());
                break;
        }
        return $this->_image;
    }

    /*********************
     * gd库函数
     *********************/

    /**
     * @desc 水印
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _gd_create_mark()
    {
        $this->_check('image');
        $this->_check('mark', 'water');
        $this->_check('mark', 'position');

        $this->_dest['mark'] = $this->getName('_mark.jpg');

        if($this->_setup == true || !file_exists($this->_dest['mark']))
        {
            $water  = $this->_gd_get($this->_mark['water']);
            $source_x = imagesx($this->_image);
            $source_y = imagesy($this->_image);
            $water_x = imagesx($water);
            $water_y = imagesy($water);
            $xy      = $this->_get_mark($source_x, $source_y, $water_x, $water_y);

            if($xy[2] == false)
            {
                $this->_gd_destroy($this->_image);
                $this->_gd_destroy($water);
                return;
            }

            $im = $this->_gd_copy($water, $water_x, $water_y, 0, 0, $l, $t, $this->_image);

            imagejpeg($im, $this->_dest['mark']);

            $this->_gd_destroy($this->_image);
            $this->_gd_destroy($water);
        }
        $this->_image = false;
    }

    /**
     * @desc 建立缩略图
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _gd_create_thumb()
    {
        $this->_check('image');
        $this->_check('thumb');
        if(!is_array($this->_thumb))
        {
			$array = explode(',',$this->_thumb);
		}
		else
		{
			$array = $this->_thumb;
		}
        $source_x = imagesx($this->_image);
        $source_y = imagesy($this->_image);
        $source_w = $source_x/$source_y;
        $source_h = $source_y/$source_x;
        foreach($array as $k => $v)
        {
            $this->_dest['thumb'][$k] = $this->getName('_' . $v . '_thumb.jpg', $k);
            if($this->_setup == true || !file_exists($this->_dest['thumb'][$k]))
            {
                $offset = explode('_',$v);
                if(isset($offset[2]) && $offset[2] == 1)
                {
                    if($source_x > $offset[0])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
                    }
                    elseif($offset[1] > 0 && $source_y > $offset[1])
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    else
                    {
                        $dest_x = $source_x;
                        $dest_y = $source_y;
                    }
                }
                elseif(isset($offset[2]) && $offset[2] == 2)
                {
                    //按照一定比例
                    if($source_x > $source_y && $source_y > $offset[1])
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    elseif($source_y > $source_x && $source_x > $offset[0])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
                    }
                    else
                    {
                        $dest_x = $source_x;
                        $dest_y = $source_y;
                    }
                }
                else
                {
                    $dest_x = $offset[0];
                    $dest_y = $offset[1];
                }

                $im = $this->_gd_copy($this->_image,$dest_x,$dest_y,$source_x,$source_y,0,0,false,1);

                imagejpeg($im, $this->_dest['thumb'][$k]);
                $this->_gd_destroy($im);
            }
        }

        $this->_gd_destroy($this->_image);
        $this->_image = false;
    }

    /**
     * @desc 建立剪切图
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _gd_create_crop()
    {
        $this->_check('image');
        $this->_check('crop');
        //$this->_check('position');
        if(!is_array($this->_crop))
        {
			$array = explode(',',$this->_crop);
		}
		else
		{
			$array = $this->_crop;
		}
        $source_x = imagesx($this->_image);
        $source_y = imagesy($this->_image);
        foreach($array as $k => $v)
        {
            $this->_dest['crop'][$k] = $this->getName('_' . $v . '_crop.jpg', $k);
            if($this->_setup == true || !file_exists($this->_dest['crop'][$k]))
            {
                $offset = explode('_',$v);
                $x = 0;
                $y = 0;
                if($this->_position)
                {
                    $x = $this->_position[0];
                    $y = $this->_position[1];
                }
                $im = $this->_gd_copy($this->_image,$offset[0],$offset[1],$offset[0],$offset[1],$x,$y);

                imagejpeg($im, $this->_dest['crop'][$k]);
                $this->_gd_destroy($im);
            }
        }
        $this->_gd_destroy($this->_image);
        $this->_image = false;
    }
    
    /**
     * @desc 添加水印文字
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _gd_create_txt()
    {
        $this->_check('source');
        $this->_check('image');
        //$this->_check('txt','file');
        $this->_check('txt','color');
        $this->_check('txt','size');
        $this->_check('txt','angle');
        $this->_check('txt','name');
        //$this->_check('txt','left');
        //$this->_check('txt','top');
        //$this->_check('txt','bgcolor');
        //$this->_check('txt','font');

        $this->_dest['txt'] = isset($this->_txt['file']) ? $this->_txt['file'] : $this->getName('_txt.jpg');

        if($this->_setup == true || !file_exists($this->_dest['txt']))
        {

            $color = $this->_txt['color'];
            $this->_txt['left'] = isset($this->_txt['left']) ? $this->_txt['left'] : 0;
            $this->_txt['top'] = isset($this->_txt['top']) ? $this->_txt['top'] : 0;
            if(!empty($color) && (strlen($color)==7))
            { 
                $R = hexdec(substr($color,1,2)); 
                $G = hexdec(substr($color,3,2)); 
                $B = hexdec(substr($color,5)); 
                putenv('GDFONTPATH=' . realpath('.'));
                $fontFile = isset($this->_txt['font']) ? $this->_txt['font'] : "SIMSUN.TTC";
                imagettftext($this->_image, $this->_txt['size'],$this->_txt['angle'], $this->_txt['left'], $this->_txt['top'], imagecolorallocate($this->_image, $R, $G, $B),$fontFile,$this->_txt['name']);
            }

            imagejpeg($this->_image, $this->_dest['txt']);
            $this->_gd_destroy($this->_image);
        }
        $this->_image = false;
    }

    /**
     * @desc 销毁资源
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _gd_destroy($im)
    {
        imagedestroy($im);
        return;
    }

    /**
     * @desc copy
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _gd_copy($im,$w,$h,$x,$y,$l,$t,$dim = false,$ti = 1)
    {
        if(false == $dim)
        {
            $dim = $this->_gd_create($w, $h,$ti); // 创建目标图gd2
            imagecopyresized($dim,$im,0,0,$l,$t,$w,$h,$x,$y);
        }
        else
        {

            imagecopy($dim, $im, $l,$t, 0, 0, $w,$h);
            //imagecopyresampled($dim, $im, $l,$t, 0, 0, $w,$h,$x,$y);
        }
        return $dim;
    }

    /**
     * @desc 获取数据源
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _gd_get($image)
    {
        $imgstream = file_get_contents($image);
        $im = imagecreatefromstring($imgstream);
        return $im;
    }

    /**
     * @desc 创建背景图
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _gd_create($w,$h,$t = 1)
    {
        $dim = imagecreatetruecolor($w,$h); // 创建目标图gd2

        //透明背景
        if($t == 2)
        {
            
            imagealphablending($dim, false);
            imagesavealpha($dim,true);
            $transparent = imagecolorallocatealpha($dim, 255, 255, 255, 127);
            imagefilledrectangle($dim, 0, 0, $w, $h, $transparent);
            
        }
        
        
        //空白背景
        if($t == 1)
        {
            $wite = ImageColorAllocate($dim,255,255,255);//白色
            imagefilledrectangle($dim, 0, 0, $w,$h, $wite);
            imagefilledrectangle($dim, $w, $h, 0,0, $wite);
            ImageColorTransparent($dim, $wite);
        }
        
        return $dim;
    }

    /*********************
     * im库函数
     *********************/

    /**
     * @desc 建立缩略图
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _im_create_thumb()
    {
        $this->_check('source');
        $this->_check('image');
        $this->_check('thumb');
        $source_x   = $this->_image->getImageWidth();
        $source_y   = $this->_image->getImageHeight();
        $source_w = $source_x/$source_y;
        $source_h = $source_y/$source_x;

        if(!is_array($this->_thumb))
        {
			$array = explode(',',$this->_thumb);
		}
		else
		{
			$array = $this->_thumb;
		}
        foreach($array as $k => $v)
        {
            $this->_dest['thumb'][$k] = $this->getName('_' . $v . '_thumb.jpg', $k);

            if($this->_setup == true || !file_exists($this->_dest['thumb'][$k]))
            {
                $offset = explode('_',$v);

                if(isset($offset[2]) && $offset[2] == 1)
                {
                    //完全等比例
                    if($source_x > $offset[0])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
                    }
                    elseif($offset[1] > 0 && $source_y > $offset[1])
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    else
                    {
                        $dest_x = $source_x;
                        $dest_y = $source_y;
                    }
                }
                elseif(isset($offset[2]) && $offset[2] == 2)
                {
                    //按照一定比例
                    if($offset[0] == 0 && $offset[1] > 0)
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    elseif($offset[1] > 0 && $source_x > $source_y && $source_y > $offset[1])
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    elseif($source_y > $source_x && $source_x > $offset[0])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
                    }
                    elseif($source_y == $source_x && $offset[0] == $offset[1])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[1];
                    }
                    elseif($source_x > $source_y && $source_y < $offset[1])
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    elseif($source_y > $source_x && $source_x < $offset[0])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
                    }
                    elseif($source_x > $offset[0])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
                    }
                    else
                    {
                        $dest_x = $source_x;
                        $dest_y = $source_y;
                    }
                }
                elseif(isset($offset[2]) && $offset[2] == 3)
                {
                    //按照比例缩放，如有多余则留白（或黑...如果实在留不了白的话）
                    $b = $offset[0]/$offset[1];
                    $l = $source_x/$source_y;
                    
                    if($b > $l)
                    {
						$dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
					}
                    else
                    {
						$dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
					}
                }
                elseif(isset($offset[2]) && $offset[2] == 4)
                {
                    //按照一定比例
                    if($offset[0] == 0 && $offset[1] > 0)
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    elseif($offset[1] > 0 && $source_x > $source_y && $source_y >= $offset[1])
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    elseif($source_y > $source_x && $source_x >= $offset[0])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
                    }
                    elseif($source_y == $source_x && $offset[0] < $offset[1])
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    elseif($source_y == $source_x && $offset[0] > $offset[1])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
                    }
                    elseif($source_y == $source_x && $offset[0] == $offset[1])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[1];
                    }
                    elseif($source_x > $source_y && $source_y < $offset[1])
                    {
                        $dest_x = $offset[1]*$source_w;
                        $dest_y = $offset[1];
                    }
                    elseif($source_y > $source_x && $source_x < $offset[0])
                    {
                        $dest_x = $offset[0];
                        $dest_y = $offset[0]*$source_h;
                    }
                    else
                    {
                        $dest_x = $source_x;
                        $dest_y = $source_y;
                    }
                }
                else
                {
                    //直接放大和缩小
                    $dest_x = $offset[0];
                    $dest_y = $offset[1];
                }
				//echo $dest_y;die;
				$this->_image = new Imagick($this->_source);  
				$this->_image->thumbnailImage($dest_x, $dest_y);
                
                if(isset($offset[2]) && $offset[2] == 3)
                {
					/* 按照缩略图大小创建一个有颜色的图片 */  
					$canvas = new Imagick();  
					$color = new ImagickPixel("white");
					$canvas->newImage($offset[0], $offset[1], $color, 'png');
					//$canvas->paintfloodfillimage('transparent',2000,NULL,0,0);
					/* 计算高度 */  
					$x = ($offset[0] - $dest_x)/2;  
					$y = ($offset[1] - $dest_y)/2;  
					/* 合并图片  */  
					$canvas->compositeImage($this->_image, imagick::COMPOSITE_OVER, $x, $y);
					
					$canvas->setCompression(Imagick::COMPRESSION_JPEG); 
					$canvas->setCompressionQuality(100);

					$canvas->writeImage($this->_dest['thumb'][$k]);

					
					if(isset($offset[3]) && $offset[3] && $this->_dest['thumb'][$k])
					{
						$offset[3] = $offset[3] * 1024;
						$size = abs(filesize($this->_dest['thumb'][$k]));
						if($size > $offset[3])
						{
							$this->_compress($canvas, $offset[3], 80, $this->_dest['thumb'][$k]);
						}
					}
					
					$canvas = false;
				}
				else
				{
					$this->_image->setCompression(Imagick::COMPRESSION_JPEG); 
					$this->_image->setCompressionQuality(90);
					$this->_image->writeImage($this->_dest['thumb'][$k]);
				}
            }
        }
        
        $this->_name = false;

        //$this->_image = false;
    }
    /**
     * @desc 建立原图压缩图
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _im_create_compress()
    {
		$this->_check('source');
		$this->_check('quality');
		$this->_dest['compress'] = $this->getName('_compress.jpg');
		

		if(strstr($this->_dest['compress'], '.png'))
        {
            $this->_image->stripImage();
            $this->_image->setImageType(Imagick::IMGTYPE_PALETTE);
            $this->_image->writeImage($this->_dest['compress']);      
        }
        else
        {
            $this->_image->setCompression(Imagick::COMPRESSION_JPEG); 
    		$this->_image->setImageCompressionQuality($this->_quality);
    		$this->_image->writeImage($this->_dest['compress']);
        }

        $this->_image = false;
	}
    
    /**
     * @desc 对图片进行压缩
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _compress($canvas, $max, $num, $file)
    {
		$num = $num - 10;
		$temp = $file . '_temp_'.$num.'.jpg';
		$canvas->setCompressionQuality($num);
		$canvas->stripImage();
		
		$canvas->writeImage($temp);
			
		$size = abs(filesize($temp));
		if($size > $max && $num > 0)
		{
			unlink($temp);
			return $this->_compress($canvas, $max, $num, $file);
		}
		else
		{
			$canvas->destroy();
			copy($temp, $file);
			unlink($temp);
		}
	}

    /**
     * @desc 建立裁切图
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _im_create_crop()
    {
        $this->_check('source');
        $this->_check('image');
        $this->_check('crop');
        
        if(!is_array($this->_crop))
        {
			$array = explode(',',$this->_crop);
		}
		else
		{
			$array = $this->_crop;
		}
        foreach($array as $k => $v)
        {
			if($this->_name)
			{
				$this->_dest['crop'][$k] = $this->getName('_' . $v . '_crop.jpg', $k);
			}
			else
			{
				$this->_dest['crop'][$k] = (isset($this->_dest['thumb'][$k]) && $this->_dest['thumb'][$k]) ? $this->_dest['thumb'][$k] : $this->getName('_' . $v . '_crop.jpg', $k);
			}
            
            if($this->_dropSize)
			{
				$this->_image = new Imagick($this->_dest['crop'][$k]);
				$source_x   = $this->_image->getImageWidth();
				$source_y   = $this->_image->getImageHeight();
			}
			
            if($this->_setup == true || !file_exists($this->_dest['crop'][$k]))
            {
				$offset = explode('_',$v);
				if(isset($this->_dropSize[$k]) && $this->_dropSize[$k])
				{
					$offset[0] += $this->_dropSize[$k];
					$offset[1] += $this->_dropSize[$k];
				}
				
                if($this->_position)
                {
					# 加入根据百分比计算裁图
					if($this->_position[0] <= 0)
					{
						$this->_position[0] = $source_x/2 - $offset[0]/2;
					}
					elseif(strstr($this->_position[0], '%'))
                    {
						$this->_position[0] = $source_x * intval(str_replace('%', '', $this->_position[0]))/100;
					}
					if($this->_position[1] <= 0)
					{
						$this->_position[1] = $source_y/2 - $offset[1]/2;
					}
					elseif(strstr($this->_position[1], '%'))
                    {
						$this->_position[1] = $source_y * intval(str_replace('%', '', $this->_position[1]))/100;
					}
					$x = $this->_position[0];
					$y = $this->_position[1];
                }
                else
                {
					$x = $source_x/2 - $offset[0]/2;
					$y = $source_y/2 - $offset[1]/2;
				}
				if($x < 0)
				{
					$x = 0;
				}
				if($y < 0)
				{
					$y = 0;
				}

                if($this->_imageType == 'gif')
                {
                    $this->_im_gif($offset[0], $offset[1], $x, $y);
                }
                else
                {  
                    $this->_image->cropImage($offset[0], $offset[1], $x, $y);
                }
                
                if(isset($offset[2]) && $offset[2] == 3 && isset($offset[3]) && $offset[3] > 0)
                {
					$this->_image->writeImage($this->_dest['crop'][$k]);
					$offset[3] = $offset[3] * 1024;
					$size = abs(filesize($this->_dest['crop'][$k]));
					if($size > $offset[3])
					{
						$this->_compress($this->_image, $offset[3], 80, $this->_dest['crop'][$k]);
					}
				}
				else
				{
					$this->_image->setCompression(Imagick::COMPRESSION_JPEG); 
					$this->_image->setCompressionQuality(90);
					$this->_image->writeImage($this->_dest['crop'][$k]);
				}
            }
        }

        $this->_image = false;
    }

    private function _im_gif($w, $h, $x, $y, $d = false)
    {
        $canvas = new Imagick();
        $images = $this->_image->coalesceImages();
        foreach($images as $frame)
        {
            $img = new Imagick();
            $img->readImageBlob($frame);
            if($d != false)
            {
                $img->drawImage($d);
            }
            else
            {
                $img->cropImage($w, $h, $x, $y);
            }

            $canvas->addImage($img);
            $canvas->setImageDelay($img->getImageDelay());
            if($d == false) $canvas->setImagePage($w, $h, 0, 0);
        }
        
        $this->_image->destroy();
        $this->_image = $canvas;
    }

    /**
     * @desc 建立水印
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _im_create_mark()
    {
        $this->_check('source');
        $this->_check('image');
        $this->_check('mark', 'water');
        $this->_check('mark', 'position');

        $this->_dest['mark'] = $this->getName('_mark.jpg');

        if($this->_setup == true || !file_exists($this->_dest['mark']))
        {
            $water = new Imagick($this->_mark['water']);
            $draw = new ImagickDraw();

            $source_x   = $this->_image->getImageWidth();
            $source_y   = $this->_image->getImageHeight();
            $water_x    = $water->getImageWidth();
            $water_y    = $water->getImageHeight();
            $xy         = $this->_get_mark($source_x, $source_y, $water_x, $water_y);


            $draw->composite($water->getImageCompose(), $xy[0], $xy[1], $water_x, $water_y, $water);
      
            if($this->_imageType == 'gif')
            {
                $this->_im_gif(0, 0, 0, 0, $draw);
            }
            else
            {
                $this->_image->drawImage($draw);
            }
	
            $this->_image->writeImage($this->_dest['mark']);
        }

        $this->_image = false;
    }

    /**
     * @desc 建立文字
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _im_create_txt()
    {
        $this->_check('source');
        $this->_check('image');
        //$this->_check('txt','file');
        $this->_check('txt','color');
        $this->_check('txt','size');
        $this->_check('txt','angle');
        $this->_check('txt','name');
        //$this->_check('txt','left');
        //$this->_check('txt','top');
        //$this->_check('txt','bgcolor');
        //$this->_check('txt','font');

        $this->_dest['txt'] = isset($this->_txt['file']) ? $this->_txt['file'] : $this->getName('_txt.jpg');

        if($this->_setup == true || !file_exists($this->_dest['txt']))
        {

            $draw = new ImagickDraw();
            if(isset($this->_txt['font']))          $draw->setFont($this->_txt['font']);
            if(isset($this->_txt['size']))          $draw->setFontSize($this->_txt['size']);
            if(isset($this->_txt['color']))         $draw->setFillColor($this->_txt['color']);
            if(isset($this->_txt['bgcolor']))       $draw->setTextUnderColor($this->_txt['bgcolor']);
              
            if($this->_imageType == 'gif')
            {  
                foreach($this->_image as $frame)
                {
                    $frame->annotateImage($draw, 0, 0, $this->_txt['angle'], $this->_txt['name']);
                }
            }
            else
            {
                $this->_image->annotateImage($draw, 0, 0, $this->_txt['angle'], $this->_txt['name']);
            }

            $this->_image->writeImage($this->_dest['txt']);
        }

        $this->_image = false;
    }

    private function _get_mark($source_x, $source_y, $water_x, $water_y)
    {
        $this->_check('mark', 'position');
        $l = 0;
        $t = 0;
        $state = true;
        if($this->_mark['position'])
        {
            switch($this->_mark['position'])
            {
                case 1:
                    //左上
                    break;
                case 2:
                    //左下
                    $t = $source_y - $water_y;
                    break;
                case 3:
                    //右上
                    $l = $source_x - $water_x;
                    break;
                case 4:
                    //右下
                    $l = $source_x - $water_x;
                    $t = $source_y - $water_y;
                    break;
                case 5:
                    //中间
                    $l = $source_x/2 - $water_x/2;
                    $t = $source_y/2 - $water_y/2;
                    break;
                default :
                    $state = false;
                    break;
            }
        }
        return array($l, $t, $state);
    }
    
    /**
     * @desc 判断是否网络文件，如果是，则生成一个本地路径
     * @param $file 网络文件地址
     * @param $path 生成的路径，默认为项目根目录下的'data/upload'
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _setFileName($file, $path = '/data/upload/')
    {
		if(strstr($file, 'http://'))
		{
			$array = explode('/', $file);
			$filename = $array[count($array)-1];
			$file = siscon::path(siscon::path(siscon::path(siscon::path(PROJECT_ROOT_PATH . $path) . date("Y") . '/') . date("m") . '/') . date("d") . '/') . $filename;
		}
		return $file;
	}
	
	/**
     * @desc 对网络文件进行拷贝复制到本地
     * @param $file 网络文件地址
     * @param $path 生成的路径，默认为项目根目录下的'data/upload'
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function copyFile($file, $path = '/data/upload/')
    {
		if(strstr($file, 'http://'))
		{
			$new = $this->_setFileName($file);
			if(!is_file($new))
			{
				$content = file_get_contents($file);
				file_put_contents($new, $content);
			}
			$file = $new;
		}
		return $file;
	}
}
?>
