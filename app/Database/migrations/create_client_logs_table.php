<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('client_logs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->date('logdate');
            $table->integer('client_id');
            $table->string('details');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('client_logs');
    }
};
