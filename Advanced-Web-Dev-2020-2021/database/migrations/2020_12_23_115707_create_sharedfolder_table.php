<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharedfolderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sharedfolder', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained('folder')->onDelete('cascade');
            $table->foreignId('user_owner_id')->nullable()->constrained('user')->onDelete('cascade');
            $table->foreignId('admin_owner_id')->nullable()->constrained('admin')->onDelete('cascade');
            $table->foreignId('user_requester_id')->nullable()->constrained('user')->onDelete('cascade');
            $table->foreignId('admin_requester_id')->nullable()->constrained('admin')->onDelete('cascade');
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
        Schema::dropIfExists('sharedfolder');
    }
}
