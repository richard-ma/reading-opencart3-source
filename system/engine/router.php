<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Router class
*/
final class Router {
	private $registry;
	private $pre_action = array();
	private $error;
	
	/**
	 * Constructor
	 *
	 * @param	object	$route
 	*/
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	/**
	 * 
	 *
	 * @param	object	$pre_action
 	*/	
	public function addPreAction(Action $pre_action) {
		$this->pre_action[] = $pre_action;
	}

	/**
	 * 
	 *
	 * @param	object	$action
	 * @param	object	$error
 	*/		
	public function dispatch(Action $action, Action $error) { // 分发，第一个参数是正确的Action，第二个是如果发生错误执行的Action
		$this->error = $error;

		foreach ($this->pre_action as $pre_action) { // 执行Pre Action
			$result = $this->execute($pre_action);

			if ($result instanceof Action) {
				$action = $result;

				break;
			}
		}

		while ($action instanceof Action) {
			$action = $this->execute($action); // execute可以返回Action对象，形成Action链
		}
	}
	
	/**
	 * 
	 *
	 * @param	object	$action
	 * @return	object
 	*/
	private function execute(Action $action) {
		$result = $action->execute($this->registry); // 如果结果不是Action对象，Action链的循环就停止了

		if ($result instanceof Action) {
			return $result;
		} 
		
		if ($result instanceof Exception) {
			$action = $this->error;
			
			$this->error = null;
			
			return $action;
		}
	}
}
