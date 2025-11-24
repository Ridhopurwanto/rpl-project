<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Menandai satu notifikasi spesifik sebagai sudah dibaca
     * dan redirect user ke halaman terkait (jika ada) atau kembali.
     */
    public function markAsRead($id)
    {
        // Cari notifikasi berdasarkan ID milik user yang sedang login
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            // Tandai sebagai 'read' (mengisi kolom read_at di database)
            $notification->markAsRead();
            
            // (Opsional) Jika kamu ingin redirect ke halaman spesifik 
            // berdasarkan data notifikasi, bisa tambahkan logika di sini.
            // Contoh: return redirect($notification->data['url']);
        }

        // Kembali ke halaman sebelumnya (agar user tidak merasa berpindah halaman)
        return redirect()->back();
    }

    /**
     * (Opsional) Fitur tambahan: Tandai SEMUA notifikasi sebagai sudah dibaca
     * Bisa dipanggil lewat tombol "Tandai semua sudah dibaca" di dropdown
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}