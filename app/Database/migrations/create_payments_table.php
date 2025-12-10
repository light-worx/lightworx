<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->date('paymentdate');
            $table->integer('client_id');
            $table->decimal('amount', 6,2)->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
