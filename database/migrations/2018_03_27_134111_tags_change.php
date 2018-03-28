<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TagsChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('material_tags');
        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('tag_content');
        });
        Schema::create('material_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('material_id');
            $table->bigInteger('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_tag');
        Schema::dropIfExists('tags');
        Schema::create('material_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('material_id');
            $table->text('tags');
            $table->boolean('status')->default(true);
        });
    }
}
