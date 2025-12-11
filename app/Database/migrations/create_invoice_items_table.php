<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_items', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->date('itemdate');
            $table->integer('invoice_id');
            $table->string('details');
            $table->decimal('quantity', 5,2);
            $table->decimal('unit_price', 6,2)->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
};
