<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    private $dateFrom;
    private $dateTo;

    public function __construct()
    {
        $this->dateFrom = request()->get('date_from');
        $this->dateTo = request()->get('date_to');
    }

    public function paymentsAggregator()
    {
        return $this->dateFrom . '-' . $this->dateTo;
    }

    public function paymentsWallet()
    {
    }
    public function cancellationRenter()
    {
    }
    public function cancellationOwner()
    {
    }
    public function earlyReturns()
    {
    }
    public function lateReturns()
    {
    }
    public function accidentFees()
    {
    }
}
