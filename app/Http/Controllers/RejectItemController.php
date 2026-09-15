<?php

namespace App\Http\Controllers;

use App\Models\RejectItem;
use App\Models\RejectCategory;
use Illuminate\Http\Request;
use App\Exports\RejectItemsExport;
use App\Exports\RejectTemplateExport;
use App\Imports\RejectItemsImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RejectItemController extends Controller
{
    public function index(Request $request)
    {
        // LOGIKA SEARCH & FILTER
        $query = RejectItem::with('category');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('filter_status') && $request->filter_status != '') {
            $query->where('status', $request->filter_status);
        }

        $rejectItems = $query->orderBy('name')->paginate(15);
        $categories = RejectCategory::where('status', 'active')->get();

        // Append query string agar pagination tidak reset filter
        $rejectItems->appends([
            'search' => $request->search,
            'filter_status' => $request->filter_status
        ]);

        return view('reject-items.index', compact('rejectItems', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:reject_items,name',
            'category_id' => 'required|exists:reject_categories,id',
            'status' => 'required',
        ]);

        RejectItem::create($request->all());
        return redirect()->route('reject-items.index')->with('success', 'Item Reject berhasil ditambahkan');
    }

    public function update(Request $request, RejectItem $rejectItem)
    {
        $request->validate([
            'name' => 'required|unique:reject_items,name,' . $rejectItem->id,
            'category_id' => 'required|exists:reject_categories,id',
            'status' => 'required',
        ]);

        $rejectItem->update($request->all());
        return redirect()->route('reject-items.index')->with('success', 'Item Reject berhasil diupdate');
    }

    public function destroy(RejectItem $rejectItem)
    {
        $rejectItem->delete();
        return redirect()->route('reject-items.index')->with('success', 'Item Reject berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $item = RejectItem::findOrFail($id);
        $newStatus = ($item->status == 'active') ? 'inactive' : 'active';
        $item->status = $newStatus;
        $item->save();

        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    // --- IMPORT / EXPORT ---
    public function export($format)
    {
        try {
            if (ob_get_length()) ob_end_clean();
            $fileName = 'data-reject-items-' . date('d-m-Y');

            if ($format === 'pdf') {
                $rejectItems = RejectItem::with('category')->get();
                $pdf = Pdf::loadView('exports.reject_items', ['rejectItems' => $rejectItems]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($fileName . '.pdf');
            } else {
                return Excel::download(new RejectItemsExport, $fileName . '.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new RejectItemsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Sukses!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import Gagal: ' . $e->getMessage());
        }
    }

    public function template()
    {
        try {
            if (ob_get_length()) ob_end_clean();
            return Excel::download(new RejectTemplateExport, 'template_reject_item.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
