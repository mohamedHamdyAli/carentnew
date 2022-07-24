<?php

namespace App\Http\Controllers;

use App\Models\RewardPointTransaction;
use Illuminate\Http\Request;

class RewardPointController extends Controller
{
    public function transactions()
    {
        $data = RewardPointTransaction::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->simplePaginate(15, [
            'id',
            'points',
            'operation',
            'type',
        ])->toArray();

        $data['reward_points'] = auth()->user()->reward_points;

        return response()->json($data, 200);
    }
}
