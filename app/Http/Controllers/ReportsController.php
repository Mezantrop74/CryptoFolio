<?php

namespace App\Http\Controllers;

use App\Domain\Exchange\Api\Profit;
use App\Exports\ExchangeExport;
use Barryvdh\DomPDF\Facade;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class ReportsController extends Controller
{
    public function index()
    {
        $observers = auth()->user()->cryptoObservers()->with('crypto')->get();
        return view('reports.index', compact('observers'));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'observers' => 'array',
            'observers.*' => 'uuid',
            'export_format' => 'required|string|in:pdf,xlsx,csv',
            'date' => 'nullable|array',
            'date[from]' => 'nullable|date',
            'date[to]' => 'nullable|date',
        ]);

        $toDate = null;
        $fromDate = null;
        try {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->date['from']);
        } catch (Exception $e) {
            $fromDate = null;
        }

        try {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->date['to']);
        } catch (Exception $e) {
            $toDate = null;
        }

        $ids = auth()->user()->cryptoObservers()->select('id')->whereIn('observer_id', $request->observers)->pluck('id')->toArray();
        $exchanges = auth()->user()->exchanges()
            ->where(function ($q) use ($toDate, $fromDate) {
                if ($fromDate != null) {
                    $q->where('created_at', '>=', $fromDate);
                }
                if ($toDate != null) {
                    $q->where('created_at', '<=', $toDate);
                }
            })->where(function ($q) use ($ids) {
                $q->whereIntegerInRaw('receiver_crypto_observer_id', $ids);
                $q->orWhereIntegerInRaw('sender_crypto_observer_id', $ids);
            })
            ->with(['receiverCrypto',
                'senderCrypto',
                'receiverObserver',
                'senderObserver',
                'receiverWallet',
                'senderWallet'
            ])->latest()->get();
        if (count($exchanges) == 0) {
            return redirect()->route('reports.index')->with('error', __('Nothing to export.'));
        }
        foreach ($exchanges as $e) {
            $e->profit = (new Profit())->CalculateProfit($e)['amount'];
        }

        $writerType = Excel::CSV;
        switch ($request->export_format) {
            case 'csv':
                $writerType = Excel::CSV;
                break;
            case 'xlsx':
                $writerType = Excel::XLSX;
                break;
        }
        if ($request->export_format == 'pdf') {
            $pdf = Facade::loadView('reports.pdf.exchange', compact('exchanges'));
            return $pdf->setPaper('a3', 'landscape')->download('report.pdf');
        }
        return (new ExchangeExport)->withCollection($exchanges)->download(sprintf('report.%s', $request->export_format), $writerType);
    }
}
