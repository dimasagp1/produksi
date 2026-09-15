<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\RejectItem; // PENTING: Import model ini
use Illuminate\Http\Request;
use App\Exports\MachinesExport;
use App\Exports\MachineTemplateExport;
use App\Imports\MachinesImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        // LOGIKA SEARCH & FILTER
        // Eager load 'products' DAN 'rejectItems'
        $query = Machine::with(['products', 'rejectItems']);

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $machines = $query->orderBy('id', 'desc')->paginate(10);

        // Append query string agar pagination tidak reset filter
        $machines->appends($request->all());

        // AMBIL DATA REJECT UNTUK DROPDOWN
        $rejectItems = RejectItem::where('status', 'active')->orderBy('name')->get();

        return view('machines.index', compact('machines', 'rejectItems'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'status' => 'required']);

        $machine = Machine::create($request->all());

        // SIMPAN RELASI REJECT ITEMS
        if ($request->has('reject_items')) {
            $machine->rejectItems()->sync($request->reject_items);
        }

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required', 'status' => 'required']);

        $machine = Machine::findOrFail($id);
        $machine->update($request->all());

        // UPDATE RELASI REJECT ITEMS
        // Gunakan sync() untuk otomatis tambah/hapus sesuai pilihan
        $machine->rejectItems()->sync($request->input('reject_items', []));

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil diupdate');
    }

    public function toggleStatus($id)
    {
        $machine = Machine::findOrFail($id);
        $newStatus = ($machine->status == 'active') ? 'inactive' : 'active';
        $machine->status = $newStatus;
        $machine->save();

        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    public function destroy($id)
    {
        $machine = Machine::findOrFail($id);
        $machine->delete();
        return redirect()->route('machines.index')->with('success', 'Mesin berhasil dihapus!');
    }

    // --- IMPORT / EXPORT ---
    public function export($format)
    {
        try {
            if (ob_get_length()) ob_end_clean();
            $fileName = 'data-mesin-' . date('d-m-Y');

            if ($format === 'pdf') {
                $machines = Machine::with(['products', 'rejectItems'])->get(); // Load relasi lengkap
                $pdf = Pdf::loadView('exports.machines', ['machines' => $machines]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($fileName . '.pdf');
            } else {
                return Excel::download(new MachinesExport, $fileName . '.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new MachinesImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Sukses!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import Gagal: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        try {
            if (ob_get_length()) ob_end_clean();
            return Excel::download(new MachineTemplateExport, 'template_import_mesin.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
