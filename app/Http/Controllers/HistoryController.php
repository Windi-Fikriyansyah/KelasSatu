<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $transactions = DB::table('transactions')
            ->leftJoin('courses', 'transactions.course_id', '=', 'courses.id')
            ->where('transactions.user_id', Auth::id())
            ->select(
                'transactions.*',
                'courses.title' // Ambil nama course
            )
            ->orderByDesc('transactions.created_at')
            ->get();

        return view('history.index', compact('transactions'));
    }
}
