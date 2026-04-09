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


        ];
    }
}
