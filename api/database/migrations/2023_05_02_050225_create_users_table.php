<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
			$table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name'); 
            $table->string('email')->unique();  
            $table->string('password')->nullable(); 
            $table->integer('created_by')->nullable();           
            $table->integer('updated_by')->nullable();          
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
