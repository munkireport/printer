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
    public function __construct()
    {
        // Store module path
        $this->module_path = dirname(__FILE__);
    }

    /**
     * Default method
     *
     * @author AvB
     **/
    public function index()
    {
        echo "You've loaded the printer module!";
    }

    /**
     * Get printer information for printer widget
     *
     * @return void
     * @author tuxudo
     **/
    public function get_printers()
    {
        $obj = new View();

        if (! $this->authorized()) {
            $obj->view('json', array('msg' => array('error' => 'Not authenticated')));
            return;
        }
        
        $printers = new Printer_model;
        $obj->view('json', array('msg' => $printers->get_printers()));
    } 
    
    /**
     * Get printers for serial_number for client tab
     *
     * @param string $serial serial number
     **/
    public function get_data($serial = '')
    { 
        $obj = new View();

        if (! $this->authorized()) {
            $obj->view('json', array('msg' => 'Not authorized'));
            return;
        }
        
        $queryobj = new Printer_model();
        
        $sql = "SELECT name, model_make, queue_name, location, ppd, url, printer_status, default_set, printer_sharing, fax_support, scanner, shared, accepting, est_job_count, state_time, creation_date, config_time, driver_version, ppdfileversion, printer_utility, printer_utility_version, printercommands, auth_info_required, state_reasons, cupsversion, cups_filters
                        FROM printer 
                        WHERE serial_number = '$serial'";
        
        $printer_tab = $queryobj->query($sql);
        $obj->view('json', array('msg' => current(array('msg' => $printer_tab)))); 
        
    }
} // END class Printer_module
