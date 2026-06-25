<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWithdrawalRequest;
use App\Services\WithdrawalService;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class SellerWithdrawalController extends Controller implements HasMiddleware
{
    protected WithdrawalService $withdrawalService;

    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
            'role:seller',
        ];
    }

    // ponytail: list user withdrawals
    public function index()
    {
        $wallet = auth()->user()->wallet()->firstOrCreate([]);
        $withdrawals = $wallet->withdrawals()->latest()->paginate(15);
        return view('seller.withdrawals.index', compact('withdrawals'));
    }

    // ponytail: show withdrawal form
    public function create()
    {
        $wallet = auth()->user()->wallet()->firstOrCreate([]);
        return view('seller.withdrawals.create', compact('wallet'));
    }

    // ponytail: request new withdrawal
    public function store(StoreWithdrawalRequest $request)
    {
        try {
            $this->withdrawalService->requestWithdrawal(auth()->user(), $request->validated());
            return redirect()->route('seller.withdrawals.index')->with('success', 'Withdrawal requested.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
