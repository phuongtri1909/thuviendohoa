<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with('admin');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pages.feedback.index', compact('feedbacks'));
    }

    public function show($id)
    {
        $feedback = Feedback::with('admin')->findOrFail($id);
        
        // Mark as read if still pending
        if ($feedback->status === Feedback::STATUS_PENDING) {
            $feedback->markAsRead();
        }

        return view('admin.pages.feedback.show', compact('feedback'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string|min:10|max:1000',
        ], [
            'admin_reply.required' => 'Vui lòng nhập nội dung phản hồi',
            'admin_reply.min' => 'Nội dung phản hồi phải có ít nhất 10 ký tự',
            'admin_reply.max' => 'Nội dung phản hồi không được quá 1000 ký tự',
        ]);

        $feedback = Feedback::findOrFail($id);
        
        $feedback->reply(Auth::id(), $request->admin_reply);

        return redirect()->back()->with('success', 'Đã phản hồi góp ý thành công!');
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return redirect()->route('admin.feedback.index')->with('success', 'Đã xóa góp ý thành công!');
    }

    public function markAsRead($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->markAsRead();

        return redirect()->back()->with('success', 'Đã đánh dấu đã đọc!');
    }
}