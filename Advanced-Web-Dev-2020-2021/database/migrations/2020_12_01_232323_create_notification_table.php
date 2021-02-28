<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('problem_id')->nullable()->constrained('problem')->onDelete('cascade');
            $table->foreignId('file_id')->nullable()->constrained('file')->onDelete('cascade');
            $table->foreignId('message_id')->nullable()->constrained('message')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('user')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('admin')->onDelete('cascade');

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
        Schema::dropIfExists('notification');
    }
}
