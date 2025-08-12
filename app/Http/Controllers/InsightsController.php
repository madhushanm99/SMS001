<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\PaymentTransaction;
use App\Models\SalesInvoice;
use App\Models\GRN;
use App\Models\PurchaseReturn;
use App\Models\Po;
use App\Models\Customer;
use App\Models\Vehicle;

class InsightsController extends Controller
{
    public function index(Request $request): View
    {
        $days = (int) $request->get('days', 30);
        $dateFrom = now()->subDays($days)->startOfDay();
        $dateTo = now()->endOfDay();

        // Payments summary
        $payments = [
            'cash_in' => (float) PaymentTransaction::cashIn()->completed()->whereBetween('transaction_date', [$dateFrom, $dateTo])->sum('amount'),
            'cash_out' => (float) PaymentTransaction::cashOut()->completed()->whereBetween('transaction_date', [$dateFrom, $dateTo])->sum('amount'),
            'net' => 0.0,
            'completed' => (int) PaymentTransaction::completed()->whereBetween('transaction_date', [$dateFrom, $dateTo])->count(),
            'pending' => (int) PaymentTransaction::pending()->whereBetween('transaction_date', [$dateFrom, $dateTo])->count(),
            'recent' => PaymentTransaction::with(['paymentMethod','paymentCategory','customer','supplier'])
                ->whereBetween('transaction_date', [$dateFrom, $dateTo])
                ->latest('transaction_date')
                ->limit(10)
                ->get(),
        ];
        $payments['net'] = $payments['cash_in'] - $payments['cash_out'];

        // Sales Invoices summary
        $invoicesQuery = SalesInvoice::whereBetween('invoice_date', [$dateFrom, $dateTo]);
        $invoices = [
            'count' => (int) $invoicesQuery->count(),
            'total' => (float) SalesInvoice::whereBetween('invoice_date', [$dateFrom, $dateTo])->sum('grand_total'),
            'finalized' => (int) SalesInvoice::whereBetween('invoice_date', [$dateFrom, $dateTo])->where('status', 'finalized')->count(),
            'hold' => (int) SalesInvoice::whereBetween('invoice_date', [$dateFrom, $dateTo])->where('status', 'hold')->count(),
            'recent' => SalesInvoice::with('customer')->whereBetween('invoice_date', [$dateFrom, $dateTo])->latest('invoice_date')->limit(10)->get(),
        ];

        // GRN summary
        $grnList = GRN::with('items')->whereBetween('grn_date', [$dateFrom, $dateTo])->latest('grn_date')->get();
        $grn = [
            'count' => (int) $grnList->count(),
            'total' => (float) $grnList->sum(fn ($g) => $g->items->sum('line_total')),
            'recent' => $grnList->take(10),
        ];

        // Purchase Returns summary
        $purchaseReturnList = PurchaseReturn::with(['items','supplier'])->whereBetween('created_at', [$dateFrom, $dateTo])->latest('created_at')->get();
        $purchaseReturns = [
            'count' => (int) $purchaseReturnList->count(),
            'total' => (float) $purchaseReturnList->sum(fn ($r) => $r->getTotalAmount()),
            'recent' => $purchaseReturnList->take(10),
        ];

        // Purchase Orders summary
        $poQuery = Po::whereBetween('po_date', [$dateFrom, $dateTo]);
        $purchaseOrders = [
            'count' => (int) $poQuery->count(),
            'total' => (float) (clone $poQuery)->sum('grand_Total'),
            'recent' => Po::whereBetween('po_date', [$dateFrom, $dateTo])->latest('po_date')->limit(10)->get(),
        ];

        // Customers summary
        $customersQuery = Customer::whereBetween('created_at', [$dateFrom, $dateTo]);
        $customers = [
            'count' => (int) $customersQuery->count(),
            'recent' => $customersQuery->latest('created_at')->limit(10)->get(),
        ];

        // Vehicles summary
        $vehiclesQuery = Vehicle::whereBetween('created_at', [$dateFrom, $dateTo]);
        $vehicles = [
            'count' => (int) $vehiclesQuery->count(),
            'approved' => (int) (clone $vehiclesQuery)->where('is_approved', true)->count(),
            'recent' => $vehiclesQuery->latest('created_at')->limit(10)->get(),
        ];

        return view('Statistics.insights', compact(
            'days', 'dateFrom', 'dateTo',
            'payments', 'invoices', 'grn', 'purchaseReturns', 'purchaseOrders', 'customers', 'vehicles'
        ));
    }

