<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Action class
*/
class Action {
	private $id;
	private $route;
	private $method = 'index';
	
	/**
	 * Constructor
	 *
	 * @param	string	$route
 	*/
	public function __construct($route) {
		$this->id = $route;
		
		$parts = explode('/', preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route));

		// Break apart the route
		while ($parts) { // 将route解析为controller文件名和方法名
			$file = DIR_APPLICATION . 'controller/' . implode('/', $parts) . '.php';

			if (is_file($file)) {
				$this->route = implode('/', $parts);		
				
				break;
			} else {
				$this->method = array_pop($parts);
			}
		}
	}

	/**
	 * 
	 *
	 * @return	string
	 *
 	*/	
	public function getId() {
		return $this->id;
	}
	
	/**
	 * 
	 *
	 * @param	object	$registry
	 * @param	array	$args
 	*/	
	public function execute($registry, array $args = array()) { // 执行操作
		// Stop any magical methods being called
		if (substr($this->method, 0, 2) == '__') {
			return new \Exception('Error: Calls to magic methods are not allowed!'); // 不允许调用Magic方法
		}

		$file  = DIR_APPLICATION . 'controller/' . $this->route . '.php'; // 拼接文件名
		$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $this->route); // 拼接Controller类名
		
		// Initialize the class
		if (is_file($file)) { // 确认是否有对应的Controller类文件
			include_once($file);
		
			$controller = new $class($registry);
		} else {
			return new \Exception('Error: Could not call ' . $this->route . '/' . $this->method . '!');
		}
		
		$reflection = new ReflectionClass($class); // 利用反射调用Controller方法
		
		if ($reflection->hasMethod($this->method) && $reflection->getMethod($this->method)->getNumberOfRequiredParameters() <= count($args)) {
			return call_user_func_array(array($controller, $this->method), $args);
		} else {
			return new \Exception('Error: Could not call ' . $this->route . '/' . $this->method . '!');
		}
	}
}
