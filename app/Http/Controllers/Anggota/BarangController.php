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
        // Pagination untuk barang titipan
        $perPage = $request->input('per_page', 5);
        
        // Barang titipan aktif dengan pagination
        $barang_titipan = BarangTitipan::where('status', 'belum selesai')
            ->orderBy('waktu_titip', 'desc')
            ->paginate($perPage, ['*'], 'titipan_page');

        // Barang temuan aktif
        $barang_temuan = BarangTemuan::where('status', 'belum selesai')
            ->orderBy('waktu_lapor', 'desc')
            ->get();

        // 3. Filter riwayat
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori_filter = $request->input('kategori', 'semua'); // semua, titipan, temuan
        $search_filter = $request->input('search');

        // 4. Query riwayat berdasarkan kategori
        $riwayat_barang = collect();

        if ($kategori_filter === 'semua' || $kategori_filter === 'titipan') {
            // Titipan di riwayat
            $titipan = BarangTitipan::where('status', 'selesai')
                ->whereDate('waktu_selesai', $tanggal_riwayat);

            if ($search_filter) {
                $titipan->where(function ($q) use ($search_filter) {
                    $search_lower = strtolower($search_filter);
                    $search_upper = strtoupper($search_filter);
                    $pattern = '(^|[[:space:]])' . preg_quote($search_filter, '/');
                    
                    // Strict search: hanya cocok di awal kata
                    $q->whereRaw("LOWER(nama_barang) REGEXP ?", [$pattern])
                        ->orWhereRaw("LOWER(nama_penitip) REGEXP ?", [$pattern])
                        ->orWhereRaw("LOWER(nama_penerima) REGEXP ?", [$pattern])
                        ->orWhereRaw("LOWER(tujuan) REGEXP ?", [$pattern])
                        // Untuk uppercase juga
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
            // Temuan di riwayat - PERBAIKAN: gunakan 'selesai' dengan huruf kecil
            $temuan = BarangTemuan::where('status', 'selesai')
                ->whereDate('waktu_selesai', $tanggal_riwayat);

            if ($search_filter) {
                $temuan->where(function ($q) use ($search_filter) {
                    $search_lower = strtolower($search_filter);
                    $search_upper = strtoupper($search_filter);
                    $pattern = '(^|[[:space:]])' . preg_quote($search_filter, '/');
                    
                    // Strict search: hanya cocok di awal kata
                    $q->whereRaw("LOWER(nama_barang) REGEXP ?", [$pattern])
                        ->orWhereRaw("LOWER(nama_pelapor) REGEXP ?", [$pattern])
                        ->orWhereRaw("LOWER(nama_penerima) REGEXP ?", [$pattern])
                        ->orWhereRaw("LOWER(lokasi_penemuan) REGEXP ?", [$pattern])
                        // Untuk uppercase juga
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
            'per_page' => $perPage,
        ]);
    }

    /**
     * Form create barang
     */
    public function create()
    {
        return view('anggota.barang-create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:temuan,titipan',
            'foto_base64' => 'required|string',
            'tanggal' => 'required|date',
            'waktu' => 'required|date_format:H:i',
            'nama_barang' => 'required|string|max:150',
            'nama_pelapor' => 'required|string|max:150',
            'lokasi_tujuan' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        // Simpan foto barang
        $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $request->foto_base64);
        $filename = 'barang/' . $request->kategori . '/' . uniqid('barang_') . '.jpg';
        Storage::disk('public')->put($filename, base64_decode($imageData));

        // Gabungkan tanggal + waktu
        $waktu = Carbon::parse($request->tanggal . ' ' . $request->waktu);

        if ($request->kategori === 'titipan') {
            BarangTitipan::create([
                'id_pengguna' => Auth::id(),
                'nama_penitip' => $request->nama_pelapor,
                'nama_barang' => $request->nama_barang,
                'tujuan' => $request->lokasi_tujuan,
                'nama_penerima' => null,
                'foto_penerima' => null,
                'status' => 'belum selesai',
                'foto' => $filename,
                'catatan' => $request->catatan,
                'waktu_titip' => $waktu,
                'waktu_selesai' => null,
            ]);
        } else {
            BarangTemuan::create([
                'id_pengguna' => Auth::id(),
                'nama_barang' => $request->nama_barang,
                'nama_pelapor' => $request->nama_pelapor,
                'lokasi_penemuan' => $request->lokasi_tujuan,
                'nama_penerima' => null,
                'foto_penerima' => null,
                'status' => 'belum selesai',
                'foto' => $filename,
                'catatan' => $request->catatan,
                'waktu_lapor' => $waktu,
                'waktu_selesai' => null,
            ]);
        }

        return redirect()
            ->route('anggota.barang.index')
            ->with('success', 'Data barang berhasil disimpan.');
    }

    /**
     * Menandai barang TITIPAN sebagai "Selesai"
     */
    public function selesaiTitipan(Request $request, $id_barang)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:100',
            'tanggal_ambil' => 'required|date',
            'waktu_ambil' => 'required|date_format:H:i',
            'foto_penerima_base64' => 'nullable|string',
        ]);

        $barang = BarangTitipan::findOrFail($id_barang);

        // Validasi: tanggal ambil tidak boleh sebelum tanggal titip
        $waktuSelesai = Carbon::parse($request->tanggal_ambil . ' ' . $request->waktu_ambil);
        if ($waktuSelesai->lt($barang->waktu_titip)) {
            return redirect()->back()
                ->withErrors(['tanggal_ambil' => 'Tanggal pengambilan tidak boleh sebelum tanggal barang dititipkan.'])
                ->withInput();
        }

        // Simpan foto penerima (jika ada)
        $pathFotoPenerima = $barang->foto_penerima;
        if ($request->filled('foto_penerima_base64')) {
            $imageData = $request->input('foto_penerima_base64');
            $image = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $filename = 'barang/penerima/' . uniqid('penerima_') . '.jpg';
            Storage::disk('public')->put($filename, base64_decode($image));
            $pathFotoPenerima = $filename;
        }

        // Gabungkan tanggal & waktu → kolom waktu_selesai

        $barang->update([
            'nama_penerima' => $request->nama_penerima,
            'foto_penerima' => $pathFotoPenerima,
            'waktu_selesai' => $waktuSelesai,
            'status' => 'selesai',
        ]);

        return redirect()->route('anggota.barang.index')
            ->with('success', 'Barang titipan berhasil diserahkan dan dipindahkan ke riwayat.');
    }

    /**
     * Menandai barang TEMUAN sebagai "Selesai" - DIPERBAIKI
     */
    public function selesaiTemuan(Request $request, $id_barang)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:100',
            'tanggal_ambil' => 'required|date',
            'waktu_ambil' => 'required|date_format:H:i',
            'foto_penerima_base64' => 'nullable|string',
        ]);

        $barang = BarangTemuan::findOrFail($id_barang);

        // Validasi: tanggal ambil tidak boleh sebelum tanggal lapor
        $waktuSelesai = Carbon::parse($request->tanggal_ambil . ' ' . $request->waktu_ambil);
        if ($waktuSelesai->lt($barang->waktu_lapor)) {
            return redirect()->back()
                ->withErrors(['tanggal_ambil' => 'Tanggal pengambilan tidak boleh sebelum tanggal barang ditemukan.'])
                ->withInput();
        }

        // Simpan foto penerima (jika ada)
        $pathFotoPenerima = $barang->foto_penerima;
        if ($request->filled('foto_penerima_base64')) {
            $imageData = $request->input('foto_penerima_base64');
            $image = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $filename = 'barang/penerima/' . uniqid('penerima_') . '.jpg';
            // PERBAIKAN: Gunakan disk('public') agar konsisten
            Storage::disk('public')->put($filename, base64_decode($image));
            $pathFotoPenerima = $filename;
        }

        $waktuSelesai = Carbon::parse($request->tanggal_ambil . ' ' . $request->waktu_ambil);

        // PERBAIKAN: Gunakan 'selesai' dengan huruf kecil (konsisten dengan titipan)
        $barang->update([
            'nama_penerima' => $request->nama_penerima,
            'foto_penerima' => $pathFotoPenerima,
            'waktu_selesai' => $waktuSelesai,
            'status' => 'selesai',
        ]);

        return redirect()->route('anggota.barang.index')
            ->with('success', 'Barang temuan berhasil diserahkan dan dipindahkan ke riwayat.');
    }

    /**
     * API untuk dropdown suggestion
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
                $titipan = BarangTitipan::where('status', 'selesai')
                    ->whereDate('waktu_selesai', $tanggal)
                    ->where(function ($q) use ($searchTerm, $searchUpper) {
                        $pattern = '(^|[[:space:]])' . preg_quote($searchTerm, '/');
                        
                        // Strict search: hanya cocok di awal kata
                        $q->whereRaw("LOWER(nama_barang) REGEXP ?", [$pattern])
                            ->orWhereRaw("LOWER(nama_penitip) REGEXP ?", [$pattern])
                            ->orWhereRaw("LOWER(nama_penerima) REGEXP ?", [$pattern])
                            // Untuk uppercase
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
                // PERBAIKAN: gunakan 'selesai' dengan huruf kecil
                $temuan = BarangTemuan::where('status', 'selesai')
                    ->whereDate('waktu_selesai', $tanggal)
                    ->where(function ($q) use ($searchTerm, $searchUpper) {
                        $pattern = '(^|[[:space:]])' . preg_quote($searchTerm, '/');
                        
                        // Strict search: hanya cocok di awal kata
                        $q->whereRaw("LOWER(nama_barang) REGEXP ?", [$pattern])
                            ->orWhereRaw("LOWER(nama_pelapor) REGEXP ?", [$pattern])
                            ->orWhereRaw("LOWER(nama_penerima) REGEXP ?", [$pattern])
                            // Untuk uppercase
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
     * AJAX endpoint untuk live search riwayat
     */
    public function getRiwayat(Request $request)
    {
        $tanggal_riwayat = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori_filter = $request->input('kategori', 'semua');
        $search_filter = $request->input('search');
        $id_barang = $request->input('id_barang');
        $kategori_barang = $request->input('kategori_barang');

        $riwayat_barang = collect();

        // Jika ada id_barang dan kategori_barang, ambil barang spesifik tersebut
        if ($id_barang && $kategori_barang) {
            if ($kategori_barang === 'titipan') {
                $barang = BarangTitipan::where('id_barang', $id_barang)
                    ->where('status', 'selesai')
                    ->whereDate('waktu_selesai', $tanggal_riwayat)
                    ->first();
                if ($barang) {
                    $riwayat_barang->push($barang);
                }
            } elseif ($kategori_barang === 'temuan') {
                $barang = BarangTemuan::where('id_barang', $id_barang)
                    ->where('status', 'selesai')
                    ->whereDate('waktu_selesai', $tanggal_riwayat)
                    ->first();
                if ($barang) {
                    $riwayat_barang->push($barang);
                }
            }
        } else {
            // Logika pencarian normal (by name)
            if ($kategori_filter === 'semua' || $kategori_filter === 'titipan') {
                $titipan = BarangTitipan::where('status', 'selesai')
                    ->whereDate('waktu_selesai', $tanggal_riwayat);

                if ($search_filter) {
                    $titipan->where(function ($q) use ($search_filter) {
                        $search_lower = strtolower($search_filter);
                        $search_upper = strtoupper($search_filter);
                        $pattern = '(^|[[:space:]])' . preg_quote($search_filter, '/');
                        
                        // Strict search: hanya cocok di awal kata
                        $q->whereRaw("LOWER(nama_barang) REGEXP ?", [$pattern])
                            ->orWhereRaw("LOWER(nama_penitip) REGEXP ?", [$pattern])
                            ->orWhereRaw("LOWER(nama_penerima) REGEXP ?", [$pattern])
                            // Untuk uppercase juga
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
                // PERBAIKAN: gunakan 'selesai' dengan huruf kecil
                $temuan = BarangTemuan::where('status', 'selesai')
                    ->whereDate('waktu_selesai', $tanggal_riwayat);

                if ($search_filter) {
                    $temuan->where(function ($q) use ($search_filter) {
                        $search_lower = strtolower($search_filter);
                        $search_upper = strtoupper($search_filter);
                        $pattern = '(^|[[:space:]])' . preg_quote($search_filter, '/');
                        
                        // Strict search: hanya cocok di awal kata
                        $q->whereRaw("LOWER(nama_barang) REGEXP ?", [$pattern])
                            ->orWhereRaw("LOWER(nama_pelapor) REGEXP ?", [$pattern])
                            ->orWhereRaw("LOWER(nama_penerima) REGEXP ?", [$pattern])
                            // Untuk uppercase juga
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
        }

        $riwayat_barang = $riwayat_barang->sortByDesc('waktu_selesai');

        return view('anggota.barang-riwayat-cards', [
            'riwayat_barang' => $riwayat_barang
        ])->render();
    }
}