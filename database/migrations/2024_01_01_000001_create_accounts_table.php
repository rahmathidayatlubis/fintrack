<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');                          // Nama rekening (BRI, DANA, Cash)
            $table->string('account_number')->nullable();    // Nomor rekening
            $table->string('account_holder')->nullable();    // Nama pemegang rekening
            $table->string('bank_name')->nullable();         // Nama bank/provider
            $table->string('type');                          // cash, bank, e-wallet, investment
            $table->string('icon')->default('wallet');       // Icon name
            $table->string('color')->default('#1E40AF');     // Warna hex
            $table->decimal('balance', 15, 2)->default(0);  // Saldo saat ini
            $table->decimal('initial_balance', 15, 2)->default(0); // Saldo awal
            $table->string('currency', 3)->default('IDR');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('include_in_total')->default(true); // Masukkan ke total
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
