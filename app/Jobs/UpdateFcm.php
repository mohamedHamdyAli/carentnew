<?php

namespace App\Jobs;

use App\Functions\Fcm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class UpdateFcm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // unsubscribe from previous topic
        Fcm::unsubscribe($this->data['fcm'], "all-{$this->data['userCountry']}-{$this->data['userLang']}");
        Fcm::unsubscribe($this->data['fcm'], "all-{$this->data['userCountry']}");
        Fcm::unsubscribe($this->data['fcm'], "all-{$this->data['userLang']}");

        // subscribe to all-countrycode topic
        Fcm::subscribe($this->data['fcm'], "all-{$this->data['countryCode']}-{$this->data['lang']}");
        Fcm::subscribe($this->data['fcm'], "all-{$this->data['countryCode']}");
        Fcm::subscribe($this->data['fcm'], "all-{$this->data['lang']}");
        Fcm::subscribe($this->data['fcm'], "all");

        // handle role topics
        if ($this->data['role'] != null) {
            Fcm::unsubscribe($this->data['fcm'], "all-{$this->data['userCountry']}-{$this->data['role']}-{$this->data['userLang']}");
            Fcm::unsubscribe($this->data['fcm'], "all-{$this->data['userCountry']}-{$this->data['role']}");

            Fcm::subscribe($this->data['fcm'], "all-{$this->data['countryCode']}-{$this->data['role']}-{$this->data['lang']}");
            Fcm::subscribe($this->data['fcm'], "all-{$this->data['countryCode']}-{$this->data['role']}");
            Fcm::subscribe($this->data['fcm'], "all-{$this->data['role']}");
        }
    }
}
