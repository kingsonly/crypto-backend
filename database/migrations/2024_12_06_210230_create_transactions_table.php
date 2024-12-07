<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->integer('user_id');
            $table->string('method')->nullable()->comment('currency eg bitcoin etc');
            $table->enum('type', ['withdraw', 'deposit', 'transfer', 'earning', 'investment', 'referral']);
            $table->enum('group', ['credit', 'debit']);
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
