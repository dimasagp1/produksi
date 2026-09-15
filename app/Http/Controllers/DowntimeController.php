<?php

namespace App\Http\Controllers;

use App\Models\Downtime;
use Illuminate\Http\Request;
use App\Exports\DowntimesExport;
use App\Exports\DowntimeTemplateExport;
use App\Imports\DowntimesImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DowntimeController extends Controller
{
    public function index(Request $request)
    {
        // LOGIKA SEARCH & FILTER
        $query = Downtime::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('filter_status') && $request->filter_status != '') {
            $query->where('status', $request->filter_status);
        }

        $downtimes = $query->orderBy('name')->paginate(15);

        // Append query string agar pagination tidak reset filter
        $downtimes->appends([
            'search' => $request->search,
            'filter_status' => $request->filter_status
        ]);

        return view('downtimes.index', compact('downtimes'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:downtimes,name', 'status' => 'required']);
        Downtime::create($request->all());
        return redirect()->route('downtimes.index')->with('success', 'Alasan Downtime berhasil ditambahkan');
    }

    public function update(Request $request, Downtime $downtime)
    {
        $request->validate(['name' => 'required|unique:downtimes,name,' . $downtime->id, 'status' => 'required']);
        $downtime->update($request->all());
        return redirect()->route('downtimes.index')->with('success', 'Alasan Downtime berhasil diupdate');
    }

    public function destroy(Downtime $downtime)
    {
        $downtime->delete();
        return redirect()->route('downtimes.index')->with('success', 'Alasan Downtime berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $downtime = Downtime::findOrFail($id);
        $newStatus = ($downtime->status == 'active') ? 'inactive' : 'active';
        $downtime->status = $newStatus;
        $downtime->save();

        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    // --- IMPORT / EXPORT ---
    public function export($format)
    {
        try {
            if (ob_get_length()) ob_end_clean();
            $fileName = 'data-downtime-' . date('d-m-Y');

            if ($format === 'pdf') {
                $downtimes = Downtime::all();
                $pdf = Pdf::loadView('exports.downtimes', ['downtimes' => $downtimes]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($fileName . '.pdf');
            } else {
                return Excel::download(new DowntimesExport, $fileName . '.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new DowntimesImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Sukses!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import Gagal: ' . $e->getMessage());
        }
    }

    public function template()
    {
        try {
            if (ob_get_length()) ob_end_clean();
            return Excel::download(new DowntimeTemplateExport, 'template_downtime.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
