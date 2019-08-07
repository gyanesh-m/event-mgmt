<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    protected $timestamps  = false;
    public function up()
    {
        Schema::create('event_entry', function (Blueprint $table) {

//            "
//host_id:bigIncrements
//event_id:bigIncrements
//start_time:int(unsigned)
//end_time:int(unsigned)
//venue:string(256)
//topic:string(256)
//description:string(512)
//contact:string(10)
//"
            $table->bigIncrements('eid')->unsigned();
            $table->bigInteger('hostid')->unsigned();
            $table->unsignedInteger('start_time');
            $table->unsignedInteger('end_time');
            $table->string('venue',256);
            $table->string('topic',256);
            $table->string('description',512);
            $table->string('isd',4);
            $table->string('contact',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_invites');
    }
}
