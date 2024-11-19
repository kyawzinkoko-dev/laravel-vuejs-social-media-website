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
        Schema::table('post_reactions',function(Blueprint $table){
            $table->dropForeign(['post_id']);
            $table->renameColumn('post_id','object_id');
        });
        Schema::table('post_reactions',function(Blueprint $table){
            $table->string('object_type')->after('object_id');
            $table->rename('reactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reactions',function(Blueprint $table){
            $table->rename('post_reactions');
    }); 
    Schema::table('post_reactions',function(Blueprint $table){
        $table->dropColumn('object_type');
        $table->renameColumn('object_id','post_id');
        $table->foreign('post_id')->references('id')->on('posts');

    }); 
    }
};
