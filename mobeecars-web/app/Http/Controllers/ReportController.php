<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data = [
            'favorite_brand' => $this->getMostLiked('brand'),
            'favorite_model' => $this->getMostLiked('model'),
            'favorite_type' => $this->getMostLiked('type'),
        ];

        return view('reports', compact('data'));
    }

    private function getMostLiked($column)
    {
        return DB::table('user_preferences as p')
            ->join('cars as c', 'c.id', '=', 'p.car_id')
            ->where('p.liked', 1)
            ->select("c.$column", DB::raw("COUNT(*) as total"))
            ->groupBy("c.$column")
            ->orderByDesc('total')
            ->value($column);
    }
}
