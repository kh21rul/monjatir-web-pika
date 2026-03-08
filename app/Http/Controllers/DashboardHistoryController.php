<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\FanLog;
use App\Models\HumidifierLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $controls = Control::latest();

        if (request('filter')) {
            $controls->where('created_at', 'like', '%' . request('filter') . '%');
        } else {
            $controls->where('created_at', 'like', '%' . Carbon::now()->format('Y-m-d') . '%');
        }

        return view('dashboard.histories.index', [
            'title' => 'Dashboard | Histories',
            'controls' => $controls->get(),
        ]);
    }

    public function cetak()
    {
        $controls = Control::latest();

        if (request('filter')) {
            $controls->where('created_at', 'like', '%' . request('filter') . '%');
        } else {
            $controls->where('created_at', 'like', '%' . Carbon::now()->format('Y-m-d') . '%');
        }

        return view('dashboard.histories.cetakhistory', [
            'title' => 'Dashboard | Histories',
            'today' => Carbon::now()->format('Y-m-d'),
            'controls' => $controls->get(),
        ]);
    }

    public function exportCsv(Request $request)
    {
        $filter = $request->input('filter', date('Y-m-d'));
        $controls = Control::whereDate('created_at', $filter)->get();

        $filename = 'rekap-monitoring-' . $filter . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($controls, $filter) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Judul laporan
            fputcsv($file, ['REKAPITULASI MONITORING JAMUR TIRAM'], ';');
            fputcsv($file, ['Tanggal Filter', $filter], ';');
            fputcsv($file, ['Diekspor pada', now()->format('d-m-Y H:i:s') . ' WIB'], ';');
            fputcsv($file, ['Total Data', $controls->count() . ' record'], ';');
            fputcsv($file, [], ';'); // baris kosong pemisah

            // Header kolom
            fputcsv($file, [
                'No',
                'Tanggal',
                'Pukul (WIB)',
                'Suhu (°C)',
                'Kelembapan (%)',
                'Status Kipas',
                'Status Humidifier',
            ], ';');

            // Data rows
            foreach ($controls as $i => $control) {
                fputcsv($file, [
                    $i + 1,
                    $control->created_at->format('d-m-Y'),
                    $control->created_at->format('H:i'),
                    str_replace('.', ',', $control->suhu),        // koma desimal
                    str_replace('.', ',', $control->kelembapan),  // koma desimal
                    $control->kipas,
                    $control->humidifier,
                ], ';');
            }

            // Ringkasan bawah
            fputcsv($file, [], ';');
            fputcsv($file, ['--- Akhir Laporan ---'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Control $control)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Control $control)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Control $control)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Control $control)
    {
        $date = $control->created_at->format('Y-m-d');
        Control::destroy($control->id);
        return redirect('/dashboard/controls?filter=' . $date)->with('success', 'Data berhasil dihapus!');
    }

    public function getfan()
    {
        $histories = FanLog::latest();

        if (request('filter')) {
            $histories->where('created_at', 'like', '%' . request('filter') . '%');
        } else {
            $histories->where('created_at', 'like', '%' . Carbon::now()->format('Y-m-d') . '%');
        }

        return view('dashboard.histories.fan', [
            'title' => 'Dashboard | Histories | Fan',
            'histories' => $histories->get(),
        ]);
    }

    public function deletefan(FanLog $fanLog)
    {
        $date = $fanLog->created_at->format('Y-m-d');
        FanLog::destroy($fanLog->id);
        return redirect('/dashboard/history/fan?filter=' . $date)->with('success', 'Data berhasil dihapus!');
    }

    public function gethumidifier()
    {
        $histories = HumidifierLog::latest();

        if (request('filter')) {
            $histories->where('created_at', 'like', '%' . request('filter') . '%');
        } else {
            $histories->where('created_at', 'like', '%' . Carbon::now()->format('Y-m-d') . '%');
        }

        return view('dashboard.histories.humidifier', [
            'title' => 'Dashboard | Histories | Humidifier',
            'histories' => $histories->get(),
        ]);
    }

    public function deletehumidifier(HumidifierLog $humidifierLog)
    {
        $date = $humidifierLog->created_at->format('Y-m-d');
        HumidifierLog::destroy($humidifierLog->id);
        return redirect('dashboard/history/humidifier?filter=' . $date)->with('success', 'Data berhasil dihapus!');
    }
}
