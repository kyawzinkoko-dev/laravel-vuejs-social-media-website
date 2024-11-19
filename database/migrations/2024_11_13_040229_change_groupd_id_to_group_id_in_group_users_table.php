<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('group_users', function (Blueprint $table) {
            $table->dropForeign(['groupd_id']);
            $table->renameColumn('groupd_id','group_id');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_users', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->renameColumn('group_id','groupd_id');
            $table->foreign('groupd_id')->references('id')->on('groups')->onDelete('cascade');
     
        });
    }
};
