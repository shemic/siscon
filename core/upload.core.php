<?php

class Upload
{
    /**
     * @desc 保存配置数据
     * @var array
     */
    private $_config = array();

    /**
     * @desc 处理之后的数据
     * @var array
     */
    private $_data = array();

    /**
     * @desc 上传
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function __construct($name = false)
    {
        if($name) $this->save(array('name' => $name));
    }

    /**
     * @desc 设置配置
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function save($config)
    {
        $this->_config  = $config;
        $this->_file();
        return $this->_save();
    }

    /**
     * @desc 获取处理后的数据
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @desc 创建随机数
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _filename()
    {
        $ext = '.jpg';
        
        if($this->_data['tmp']['type'] == 'image/gif')
        {
			$ext = '.gif';
		}
		if(empty($this->_config['filename']))
		{
			$filename = md5($this->_data['tmp']['name']);
			$this->_data['filename'] = $filename . $ext;
		}
		else
		{
			$this->_data['filename'] = $this->_config['filename'];
		}
    }

    /**
     * @desc 创建文件名和路径
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _file()
    {
        $this->_checkConfig();
        
        $this->_post($this->_config['name']);
        
        $this->_filename();
        
        $this->_filepath(array('upload', 'view'));
    }
    
    /**
     * create path
     * 
     * @return mixed
     */
    private function _filepath($path)
    {
		if(is_array($path))
		{
			foreach($path as $v)
			{
				$this->_filepath($v);
			}
		}
		else
		{
			$root = siscon::path(siscon::path(SIS_WRITE_ROOT . $path . '/') . $this->_config['filepath']);
			if(isset($this->_config['id']) && $this->_config['id'] > 0)
			{
				$id = ceil($this->_config['id']/1000);
				
				$filepath = siscon::path($root . $id . '/');
			}
			else
			{
				$filepath = siscon::path(siscon::path(siscon::path($root . date("Y") . '/') . date("m") . '/') . date("d") . '/');
			}
			
			$this->_data[$path . '_file'] = $filepath . $this->_data['filename'];
		}
    }

