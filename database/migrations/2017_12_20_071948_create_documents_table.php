<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('document_id');
            $table->integer('employee_id')->unsigned();
            $table->string('name');
            $table->string('custom_folder_path')->nullable();
            $table->string('keywords');
            $table->timestamps();

            $table->foreign('employee_id')
                                    ->references('employee_id')
                                    ->on('employees')
                                    ->onUpdate('cascade')
                                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
