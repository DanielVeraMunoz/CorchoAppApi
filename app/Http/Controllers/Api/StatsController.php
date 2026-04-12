<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


StatsController:
/**
 * @group Stats
 */

class StatsController extends Controller
{
    /**
     * Community Stats
     * 
     * Returns statistics about the community.
     * 
     */
    public function communityStats(Request $request)
    {
        $communityId = $request->user()->community_id;

        $statsService = new \App\Services\StatsService();
        $stats = $statsService->getCommunityStats($communityId);

        return response()->json([
            'message' => 'Estadísticas de la comunidad obtenidas correctamente',
            'data' => $stats
        ], 200);
    }

    /**
     * Top Helpers
     * 
     * Returns a list of the top helpers in the community.
     * 
     */
    public function topHelpers(Request $request)
    {
        $communityId = $request->user()->community_id;

        $statsService = new \App\Services\StatsService();
        $topHelpers = $statsService->getTopHelpers($communityId);

        return response()->json([
            'message' => 'Top helpers obtenidos correctamente',
            'data' => $topHelpers
        ], 200);
    }
}
