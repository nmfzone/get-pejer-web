<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTokenablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_tokenables', function (Blueprint $table) {
            $table->unsignedBigInteger('device_token_id');
            $table->foreign('device_token_id')
                ->references('id')
                ->on('device_tokens');
            $table->morphs('device_tokenable', 'device_tokenables_index_1');
            $table->index(['device_token_id', 'device_tokenable_id'], 'device_tokenables_index_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_tokenables');
    }
}
