<?php 

/**
 * Printer module class
 *
 * @package munkireport
 * @author 
 **/
class Printer_controller extends Module_controller
{
	
	/*** Protect methods with auth! ****/
	function __construct()
	{
		// Store module path
		$this->module_path = dirname(__FILE__);
	}

	/**
	 * Default method
	 *
	 * @author AvB
	 **/
	function index()
	{
		echo "You've loaded the printer module!";
	}

	
} // END class default_module