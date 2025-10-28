<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GetLinkHistory;

class GetLinkHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = GetLinkHistory::with('user:id,full_name,email');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('full_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pages.get-link-histories.index', compact('histories'));
    }

    public function show($id)
    {
        $history = GetLinkHistory::with('user')->findOrFail($id);
        return view('admin.pages.get-link-histories.show', compact('history'));
    }

    public function destroy($id)
    {
        $history = GetLinkHistory::findOrFail($id);
        $history->delete();

        return redirect()->route('admin.get-link-histories.index')
            ->with('success', 'Xóa lịch sử get link thành công!');
    }
}
