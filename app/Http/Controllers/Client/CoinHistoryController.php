<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CoinHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoinHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $coinHistories = CoinHistory::where('user_id', $user->id)
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('client.pages.user.coin-history', compact('coinHistories'));
    }

    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        
        if ($request->has('id')) {
            $history = CoinHistory::where('user_id', $user->id)
                ->where('id', $request->id)
                ->first();
                
            if ($history) {
                $history->markAsRead();
                return response()->json(['success' => true]);
            }
        } else {
            CoinHistory::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
                
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 400);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = CoinHistory::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    public function getUnreadHistories()
    {
        $user = Auth::user();
        $histories = CoinHistory::where('user_id', $user->id)
            ->where('is_read', false)
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return response()->json(['histories' => $histories]);
    }
}
