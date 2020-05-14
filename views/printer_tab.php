
<div id="printer-tab"></div>
<h2 data-i18n="printer.tab_title"></h2>

<div id="printer-msg" data-i18n="listing.loading" class="col-lg-12 text-center"></div>

<script>
$(document).on('appReady', function(){
    $.getJSON(appUrl + '/module/printer/get_data/' + serialNumber, function(data){
        
        if( ! data || ! data[0] ){
            $('#printer-msg').text(i18n.t('printer.no_info_found'));
            $('#printer-cnt').text("");
        } else if (data[0]['printer_status'] == 'no_info_found') {
			$('#printer-msg').text(i18n.t('printer.no_info_found'));
            // Update the tab badge
            $('#printer-cnt').text('0');
        } else {
            // Set count of printers
            $('#printer-cnt').text(data.length);
            
            // Hide
            $('#printer-msg').text('');
            $('#printer-count-view').removeClass('hide');

            var skipThese = ['id', 'serial_number', 'name'];
            $.each(data, function(i,d){

                // Generate rows from data
                var rows = ''
                var cups_filter_rows = ''
                for (var prop in d){
                    // Skip skipThese
                    if(skipThese.indexOf(prop) == -1){
                        if (d[prop] == '' || d[prop] == null || d[prop] == "{}"){
                        // Do nothing for empty values to blank them

                        } else if((prop == 'printer_sharing' || prop == 'default_set' || prop == 'fax_support' || prop == 'scanner' || prop == 'shared' || prop == 'accepting') && (d[prop] == "yes" || d[prop] == 1)){
                           rows = rows + '<tr><th>'+i18n.t('printer.'+prop)+'</th><td>'+i18n.t('yes')+'</td></tr>';
                        }
                        else if((prop == 'printer_sharing' || prop == 'default_set' || prop == 'fax_support' || prop == 'scanner' || prop == 'shared' || prop == 'accepting') && (d[prop] == "no" || d[prop] == 0)){
                           rows = rows + '<tr><th>'+i18n.t('printer.'+prop)+'</th><td>'+i18n.t('no')+'</td></tr>';
                        }
                        else if(prop == 'printer_status' && d[prop] == "idle"){
                           rows = rows + '<tr><th>'+i18n.t('printer.'+prop)+'</th><td>'+i18n.t('printer.idle')+'</td></tr>';
                        }
                        else if(prop == 'printer_status' && d[prop] == "offline"){
                           rows = rows + '<tr><th>'+i18n.t('printer.'+prop)+'</th><td>'+i18n.t('printer.offline')+'</td></tr>';
                        }
                        else if(prop == 'printer_status' && d[prop] == "in use"){
                           rows = rows + '<tr><th>'+i18n.t('printer.'+prop)+'</th><td>'+i18n.t('printer.in_use')+'</td></tr>';
                        }
                        else if(prop == 'printer_status' && d[prop] == "error"){
                           rows = rows + '<tr><th>'+i18n.t('printer.'+prop)+'</th><td>'+i18n.t('printer.error')+'</td></tr>';
                        }

                        else if(prop == "config_time" || prop == "state_time"){
                           var date = new Date(d[prop] * 1000);
                           rows = rows + '<tr><th>'+i18n.t('printer.'+prop)+'</th><td><span title="'+moment(date).fromNow()+'">'+moment(date).format('llll')+'</span></td></tr>';
                        }
                        
                        // Else if build out the cups_filters table
                        else if(prop == "cups_filters"){
                            var cups_filters_data = JSON.parse(d['cups_filters']);
                            // Only build table if it contains a filter name
                            if (d['cups_filters'].includes("_name")){
                                cups_filter_rows = '<tr><th style="max-width: 140px;">'+i18n.t('printer.filter_name')+'</th><th style="max-width: 85px;">'+i18n.t('printer.filter_permissions')+'</th><th style="max-width: 60px;">'+i18n.t('printer.filter_version')+'</th><th style="max-width: 300px;">'+i18n.t('printer.filter_path')+'</th></tr>'
                                $.each(cups_filters_data.reverse(), function(i,d){
                                    // Only add row if it has a filter name
                                    if(d.hasOwnProperty('_name')){
                                        if (typeof d['_name'] !== "undefined") {var printer_name = d['_name']} else {var printer_name = ""}
                                        if (typeof d['filter permissions'] !== "undefined") {var filter_permissions = d['filter permissions']} else {var filter_permissions = ""}
                                        if (typeof d['filter version'] !== "undefined") {var filter_version = d['filter version']} else {var filter_version = ""}
                                        if (typeof d['filter path'] !== "undefined") {var filter_path = d['filter path']} else {var filter_path = ""}
                                        // Generate rows from data
                                        cups_filter_rows = cups_filter_rows + '<tr><td>'+printer_name+'</td><td>'+filter_permissions+'</td><td>'+filter_version+'</td><td>'+filter_path+'</td></tr>';
                                    }
                                })
                                cups_filter_rows = cups_filter_rows // Close cups_filters table framework
                            }
                        }

                        else {
                            rows = rows + '<tr><th style="width: 165px;">'+i18n.t('printer.'+prop)+'</th><td style="max-width: 500px;">'+d[prop]+'</td></tr>';
                        }
                    }
                }
                
                $('#printer-tab')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-print'))
                        .append(' '+d.name))
                    .append($('<div>')
                        .append($('<table style="width: 750px;">')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(rows))))
                
                // Only draw the cups filter table if there is something in it
                if (cups_filter_rows !== ""){
                    $('#printer-tab')
                        .append($('<h4>')
                            .append(" "+i18n.t('printer.cups_filters')))
                        .append($('<div>')
                            .append($('<table style="width: 985px;">')
                                .addClass('table table-striped table-condensed')
                                .append($('<tbody>')
                                    .append(cups_filter_rows))))
                        .append($('<br><br>'))
                } else {
                    $('#printer-tab')
                        .append($('<br>'))
                }
            })
        }
    });
});
</script>
