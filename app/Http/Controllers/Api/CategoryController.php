<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

CategoryController:
/**
 * @group Category
 */
class CategoryController extends Controller
{
    /**
     * List all categories
     * 
     * Return all categories.
     */
    public function index()
    {
        $categories = \App\Models\Category::all();

        return response()->json([
            'message' => 'Categorías obtenidas correctamente',
            'data' => $categories,
        ]);
    }
}
