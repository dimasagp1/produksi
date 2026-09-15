<?php

namespace App\Http\Controllers;

use App\Models\Coordinator;
use Illuminate\Http\Request;
use App\Exports\CoordinatorsExport;
use App\Exports\CoordinatorTemplateExport;
use App\Imports\CoordinatorsImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class CoordinatorController extends Controller
{
    public function index(Request $request)
    {
        // LOGIKA PENCARIAN & FILTER (Wajib ada untuk fitur Search di View)
        $query = Coordinator::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('filter_status') && $request->filter_status != '') {
            $query->where('status', $request->filter_status);
        }

        $coordinators = $query->orderBy('name')->paginate(15);

        // Agar pagination tidak reset filter saat diklik
        $coordinators->appends([
            'search' => $request->search,
            'filter_status' => $request->filter_status
        ]);

        return view('coordinators.index', compact('coordinators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:coordinators,name',
            'status' => 'required',
        ]);

        Coordinator::create($request->all());
        return redirect()->route('coordinators.index')->with('success', 'Koordinator berhasil ditambahkan');
    }

    public function update(Request $request, Coordinator $coordinator)
    {
        $request->validate([
            'name' => 'required|unique:coordinators,name,' . $coordinator->id,
            'status' => 'required',
        ]);

        $coordinator->update($request->all());
        return redirect()->route('coordinators.index')->with('success', 'Koordinator berhasil diupdate');
    }

    public function destroy(Coordinator $coordinator)
    {
        $coordinator->delete();
        return redirect()->route('coordinators.index')->with('success', 'Koordinator berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $coordinator = Coordinator::findOrFail($id);
        $newStatus = ($coordinator->status == 'active') ? 'inactive' : 'active';
        $coordinator->status = $newStatus;
        $coordinator->save();

        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    // --- IMPORT / EXPORT ---
    public function export($format)
    {
        try {
            if (ob_get_length()) ob_end_clean();
            $fileName = 'data-koordinator-' . date('d-m-Y');

            if ($format === 'pdf') {
                $coordinators = Coordinator::all();
                $pdf = Pdf::loadView('exports.coordinators', ['coordinators' => $coordinators]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($fileName . '.pdf');
            } else {
                return Excel::download(new CoordinatorsExport, $fileName . '.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new CoordinatorsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Sukses!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import Gagal: ' . $e->getMessage());
        }
    }

    public function template()
    {
        try {
            if (ob_get_length()) ob_end_clean();
            return Excel::download(new CoordinatorTemplateExport, 'template_koordinator.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