    /**
     * @desc 开始上传
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function _save()
    {
		if(file_exists($this->_data['view_file']))
		{
			return $this->_data;
		}
        if(isset($this->_config['filesize']) && $this->_config['filesize'] > 0 && $this->_data['tmp']['size'] > $this->_config['filesize'])
        {
            $this->_error('file size error -1');
            return -1;
        }
        if(isset($this->_config['filelimit']) && strstr($this->_config['filelimit'], '*'))
        {
            $imgstream = file_get_contents($this->_data['tmp']['tmp_name']);
            $im = imagecreatefromstring($imgstream);

            $width = imagesx($im);
            $height = imagesy($im);

            @imagedestroy($im);
            $attribute = explode(',', $this->_config['filelimit']);
            $array = explode('*', $attribute[0]);

            if($width > $array[0])
            {
                $this->_error('file max width error -2');
                return -2;
            }
            if($height > $array[1])
            {
                $this->_error('file max height error -3');
                return -3;
            }

            if(isset($attribute[1]))
            {
                $array = explode('*', $attribute[1]);
                if($width < $array[0])
                {
                    $this->_error('file min width error -4');
                    return -4;
                }
                if($height < $array[1])
                {
                    $this->_error('file min height error -5');
                    return -5;
                }
            }
        }

        if($this->_data['type'] && $this->_data['tmp']['type'] && !strstr($this->_data['type'], $this->_data['tmp']['type']))
        {
            $this->_error('upload type error -6');
            return -6;
        }
        
        //if(!copy($this->_data['tmp']['tmp_name'], $this->_data['file']))
        if(!copy($this->_data['tmp']['tmp_name'], $this->_data['view_file']))
        {
            $this->_error('upload error -7');
            return -7;
        }
        else
        {
			
			# 复制一份用来给用户看的，我们保留一份吧
			//copy($this->_data['file'], $this->_data['view_file']);
			//@unlink($this->_data['tmp']['tmp_name']);
            $this->_data['name'] = $this->_data['tmp']['name'];
            $this->_data['type'] = $this->_data['tmp']['type'];

            if(isset($this->_config['width']) && $this->_config['width'])
            {
                $imgstream = file_get_contents($this->_data['file']);
                $im = imagecreatefromstring($imgstream);

                $this->_data['width'] = imagesx($im);
                $this->_data['height'] = imagesy($im);

                @imagedestroy($im);
            }
            $img = false;

            //图片压缩
            if(isset($this->_config['compress']))
            {
                $img = siscon::core('img');
                $img->compress($this->_data['file'], $this->_config['compress']);
            }
            
            //添加水印
            if(isset($this->_config['mark']))
            {
                if(!$img)
                {
                    $img = siscon::core('img');
                }
                $img->mark($this->_data['file'], $this->_config['mark']);
            }

            //建立小图
            if(isset($this->_config['thumb']))
            {
                if(!$img)
                {
                    $img = siscon::core('img');
                }
                $img->thumb($this->_data['file'], $this->_config['thumb']);
            }
            //建立小图
            if(isset($this->_config['crop']))
            {
                if(!$img)
                {
                    $img = siscon::core('img');
                }
                $img->crop($this->_data['file'], $this->_config['crop']);
            }

            return $this->_data;
        }
    }

    /**
     * @desc 检测是否设置了配置
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _checkConfig()
    {
        if(!$this->_config)
        {
            $this->_error('config error');
        }
        if(!isset($this->_config['name']))
        {
            $this->_error('name error');
        }
        if(!isset($this->_config['filetype']))
        {
            $this->_config['filetype'] = 'file';
        }
        if(isset($this->_config['filepath']) && $this->_config['filepath'])
        {
            $this->_config['filepath'] .= '/';
        }

        $this->_data['type'] = false;
        switch($this->_config['filetype'])
        {
            case 'file':
                $this->_data['type'] = 'image/png,image/x-png,image/jpg,image/jpeg,image/pjpeg,image/gif,image/bmp,application/javascript,text/css,application/octet-stream';
                break;
            case 'img':
                $this->_data['type'] = 'image/png,image/jpg,image/x-png,image/jpeg,image/pjpeg,image/gif,image/bmp,application/octet-stream';
                break;
            case 'excel':
                $this->_data['type'] = 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'stream':
                $this->_data['type'] = 'application/octet-stream';
                break;
        }
    }

    /**
     * @desc 获取post数据
     * @param *
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _post($name)
    {
		# 判断是否网络文件
		if(strpos($name, 'http://') !== false)
		{
			$state = false;
			if(strpos($name, '.jpg') !== false)
			{
				$state = true;
				$data['type'] = 'image/jpeg';
			}
			elseif(strpos($name, '.gif') !== false)
			{
				$state = true;
				$data['type'] = 'image/gif';
			}
			else
			{
				$data['type'] = '';
			}
			
			if($state == true)
			{
				$data['name'] = $name;
				$data['tmp_name'] = $name;
				/*
				# 生成临时文件
				$content = file_get_contents($name);
				$name = md5($name);
				$file = siscon::path(SIS_WRITE_ROOT . 'tmp/') . $name . '.jpg';

				file_put_contents($file, $content);
				
				$data['name'] = $name;
				$data['tmp_name'] = $file;
				*/
			}
			
			return $this->_data['tmp'] = $data;
		}
		else
		{
			if(isset($_FILES[$name]) && $_FILES[$name])
			{
				return $this->_data['tmp'] = $_FILES[$name];
			}
			elseif(isset($_POST[$name]) && $_POST[$name])
			{
				return $this->_data['tmp'] = $_POST[$name];
			}
			elseif(isset($_GET[$name]) && $_GET[$name])
			{
				return $this->_data['tmp'] = $_GET[$name];
			}
		}

        return false;
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
        $errstr .= "Upload Error:" . $string . "\n";
        return $errstr;
    }
}
?>
