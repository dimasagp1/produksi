<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Exports\ShiftsExport;
use App\Exports\ShiftTemplateExport;
use App\Imports\ShiftsImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        // LOGIKA SEARCH & FILTER (Wajib untuk fitur AJAX Search)
        $query = Shift::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('filter_status') && $request->filter_status != '') {
            $query->where('status', $request->filter_status);
        }

        $shifts = $query->orderBy('name')->paginate(15);

        // Agar pagination tidak reset filter
        $shifts->appends([
            'search' => $request->search,
            'filter_status' => $request->filter_status
        ]);

        return view('shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:shifts,name',
            'status' => 'required',
        ]);

        Shift::create($request->all());
        return redirect()->route('shifts.index')->with('success', 'Shift berhasil ditambahkan');
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|unique:shifts,name,' . $shift->id,
            'status' => 'required',
        ]);

        $shift->update($request->all());
        return redirect()->route('shifts.index')->with('success', 'Shift berhasil diupdate');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('shifts.index')->with('success', 'Shift berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $shift = Shift::findOrFail($id);
        $newStatus = ($shift->status == 'active') ? 'inactive' : 'active';
        $shift->status = $newStatus;
        $shift->save();

        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    // --- IMPORT / EXPORT ---
    public function export($format)
    {
        try {
            if (ob_get_length()) ob_end_clean();
            $fileName = 'data-shift-' . date('d-m-Y');

            if ($format === 'pdf') {
                $shifts = Shift::all();
                $pdf = Pdf::loadView('exports.shifts', ['shifts' => $shifts]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($fileName . '.pdf');
            } else {
                return Excel::download(new ShiftsExport, $fileName . '.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new ShiftsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Sukses!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import Gagal: ' . $e->getMessage());
        }
    }

    public function template()
    {
        try {
            if (ob_get_length()) ob_end_clean();
            return Excel::download(new ShiftTemplateExport, 'template_shift.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
