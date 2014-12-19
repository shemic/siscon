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

class User_Front extends Module
{
    public function init()
    {        
        $this->_data['refer'] = siscon::link(str_replace('.', '/', siscon::input('refer', '')));
        if(!siscon::input('refer'))
        {
            $this->_data['refer'] = ($_SERVER['HTTP_REFERER'] && !strstr($_SERVER['HTTP_REFERER'], 'reg')) ? $_SERVER['HTTP_REFERER'] : $this->_data['refer'];
        }
        $this->_data['save'] = siscon::core('save')->init();
    }
    public function reg_suc_get()
    {
		$info = $this->_data['save']->get('user_valid');
		if($info)
		{
			//注册成功页面，同时发送验证邮件
			# 发送邮件
			$link = siscon::link('reg-activate-' . $info['code']);
			siscon::$global['define']['email']['body'] = str_replace('{username}', $info['username'], siscon::$global['define']['email']['body']);
			siscon::$global['define']['email']['body'] = str_replace('{link}', $link, siscon::$global['define']['email']['body']);
			siscon::$global['define']['email']['body'] = str_replace('{email}', $info['email'], siscon::$global['define']['email']['body']);
			siscon::$global['define']['email']['body'] = str_replace('{time}', date('Y-m-d H:i:s'), siscon::$global['define']['email']['body']);
            $subject = siscon::$global['define']['email']['title'];
            $body = siscon::$global['define']['email']['body'];
            siscon::email($info['email'], $subject, $body);
            
            $this->template('reg_suc');
		}
		else
		{
			siscon::template('404');
		}
	}
	public function reg_activate_get()
	{
		$code = siscon::input('code');
		$info = $this->_data['save']->get('user_valid');
		if($info && $info['code'] == $code && $info['id'] > 0)
		{
			$data['status'] = 1;
			$where['id'] = $info['id'];

			$id = siscon::model('user')->eupdate($where, $data, true);

			$info = siscon::model('user')->one($where['id']);
			
			$this->_data['save']->add('user', $info);
			
			$this->_data['save']->un('user_valid');
			
			$link = siscon::link('profile');
			
			siscon::location($link);
			
			return;
        
			$this->template('reg_act');
		}
		else
		{
			siscon::template('404');
		}
	}
    public function photo_get()
	{
		$user = $this->_data['save']->get('user');
        if(!$user)
        {
            siscon::location('/');
        }
		
        $user = $this->_data['save']->get('user');
		$this->_data['info'] = siscon::model('user')->one($user['id']);
		$data = scandir(SIS_WRITE_ROOT . 'photo/');
		foreach($data as $k  => $v)
		{
			if(strstr($v, '.jpg'))
			{
				$this->_data['photo'][] = siscon::$global['define']['photo'] . $v;
			}
		}
        $this->template('photo');
	}
	
	public function save_photo_post_ajax()
	{
		$user = $this->_data['save']->get('user');
        if(!$user)
        {
            siscon::error('请登录！');
        }
		$type = siscon::input('type');
		$update['pic'] = siscon::input('photo');
		if($type == 1)
		{
			$x = siscon::input('x');
			$y = siscon::input('y');
			$w = siscon::input('w');
			$h = siscon::input('h');
			
			$crop[0] = siscon::input('c1');
			$crop[1] = siscon::input('c2');
			$crop[2] = siscon::input('c3');
			$img = siscon::core('img');
			$pic = str_replace(siscon::$global['define']['pic'], SIS_WRITE_ROOT, $update['pic']);
			foreach($crop as $k => $v)
			{
				$config = explode('|', $v);
				$file = $img->thumb($pic, $config[1] . '_' . $config[2], true);
				if($file[0])
				{
					$return = $img->crop($file[0], $config[0], array($config[3], $config[4]), true, $pic . '.'.$config[0].'.jpg');
					$create[] = str_replace(SIS_WRITE_ROOT, siscon::$global['define']['pic'], $return[0]);
				}
			}
			//$echo = implode('|', $create);
		}
        
        $id = siscon::model('user')->update($update, array('id' => $user['id']));
        if($user['level'] == 100)
        {
			$update['pic'] = siscon::userPhoto($update['pic'], '180_180');
			siscon::model('admin', 'admin')->update($update, array('uid' => $user['id']));
		}

        siscon::out('操作成功');
	}
	
