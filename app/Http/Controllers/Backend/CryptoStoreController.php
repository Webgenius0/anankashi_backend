<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CryptoStore;
use Illuminate\Http\Request;

class CryptoStoreController extends Controller
{
    // ===================== INDEX (Yajra DataTable inline) =====================
   public function index(Request $request)
{
    if ($request->ajax()) {
        return datatables()->eloquent(CryptoStore::query())
            ->addColumn('image', function ($row) {
                return $row->image
                    ? '<img src="' . asset($row->image) . '" width="60" class="img-thumbnail">'
                    : '—';
            })
            ->editColumn('type', function ($row) {
                return str_replace('_', ' ', ucwords($row->type ?? ''));
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y');
            })
            ->editColumn('rating', function ($row) {
                if (!$row->rating) return '—';
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= $i <= $row->rating
                        ? '<i class="fe fe-star text-warning"></i>'
                        : '<i class="fe fe-star text-muted"></i>';
                }
                return '<span>' . $stars . ' (' . number_format($row->rating, 1) . ')</span>';
            })
            ->addColumn('action', function ($row) {
                $imageUrl = $row->image ? asset($row->image) : '';
                $review   = addslashes($row->review ?? '');
                $name     = addslashes($row->name);
                $title    = addslashes($row->title);

                return '
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-primary"
                            onclick="openEditModal(
                                \'' . $row->id . '\',
                                \'' . $name . '\',
                                \'' . $title . '\',
                                \'' . $row->type . '\',
                                \'' . $row->rating . '\',
                                \'' . $review . '\',
                                \'' . $imageUrl . '\'
                            )">
                            <i class="fe fe-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-danger"
                            onclick="openDeleteModal(\'' . $row->id . '\', \'' . $name . '\')">
                            <i class="fe fe-trash-2"></i> Delete
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['image', 'rating', 'action'])
            ->toJson();
    }

    return view('backend.layouts.cryptostores.index');
}

    // ===================== CREATE =====================

    // ===================== STORE =====================
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'title'   => 'required|string|max:255',
            'image'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'review'  => 'nullable|string',
            'rating'  => 'nullable|numeric|min:0|max:5',
            'type'    => 'required|in:Tax_advisors,Legal_advisors,Crypto_partners',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $data['image'] = Helper::fileUpload($image, 'crypto_store', $imageName);
        }

        CryptoStore::create($data);

        return redirect()->route('admin.crypto-stores.index')
            ->with('t-success', 'Crypto Store created successfully!');
    }
    // ===================== SHOW =====================
    public function show(CryptoStore $cryptoStore)
    {
        return view('backend.crypto_stores.show', compact('cryptoStore'));
    }

    // ===================== EDIT =====================
    public function edit(CryptoStore $cryptoStore)
    {
        return view('backend.crypto_stores.edit', compact('cryptoStore'));
    }

    // ===================== UPDATE =====================
    public function update(Request $request, $id)
    {

        $request->validate([
            'name'    => 'required|string|max:255',
            'title'   => 'required|string|max:255',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'review'  => 'nullable|string',
            'rating'  => 'nullable|numeric|min:0|max:5',
            'type'    => 'required|in:Tax_advisors,Legal_advisors,Crypto_partners',
        ]);

         $cryptoStore = CryptoStore::findOrFail($id);

        $data = $request->except('image');

        if ($request->hasFile('image')) {

            // delete old image
            if ($cryptoStore->image && file_exists(public_path($cryptoStore->image))) {
                Helper::fileDelete(public_path($cryptoStore->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $data['image'] = Helper::fileUpload($image, 'crypto_store', $imageName);
        }

        $cryptoStore->update($data);

        return redirect()->route('admin.crypto-stores.index')
            ->with('t-success', 'Crypto Store updated successfully!');
    }

    public function destroy($id)
    {
        $cryptoStore = CryptoStore::findOrFail($id);
        if ($cryptoStore->image && file_exists(public_path($cryptoStore->image))) {
            Helper::fileDelete(public_path($cryptoStore->image));
        }

        $cryptoStore->delete();

        return redirect()->route('admin.crypto-stores.index')
            ->with('t-success', 'Crypto Store deleted successfully!');
    }
}
