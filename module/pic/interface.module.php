
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

class Pic_Interface extends Module
{
    /**
     * @TYPE var
     * @NAME model 自动载入的数据模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    protected $_model = 'pic_config';

    /**
     * 上传图片
     */
	public function upload_post()
	{
		$image['newimage'] = $_FILES['Filedata'];
		$image['key'] = siscon::input('key');
		$output = $this->_upload($image);
		siscon::out($output);
	}
	
	public function ueditor_post()
	{
		$callback = siscon::input('callback');
		$image['newimage'] = $_FILES['Filedata'];
		$image['key'] = siscon::input('key');
		$output = $this->_upload($image);
		if($output['status'] == 1)
		{
			echo '{"url":"' .$output[ "url" ] . '","title":"'.$_POST['pictitle'].'","original":"1","state":"SUCCESS"}';
		}
		else
		{
			echo '{"url":"","fileType":"1","original":"1","state":"'.$output['message'].'"}';
		}
	}
	
	private function _upload($image)
	{
		$output['status'] = 0;

		if($image['newimage']['tmp_name'] == '')
        {
			$output['message'] = '没有选择文件';
			return $output;
		}
		
		if(trim($image['key']) == '')
        {
			$output['message'] = '请添加配置key';
			return $output;
		}
		
		$config = $this->_model->one($image['key'], 'key');
		
		
		if(!is_array($config) || empty($config))
        {
			$output['message'] = '此配置不存在';
			return $output;
		}

		$imageSize = $config['size'] > 0 ? 1024*$config['size'] : 5*1024*1024;
		
		if(($imageSize < $image['newimage']['size']) && $imageSize > 0)
        {
			$output['message'] = '文件不能超过'.$config['size'].'k';
			return $output;
		}

		$fileType = $this->_checktype($image['newimage']['tmp_name']);
		
        $fileTypeArray = array('JPG','PNG');
        
		if(!in_array(strtoupper($fileType),$fileTypeArray))
        {
			$output['message'] = '文件格式不符合要求';
			return $output;
		}

		$newimage = getimagesize($image['newimage']['tmp_name']);
		
		if(($config['width'] && $config['width'] < $newimage[0]) || ($config['height'] && $config['height'] < $newimage[1]))
        {
			$output['message'] = '宽图片不能超过'.$config['width'].',高不能超过'.$config['height'];
			return $output;
		}
		$_POST['newimage'] = $image['newimage'];

		$upload = siscon::core('upload');

		$config['type'] = 'img';
		$config['name'] = 'newimage'; 
		$config['filepath'] = $config['key'];
		
		$uploadInfo = $upload->save($config);
		
		//print_r($uploadInfo);die;
        
        $img = siscon::core('img');
        
        if($config['w_type'] > 0 && isset(siscon::$global['define']['water']['pic'][$config['w_pic']]))
        {
			$water = siscon::$global['define']['water']['pic'][$config['w_pic']][1];
			//$config['w_pic'] = SIS_WRITE_ROOT . 'water/' . $water;
			$config['w_pic'] = $water;
			if($config['w_pic'] && file_exists($config['w_pic']))
			{
				$method = 'mark';
				
				$img->mark($uploadInfo['view_file'], array('water'=>$config['w_pic'] ,'position'=>$config['w_type']), true, $uploadInfo['view_file']);
			}
		}
        
		if($config['c_width'] || $config['c_height'])
        {

            if(isset($config['c_type']) && $config['c_type'] == 1)
            {
				# 居中
                $size = ($config['c_width']+20).'_'.($config['c_height']+20).'_4';
                $file = $img->thumbAndCrop($uploadInfo['view_file'],$size,array(-20),false,true,$uploadInfo['view_file']);
            }
            elseif(isset($config['c_type']) && $config['c_type'] == 2)
            { 
				# 居上
                $size = ($config['c_width']+20).'_'.($config['c_height']+20).'_4';
                $file = $img->thumbAndCrop($uploadInfo['view_file'],$size,array(-20),array(false,'1%'),true,$uploadInfo['view_file']);
            }
            elseif(isset($config['c_type']) && $config['c_type'] == 3)
            {
				# 居下
                $size = ($config['c_width']+20).'_'.($config['c_height']+20).'_4';
                $file = $img->thumbAndCrop($uploadInfo['view_file'],$size,array(-20),array(false,'99%'),true,$uploadInfo['view_file']);
            }
            else
            {
				# 等比
                $size = $config['c_width'].'_'.$config['c_height'];
                $file = $img->crop($uploadInfo['view_file'], $size,false,true,$uploadInfo['view_file']);
            }
		}
        
		if(isset($config['quality']) && $config['quality'])
        {
            $img->compress($uploadInfo['view_file'], $config['quality'], $uploadInfo['view_file']);
        }
	
		if($config['t_width'] || $config['t_height'])
        {
            # 等比
            $size = $config['t_width'].'_'.$config['t_height'].'_2';
            $file = $img->thumb($uploadInfo['view_file'], $size,true,$uploadInfo['view_file']);
		}
		
		if(!empty($uploadInfo))
        {
			$data['config_id'] = $config['id'];
			$data['did'] = 0;
			@system('chmod -R 777 ' . $uploadInfo['view_file']);
			$data['file'] = str_replace(SIS_WRITE_ROOT, '', $uploadInfo['view_file']);
			$data['source'] = str_replace('view/', 'upload/', $data['file']);
			siscon::model('pic')->insert($data);

			$output['status'] = 1;
			$output['url'] = siscon::$global['define']['pic'] . $data['file'];
			return $output;
		}
		else
        {
            $output['message'] = '上传文件失败';
            return $output;
		}
	}
	
	/**
	* @desc 根据文件名称判断文件格式
	* @param $filename 文件名称
	* return string
	**/
	private function _checktype($filename)
	{
		$file = fopen($filename,"rb");
		$bin = fread($file,2);
		fclose($file);
		$strInfo = @unpack("c2chars",$bin);
		$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
		$fileType = '';
		switch($typeCode)
		{
			case 7790:
				$fileType = 'exe';
			break;
			case 7784:
				$fileType = 'midi';
			break;
			case 8297:
				$fileType = 'rar';
			break;
			case 255216:
				$fileType = 'jpg';
			break;
			case 7173:
				$fileType = 'gif';
			break;
			case 13780:
				$fileType = 'png';
			break;
			case 6677:
				$fileType = 'bmp';
			break;
			case 6787:
				$fileType = 'swf';
			break;
			case 6063;
				$fileType = 'php|xml';
			break;
			case 6033:
				$fileType = 'html|htm|shtml';
			break;
			case 8075:
				$fileType = 'zip';
			break;
			case 6782:
				$fileType = 'txt';
			break;
			case 4742:
				$fileType = 'js';
			break;
			case 8273:
				$fileType = 'wav';
			break;
			case 7368:
				$fileType = 'mp3';
			break;
			default:
				$fileType = 'unknown'.$typeCode;
			break;
		}
		if($strInfo['chars1'] == '-1' && $strInfo['chars2'] == '-40')
		{
			return 'jpg';
		}
		if($strInfo['chars1'] == '-119' && $strInfo['chars2'] == '80')
		{
			return 'png';
		}
		if($strInfo['chars1'] == '-48' && $strInfo['chars2'] == '-49')
		{
			return 'msi';
		}
		return $fileType;
	}
}
