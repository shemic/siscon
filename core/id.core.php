<?php
/**
 *@filename id.class.php
 *@desc 生成唯一id
 *
 *@author leo
 *@date 2012-08-27
 */

class Id
{
    /**
     * @desc 设置IPC地址
     * @var string
     */
    private $_address = 1234;
    
    /**
     * @desc 设置序列号 key
     * @var string
     */
    private $_key = 4;
    
    /**
     * @desc 设置自增id的最大数值
     * @var string
     */
    private $_max = 1000000000;
    
    /**
     * @desc 设置自增id的最小数值
     * @var string
     */
    private $_min = 1000000;
    
    /**
     * @desc get
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    public function get($index)
    {
		# 创建或获得一个信号量
		$sem = sem_get($this->_address);
		
		# 创建或关联一个共享内存
		$shm = shm_attach($this->_address, 1024);
		# 加锁，占有信号量
		sem_acquire($sem);
		# 从共享内存中读取序列号
		$data = @shm_get_var($shm, $this->_key);
		
		if(empty($data[$index]))
		{
			if(!$data['num'] || (isset($data['num']) && $data['num'] >= $this->_max))
			{
				$data['num'] = $this->_min;
			}
			else
			{
				$data['num']++;
			}
			
			$data[$index] = $data['num'];
			
			# 写入序列号
			shm_put_var($shm, $this->_key, $data);
		}
		# 解锁
		sem_release($sem);
		# 关闭共享内存
		shm_detach($shm);
		
		return $data[$index];
	}
	
	/**
     * @desc key
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    public function key($key)
    {
		$this->_key = $key;
		
		return $this;
	}
	
	/**
     * @desc address
     * @author leo(suwi.bin)
     * @date 2012-08-27
     */
    public function address($address)
    {
		$this->_address = $address;
		
		return $this;
	}
}
