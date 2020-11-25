<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_logs', function (Blueprint $table) {
            $table->increments('document_log_id');
            $table->integer('document_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('action');
            $table->timestamps();

            $table->foreign('document_id')
                                    ->references('document_id')
                                    ->on('documents')
                                    ->onUpdate('cascade')
                                    ->onDelete('cascade');

            $table->foreign('user_id')
                                    ->references('user_id')
                                    ->on('users')
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
        Schema::dropIfExists('document_logs');
    }
}
