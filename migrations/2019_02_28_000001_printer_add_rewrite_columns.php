<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class PrinterAddRewriteColumns extends Migration
{
    private $tableName = 'printer';

    public function up()
    {
        $capsule = new Capsule();
        
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->boolean('fax_support')->nullable();
            $table->boolean('scanner')->nullable();
            $table->boolean('shared')->nullable();
            $table->boolean('accepting')->nullable();
            $table->integer('est_job_count')->nullable();
            $table->string('creation_date')->nullable();
            $table->bigInteger('state_time')->nullable();
            $table->bigInteger('config_time')->nullable();
            $table->text('cups_filters')->nullable();
            $table->string('cupsversion')->nullable();
            $table->string('ppdfileversion')->nullable();
            $table->string('printer_utility')->nullable();
            $table->string('printer_utility_version')->nullable();
            $table->string('printercommands')->nullable();
            $table->string('queue_name')->nullable();
            $table->string('model_make')->nullable();
            $table->string('auth_info_required')->nullable();
            $table->string('location')->nullable();
            $table->text('state_reasons')->nullable();
        });

        // Create indexes
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->index('fax_support');
            $table->index('scanner');
            $table->index('shared');
            $table->index('accepting');
            $table->index('est_job_count');
            $table->index('queue_name');
            $table->index('model_make');
            $table->index('auth_info_required');
            $table->index('location');
        });
        
        // Change existing columns
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->string('ppd')->nullable()->change();
            $table->string('driver_version')->nullable()->change();
            $table->string('url')->nullable()->change();
            $table->string('default_set')->nullable()->change();
            $table->string('printer_status')->nullable()->change();
            $table->string('printer_sharing')->nullable()->change();
        });
    }

    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('fax_support');
            $table->dropColumn('scanner');
            $table->dropColumn('shared');
            $table->dropColumn('accepting');
            $table->dropColumn('est_job_count');
            $table->dropColumn('creation_date');
            $table->dropColumn('state_time');
            $table->dropColumn('config_time');
            $table->dropColumn('cups_filters');
            $table->dropColumn('cupsversion');
            $table->dropColumn('ppdfileversion');
            $table->dropColumn('printer_utility');
            $table->dropColumn('printer_utility_version');
            $table->dropColumn('printercommands');
            $table->dropColumn('queue_name');
            $table->dropColumn('model_make');
            $table->dropColumn('auth_info_required');
            $table->dropColumn('location');
            $table->dropColumn('state_reasons');
        });
    }
}
