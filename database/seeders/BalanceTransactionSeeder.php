<?php

namespace Database\Seeders;

use App\Models\BalanceTransaction;
use Illuminate\Database\Seeder;

class BalanceTransactionSeeder extends Seeder
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
            \App\Models\BalanceTransaction::factory(50)->create([
                'user_id' => $user->id,
            ]);
            $balance = 0;
            $userTransactions = BalanceTransaction::where('user_id', $user->id)->get();
            foreach ($userTransactions as $transaction) {
                if ($transaction->operation == 'in') {
                    $balance += $transaction->amount;
                } else {
                    $balance -= $transaction->amount;
                }
            }
            $user->balance = $balance;
            $user->save();
        }
    }
}
