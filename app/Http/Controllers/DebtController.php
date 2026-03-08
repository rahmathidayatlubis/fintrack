<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index()
    {
        $debts = Debt::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->with(['transaction', 'payments'])
            ->orderBy('recipient_name')
            ->get();

        $grouped = $debts->groupBy('recipient_name')->map(function ($items, $name) {
            return [
                'name' => $name,
                'items' => $items,
                'total_debt' => $items->sum('effective_amount'),
                'total_paid' => $items->sum('paid_amount'),
                'total_remaining' => $items->sum('remaining_amount'),
                'has_unpaid' => $items->whereIn('status', ['unpaid', 'partial'])->count() > 0,
                'has_overdue' => $items->filter(fn ($d) => $d->is_overdue)->count() > 0,
            ];
        })->sortByDesc('has_unpaid');

        $summary = [
            'total_unpaid' => $debts->whereIn('status', ['unpaid', 'partial'])->sum('remaining_amount'),
            'total_paid' => $debts->where('status', 'paid')->sum('effective_amount'),
            'count_unpaid' => $debts->whereIn('status', ['unpaid', 'partial'])->count(),
            'count_overdue' => $debts->filter(fn ($d) => $d->is_overdue)->count(),
        ];

        return view('debts.index', compact('grouped', 'summary'));
    }

    public function show(Debt $debt)
    {
        $this->authorize('view', $debt);
        $debt->load(['transaction.account', 'payments.account']);

        $accounts = Account::where('user_id', Auth::id())
            ->where('is_active', true)->get();

        return view('debts.show', compact('debt', 'accounts'));
    }

    public function edit(Debt $debt)
    {
        $this->authorize('update', $debt);
        $debt->load(['transaction.account', 'payments']);

        return view('debts.edit', compact('debt'));
    }

    public function update(Request $request, Debt $debt)
    {
        $this->authorize('update', $debt);

        $request->validate([
            'recipient_name' => 'required|string|max:100',
            'recipient_account' => 'nullable|string|max:50',
            'recipient_bank' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'due_date' => 'nullable|date',
        ]);

        $debt->update([
            'recipient_name' => $request->recipient_name,
            'recipient_account' => $request->recipient_account,
            'recipient_bank' => $request->recipient_bank,
            'notes' => $request->notes,
            'due_date' => $request->due_date ?: null,
        ]);

        return redirect()->route('debts.show', $debt)
            ->with('success', 'Data hutang berhasil diperbarui!');
    }

    public function pay(Request $request, Debt $debt)
    {
        $this->authorize('update', $debt);

        $request->validate([
            'amount' => 'required|numeric|min:1|max:'.$debt->remaining_amount,
            'account_id' => 'nullable|exists:accounts,id',
            'notes' => 'nullable|string|max:255',
            'paid_at' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $debt) {
            DebtPayment::create([
                'debt_id' => $debt->id,
                'user_id' => Auth::id(),
                'account_id' => $request->account_id ?: null,
                'amount' => $request->amount,
                'type' => 'payment',
                'notes' => $request->notes,
                'paid_at' => $request->paid_at,
            ]);

            $debt->increment('paid_amount', $request->amount);
            $debt->refresh()->recalculateStatus();

            // Tambah ke rekening jika dipilih
            if ($request->account_id) {
                $account = Account::findOrFail($request->account_id);
                $account->increment('balance', $request->amount);

                // Catat sebagai transaksi pemasukan
                Transaction::create([
                    'user_id' => Auth::id(),
                    'account_id' => $account->id,
                    'type' => 'income',
                    'amount' => $request->amount,
                    'admin_fee' => 0,
                    'balance_before' => $account->balance - $request->amount,
                    'balance_after' => $account->balance,
                    'description' => 'Pelunasan hutang dari '.$debt->recipient_name,
                    'notes' => $request->notes,
                    'transaction_date' => $request->paid_at,
                    'is_confirmed' => true,
                ]);
            }
        });

        return back()->with('success', 'Pembayaran berhasil dicatat!');
    }

    public function adjust(Request $request, Debt $debt)
    {
        $this->authorize('update', $debt);

        $request->validate([
            'adjusted_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $debt) {
            $diff = $request->adjusted_amount - $debt->effective_amount;

            DebtPayment::create([
                'debt_id' => $debt->id,
                'user_id' => Auth::id(),
                'amount' => $diff,
                'type' => 'adjustment',
                'notes' => $request->notes ?: 'Penyesuaian nominal',
                'paid_at' => now(),
            ]);

            $debt->update(['adjusted_amount' => $request->adjusted_amount]);
            $debt->refresh()->recalculateStatus();
        });

        return back()->with('success', 'Nominal hutang berhasil disesuaikan!');
    }

    // Hapus satu riwayat pembayaran & rollback paid_amount
    public function deletePayment(DebtPayment $payment)
    {
        $debt = $payment->debt;
        $this->authorize('update', $debt);

        DB::transaction(function () use ($payment, $debt) {
            if ($payment->type === 'payment') {
                $debt->decrement('paid_amount', $payment->amount);
            } elseif ($payment->type === 'adjustment') {
                // Rollback adjustment
                $debt->update([
                    'adjusted_amount' => $debt->adjusted_amount
                        ? $debt->adjusted_amount - $payment->amount
                        : null,
                ]);
            }

            $payment->delete();
            $debt->refresh()->recalculateStatus();
        });

        return back()->with('success', 'Riwayat pembayaran dihapus.');
    }

    // Tandai lunas sekaligus (tanpa input jumlah)
    public function markPaid(Request $request, Debt $debt)
    {
        $this->authorize('update', $debt);

        if ($debt->status === 'paid') {
            return back()->with('error', 'Hutang ini sudah lunas.');
        }

        DB::transaction(function () use ($request, $debt) {
            $remaining = $debt->remaining_amount;

            DebtPayment::create([
                'debt_id' => $debt->id,
                'user_id' => Auth::id(),
                'amount' => $remaining,
                'type' => 'payment',
                'notes' => 'Tandai lunas',
                'paid_at' => now(),
            ]);

            $debt->update([
                'paid_amount' => $debt->effective_amount,
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Tambah ke rekening jika dipilih
            if ($request->account_id) {
                $account = Account::findOrFail($request->account_id);
                $balanceBefore = $account->balance;
                $account->increment('balance', $remaining);

                Transaction::create([
                    'user_id' => Auth::id(),
                    'account_id' => $account->id,
                    'type' => 'income',
                    'amount' => $remaining,
                    'admin_fee' => 0,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore + $remaining,
                    'description' => 'Pelunasan hutang dari '.$debt->recipient_name,
                    'transaction_date' => now(),
                    'is_confirmed' => true,
                ]);
            }
        });

        return back()->with('success', 'Hutang berhasil ditandai lunas!');
    }

    public function destroy(Debt $debt)
    {
        $this->authorize('delete', $debt);
        $debt->delete();

        return redirect()->route('debts.index')
            ->with('success', 'Data hutang dihapus.');
    }
}
