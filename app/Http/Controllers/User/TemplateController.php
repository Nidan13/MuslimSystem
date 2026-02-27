<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TaskTemplate;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        
        $query = TaskTemplate::query();
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $templates = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }
}
