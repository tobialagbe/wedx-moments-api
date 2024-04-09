<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Assuming a Payment belongs to a User
            $table->unsignedBigInteger('event_id'); // Assuming a Payment is related to an Event
            $table->decimal('amount', 8, 2); // Assuming standard currency format, adjust as needed
            $table->string('status'); // E.g., pending, completed, failed
            $table->string('transaction_id')->nullable(); // For storing the payment gateway transaction ID
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
