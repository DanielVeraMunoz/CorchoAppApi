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

        $totalThanks = \App\Models\Thank::whereHas('recipient', function ($query) use ($communityId) {
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

    public function topHelpers(Request $request)
    {
        $communityId = $request->user()->community_id;

        $topGeneralHelpers = \App\Models\User::where('community_id', $communityId)
            ->withCount('receivedThanks')
            ->orderBy('received_thanks_count', 'desc')
            ->take(5)
            ->get();

        $result = [];
        foreach ($topGeneralHelpers as $user) {
            $result[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'thanks_count' => $user->received_thanks_count,
            ];
        }

        $categories = \App\Models\Category::all();

        $topByCategory = [];

        foreach ($categories as $category) {
            $topUsers = \App\Models\User::where('community_id', $communityId)
                ->withCount(['receivedThanks' => function ($query) use ($category) {
                    $query->whereHas('note', function ($q) use ($category) {
                        $q->where('category_id', $category->id);
                    });
                }])
                ->orderBy('received_thanks_count', 'desc')
                ->take(3)
                ->get();

            $topByCategory[] = [
                'category_id'   => (string) $category->id,
                'category_name' => $category->name,
                'helpers'       => $topUsers->map(fn($user) => [
                    'user_id'      => (string) $user->id,
                    'name'         => $user->name,
                    'thanks_count' => $user->received_thanks_count,
                ]),
            ];
        }



        return response()->json([
            'message' => 'Top helpers obtenidos correctamente',
            'data' => [
                'top_general_helpers' => $result,
                'top_by_category_helpers' => $topByCategory
            ]
        ], 200);
    }
}
