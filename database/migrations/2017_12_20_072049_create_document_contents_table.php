<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_contents', function (Blueprint $table) {
            $table->increments('document_content_id');
            $table->integer('document_id')->unsigned();
            $table->string('page_number', 3);
            $table->string('file_name', 8);
            $table->timestamps();

            $table->foreign('document_id')
                                    ->references('document_id')
                                    ->on('documents')
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
        Schema::dropIfExists('document_contents');
    }
}
