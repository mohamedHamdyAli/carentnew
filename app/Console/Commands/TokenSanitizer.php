<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TokenSanitizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:sanitize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete inactive personal tokens';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Deleting inactive personal tokens...');
        $monthAgo = Carbon::now()->subDays(30);
        DB::delete('delete from personal_access_tokens where (last_used_at < ?) or (last_used_at is null and created_at < ?)', [$monthAgo, $monthAgo]);
        $this->info('Token Sanitization Done!');
        return Command::SUCCESS;
    }
}
