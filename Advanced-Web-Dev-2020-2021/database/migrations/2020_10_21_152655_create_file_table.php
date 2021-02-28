<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_owner_id')->nullable()->constrained('user')->onDelete('cascade');
            $table->foreignId('admin_owner_id')->nullable()->constrained('admin')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->unsignedBigInteger('version_group_id')->nullable();
            $table->boolean('is_current_version')->nullable();
            $table->string('filename');
            $table->string('file');
            $table->string('filetype');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file');
    }
}
