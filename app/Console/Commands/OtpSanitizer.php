<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OtpSanitizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:sanitizer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired user otps';

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
        $this->info('Deleting expired user opts...');
        $now = Carbon::now();
        DB::delete('delete from user_otps where expire_at < ?', [$now]);
        $this->info('OTP Sanitization Done!');
        return Command::SUCCESS;
    }
}