	public function find_password_get()
	{
        $this->template('find_password');
	}
	
	public function find_password_post()
	{

        $email = siscon::input('email');
        if(!$email)
        {
            siscon::error('email不正确');
        }
        if($_SESSION['randcode'] != siscon::input('code'))
		{
			siscon::error('验证码不正确');
		}
		$this->_data['info'] = siscon::model('user')->one($email, 'email');
        if(!$this->_data['info'])
        {
            siscon::error($email . '还未注册');
        }
        siscon::core('security',false);
        $code = microtime();
        $info['code'] = md5(Security::encrypt($code));
        $info['email'] = $email;
        $this->_data['save']->add('user_password', $info);
        $link = siscon::link('password-' . $info['code']);
        siscon::$global['define']['email']['body'] = str_replace('{username}', '尊敬的用户', siscon::$global['define']['email']['password_body']);
        siscon::$global['define']['email']['body'] = str_replace('{link}', $link, siscon::$global['define']['email']['body']);
        siscon::$global['define']['email']['body'] = str_replace('{email}', $info['email'], siscon::$global['define']['email']['body']);
		siscon::$global['define']['email']['body'] = str_replace('{time}', date('Y-m-d H:i:s'), siscon::$global['define']['email']['body']);
		
        $subject = siscon::$global['define']['email']['password_title'];
        $body = siscon::$global['define']['email']['body'];
        siscon::email($info['email'], $subject, $body);
        siscon::out('操作成功');
	}

