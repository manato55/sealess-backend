<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsToEmailVerifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_verifications', function (Blueprint $table) {
            $table->integer('department_id');
            $table->integer('section_id');
            $table->integer('job_title_id');
            $table->dropColumn('department');
            $table->dropColumn('section');
            $table->dropColumn('job_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_verifications', function (Blueprint $table) {
            $table->string('department');
            $table->string('section');
            $table->string('job_title');
            $table->dropColumn('department_id');
            $table->dropColumn('section_id');
            $table->dropColumn('job_title_idroute');
        });
    }
}
