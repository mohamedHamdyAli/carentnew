<?php

namespace Database\Seeders;

use App\Models\RewardPointTransaction;
use Illuminate\Database\Seeder;

class RewardPointTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            \App\Models\RewardPointTransaction::factory(50)->create([
                'user_id' => $user->id,
            ]);
            $reward_points = 0;
            $userTransactions = RewardPointTransaction::where('user_id', $user->id)->get();
            foreach ($userTransactions as $transaction) {
                if ($transaction->operation == 'in') {
                    $reward_points += $transaction->points;
                } else {
                    $reward_points -= $transaction->points;
                }
            }
            $user->reward_points = $reward_points;
            $user->save();
        }
    }
}
