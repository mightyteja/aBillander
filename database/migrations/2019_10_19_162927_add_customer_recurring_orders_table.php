<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerRecurringOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_recurring_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_order_id')->unsigned();
            $table->integer('customer_id')->unsigned();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('next_occurring_at')->nullable();
            $table->integer('frequency');
            $table->boolean('active')->default(1);

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
        Schema::dropIfExists('customer_recurring_orders');
    }
}
