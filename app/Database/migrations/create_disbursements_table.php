<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('disbursements', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->date('disbursementdate');
            $table->integer('disbursable_id');
            $table->string('disbursable_type');
            $table->string('details');
            $table->decimal('disbursement', 5,2)->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('disbursements');
    }
};
