<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index()
    {
        $user = Auth::user();

        $accounts = Account::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // $totalBalance = $accounts->where('include_in_total', true)->sum('balance');
        $totalBalance = $accounts
            ->where('include_in_total', true)
            ->sum(function ($account) {
                return $account->type === 'debt'
                    ? -$account->balance
                    : $account->balance;
            });

        $summary = $this->transactionService->getMonthlySummary($user->id);

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with(['account', 'category', 'destinationAccount'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // Spending by category (this month)
        $spendingByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->with('category')
            ->get()
            ->groupBy(fn ($t) => $t->category?->name ?? 'Lainnya')
            ->map(fn ($items) => $items->sum(fn ($t) => $t->amount + $t->admin_fee))
            ->sortByDesc(fn ($v) => $v)
            ->take(5);

        // Last 6 months income vs expense
        $monthlyChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyChart[] = [
                'month' => $date->format('M'),
                'income' => Transaction::where('user_id', $user->id)->where('type', 'income')
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year)->sum('amount'),
                'expense' => Transaction::where('user_id', $user->id)->where('type', 'expense')
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year)
                    ->sum(\DB::raw('amount + admin_fee')),
            ];
        }

        return view('dashboard.index', compact(
            'accounts', 'totalBalance', 'summary',
            'recentTransactions', 'spendingByCategory', 'monthlyChart'
        ));
    }
}
