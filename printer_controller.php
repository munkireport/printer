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
        $sql = "SELECT COUNT(1) AS count, name 
				    FROM printer
				    LEFT JOIN reportdata USING (serial_number)
                    ".get_machine_group_filter()."
                    GROUP BY name
                    ORDER BY count DESC";

        $out = array();
        $queryobj = new Printer_model;
        foreach ($queryobj->query($sql) as $obj) {
            if ("$obj->count" !== "0") {
                $obj->name = $obj->name ? $obj->name : 'Unknown';
                $out[] = $obj;
            }
        }

        jsonView($out);
    } 
    
    /**
     * Get printers for serial_number for client tab
     *
     * @param string $serial_number
     **/
    public function get_data($serial_number = '')
    {         
        // Remove non-serial number characters
        $serial_number = preg_replace("/[^A-Za-z0-9_\-]]/", '', $serial_number);

        $sql = "SELECT name, model_make, queue_name, location, ppd, url, printer_status, default_set, printer_sharing, fax_support, scanner, shared, accepting, est_job_count, state_time, creation_date, config_time, driver_version, ppdfileversion, printer_utility, printer_utility_version, printercommands, auth_info_required, state_reasons, cupsversion, cups_filters
                        FROM printer 
                        WHERE serial_number = '$serial_number'";
        
        $queryobj = new Printer_model;
        jsonView($queryobj->query($sql));        
    }
} // END class Printer_controller