    public function overview(Request $request): View
    {
        $days = (int) $request->get('days', 30);
        $start = now()->subDays($days - 1)->startOfDay();
        $end = now()->endOfDay();

        // Helper to build date labels
        $dateKeys = [];
        $labels = [];
        for ($d = 0; $d < $days; $d++) {
            $date = $start->copy()->addDays($d);
            $key = $date->format('Y-m-d');
            $dateKeys[] = $key;
            $labels[] = $date->format('M d');
        }

        // Payments (cash in/out by day)
        $paymentsDaily = PaymentTransaction::completed()
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('DATE(transaction_date) as d')
            ->selectRaw('SUM(CASE WHEN type = "cash_in" THEN amount ELSE 0 END) as cash_in')
            ->selectRaw('SUM(CASE WHEN type = "cash_out" THEN amount ELSE 0 END) as cash_out')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $paymentsChart = [
            'labels' => $labels,
            'cash_in' => array_map(fn($k) => (float) ($paymentsDaily[$k]->cash_in ?? 0), $dateKeys),
            'cash_out' => array_map(fn($k) => (float) ($paymentsDaily[$k]->cash_out ?? 0), $dateKeys),
        ];

        // Sales Invoices totals by day
        $invoicesDaily = SalesInvoice::whereBetween('invoice_date', [$start, $end])
            ->selectRaw('DATE(invoice_date) as d')
            ->selectRaw('COUNT(*) as cnt')
            ->selectRaw('SUM(grand_total) as total')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');
        $invoicesChart = [
            'labels' => $labels,
            'count' => array_map(fn($k) => (int) ($invoicesDaily[$k]->cnt ?? 0), $dateKeys),
            'total' => array_map(fn($k) => (float) ($invoicesDaily[$k]->total ?? 0), $dateKeys),
        ];

        // GRN totals by day (sum of line_total)
        $grnRecords = GRN::with('items')->whereBetween('grn_date', [$start, $end])->get();
        $grnMap = [];
        foreach ($grnRecords as $g) {
            $d = \Illuminate\Support\Carbon::parse($g->grn_date)->format('Y-m-d');
            $grnMap[$d] = ($grnMap[$d] ?? 0) + (float) $g->items->sum('line_total');
        }
        $grnChart = [
            'labels' => $labels,
            'total' => array_map(fn($k) => (float) ($grnMap[$k] ?? 0), $dateKeys),
        ];

        // Purchase Orders totals by day
        $poDaily = Po::whereBetween('po_date', [$start, $end])
            ->selectRaw('DATE(po_date) as d')
            ->selectRaw('COUNT(*) as cnt')
            ->selectRaw('SUM(grand_Total) as total')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');
        $poChart = [
            'labels' => $labels,
            'count' => array_map(fn($k) => (int) ($poDaily[$k]->cnt ?? 0), $dateKeys),
            'total' => array_map(fn($k) => (float) ($poDaily[$k]->total ?? 0), $dateKeys),
        ];

        // Purchase Returns totals by day
        $returnRecords = PurchaseReturn::with('items')->whereBetween('created_at', [$start, $end])->get();
        $retMap = [];
        foreach ($returnRecords as $r) {
            $d = $r->created_at?->format('Y-m-d');
            if ($d) {
                $retMap[$d] = ($retMap[$d] ?? 0) + (float) $r->getTotalAmount();
            }
        }
        $returnsChart = [
            'labels' => $labels,
            'total' => array_map(fn($k) => (float) ($retMap[$k] ?? 0), $dateKeys),
        ];

        // New Customers by day
        $custDaily = Customer::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as d')
            ->selectRaw('COUNT(*) as cnt')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');
        $customersChart = [
            'labels' => $labels,
            'count' => array_map(fn($k) => (int) ($custDaily[$k]->cnt ?? 0), $dateKeys),
        ];

        // New Vehicles by day
        $vehDaily = Vehicle::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as d')
            ->selectRaw('COUNT(*) as cnt')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');
        $vehiclesChart = [
            'labels' => $labels,
            'count' => array_map(fn($k) => (int) ($vehDaily[$k]->cnt ?? 0), $dateKeys),
        ];

        return view('Statistics.overview', compact(
            'days',
            'paymentsChart', 'invoicesChart', 'grnChart', 'poChart', 'returnsChart', 'customersChart', 'vehiclesChart'
        ));
    }

