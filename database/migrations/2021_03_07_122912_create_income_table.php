<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->foreignId('period_id')
            ->constrained('periods')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->double('total');
            $table->double('remainder');
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
        Schema::table('income', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['period_id']);
        });
        Schema::dropIfExists('income');
    }
}
