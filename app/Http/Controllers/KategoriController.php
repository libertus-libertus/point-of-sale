<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    // menampilkan seluruh data
    public function index()
    {
        return view('kategori.index');
    }

    // kategori lewat datatables
    public function data()
    {
        $kategori = Kategori::orderBy('id_kategori', 'desc')->get();

        return datatables()
                ->of($kategori)
                ->addIndexColumn()
                ->addColumn('aksi', function ($kategori) {
                    return '
                        <div class="btn-group">
                            <button onclick="editData(`'. route('kategori.update', $kategori->id_kategori) .'`)" class="btn btn-xs btn-flat btn-info"><i class="fa fa-pencil"></i></button>
                            <button onclick="deleteData(`'. route('kategori.destroy', $kategori->id_kategori) .'`)" class="btn btn-xs btn-flat btn-danger"><i class="fa fa-trash"></i></button>
                        </div>
                    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
    }

    // menyimpan data
    public function store(Request $request)
    {
        $request->validate(
            ['nama_kategori' => 'required'],
            ['nama_kategori.required' => 'Kategori wajib diisi'],
        );

        $kategori = new Kategori();
        $kategori->nama_kategori = $request->nama_kategori;
        $kategori->save();

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    // tampil data edit
    public function show(string $id)
    {
        $kategori = Kategori::find($id);

        return response()->json($kategori);
    }

    // ubah data
    public function update(Request $request, string $id)
    {
        $kategori = Kategori::find($id);
        $kategori->nama_kategori = $request->nama_kategori;
        $kategori->update();

        return response()->json(['message' => 'Data berhasil diubah'], 200);
    }

    // hapus data
    public function destroy(string $id)
    {
        $kategori = Kategori::find($id);
        $kategori->delete();

        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }
}