    public function find_password_set_get()
    {
        $code = siscon::input('code');
        $info = $this->_data['save']->get('user_password');
        if($info && $info['email'] && $info['code'] == $code)
        {
            $this->_data['info'] = $info;

            $this->template('find_password_1');
        }
        else
        {
            siscon::template('404');
        }
    }
    public function find_password_set_post()
    {
        $data['new_password'] = siscon::input('new_password');
        if(!$data['new_password'])
        {
            siscon::error('请输入新密码');
        }
        $data['c_password'] = siscon::input('c_password');
        if($data['new_password'] != $data['c_password'])
        {
            siscon::error('确认密码不正确');
        }
        $update['password'] = md5($data['new_password']);

        $code = siscon::input('code');
        $info = $this->_data['save']->get('user_password');
        if($info && $info['email'] && $info['code'] == $code)
        {
            $id = siscon::model('user')->update($update, array('email' => $info['email']));

            siscon::out('操作成功');
            $this->_data['save']->un('user_password');
        }
        else
        {
            siscon::error('操作失败');
        }
    }
    public function password_get()
	{
		$user = $this->_data['save']->get('user');
        if(!$user)
        {
            siscon::location('/');
        }
        $user = $this->_data['save']->get('user');
		$this->_data['info'] = siscon::model('user')->one($user['id']);
        $this->template('password');
	}
	public function password_post()
	{
        $user = $this->_data['save']->get('user');
        if(!$user)
        {
            siscon::error('请登录！');
        }
        if($_SERVER['HTTP_REFERER'] != siscon::link('user-password'))
        {
            siscon::error('错误的来源');
        }
        $data['old_password'] = siscon::input('old_password');
        if(!$data['old_password'])
        {
            siscon::error('请输入旧密码');
        }
        $data['old_password'] = md5($data['old_password']);
        $user = siscon::model('user')->one($user['id']);
        if($data['old_password'] != $user['password'])
        {
            siscon::error('旧密码不正确');
        }
        $data['new_password'] = siscon::input('new_password');
        if(!$data['new_password'])
        {
            siscon::error('请输入新密码');
        }
        $data['c_password'] = siscon::input('c_password');
        if($data['new_password'] != $data['c_password'])
        {
            siscon::error('确认密码不正确');
        }
        $update['password'] = md5($data['new_password']);
        if($update['password'] == $data['old_password'])
        {
            siscon::error('新密码和旧密码相同');
        }

        $id = siscon::model('user')->update($update, array('id' => $user['id']));

        siscon::out('操作成功');
	}
    public function profile_get()
	{
		$user = $this->_data['save']->get('user');
        if(!$user)
        {
            siscon::location('/');
        }
        $user = $this->_data['save']->get('user');
		$this->_data['info'] = siscon::model('user')->one($user['id']);
        $this->template('profile');
	}
	public function profile_post()
	{
        $user = $this->_data['save']->get('user');
        if(!$user)
        {
            siscon::error('请登录！');
        }
        if($_SERVER['HTTP_REFERER'] != siscon::link('profile'))
        {
            //siscon::error('错误的来源');
        }

        if(siscon::input('email'))
        {
            $data['email'] = siscon::input('email');
            if(!$data['email'])
            {
		        siscon::error('用户邮箱不能为空');
            }

            $this->check_post_ajax('email', $data['email'], 2, '邮箱有错误');
            # 发送邮件
            $subject = '';
            $body = '';
            //siscon::email($data['email'], $subject, $body);
        }

        if(siscon::input('sex'))
        {
			$data['sex'] = siscon::input('sex');
			if(!$user['pic'] || strstr($user['pic'], '/face/'))
			{
				$data['pic'] = siscon::face($data['sex']);
			}
		}
        if(siscon::input('username'))
        {
            $data['username'] = siscon::input('username');
            $this->check_post_ajax('username', $data['username'], 2, '昵称有错误');
        }
        
        if(siscon::input('avatar'))
        {
            $data['pic'] = siscon::input('avatar');
        }
        if(siscon::input('birth'))
        {
            $data['birth'] = siscon::input('birth');
        }
        
        if(siscon::input('mobile')) $data['mobile'] = siscon::input('mobile');
        if(siscon::input('truename')) $data['truename'] = siscon::input('truename');
        
		
        $id = siscon::model('user')->update($data, array('id' => $user['id']));

        $info = siscon::model('user')->one($user['id']);
        if($info)
        {
            # 重写session
            $this->_data['save']->add('user', $info);
        }

        siscon::out('操作成功');
	}
    /**
     * 注册页
     */
	public function reg_get()
	{
        if($this->_data['save']->get('user'))
        {
            siscon::location(siscon::link());die;
        }
        $this->template('reg');
	}
	public function out_get()
	{
        $this->_data['save']->un('user');
        $this->_data['save']->un('admin');
        siscon::core('save')->init(false, 'cookie')->un('user');
        siscon::location($this->_data['refer']);
	}
	public function reg_post()
	{
        if($_SERVER['HTTP_REFERER'] != siscon::link('reg'))
        {
            siscon::error('错误的来源');
        }

        $data['email'] = siscon::input('email');
        if(!$data['email'])
        {
		    siscon::error('用户邮箱不能为空');
        }

        $this->check_post_ajax('email', $data['email'], 2, '邮箱有错误');
        $exists = $data;
        $data['password'] = siscon::input('password');
        $data['username'] = siscon::input('username');
        $this->check_post_ajax('username', $data['username'], 2, '昵称有错误');
        if(!$data['password'])
        {
		    siscon::error('用户密码不能为空');
        }
        $cpassword = siscon::input('cpassword');
        if($data['password'] != $cpassword)
        {
            siscon::error('确认密码不正确');
        }
        
        if($_SESSION['randcode'] != siscon::input('code'))
		{
			siscon::error('验证码不正确');
		}
        $data['sex'] = siscon::input('sex', 'bm');
        $data['mobile'] = siscon::input('mobile');
        $data['password'] = md5($data['password']);
		$data['pic'] = siscon::face($data['sex']);
        $data['ding'] = siscon::input('ding', 2);
        $data['status'] = 3;

        $id = siscon::model('user')->eupdate($exists, $data);

        $info = siscon::model('user')->one($id);
        if($info)
        {
            # 临时登录，方便激活帐号
            $info['refer'] = $this->_data['refer'];
            siscon::core('security',false);
            $code = microtime();
            $info['code'] = md5(Security::encrypt($code));
            $this->_data['save']->add('user_valid', $info);
            
            $this->_data['save']->add('user', $info);
            # 订阅电子报
            if($data['ding'] == 1)
            {
                $this->ding_post_ajax($info);
            }
            # 新增创始人
            if($id == 1)
            {
                $where['id'] = $id;
                $where['uid'] = $id;
                $info = siscon::model('admin', 'admin')->info($where);
                if(!$info)
                {
                    # 创始人
                    $group['name'] = '默认职位';
                    $role['name'] = '系统管理员';
                    $role['auth'] = 'all';
                    $exists = $where;
                    $group_exists = $group;
                    $role_exists = $role;
                    $where['cuser'] = $where['muser'] = $role['muser'] = $role['cuser'] = $group['muser'] = $group['cuser'] = $where['name'];
                    $where['group_id'] = siscon::model('group','admin')->eupdate($group_exists, $group, true);
                    $group['name'] = '专栏作家';
                    $group_exists = $group;
                    siscon::model('group','admin')->eupdate($group_exists, $group, true);
                    $where['role_id'] = siscon::model('role','admin')->eupdate($role_exists, $role, true);
                    $role['name'] = '编辑';
                    $role_exists = $role;
                    siscon::model('role','admin')->eupdate($role_exists, $role, true);
                    unset($where['id']);

                    $where['name'] = $data['username'];
                    $where['email'] = $data['email'];
                    $where['password'] = $data['password'];
                    $where['status'] = 1;
                    $id = siscon::model('admin', 'admin')->insert($where);
                    $w['id'] = $where['uid'];
                    $d['level'] = 100;
                    $id = siscon::model('user', 'user')->update($d, $w);
                }
            }
        }

        siscon::out('操作成功');
	}
    public function ding_post_ajax($info = array())
    {
        if($info && $info['email'])
        {
            $email = $info['email'];
            $user = $this->_data['save']->get('user');
        }
        else
        {
            $email = siscon::input('email');
        }
        $this->check_post_ajax('email', $email, 3, '请输入正确的邮箱');
        if($email)
        {
            $ding['email'] = $email;
            $ding_exists = $ding;
            $ding['uid'] = $user['id'] ? $user['id'] : -1;
            $ding['status'] = 1;
            siscon::model('user_ding')->eupdate($ding_exists, $ding, true);
            if(!$info)
            {
                siscon::out(1);die;
            }
        }

        siscon::out('请输入邮箱');
    }
	public function login_get()
	{
        $this->_data['refer'] = siscon::link(str_replace('.', '/', siscon::input('refer', '')));
        if(!siscon::input('refer'))
        {
            $this->_data['refer'] = ($_SERVER['HTTP_REFERER'] && !strstr($_SERVER['HTTP_REFERER'], 'reg')) ? $_SERVER['HTTP_REFERER'] : $this->_data['refer'];
        }
        $this->template('login');
	}
    public function login_post_ajax()
    {
        $this->login_get_ajax();
    }

