<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use App\Exports\ColorsExport;
use App\Exports\ColorTemplateExport;
use App\Imports\ColorsImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        // LOGIKA SEARCH & FILTER
        $query = Color::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('filter_status') && $request->filter_status != '') {
            $query->where('status', $request->filter_status);
        }

        $colors = $query->orderBy('name')->paginate(15);

        // Append query string agar pagination tidak reset filter
        $colors->appends([
            'search' => $request->search,
            'filter_status' => $request->filter_status
        ]);

        return view('colors.index', compact('colors'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:colors,name', 'status' => 'required']);
        Color::create($request->all());
        return redirect()->route('colors.index')->with('success', 'Warna berhasil ditambahkan');
    }

    public function update(Request $request, Color $color)
    {
        $request->validate(['name' => 'required|unique:colors,name,' . $color->id, 'status' => 'required']);
        $color->update($request->all());
        return redirect()->route('colors.index')->with('success', 'Warna berhasil diupdate');
    }

    public function destroy(Color $color)
    {
        $color->delete();
        return redirect()->route('colors.index')->with('success', 'Warna berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $color = Color::findOrFail($id);
        $newStatus = ($color->status == 'active') ? 'inactive' : 'active';
        $color->status = $newStatus;
        $color->save();

        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    // --- IMPORT / EXPORT ---
    public function export($format)
    {
        try {
            if (ob_get_length()) ob_end_clean();
            $fileName = 'data-warna-' . date('d-m-Y');

            if ($format === 'pdf') {
                $colors = Color::all();
                $pdf = Pdf::loadView('exports.colors', ['colors' => $colors]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($fileName . '.pdf');
            } else {
                return Excel::download(new ColorsExport, $fileName . '.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new ColorsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Sukses!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import Gagal: ' . $e->getMessage());
        }
    }

    public function template()
    {
        try {
            if (ob_get_length()) ob_end_clean();
            return Excel::download(new ColorTemplateExport, 'template_warna.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
