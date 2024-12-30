<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_chain_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_chain_id')->constrained('approval_chains')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('step_order');
            $table->boolean('approved')->default(false);
            $table->timestamps();
            $table->unique(['approval_chain_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_chain_steps');
    }
};
