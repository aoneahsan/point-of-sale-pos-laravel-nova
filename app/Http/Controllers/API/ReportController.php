<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Generate sales report.
     */
    public function sales(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportService->generateSalesReport(
            $request->store_id,
            $request->start_date,
            $request->end_date
        );

        return response()->json($report);
    }

    /**
     * Generate inventory report.
     */
    public function inventory(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);

        $report = $this->reportService->generateInventoryReport($request->store_id);

        return response()->json($report);
    }

    /**
     * Generate customer report.
     */
    public function customers(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $report = $this->reportService->generateCustomerReport(
            $request->store_id,
            $request->start_date,
            $request->end_date
        );

        return response()->json($report);
    }
}
