<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TransactionExportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class LedgerController extends Controller
{
    public function home(): View
    {
        return view('ledger.home');
    }

    public function export(TransactionExportService $exportService)
    {
        return $exportService->export();
    }
}
