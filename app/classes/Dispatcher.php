<?php

class Dispatcher {

	private $_router;

	public function __construct(Router $router)
	{
		$this->_router = $router;
	}

	public function dispatch()
	{
		$controller_name = 'Controller_'.$this->_router->getControllerName();
		$action_name     = $this->_router->getActionName().'Action';

		// Check if class exists
		if ( ! @class_exists($controller_name))
			throw new Exception('Controller '.$controller_name.' not found');

		$controller = new $controller_name;
		$controller->setDispatcher($this);
		$controller->setRouter($this->_router);

		// Check if the action exists
		if ( ! method_exists($controller, $action_name))
			throw new Exception('Action '.$action_name.' not found on Controller '.$controller_name);

		ob_start();
		// Call the before function
		$controller->before();

		// Call the action on the controller
		call_user_func_array(array($controller, $action_name), array());

		// Call the after function
		$controller->after();

		// Get the output
		$result = ob_get_clean();

		return $result;
	}

}