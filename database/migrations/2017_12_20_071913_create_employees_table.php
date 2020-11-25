<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('employee_id');
            $table->integer('office_id')->unsigned()->nullable();
            $table->integer('division_id')->unsigned()->nullable();
            $table->string('first_name', 40);
            $table->string('middle_name', 30)->nullable();
            $table->string('last_name', 30);
            $table->string('suffix_name', 4)->nullable();
            $table->timestamps();

            $table->foreign('office_id')
                                    ->references('office_id')
                                    ->on('offices')
                                    ->onUpdate('cascade')
                                    ->onDelete('cascade');

            $table->foreign('division_id')
                                    ->references('division_id')
                                    ->on('divisions')
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
        Schema::dropIfExists('employees');
    }
}
