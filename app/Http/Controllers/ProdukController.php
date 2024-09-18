<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    // menampilkan seluruh data
    public function index()
    {
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');
        return view('produk.index', compact('kategori'));
    }

    // kategori lewat datatables
    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            ->orderBy('kode_produk', 'asc')
            ->get();

        return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-info">'. $produk->kode_produk .'</span>';
            })
            ->addColumn('harga_beli', function ($produk) {
                return format_uang($produk->harga_beli);
            })
            ->addColumn('harga_jual', function ($produk) {
                return format_uang($produk->harga_jual);
            })
            ->addColumn('aksi', function ($produk) {
                return '
                        <div class="btn-group">
                            <button onclick="editData(`' . route('produk.update', $produk->id_produk) . '`)" class="btn btn-xs btn-flat btn-warning"><i class="fa fa-pencil"></i> Ubah</button>
                            <button onclick="deleteData(`' . route('produk.destroy', $produk->id_produk) . '`)" class="btn btn-xs btn-flat btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                        </div>
                    ';
            })
            ->rawColumns(['aksi', 'kode_produk'])
            ->make(true);
    }

    // menyimpan data
    public function store(Request $request)
    {
        $produk = Produk::latest()->first();
        $request['kode_produk'] = 'P'. tambah_nol_didepan((int)$produk->id_produk+1, 6);
        $produk = Produk::create($request->all());

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    // tampil data edit
    public function show(string $id)
    {
        $produk = Produk::find($id);

        return response()->json($produk);
    }

    // ubah data
    public function update(Request $request, string $id)
    {
        $produk = Produk::find($id);
        $produk->update($request->all());

        return response()->json(['message' => 'Data berhasil diubah'], 200);
    }

    // hapus data
    public function destroy(string $id)
    {
        $produk = Produk::find($id);
        $produk->delete();

        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }
}
