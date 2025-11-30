<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\BarangTitipan;
use App\Models\BarangTemuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BarangController extends Controller
{
    /**
     * Menampilkan halaman index (barang aktif & riwayat)
     */
    public function index(Request $request)
    {
        // 1. Ambil barang titipan yang belum selesai
        $barang_titipan = BarangTitipan::where('status', 'Belum Selesai')
            ->orderBy('waktu_titip', 'desc')
            ->get();

        // 2. Ambil barang temuan yang belum selesai
        $barang_temuan = BarangTemuan::where('status', 'Belum Selesai')
            ->orderBy('waktu_lapor', 'desc')
            ->get();

        // 3. Filter riwayat
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori_filter = $request->input('kategori', 'semua'); // semua, titipan, temuan
        $search_filter = $request->input('search');

        // 4. Query riwayat berdasarkan kategori
        $riwayat_barang = collect();

        if ($kategori_filter === 'semua' || $kategori_filter === 'titipan') {
            $titipan = BarangTitipan::where('status', 'Selesai')
                ->whereDate('waktu_selesai', $tanggal_riwayat);

            if ($search_filter) {
                $titipan->where(function ($q) use ($search_filter) {
                    $search_upper = strtoupper($search_filter);
                    $q->where('nama_barang', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('nama_penitip', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('nama_penerima', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('tujuan', 'LIKE', '%' . $search_filter . '%')
                        ->orWhereRaw("nama_penitip REGEXP ?", [
                            '(^|[[:space:]])' . preg_quote($search_upper, '/')
                        ])
                        ->orWhereRaw("nama_penerima REGEXP ?", [
                            '(^|[[:space:]])' . preg_quote($search_upper, '/')
                        ]);
                });
            }

            $riwayat_barang = $riwayat_barang->merge($titipan->get());
        }

        if ($kategori_filter === 'semua' || $kategori_filter === 'temuan') {
            $temuan = BarangTemuan::where('status', 'Selesai')
                ->whereDate('waktu_selesai', $tanggal_riwayat);

            if ($search_filter) {
                $temuan->where(function ($q) use ($search_filter) {
                    $search_upper = strtoupper($search_filter);
                    $q->where('nama_barang', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('nama_pelapor', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('nama_penerima', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('lokasi_penemuan', 'LIKE', '%' . $search_filter . '%')
                        ->orWhereRaw("nama_pelapor REGEXP ?", [
                            '(^|[[:space:]])' . preg_quote($search_upper, '/')
                        ])
                        ->orWhereRaw("nama_penerima REGEXP ?", [
                            '(^|[[:space:]])' . preg_quote($search_upper, '/')
                        ]);
                });
            }

            $riwayat_barang = $riwayat_barang->merge($temuan->get());
        }

        // Sort by waktu_selesai descending
        $riwayat_barang = $riwayat_barang->sortByDesc('waktu_selesai');

        return view('anggota.barang-index', [
            'barang_titipan' => $barang_titipan,
            'barang_temuan' => $barang_temuan,
            'riwayat_barang' => $riwayat_barang,
            'tanggal_terpilih' => $tanggal_riwayat,
            'kategori_terpilih' => $kategori_filter,
            'search_filter' => $search_filter,
        ]);
    }

    /**
     * Form create barang
     */
    public function create()
    {
        return view('anggota.barang-create');
    }

    /**
     * Menyimpan barang baru ke tabel yang benar
     */
    public function store(Request $request)
    {
        // Validasi data umum
        $request->validate([
            'kategori' => 'required|in:titipan,temuan',
            'nama_barang' => 'required|string|max:255',
            'nama_pelapor' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'lokasi_tujuan' => 'required',
            'foto_base64' => 'required',// <-- PERUBAHAN 1: Validasi sebagai string (Base64)
            // 'tanggal' => 'nullable|date', // Tambahkan ini jika Anda tetap menggunakan field tanggal
        ]);

        try {
            // 1. Decode Foto Base64
            $image_parts = explode(";base64,", $request->foto_base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);

            // 2. Simpan Foto
            $fileName = 'barang_' . uniqid() . '.' . $image_type;
            $path = 'foto_barang/' . $fileName;
            Storage::disk('public')->put($path, $image_base64);

            // 3. Logika Simpan Berdasarkan Kategori
            if ($request->kategori == 'temuan') {
                BarangTemuan::create([
                    'nama_barang' => $request->nama_barang,
                    'nama_pelapor' => $request->nama_pelapor,
                    'lokasi_penemuan' => $request->lokasi_tujuan, // Mapping field
                    'waktu_lapor' => now(), // Atau ambil dari request jika ada input tanggal manual
                    'foto' => $path,
                    'catatan' => $request->catatan,
                    'status' => 'belum selesai', // Sesuaikan dengan struktur DB Anda
                    'id_pengguna' => Auth::id(), // Jika ada kolom relasi user
                ]);
            } else {
                // Barang Titipan
                BarangTitipan::create([
                    'nama_barang' => $request->nama_barang,
                    'nama_penitip' => $request->nama_pelapor, // Mapping field
                    'tujuan' => $request->lokasi_tujuan,     // Mapping field
                    'waktu_titip' => now(),
                    'foto' => $path,
                    'catatan' => $request->catatan,
                    'status' => 'belum selesai',
                    'id_pengguna' => Auth::id(), // Jika ada kolom relasi user
                ]);
            }

            return redirect()->route('anggota.barang.index')->with('success', 'Data barang berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Menandai barang TITIPAN sebagai "Selesai"
     */
    public function selesaiTitipan(Request $request, $id_barang)
    {
        $request->validate([
            'nama_penerima' => 'required',
            'foto_penerima_base64' => 'required', // Foto bukti serah terima
            'waktu_selesai' => 'required|date',
            'waktu_ambil' => 'required',
        ]);

        try {
            // 1. Proses Foto Penerima
            $image_parts = explode(";base64,", $request->foto_penerima_base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);

            $fileName = 'penerima_titipan_' . uniqid() . '.' . $image_type;
            $path = 'foto_penerima/' . $fileName;
            Storage::disk('public')->put($path, $image_base64);

            // 2. Gabung Waktu
            $waktu_selesai = Carbon::parse($request->waktu_selesai);

            // 3. Update Database
            $barang = BarangTitipan::findOrFail($id_barang);
            $barang->update([
                'nama_penerima' => $request->nama_penerima,
                'foto_penerima' => $path,
                'waktu_selesai' => $waktu_selesai, // Pastikan kolom ini ada di DB
                'status' => 'selesai',
            ]);

            return redirect()->back()->with('success', 'Barang titipan telah diselesaikan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Menandai barang TEMUAN sebagai "Selesai" (Diperbarui untuk Base64)
     */
    public function selesaiTemuan(Request $request, $id_barang)
    {
        $request->validate([
            'nama_penerima' => 'required',
            'foto_penerima_base64' => 'required',
            'waktu_selesai' => 'required|date',
            'waktu_ambil' => 'required',
        ]);

        try {
            // 1. Proses Foto Penerima
            $image_parts = explode(";base64,", $request->foto_penerima_base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);

            $fileName = 'penerima_temuan_' . uniqid() . '.' . $image_type;
            $path = 'foto_penerima/' . $fileName;
            Storage::disk('public')->put($path, $image_base64);

            // 2. Gabung Waktu
            $waktu_selesai = Carbon::parse($request->waktu_selesai . ' ' . $request->waktu_ambil);

            // 3. Update Database
            $barang = BarangTemuan::findOrFail($id_barang);
            $barang->update([
                'nama_penerima' => $request->nama_penerima,
                'foto_penerima' => $path,
                'waktu_selesai' => $waktu_selesai,
                'status' => 'selesai',
            ]);

            return redirect()->back()->with('success', 'Barang temuan telah diselesaikan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * ▼▼▼ NEW: API untuk dropdown suggestion ▼▼▼
     */
    public function searchBarang(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:50',
            'tanggal' => 'nullable|date',
            'kategori' => 'nullable|string|in:semua,titipan,temuan',
        ]);

        $searchTerm = $request->input('search');
        $tanggal = $request->input('tanggal');
        $kategori = $request->input('kategori', 'semua');

        if (empty($searchTerm)) {
            return response()->json([]);
        }

        $searchTerm = trim($searchTerm);
        $searchUpper = strtoupper($searchTerm);
        $results = collect();

        // Search dari riwayat barang yang sudah selesai
        if ($tanggal) {
            if ($kategori === 'semua' || $kategori === 'titipan') {
                $titipan = BarangTitipan::where('status', 'Selesai')
                    ->whereDate('waktu_selesai', $tanggal)
                    ->where(function ($q) use ($searchTerm, $searchUpper) {
                        $q->where('nama_barang', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhere('nama_penitip', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhere('nama_penerima', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhereRaw("nama_penitip REGEXP ?", [
                                '(^|[[:space:]])' . preg_quote($searchUpper, '/')
                            ])
                            ->orWhereRaw("nama_penerima REGEXP ?", [
                                '(^|[[:space:]])' . preg_quote($searchUpper, '/')
                            ]);
                    })
                    ->select('id_barang', 'nama_barang', 'nama_penitip as nama_pelapor', 'nama_penerima')
                    ->selectRaw("'titipan' as kategori")
                    ->take(5)
                    ->get();

                $results = $results->merge($titipan);
            }

            if ($kategori === 'semua' || $kategori === 'temuan') {
                $temuan = BarangTemuan::where('status', 'Selesai')
                    ->whereDate('waktu_selesai', $tanggal)
                    ->where(function ($q) use ($searchTerm, $searchUpper) {
                        $q->where('nama_barang', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhere('nama_pelapor', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhere('nama_penerima', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhereRaw("nama_pelapor REGEXP ?", [
                                '(^|[[:space:]])' . preg_quote($searchUpper, '/')
                            ])
                            ->orWhereRaw("nama_penerima REGEXP ?", [
                                '(^|[[:space:]])' . preg_quote($searchUpper, '/')
                            ]);
                    })
                    ->select('id_barang', 'nama_barang', 'nama_pelapor', 'nama_penerima')
                    ->selectRaw("'temuan' as kategori")
                    ->take(5)
                    ->get();

                $results = $results->merge($temuan);
            }

            return response()->json($results->take(10));
        }

        return response()->json([]);
    }

    /**
     * ▼▼▼ NEW: AJAX endpoint untuk live search riwayat ▼▼▼
     */
    public function getRiwayat(Request $request)
    {
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori_filter = $request->input('kategori', 'semua');
        $search_filter = $request->input('search');

        $riwayat_barang = collect();

        if ($kategori_filter === 'semua' || $kategori_filter === 'titipan') {
            $titipan = BarangTitipan::where('status', 'Selesai')
                ->whereDate('waktu_selesai', $tanggal_riwayat);

            if ($search_filter) {
                $titipan->where(function ($q) use ($search_filter) {
                    $search_upper = strtoupper($search_filter);
                    $q->where('nama_barang', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('nama_penitip', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('nama_penerima', 'LIKE', '%' . $search_filter . '%')
                        ->orWhereRaw("nama_penitip REGEXP ?", [
                            '(^|[[:space:]])' . preg_quote($search_upper, '/')
                        ])
                        ->orWhereRaw("nama_penerima REGEXP ?", [
                            '(^|[[:space:]])' . preg_quote($search_upper, '/')
                        ]);
                });
            }

            $riwayat_barang = $riwayat_barang->merge($titipan->get());
        }

        if ($kategori_filter === 'semua' || $kategori_filter === 'temuan') {
            $temuan = BarangTemuan::where('status', 'Selesai')
                ->whereDate('waktu_selesai', $tanggal_riwayat);

            if ($search_filter) {
                $temuan->where(function ($q) use ($search_filter) {
                    $search_upper = strtoupper($search_filter);
                    $q->where('nama_barang', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('nama_pelapor', 'LIKE', '%' . $search_filter . '%')
                        ->orWhere('nama_penerima', 'LIKE', '%' . $search_filter . '%')
                        ->orWhereRaw("nama_pelapor REGEXP ?", [
                            '(^|[[:space:]])' . preg_quote($search_upper, '/')
                        ])
                        ->orWhereRaw("nama_penerima REGEXP ?", [
                            '(^|[[:space:]])' . preg_quote($search_upper, '/')
                        ]);
                });
            }

            $riwayat_barang = $riwayat_barang->merge($temuan->get());
        }

        $riwayat_barang = $riwayat_barang->sortByDesc('waktu_selesai');

        return view('anggota.barang-riwayat-cards', [
            'riwayat_barang' => $riwayat_barang
        ])->render();
    }
}