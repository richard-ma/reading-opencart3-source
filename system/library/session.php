<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Session class
*/
class Session {
	protected $adaptor;
	protected $session_id;
	public $data = array();

	/**
	 * Constructor
	 *
	 * @param	string	$adaptor
	 * @param	object	$registry
 	*/
	public function __construct($adaptor, $registry = '') {
		$class = 'Session\\' . $adaptor; // session可以存在数据库或是文件中
		
		if (class_exists($class)) { // 是否需要传入registry作为参数，根据class构造函数决定
			if ($registry) {
				$this->adaptor = new $class($registry);
			} else {
				$this->adaptor = new $class();
			}	
			
			register_shutdown_function(array($this, 'close')); // 将Sesion->close注册为脚本完成时的函数
		} else {
			trigger_error('Error: Could not load cache adaptor ' . $adaptor . ' session!');
			exit();
		}	
	}
	
	/**
	 * 
	 *
	 * @return	string
 	*/	
	public function getId() {
		return $this->session_id;
	}

	/**
	 *
	 *
	 * @param	string	$session_id
	 *
	 * @return	string
 	*/	
	public function start($session_id = '') {
		if (!$session_id) { // 创建sessionid
			if (function_exists('random_bytes')) {
				$session_id = substr(bin2hex(random_bytes(26)), 0, 26);
			} else {
				$session_id = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
			}
		}

		if (preg_match('/^[a-zA-Z0-9,\-]{22,52}$/', $session_id)) { 
			$this->session_id = $session_id;
		} else {
			exit('Error: Invalid session ID!');
		}
		
		$this->data = $this->adaptor->read($session_id); // 读取session数据
		
		return $session_id;
	}
	
	/**
	 * 
 	*/
	public function close() {
		$this->adaptor->write($this->session_id, $this->data); // 保存sessoin数据
	}
	
	/**
	 * 
 	*/	
	public function __destroy() {
		$this->adaptor->destroy($this->session_id); // 销毁sessoin
	}
}