    public function reports(Request $request): View
    {
        $type = $request->get('type');
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $columns = [];
        $rows = [];
        if ($type) {
            [$columns, $rows] = $this->buildReportData($type, $dateFrom, $dateTo, 200);
        }

        return view('Statistics.reports', compact('type', 'dateFrom', 'dateTo', 'columns', 'rows'));
    }

    public function exportReport(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:payments,invoices,grn,purchase_returns,purchase_orders,customers,vehicles',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'nullable|in:csv',
        ]);

        $type = $validated['type'];
        $from = $validated['date_from'];
        $to = $validated['date_to'];
        $format = $validated['format'] ?? 'csv';

        // Build report data
        [$columns, $rows, $filename] = $this->buildReportDataFull($type, $from, $to);

        if ($format === 'csv') {
            $csv = $this->toCsvWithHeaders($columns, $rows);
            $name = $filename . '_' . now()->format('Ymd_His') . '.csv';
            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $name . '"');
        }

        if ($format === 'pdf') {
            $title = ucfirst(str_replace('_', ' ', $filename)) . ' Report';
            $data = compact('title', 'columns', 'rows') + ['dateFrom' => $from, 'dateTo' => $to];
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Statistics.reports_pdf', $data)->setPaper('a4', 'portrait');
            $name = $filename . '_' . now()->format('Ymd_His') . '.pdf';
            return $pdf->download($name);
        }

        abort(400, 'Unsupported format');
    }

    private function toCsvWithHeaders(array $headers, array $rows): string
    {
        $fp = fopen('php://temp', 'r+');
        if (empty($headers)) { $headers = ['No data']; }
        fputcsv($fp, $headers);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);
        return $csv;
    }

    private function buildReportData(string $type, string $from, string $to, int $limit = 0): array
    {
        [$columns, $rows] = [[], []];

        switch ($type) {
            case 'payments':
                $columns = ['Date','Transaction No','Type','Amount','Method','Category','Customer','Supplier','Status','Description'];
                $query = PaymentTransaction::with(['paymentMethod','paymentCategory','customer','supplier'])
                    ->whereBetween('transaction_date', [$from, $to])
                    ->orderBy('transaction_date');
                if ($limit > 0) { $query->limit($limit); }
                $rows = $query->get()->map(function ($t) {
                    return [
                        optional($t->transaction_date)->format('Y-m-d'),
                        $t->transaction_no,
                        $t->type,
                        (float) $t->amount,
                        $t->paymentMethod->name ?? '',
                        $t->paymentCategory->name ?? '',
                        $t->customer->name ?? '',
                        $t->supplier->Supp_Name ?? '',
                        $t->status,
                        $t->description,
                    ];
                })->toArray();
                break;
            case 'invoices':
                $columns = ['Date','Invoice No','Customer','Grand Total','Status'];
                $query = SalesInvoice::with('customer')->whereBetween('invoice_date', [$from, $to])->orderBy('invoice_date');
                if ($limit > 0) { $query->limit($limit); }
                $rows = $query->get()->map(function ($inv) {
                    return [
                        optional($inv->invoice_date)->format('Y-m-d'),
                        $inv->invoice_no,
                        $inv->customer->name ?? '',
                        (float) $inv->grand_total,
                        $inv->status,
                    ];
                })->toArray();
                break;
            case 'grn':
                $columns = ['Date','GRN No','Supplier','Total','Status'];
                $query = GRN::with(['items','supplier'])->whereBetween('grn_date', [$from, $to])->orderBy('grn_date');
                if ($limit > 0) { $query->limit($limit); }
                $rows = $query->get()->map(function ($g) {
                    $total = (float) $g->items->sum('line_total');
                    return [
                        optional($g->grn_date)->format('Y-m-d'),
                        $g->grn_no,
                        $g->supplier->Supp_Name ?? '',
                        $total,
                        $g->status,
                    ];
                })->toArray();
                break;
            case 'purchase_returns':
                $columns = ['Date','Return No','Supplier','Total','Status'];
                $query = PurchaseReturn::with(['items','supplier'])->whereBetween('created_at', [$from, $to])->orderBy('created_at');
                if ($limit > 0) { $query->limit($limit); }
                $rows = $query->get()->map(function ($r) {
                    return [
                        optional($r->created_at)->format('Y-m-d'),
                        $r->return_no,
                        $r->supplier->Supp_Name ?? '',
                        (float) $r->getTotalAmount(),
                        $r->status ? 'Completed' : 'Pending',
                    ];
                })->toArray();
                break;
            case 'purchase_orders':
                $columns = ['Date','PO No','Supplier ID','Total','Status'];
                $query = Po::whereBetween('po_date', [$from, $to])->orderBy('po_date');
                if ($limit > 0) { $query->limit($limit); }
                $rows = $query->get()->map(function ($po) {
                    return [
                        optional($po->po_date)->format('Y-m-d'),
                        $po->po_No,
                        $po->supp_Cus_ID,
                        (float) $po->grand_Total,
                        $po->status,
                    ];
                })->toArray();
                break;
            case 'customers':
                $columns = ['Date','Customer ID','Name','Phone','Email','Status'];
                $query = Customer::whereBetween('created_at', [$from, $to])->orderBy('created_at');
                if ($limit > 0) { $query->limit($limit); }
                $rows = $query->get()->map(function ($c) {
                    return [
                        optional($c->created_at)->format('Y-m-d'),
                        $c->custom_id,
                        $c->name,
                        $c->phone,
                        $c->email,
                        $c->status ? 'Active' : 'Inactive',
                    ];
                })->toArray();
                break;
            case 'vehicles':
                $columns = ['Date','Vehicle No','Customer','Model','Approved'];
                $query = Vehicle::with('customer')->whereBetween('created_at', [$from, $to])->orderBy('created_at');
                if ($limit > 0) { $query->limit($limit); }
                $rows = $query->get()->map(function ($v) {
                    return [
                        optional($v->created_at)->format('Y-m-d'),
                        $v->vehicle_no,
                        $v->customer->name ?? '',
                        $v->model,
                        $v->is_approved ? 'Yes' : 'No',
                    ];
                })->toArray();
                break;
        }

        return [$columns, $rows];
    }

    private function buildReportDataFull(string $type, string $from, string $to): array
    {
        [$columns, $rows] = $this->buildReportData($type, $from, $to, 0);
        $filename = match($type) {
            'payments' => 'payments',
            'invoices' => 'invoices',
            'grn' => 'grn',
            'purchase_returns' => 'purchase_returns',
            'purchase_orders' => 'purchase_orders',
            'customers' => 'customers',
            'vehicles' => 'vehicles',
            default => 'report',
        };
        return [$columns, $rows, $filename];
    }
}


