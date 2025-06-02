<?php

namespace App\Http\Controllers\FrontPanel;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class CVSmartRewardController extends Controller
{
    public function index(Request $request, Location $location){
        dd($request->all(), $location);
    }
}
