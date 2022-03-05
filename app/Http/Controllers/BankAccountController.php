<?php

namespace App\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function show()
    {
        $data = cache()->rememberForever('bankaccount-' . auth()->id(), function () {
            return BankAccount::where('user_id', auth()->id())->latest()->first();
        });

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $data,
            'error' => null,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_id' => ['required', 'exists:banks,id'],
            'branch_code' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255', 'regex:/^(?!.*\d)[a-zA-Z\s]{2,255}$/iu'],
            'swift_code' => ['required', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:255'],
        ]);

        $bankAccount = BankAccount::create([
            'user_id' => auth()->id(),
            'bank_id' => $request->bank_id,
            'branch_code' => $request->branch_code,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'swift_code' => $request->swift_code,
            'iban' => $request->iban,
        ]);

        cache()->forget('bankaccount-' . auth()->id());

        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $bankAccount,
            'error' => null,
        ], 200);
    }
}
