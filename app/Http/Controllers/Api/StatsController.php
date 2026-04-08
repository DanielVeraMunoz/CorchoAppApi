<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function communityStats(Request $request)
    {
        $totalUsers = \App\Models\User::count();
        $totalNotes = \App\Models\Note::count();
        $totalComments = \App\Models\Comment::count();
        $totalThanks = \App\Models\Thank::count();

        return response()->json([
            'message' => 'Estadísticas de la comunidad obtenidas correctamente',
            'data' => [
                'total_users' => $totalUsers,
                'total_notes' => $totalNotes,
                'total_comments' => $totalComments,
                'total_thanks' => $totalThanks,
            ]
        ], 200);
    }
}
