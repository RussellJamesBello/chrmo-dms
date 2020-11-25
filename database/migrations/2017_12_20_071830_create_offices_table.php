 <?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->increments('office_id');
            $table->string('name')->unique();
            $table->string('name_acronym', 10)->unique();
            $table->boolean('linkable_to_employee')
                    ->default(true)
                    ->comment('This column is used to identify if a record in offices is linkable to an employee.
                                One example is the CMO itself is not linkable for reasons in the business logic
                                while CHO and LCGH is linkable despite the fact that it also has its division(s).
                                The best way to connect an employee working in the CMO is to connect to the 
                                division linked to CMO where the employee is designated to work.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offices');
    }
}
