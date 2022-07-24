<?php

namespace App\Http\Controllers;

use App\Models\BalanceTransaction;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function transactions()
    {
        $data = BalanceTransaction::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->simplePaginate(15, [
            'id',
            'amount',
            'operation',
            'type',
        ])->toArray();

        $data['balance'] = number_format(auth()->user()->balance, 2, '.', '');

        return response()->json($data, 200);
    }
}
