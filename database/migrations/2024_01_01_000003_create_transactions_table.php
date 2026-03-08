<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            // Untuk transfer: rekening tujuan
            $table->foreignId('destination_account_id')->nullable()->constrained('accounts')->nullOnDelete();

            $table->string('type'); // income, expense, transfer, adjustment

            $table->decimal('amount', 15, 2);           // Jumlah transaksi utama
            $table->decimal('admin_fee', 15, 2)->default(0); // Biaya admin transfer
            $table->decimal('balance_before', 15, 2)->default(0); // Saldo sebelum
            $table->decimal('balance_after', 15, 2)->default(0);  // Saldo sesudah

            $table->string('reference_code')->nullable(); // Kode referensi/norek tujuan
            $table->string('recipient_name')->nullable();  // Nama penerima (untuk expense transfer)
            $table->string('recipient_account')->nullable(); // Nomor rekening penerima

            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date');

            // Untuk pair transfer (parent-child)
            $table->unsignedBigInteger('transfer_pair_id')->nullable(); // ID transaksi pasangan
            $table->string('transfer_type')->nullable(); // debit, credit (untuk transfer)

            $table->boolean('is_confirmed')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'transaction_date']);
            $table->index(['account_id', 'transaction_date']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
