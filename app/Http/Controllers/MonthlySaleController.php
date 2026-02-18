<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonthlySaleController extends Controller
{
    public function index()
    {


        return view('pages.monthlysale.index');
    }
}
