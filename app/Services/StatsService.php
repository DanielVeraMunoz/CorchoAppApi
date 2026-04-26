<?php

namespace App\Services;

use App\Models\User;
use App\Models\Note;
use App\Models\Comment;
use App\Models\Thank;
use App\Models\Category;

class StatsService
{


    public function getCommunityStats($communityId)
    {

        return [
            'total_users' => User::where('community_id', $communityId)->count(),

            'total_notes' => Note::whereHas('user', fn($q) => $q->where('community_id', $communityId))->count(),

            'total_comments' => Comment::whereHas('user', fn($q) => $q->where('community_id', $communityId))->count(),

            'total_thanks' => Thank::whereHas('recipient', fn($q) => $q->where('community_id', $communityId))->count(),

            'community_name' => \App\Models\Community::find($communityId)->name,

        ];
    }

    public function getTopHelpers($communityId)
    {

        $tophelpers = User::where('community_id', $communityId)
            ->withCount('receivedThanks')
            ->orderByDesc('received_thanks_count')
            ->take(5)
            ->get();

        $result = [];

        foreach ($tophelpers as $user) {
            $result[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'thanks_count' => $user->received_thanks_count,
                'role' => $user->role,
            ];
        }

        return $result;

    }
}
