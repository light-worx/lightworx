<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hours', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->date('hourdate');
            $table->integer('hourable_id');
            $table->string('hourable_type');
            $table->string('details');
            $table->decimal('hours', 5,2)->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hours');
    }
};
