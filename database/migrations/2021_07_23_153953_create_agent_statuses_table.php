<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('draft_id')->unsigned();
            $table->foreign('draft_id')->references('id')->on('drafts')->onDelete('cascade');
            $table->integer('original_user');
            $table->integer('agent_user');
            $table->string('route');
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
        Schema::dropIfExists('agent_statuses');
    }
}
