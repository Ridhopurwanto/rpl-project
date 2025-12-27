<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
     
    public function markAsRead($id)
    {
        
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            
            $notification->markAsRead();
            
            
            
            
            if (isset($notification->data['url'])) {
                
                return redirect($notification->data['url']);
            }
        }

        
        return redirect()->back();
    }

     
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}