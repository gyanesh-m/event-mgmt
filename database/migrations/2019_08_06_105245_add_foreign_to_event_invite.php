<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignToEventInvite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_invites', function (Blueprint $table) {
            //
            $table->foreign('attendee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('eid')->references('eid')->on('event_entry')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_invite', function (Blueprint $table) {
            //
        });
    }
}
