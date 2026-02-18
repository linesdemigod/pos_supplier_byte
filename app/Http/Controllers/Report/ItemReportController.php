<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemReportController extends Controller
{
    public function stock()
    {

        return view('pages.report.inventory.stock');
    }

    public function price()
    {

        return view('pages.report.inventory.price');
    }
}
