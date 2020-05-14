<?php

use CFPropertyList\CFPropertyList;

class Printer_model extends \Model
{
    public function __construct($serial = '')
    {
        parent::__construct('id', 'printer'); //primary key, tablename
        $this->rs['id'] = '';
        $this->rs['serial_number'] = $serial;
        $this->rs['name'] = '';
        $this->rs['ppd'] = '';
        $this->rs['driver_version'] = '';
        $this->rs['url'] = '';
        $this->rs['default_set'] = '';
        $this->rs['printer_status'] = '';
        $this->rs['printer_sharing'] = '';
        $this->rs['fax_support'] = ''; // True/False
        $this->rs['scanner'] = ''; // True/False
        $this->rs['shared'] = ''; // True/False
        $this->rs['accepting'] = ''; // True/False
        $this->rs['est_job_count'] = '';
        $this->rs['creation_date'] = '';
        $this->rs['state_time'] = '';
        $this->rs['config_time'] = '';
        $this->rs['cups_filters'] = '';
        $this->rs['cupsversion'] = '';
        $this->rs['ppdfileversion'] = '';
        $this->rs['printer_utility'] = '';
        $this->rs['printer_utility_version'] = '';
        $this->rs['printercommands'] = '';
        $this->rs['queue_name'] = '';
        $this->rs['model_make'] = '';
        $this->rs['auth_info_required'] = '';
        $this->rs['location'] = '';
        $this->rs['state_reasons'] = '';

        if ($serial) {
            $this->retrieve_record($serial);
        }
        
        $this->serial = $serial;
    }
    
    
    // ------------------------------------------------------------------------

    /**
     * Get printer names for widget
     *
     **/
    public function get_printers()
    {
        $out = array();
        $sql = "SELECT COUNT(1) AS count, name 
				    FROM printer
				    LEFT JOIN reportdata USING (serial_number)
                    ".get_machine_group_filter()."
                    GROUP BY name
                    ORDER BY count DESC";
        
        foreach ($this->query($sql) as $obj) {
            if ("$obj->count" !== "0") {
                $obj->name = $obj->name ? $obj->name : 'Unknown';
                $out[] = $obj;
            }
        }
        
        return $out;
    }
    
    /**
     * Process data sent by postflight
     *
     * @param string data
     *
     **/
    public function process($data)
    {
        // If data is empty, throw error
        if (! $data) {
            print_r("Error Processing Printer Module Request: No data found");
        } else if (substr( $data, 0, 30 ) != '<?xml version="1.0" encoding="' ) { // Else if old style text, process with old text based handler
        
            // Delete previous entries
            $this->deleteWhere('serial_number=?', $this->serial_number);

            // Translate printer strings to db fields
            $translate = array(
                'Name: ' => 'name',
                'PPD: ' => 'ppd',
                'Driver Version: ' => 'driver_version',
                'URL: ' => 'url',
                'Default Set: ' => 'default_set',
                'Printer Status: ' => 'printer_status',
                'Printer Sharing: ' => 'printer_sharing');

            // Clear any previous data we had
            foreach ($translate as $search => $field) {
                $this->$field = '';
            }
            
            // Parse data
            foreach (explode("\n", $data) as $line) {
                // Translate standard entries
                foreach ($translate as $search => $field) {
                    if (strpos($line, $search) === 0) {
                        $value = substr($line, strlen($search));

                        $this->$field = $value;

                        // Check if this is the last field
                        if ($field == 'printer_sharing') {
                            $this->id = '';
                            $this->save();
                        }
                        break;
                    }
                }
            } // End foreach explode lines
            
        } else { // Else process with new XML handler
                    
            // Delete previous entries
            $this->deleteWhere('serial_number=?', $this->serial_number);
            
            // Process incoming printer.plist
            $parser = new CFPropertyList();
            $parser->parse($data, CFPropertyList::FORMAT_XML);
            $plist = $parser->toArray();

            // Process each printer
            foreach ($plist as $printer) {
            
                // Process each of the items
                foreach (array('name','ppd','driver_version','url','default_set','printer_status','printer_sharing','fax_support','scanner','shared','accepting','est_job_count','creation_date','state_time','config_time','cups_filters','cupsversion','ppdfileversion','printer_utility','printer_status','printer_utility_version','printercommands','queue_name','model_make','auth_info_required','location','state_reasons') as $item) {
                    // If key exists and is zero, set it to zero
                    if ( array_key_exists($item, $printer) && $printer[$item] === 0 && $item != "cups_filters") {
                        $this->$item = 0;
                    // Else if key does not exist in $plist, null it
                    } else if (! array_key_exists($item, $printer) || $printer[$item] == '' || $printer[$item] == "{}") {
                        $this->$item = null;

                    // Else if cups_filters, turn it into a JSON string
                    } else if (array_key_exists($item, $printer) && $item == 'cups_filters'){
                        $this->$item = json_encode($printer[$item]);

                    // Set the db fields to be the same as those in the preference file
                    } else {
                        $this->$item = $printer[$item];
                    }                    
                }
                                
                // Save the data as one would save a print job
                $this->id = '';
                $this->save(); 
            }
        }
    }
}