    public function login_get_ajax()
    {
        $username = siscon::input('username');
        $password = md5(siscon::input('password'));
        $remember = siscon::input('remember');
        $where['password'] = $password;
        //$where['status'] = 1;
        $info = siscon::model('user')->one($username, 'email', $where);
        if($info)
        {
			if($info['status'] == 2)
			{
				echo -2;die;
			}
			/*
			if($info['status'] == 3)
			{
				# 临时登录，方便激活帐号
				$info['refer'] = $this->_data['refer'];
				siscon::core('security',false);
				$code = microtime();
				$info['code'] = md5(Security::encrypt($code));
				$this->_data['save']->add('user_valid', $info);
				echo -3;die;
			}
			*/
            $this->_data['save']->add('user', $info);

			if($info['level'] == 100)
			{
				$admin = siscon::model('admin', 'admin')->one($info['id'], 'uid');
				if($admin)
				{
					$role = siscon::model('role', 'admin')->one($admin['role_id']);
					
					if($role['menu'])
					{
						$admin['menu'] = unserialize(base64_decode($role['menu']));
					}
					$admin['role_config'] = $role;
					$this->_data['save']->add('admin', $admin);
					//$info['admin'] = $admin;
					
				}
			}
            
            if($remember)
            {
				siscon::core('save')->init(false, 'cookie')->add('user', $info);
			}
			else
			{
				siscon::core('save')->init(false, 'cookie')->un('user');
			}
            echo 1;die;
        }
        else
        {
            echo -1;die;
        }
    }
    public function status_get_ajax()
    {
        $this->_data['user'] = false;
        if($user = $this->_data['save']->get('user'))
        {
            $this->_data['user'] = $user;   
        }
        $this->template('status');
    }
    /**
     * 验证一个字段是否存在
     */
	public function check_post_ajax($key = false, $value = false, $type = 1, $error = '')
	{
        $key = siscon::input('fieldId', $key);
        $value = siscon::input('fieldValue', $value);
        if($key == 'code')
        {
            $one = 'false';
            if($_SESSION['randcode'] == $value)
            {
				$one = 'true';
			}
            $this->_out($type, $one, $key, $error);
        }
        else
        {
            switch($key)
            {
                case 'email':
                    $reg = '/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/';
                    break;
                case 'username':
                    $reg = '/[0-9a-zA-Z\x{4e00}-\x{9fa5}]{1,10}/u';
                    break;
                case 'mobile':
                    $reg = '^([\+][0-9]{1,3}[ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9 \.\-\/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$';
                    break;
            }
            if($reg)
            {
                preg_match($reg, $value, $state);
                if(!$state)
                {
                    $this->_out($type, 'false', $key, $error);
                }
                if($type == 3)
                {
                    return;
                }
            }
            $where = array();
            if($user = $this->_data['save']->get('user'))
            {
                $where['id!'] = $user['id'];
            }
            $one = siscon::model('user')->one($value, $key, $where);
        }
        if($one)
        {
            $this->_out($type, 'false', $key, $error);
        }
        else
        {
            $this->_out($type, 'true', $key, $error);
        }
	}
    private function _out($type, $state = 'false', $key = false, $error = '')
    {
        if($type == 1)
        {
            echo '["'.$key.'", '.$state.']';die;
        }
        elseif($state == 'false')
        {
            siscon::error($error);
        }
    }
    /**
     * 验证码
     */
	public function code_get()
	{
        $an = siscon::core('code');
        
        /**使用验证码类的方法： 
        * $an = new Authnum(验证码长度,图片宽度,图片高度); 
        * 实例化时不带参数则默认是四位的60*25尺寸的常规验证码图片 
        * 表单页面检测验证码的方法，对比 $_SESSION[an] 是否等于 $_POST[验证码文本框ID] 
        * 可选配置： 
        * 1.验证码类型：$an->ext_num_type=1; 值为1是小写类型，2是大写类型，3是数字类型 
        * 2.干扰点：$an->ext_pixel = false; 值为false表示不添加干扰点 
        * 3.干扰线：$an->ext_line = false; 值为false表示不添加干扰线 
        * 4.Y轴随机：$an->ext_rand_y = false; 值为false表示不支持图片Y轴随机 
        * 5.图片背景：改变 $red $green $blue 三个成员变量的值即可 
        **/ 
        $an->ext_num_type=''; 
        $an->ext_pixel = true; //干扰点 
        $an->ext_line = false; //干扰线 
        $an->ext_rand_y= true; //Y轴随机 
        $an->green = 238; 
        $an->create();  
	}
}
