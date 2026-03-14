<?php
/* [NEW] CategoryController.php */
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * List categories by type.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        
        $query = Category::query()->where('is_active', true);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $categories = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
