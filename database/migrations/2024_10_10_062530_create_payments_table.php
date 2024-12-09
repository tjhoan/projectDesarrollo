<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('full_name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('phone');
            $table->text('additional_info')->nullable();
            $table->enum('payment_method', ['nequi', 'daviplata', 'bancolombia']);
            $table->boolean('pdf_invoice')->default(false);
            $table->boolean('email_invoice')->default(false);
            $table->string('confirmation_code')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
