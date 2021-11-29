<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TempFileSanitizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tempfiles:sanitize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete uploads temp files that are more than 10 minutes old';

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
        $this->info('Deleting upload old temp files...');
        $tenMinutesAgo = Carbon::now()->subMinutes(10);
        $oldTempFiles = DB::table('temp_files')->where('uploaded_at', '<', $tenMinutesAgo)->get();
        foreach ($oldTempFiles as $oldTempFile) {
            $this->info('Deleting temp file: ' . $oldTempFile->path);
            Storage::delete($oldTempFile->path);
            DB::table('temp_files')->where('id', $oldTempFile->id)->delete();
        }
        $this->info('Temp file Sanitization Done!');
        return Command::SUCCESS;
    }
}
