<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ReportItemSummaryExport implements FromView
{
    // /**
    //  * @return \Illuminate\Support\Collection
    //  */

    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function view(): View
    {

        return view('pages.report.exports.item-summary', [
            'records' => $this->records
        ]);
    }
}
