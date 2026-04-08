<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function communityStats(Request $request)
    {
        $communityId = $request->user()->community_id;
        $totalUsers = \App\Models\User::where('community_id', $communityId)->count();
        $totalNotes = \App\Models\Note::whereHas('user', function ($query) use ($communityId) {
            $query->where('community_id', $communityId);
        })->count();
        $totalComments = \App\Models\Comment::whereHas('note.user', function ($query) use ($communityId) {
            $query->where('community_id', $communityId);
        })->count();
        $totalThanks = \App\Models\Thank::whereHas('note.user', function ($query) use ($communityId) {
            $query->where('community_id', $communityId);
        })->count();

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
