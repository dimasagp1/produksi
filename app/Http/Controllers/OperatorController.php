<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;
use App\Exports\OperatorsExport;
use App\Exports\OperatorTemplateExport;
use App\Imports\OperatorsImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class OperatorController extends Controller
{

    public function index(Request $request)
    {
        $query = Operator::query();

        // 1. Logika Search (Sudah ada)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // 2. TAMBAHAN: Logika Filter Status
        if ($request->has('filter_status') && $request->filter_status != '') {
            $query->where('status', $request->filter_status);
        }

        $operators = $query->orderBy('name')->paginate(10);

        // Penting: Appends agar filter tetap terbawa saat klik pagination
        $operators->appends([
            'search' => $request->search,
            'filter_status' => $request->filter_status
        ]);

        return view('operators.index', compact('operators'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:operators,name', 'status' => 'required']);
        Operator::create($request->all());
        return redirect()->route('operators.index')->with('success', 'Operator berhasil ditambahkan');
    }


    public function update(Request $request, Operator $operator)
    {
        $request->validate(['name' => 'required|unique:operators,name,' . $operator->id, 'status' => 'required']);
        $operator->update($request->all());
        return redirect()->route('operators.index')->with('success', 'Operator berhasil diupdate');
    }

    public function destroy(Operator $operator)
    {
        $operator->delete();
        return redirect()->route('operators.index')->with('success', 'Operator berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $operator = Operator::findOrFail($id);
        $newStatus = ($operator->status == 'active') ? 'inactive' : 'active';
        $operator->status = $newStatus;
        $operator->save();
        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    public function export($format)
    {
        try {
            if (ob_get_length()) ob_end_clean();
            $fileName = 'data-operator-' . date('d-m-Y');
            if ($format === 'pdf') {
                $operators = Operator::all();
                $pdf = Pdf::loadView('exports.operators', ['operators' => $operators]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($fileName . '.pdf');
            } else {
                // Default ke Excel (XLSX), CSV dihapus
                return Excel::download(new OperatorsExport, $fileName . '.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new OperatorsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Sukses!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import Gagal: ' . $e->getMessage());
        }
    }

    public function template()
    {
        try {
            if (ob_get_length()) ob_end_clean();
            return Excel::download(new OperatorTemplateExport, 'template_operator.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
