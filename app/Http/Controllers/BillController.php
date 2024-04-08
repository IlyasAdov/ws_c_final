<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BillController extends Controller
{
    public function show($workspaceId, $year, $month) {
        $workspace = Workspace::findOrFail($workspaceId);

        if (!$workspace || !$year || !$month) abort(404);

        $billData = $workspace->getBill($workspaceId, $year, $month);

        return view('bills.show', [
            'workspace' => $workspace,
            'apiTokens' => $billData['apiTokens'],
            'services' => $billData['services'],
            'year' => $year,
            'month' => $month,
            'monthName' => Carbon::createFromFormat('Y-m-d', "$year-$month-01")->format('F'),
            'total' => $billData['total']
        ]);
    }
}
