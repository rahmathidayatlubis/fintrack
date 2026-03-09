<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // $totalBalance = $accounts->where('include_in_total', true)->sum('balance');
        $totalBalance = $accounts
            ->where('include_in_total', true)
            ->sum(function ($account) {
                return $account->type === 'debt'
                    ? -$account->balance
                    : $account->balance;
            });

        return view('accounts.index', compact('accounts', 'totalBalance'));
    }

    public function create()
    {
        $types = Account::TYPES;
        $presets = Account::PRESETS;

        return view('accounts.create', compact('types', 'presets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:'.implode(',', array_keys(Account::TYPES)),
            'initial_balance' => 'required|numeric|min:0',
            'color' => 'required|string',
            'icon' => 'required|string',
            'account_number' => 'nullable|string|max:50',
            'account_holder' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'include_in_total' => 'boolean',
        ]);

        $sortOrder = Account::where('user_id', Auth::id())->max('sort_order') + 1;

        Account::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'bank_name' => $request->bank_name,
            'type' => $request->type,
            'icon' => $request->icon,
            'color' => $request->color,
            'balance' => $request->initial_balance,
            'initial_balance' => $request->initial_balance,
            'currency' => 'IDR',
            'description' => $request->description,
            'is_active' => true,
            'include_in_total' => $request->boolean('include_in_total', true),
            'sort_order' => $sortOrder,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Rekening berhasil ditambahkan!');
    }

    public function show(Account $account)
    {
        $this->authorize('view', $account);

        $query = Transaction::where('account_id', $account->id)
            ->with(['category', 'destinationAccount'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        // Filter
        $filterType = request('type', 'all');
        if ($filterType !== 'all') {
            $query->where('type', $filterType);
        }

        $filterMonth = request('month', now()->format('Y-m'));

        if ($filterMonth && $filterMonth !== 'all') {
            [$year, $month] = explode('-', $filterMonth);
            $query->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month);
        }

        $transactions = $query->paginate(20);

        // Stats for this account
        $stats = [
            'total_income' => Transaction::where('account_id', $account->id)
                ->where('type', 'income')->sum('amount') +
                               Transaction::where('account_id', $account->id)
                                   ->where('type', 'transfer')->where('transfer_type', 'credit')->sum('amount'),
            'total_expense' => Transaction::where('account_id', $account->id)
                ->where('type', 'expense')->sum(\DB::raw('amount + admin_fee')) +
                               Transaction::where('account_id', $account->id)
                                   ->where('type', 'transfer')->where('transfer_type', 'debit')->sum(\DB::raw('amount + admin_fee')),
            'this_month_income' => Transaction::where('account_id', $account->id)
                ->where('type', 'income')->thisMonth()->sum('amount'),
            'this_month_expense' => Transaction::where('account_id', $account->id)
                ->where('type', 'expense')->thisMonth()->sum(\DB::raw('amount + admin_fee')),
        ];

        return view('accounts.show', compact('account', 'transactions', 'stats', 'filterType', 'filterMonth'));
    }

    public function edit(Account $account)
    {
        $this->authorize('update', $account);
        $types = Account::TYPES;
        $presets = Account::PRESETS;

        return view('accounts.edit', compact('account', 'types', 'presets'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorize('update', $account);

        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:'.implode(',', array_keys(Account::TYPES)),
            'color' => 'required|string',
            'icon' => 'required|string',
            'account_number' => 'nullable|string|max:50',
            'account_holder' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'include_in_total' => 'boolean',
        ]);

        $account->update([
            'name' => $request->name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'bank_name' => $request->bank_name,
            'type' => $request->type,
            'icon' => $request->icon,
            'color' => $request->color,
            'description' => $request->description,
            'include_in_total' => $request->boolean('include_in_total', true),
        ]);

        return redirect()->route('accounts.show', $account)
            ->with('success', 'Rekening berhasil diperbarui!');
    }

    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);

        if ($account->transactions()->count() > 0) {
            return back()->with('error', 'Rekening tidak bisa dihapus karena memiliki transaksi.');
        }

        $account->delete();

        return redirect()->route('dashboard')->with('success', 'Rekening berhasil dihapus!');
    }

    public function updateOrder(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $id) {
            Account::where('id', $id)->where('user_id', Auth::id())
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
