<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharedfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sharedfile', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('file_id')->constrained('file')->onDelete('cascade');
            $table->foreignId('user_owner_id')->nullable()->constrained('user')->onDelete('cascade');
            $table->foreignId('admin_owner_id')->nullable()->constrained('admin')->onDelete('cascade');
            $table->foreignId('user_requester_id')->nullable()->constrained('user')->onDelete('cascade');
            $table->foreignId('admin_requester_id')->nullable()->constrained('admin')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sharedfile');
    }
}
