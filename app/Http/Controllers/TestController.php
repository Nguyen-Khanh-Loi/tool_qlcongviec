<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tasks;

class TestController extends Controller
{
    //
    public function test(Request $request)
    {  
        $task = Tasks::get();
        return response()->json($task);

    }
}
