<?php

namespace App\Jobs;

use App\Functions\Fcm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

        // subscribe to all-countrycode topic
        Fcm::subscribe($this->data['fcm'], "all-{$this->data['countryCode']}-{$this->data['lang']}");
        Fcm::subscribe($this->data['fcm'], "all-{$this->data['countryCode']}");

        // handle role topics
        $user = auth()->user();
        if ($user->hasRole('renter')) {
            Fcm::subscribe($this->data['fcm'], "admin-{$this->data['countryCode']}-renter-{$this->data['lang']}");
            Fcm::subscribe($this->data['fcm'], "admin-{$this->data['countryCode']}-renter");
        }
        if ($user->hasRole('owner')) {
            Fcm::subscribe($this->data['fcm'], "admin-{$this->data['countryCode']}-owner-{$this->data['lang']}");
            Fcm::subscribe($this->data['fcm'], "admin-{$this->data['countryCode']}-owner");
        }
        if ($user->hasRole('agency')) {
            Fcm::subscribe($this->data['fcm'], "admin-{$this->data['countryCode']}-agency-{$this->data['lang']}");
            Fcm::subscribe($this->data['fcm'], "admin-{$this->data['countryCode']}-agency");
        }
    }
}
