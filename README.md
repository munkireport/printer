Printer module
==============

Reports on installed printers and information about them.

Table Schema
---

* name - varchar(255) - Name of the printer
* ppd - varchar(255) - What PPD is in use
* driver_version - varchar(255) - Driver version
* url - varchar(255) - URI of the printer
* default_set - varchar(255) - If printer is set to be the system default
* printer_status - varchar(255) - Current status of the printer
* printer_sharing - varchar(255) - If printer sharing is turned on
* fax_support - boolean - If printer supports faxing
* scanner - boolean - If printer supports scanning
* shared - boolean - If printer is shared
* accepting - boolean - If printer is accepting jobs
* est_job_count - boolean - Estimated job count
* creation_date - varchar(255) - When printer was added to system
* state_time - big integer  - Timestamp of last state check
* config_time - big integer - Timestamp of last configuration change
* cups_filters - text - JSON object containing info about the CUPS filters
* cupsversion - varchar(255) - Version of CUPS installed
* ppdfileversion - varchar(255) - Version of PPD file
* printer_utility - varchar(255) - Path to printer's utility application
* printer_utility_version - varchar(255) - Version of printer's utility application
* printercommands - varchar(255) - Supported printer commands
* queue_name - varchar(255) - Name of print queue
* model_make - varchar(255) - Model and make of printer
* auth_info_required - varchar(255) - What kind of authentication is set for printer
* location - varchar(255) - Set location of printer
* state_reasons - text - Reason why printer is in current state


