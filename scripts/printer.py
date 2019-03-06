#!/usr/bin/python

import subprocess
import os
import plistlib
import sys
sys.path.insert(0, '/usr/local/munki')

from munkilib import FoundationPlist


def get_printer_info():
    '''Uses system profiler to get info about the printers machine.'''
    cmd = ['/usr/sbin/system_profiler', 'SPPrintersDataType', '-xml']
    proc = subprocess.Popen(cmd, shell=False, bufsize=-1,
                            stdin=subprocess.PIPE,
                            stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    (output, unused_error) = proc.communicate()
    try:
        plist = plistlib.readPlistFromString(output)
        # system_profiler xml is an array
        sp_dict = plist[0]
        items = sp_dict['_items']
    except Exception:
        return {}
    
    cups_prefs = get_cups_prefs()
    cups_conf = get_cups_conf()
    
    if os.path.isfile('/var/spool/cups/cache/job.cache'):
            job_cache = open('/var/spool/cups/cache/job.cache', "r")
            job_cache_data = ''.join(job_cache.readlines())
            job_cache.close()
    
    '''Un-nest printers, return array with objects with relevant keys'''
    out = []
    for obj in items:        
        device = {}
        for item in obj:
            if item == '_name':
                device['name'] = obj[item]
            elif item == 'Fax Support' or obj[item] == 'fax':
                device['fax_support'] = str_to_bool(obj[item])
            elif item == 'creationDate':
                device['creation_date'] = obj[item]
            elif item == 'cups filters':
                device['cups_filters'] = obj[item]
            elif item == 'cupsversion':
                device['cupsversion'] = obj[item]
            elif item == 'default':
                device['default_set'] = str_to_bool(obj[item])
            elif item == 'driverversion':
                device['driver_version'] = obj[item]
            elif item == 'ppd':
                device['ppd'] = obj[item]
            elif item == 'ppdfileversion':
                device['ppdfileversion'] = obj[item]
            elif item == 'printer utility':
                device['printer_utility'] = obj[item]
            elif item == 'printer utility version':
                device['printer_utility_version'] = obj[item]
            elif item == 'printercommands':
                device['printercommands'] = obj[item].strip().replace(" ", ", ")
            elif item == 'printersharing':
                device['printer_sharing'] = str_to_bool(obj[item])
            elif item == 'scanner':
                device['scanner'] = str_to_bool(obj[item])
            elif item == 'shared':
                device['shared'] = str_to_bool(obj[item]) 
            elif item == 'status':
                device['printer_status'] = obj[item]
            elif item == 'uri':
                device['url'] = obj[item]
        
        # Add in state and queue name
        for printer in cups_prefs:
            if 'printer_name' in printer and 'name' in device:
                if printer['printer_name'] == device['name']:
                    for item in printer:
                        if item == 'queue_name':
                            device['queue_name'] = printer[item]
                        elif item == 'state_reasons':
                            device['state_reasons'] = ', '.join(list(printer[item]))
                        elif item == 'model_make':
                            device['model_make'] = printer[item]

                    break
            break
 
        # Add in config options        
        for printer in cups_conf:
            if 'name' in printer and 'queue_name' in device:
                if printer['name'] == device['queue_name']:
                    for item in printer:
                        if item == 'auth_info_required':
                            device['auth_info_required'] = printer[item]
                        elif item == 'location':
                            device['location'] = printer[item]
                        elif item == 'state_time':
                            device['state_time'] = printer[item]
                        elif item == 'config_time':
                            device['config_time'] = printer[item]
                        elif item == 'accepting':
                            device['accepting'] = str_to_bool(printer[item])
                    break
            break
                
        if job_cache_data is not "" and 'queue_name' in device:
            device['est_job_count'] = job_cache_data.count(device['queue_name'])
                
        out.append(device)
    return out

def get_cups_prefs():
    
    try:
        plist = FoundationPlist.readPlist("/Library/Preferences/org.cups.printers.plist")
        out = []
        
        for printer in plist:
            device = {}
            for item in printer:
                if item == 'printer-name':
                    device['queue_name'] = printer[item]
                elif item == 'printer-state-reasons':
                    device['state_reasons'] = printer[item]
                elif item == 'printer-info':
                    device['printer_name'] = printer[item]
                elif item == 'printer-make-and-model':
                    device['model_make'] = printer[item]
            out.append(device)
        return out

    except Exception:
        return []      

def get_cups_conf():
    try:
        printer_conf = open("/private/etc/cups/printers.conf", "r")
        printer_conf_data = ''.join(printer_conf.readlines())
        printer_conf.close()
        
        out = []
        
        for obj in printer_conf_data.split('\n<Printer '):
            device = {}
            for item in obj.split('\n'):
                device['name'] = obj.split('\n')[0][:-1].strip()
                if item.startswith('AuthInfoRequired'):
                    device['auth_info_required'] = item.replace("AuthInfoRequired ", "").strip()
                elif item.startswith('Info'):
                    device['description'] = item.replace("Info ", "").strip()
                elif item.startswith('Location'):
                    device['location'] = item.replace("Location ", "").strip()
                elif item.startswith('StateTime'):
                    device['state_time'] = item.replace("StateTime ", "").strip()
                elif item.startswith('ConfigTime'):
                    device['config_time'] = item.replace("ConfigTime ", "").strip()
                elif item.startswith('Accepting'):
                    device['accepting'] = item.replace("Accepting ", "").strip()
                    
            out.append(device)
        return out

    except Exception:
        return []

def str_to_bool(s):
    if s == "yes" or s == "Yes" :
        return 1
    else:
        return 0
    
def to_bool(s):
    if s == "":
        return ""
    elif s == True:
        return 1
    else:
        return 0
    
def main():
    """Main"""
    # Create cache dir if it does not exist
    cachedir = '%s/cache' % os.path.dirname(os.path.realpath(__file__))
    if not os.path.exists(cachedir):
        os.makedirs(cachedir)

    # Skip manual check
    if len(sys.argv) > 1:
        if sys.argv[1] == 'manualcheck':
            print 'Manual check: skipping'
            exit(0)
            
    # Set the encoding
    reload(sys)  
    sys.setdefaultencoding('utf8')

    # Get results
    result = dict()
    result = get_printer_info()
    
    # Write printer results to cache
    output_plist = os.path.join(cachedir, 'printer.plist')
    plistlib.writePlist(result, output_plist)
    #print plistlib.writePlistToString(result)

if __name__ == "__main__":
    main()
