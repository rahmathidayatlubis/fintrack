<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Buat transaksi pemasukan
     */
    public function createIncome(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $account = Account::findOrFail($data['account_id']);

            $balanceBefore = $account->balance;
            $balanceAfter = $balanceBefore + $data['amount'];

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $account->id,
                'category_id' => $data['category_id'] ?? null,
                'type' => 'income',
                'amount' => $data['amount'],
                'admin_fee' => 0,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $data['description'] ?? null,
                'notes' => $data['notes'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now(),
                'is_confirmed' => true,
            ]);

            $account->update(['balance' => $balanceAfter]);

            return $transaction;
        });
    }

    /**
     * Buat transaksi pengeluaran
     * Jika ada admin fee, fee masuk ke rekening cash (jika ada) sebagai pemasukan
     */
    public function createExpense(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $account = Account::findOrFail($data['account_id']);
            $adminFee = $data['admin_fee'] ?? 0;

            $balanceBefore = $account->balance;
            $balanceAfter = $balanceBefore - ($data['amount'] + $adminFee);

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $account->id,
                'category_id' => $data['category_id'] ?? null,
                'type' => 'expense',
                'amount' => $data['amount'],
                'admin_fee' => $adminFee,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'recipient_name' => $data['recipient_name'] ?? null,
                'recipient_account' => $data['recipient_account'] ?? null,
                'reference_code' => $data['reference_code'] ?? null,
                'description' => $data['description'] ?? null,
                'notes' => $data['notes'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now(),
                'is_confirmed' => true,
            ]);

            $account->update(['balance' => $balanceAfter]);

            // Top Up: saldo keluar langsung masuk ke rekening lain (cash/rekening sendiri)
            if (! empty($data['top_up_account_id'])) {
                $topUpAccount = Account::where('id', $data['top_up_account_id'])
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

                // Nominal top up = amount + admin_fee + komisi (semua uang yang keluar)
                $topUpAmount = (float) $data['amount'] + (float) $adminFee + (float) ($data['fee_income_amount'] ?? 0);

                $tbBefore = $topUpAccount->fresh()->balance;
                $topUpAccount->update(['balance' => $tbBefore + $topUpAmount]);

                Transaction::create([
                    'user_id' => Auth::id(),
                    'account_id' => $topUpAccount->id,
                    'type' => 'income',
                    'amount' => $topUpAmount,
                    'admin_fee' => 0,
                    'balance_before' => $tbBefore,
                    'balance_after' => $tbBefore + $topUpAmount,
                    'description' => 'Top up dari '.$account->name.($data['description'] ? ' - '.$data['description'] : ''),
                    'notes' => $data['notes'] ?? null,
                    'transaction_date' => $data['transaction_date'] ?? now(),
                    'is_confirmed' => true,
                ]);
            } elseif (! empty($data['fee_income_account_id']) && ! empty($data['fee_income_amount'])) {
                // Catat komisi/jasa saja (tanpa top up)
                $this->createIncome([
                    'account_id' => $data['fee_income_account_id'],
                    'amount' => (float) $data['fee_income_amount'],
                    'description' => 'Jasa transfer dari '.($data['description'] ?? 'pengeluaran'),
                    'transaction_date' => $data['transaction_date'] ?? now(),
                    'category_id' => Category::where('name', 'Lainnya')->where('type', 'income')->first()?->id,
                ]);
            }

            // Catat hutang jika statusnya 'debt'
            if (($data['payment_status'] ?? 'paid') === 'debt') {
                // Nominal hutang = amount + admin_fee (total yang harus dikembalikan)
                $feeIncomeAmount = isset($data['fee_income_amount']) ? (float) $data['fee_income_amount'] : 0;
                $debtAmount = $data['amount'] + $feeIncomeAmount;

                Debt::create([
                    'user_id' => Auth::id(),
                    'transaction_id' => $transaction->id,
                    'recipient_name' => $data['recipient_name'] ?? 'Tidak diketahui',
                    'recipient_account' => $data['recipient_account'] ?? null,
                    'recipient_bank' => $data['recipient_bank'] ?? null,
                    'original_amount' => $debtAmount,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                    'notes' => $data['notes'] ?? null,
                    'due_date' => ! empty($data['due_date']) ? $data['due_date'] : null,
                ]);
            }

            return $transaction;
        });
    }

    /**
     * Buat transaksi transfer antar rekening
     */
    public function createTransfer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $sourceAccount = Account::findOrFail($data['account_id']);
            $destAccount = Account::findOrFail($data['destination_account_id']);
            $adminFee = $data['admin_fee'] ?? 0;

            // Debit (source)
            $sourceBalanceBefore = $sourceAccount->balance;
            $sourceBalanceAfter = $sourceBalanceBefore - ($data['amount'] + $adminFee);

            // Credit (destination)
            $destBalanceBefore = $destAccount->balance;
            $destBalanceAfter = $destBalanceBefore + $data['amount'];

            // Buat transaksi debit (dari rekening sumber)
            $debitTx = Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $sourceAccount->id,
                'destination_account_id' => $destAccount->id,
                'category_id' => $data['category_id'] ?? null,
                'type' => 'transfer',
                'transfer_type' => 'debit',
                'amount' => $data['amount'],
                'admin_fee' => $adminFee,
                'balance_before' => $sourceBalanceBefore,
                'balance_after' => $sourceBalanceAfter,
                'description' => $data['description'] ?? null,
                'notes' => $data['notes'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now(),
                'is_confirmed' => true,
            ]);

            // Buat transaksi credit (ke rekening tujuan)
            $creditTx = Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $destAccount->id,
                'destination_account_id' => $sourceAccount->id,
                'category_id' => $data['category_id'] ?? null,
                'type' => 'transfer',
                'transfer_type' => 'credit',
                'amount' => $data['amount'],
                'admin_fee' => 0,
                'balance_before' => $destBalanceBefore,
                'balance_after' => $destBalanceAfter,
                'description' => $data['description'] ?? null,
                'notes' => $data['notes'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now(),
                'is_confirmed' => true,
            ]);

            // Link pair
            $debitTx->update(['transfer_pair_id' => $creditTx->id]);
            $creditTx->update(['transfer_pair_id' => $debitTx->id]);

            // Update saldo
            $sourceAccount->update(['balance' => $sourceBalanceAfter]);
            $destAccount->update(['balance' => $destBalanceAfter]);

            // Jika admin fee > 0 dan ada rekening penerima fee
            if ($adminFee > 0 && ! empty($data['fee_income_account_id'])) {
                $this->createIncome([
                    'account_id' => $data['fee_income_account_id'],
                    'amount' => $adminFee,
                    'description' => 'Jasa transfer ke '.$destAccount->name,
                    'transaction_date' => $data['transaction_date'] ?? now(),
                    'category_id' => Category::where('name', 'Lainnya')->where('type', 'income')->first()?->id,
                ]);
            }

            return [$debitTx, $creditTx];
        });
    }

    /**
     * Penyesuaian saldo rekening
     */
    public function createAdjustment(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $account = Account::findOrFail($data['account_id']);

            $balanceBefore = $account->balance;
            $newBalance = $data['amount'];

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $account->id,
                'type' => 'adjustment',
                'amount' => $newBalance,
                'admin_fee' => 0,
                'balance_before' => $balanceBefore,
                'balance_after' => $newBalance,
                'description' => $data['description'] ?? 'Penyesuaian saldo',
                'notes' => $data['notes'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now(),
                'is_confirmed' => true,
            ]);

            $account->update(['balance' => $newBalance]);

            return $transaction;
        });
    }

    /**
     * Delete transaksi dan rollback saldo
     */
    public function deleteTransaction(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Rollback saldo
            $account = $transaction->account;

            if ($transaction->type === 'income') {
                $account->decrement('balance', $transaction->amount);
            } elseif ($transaction->type === 'expense') {
                $account->increment('balance', $transaction->amount + $transaction->admin_fee);
            } elseif ($transaction->type === 'transfer') {
                if ($transaction->transfer_type === 'debit') {
                    $account->increment('balance', $transaction->amount + $transaction->admin_fee);
                    // Rollback dest
                    if ($transaction->transfer_pair_id) {
                        $pair = Transaction::find($transaction->transfer_pair_id);
                        if ($pair) {
                            $pair->account->decrement('balance', $pair->amount);
                            $pair->delete();
                        }
                    }
                }
                // Skip if credit, already handled by debit
            }

            $transaction->delete();
        });
    }

    /**
     * Get summary stats for a user
     */
    public function getMonthlySummary(int $userId, ?int $month = null, ?int $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $transactions = Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->whereNull('deleted_at')
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum(fn ($t) => $t->amount + $t->admin_fee);
        $transfers = $transactions->where('type', 'transfer')->count();

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
            'transfers' => $transfers,
        ];
    }
}
