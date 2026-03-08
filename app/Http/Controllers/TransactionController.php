<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService) {}

    public function index()
    {
        $user = Auth::user();

        $query = Transaction::where('user_id', $user->id)
            ->with(['account', 'category', 'destinationAccount'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        // Filters
        if ($type = request('type')) {
            $query->where('type', $type);
        }
        if ($accountId = request('account_id')) {
            $query->where('account_id', $accountId);
        }
        if ($month = request('month', now()->format('Y-m'))) {
            [$year, $mon] = explode('-', $month);
            $query->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $mon);
        }
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%$search%")
                    ->orWhere('recipient_name', 'like', "%$search%")
                    ->orWhere('reference_code', 'like', "%$search%");
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->get();
        $categories = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhereNull('user_id');
        })->get();

        $summary = $this->transactionService->getMonthlySummary($user->id);

        return view('transactions.index', compact('transactions', 'accounts', 'categories', 'summary'));
    }

    public function create()
    {
        $user = Auth::user();
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->get();

        $categoryQuery = Category::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhereNull('user_id');
        });

        $incomeCategories = (clone $categoryQuery)
            ->where('type', 'income')
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();

        $expenseCategories = (clone $categoryQuery)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();

        $type = request('type', 'income');

        return view('transactions.create', [
            'accounts' => $accounts,
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'type' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->type;

        $baseRules = [
            'type' => 'required|in:income,expense,transfer,adjustment',
            'account_id' => 'required|exists:accounts,id',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ];

        switch ($type) {
            case 'income':
                $rules = array_merge($baseRules, [
                    'amount' => 'required|numeric|min:1',
                    'category_id' => 'nullable|exists:categories,id',
                ]);
                break;
            case 'expense':
                $rules = array_merge($baseRules, [
                    'amount' => 'required|numeric|min:1',
                    'admin_fee' => 'nullable|numeric|min:0',
                    'recipient_name' => 'nullable|string|max:100',
                    'recipient_account' => 'nullable|string|max:50',
                    'category_id' => 'nullable|exists:categories,id',
                    'fee_income_account_id' => 'nullable|exists:accounts,id',
                    'fee_income_amount' => 'nullable|numeric|min:0',
                    'payment_status' => 'nullable|in:paid,debt',
                    'due_date' => 'nullable|date',
                    'recipient_bank' => 'nullable|string|max:100',
                ]);
                break;
            case 'transfer':
                $rules = array_merge($baseRules, [
                    'amount' => 'required|numeric|min:1',
                    'admin_fee' => 'nullable|numeric|min:0',
                    'destination_account_id' => 'required|exists:accounts,id|different:account_id',
                    'fee_income_account_id' => 'nullable|exists:accounts,id',
                ]);
                break;
            case 'adjustment':
                $rules = array_merge($baseRules, [
                    'amount' => 'required|numeric|min:0',
                ]);
                break;
            default:
                return back()->withErrors(['type' => 'Tipe transaksi tidak valid']);
        }

        $validated = $request->validate($rules);

        // Pastikan rekening milik user
        $account = Account::where('id', $request->account_id)
            ->where('user_id', Auth::id())->firstOrFail();

        // Cek saldo cukup untuk expense/transfer
        if (in_array($type, ['expense', 'transfer'])) {
            $needed = $request->amount + ($request->admin_fee ?? 0);
            if ($account->balance < $needed) {
                return back()->withErrors(['amount' => 'Saldo tidak mencukupi. Saldo tersedia: Rp '.number_format($account->balance, 0, ',', '.')])->withInput();
            }
        }

        try {
            switch ($type) {
                case 'income':
                    $this->transactionService->createIncome($validated);
                    break;
                case 'expense':
                    $this->transactionService->createExpense($validated);
                    break;
                case 'transfer':
                    $this->transactionService->createTransfer($validated);
                    break;
                case 'adjustment':
                    $this->transactionService->createAdjustment($validated);
                    break;
            }

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan transaksi: '.$e->getMessage()])->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        $transaction->load(['account', 'category', 'destinationAccount', 'transferPair']);

        return view('transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        try {
            $this->transactionService->deleteTransaction($transaction);

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus transaksi.']);
        }
    }
}
