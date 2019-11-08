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
            $table->string('name', 128)->nullable();
            $table->integer('customer_order_id')->unsigned();
            $table->date('start_at')->nullable();
            $table->date('next_at')->nullable();
            $table->date('end_at')->nullable();
            // frequency in days
            $table->integer('frequency');
            $table->text('notes')->nullable();
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
