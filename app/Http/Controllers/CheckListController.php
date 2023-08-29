<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\CheckList;
use App\Models\WorkToDo;

class CheckListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $results = CheckList::all();
        // return response()->json($results);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $title   = htmlspecialchars($request->input('title'));
        $work_id = $request->input('id');
        $slug    = Str::slug($title);
        if (CheckList::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . uniqid();
        }
        $checklist = new CheckList([
            'title'         => $title,
            'work_todo_id'  => $work_id,
            'list_user_ids' => "",
            'slug'          => $slug,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        $checklist->save();
        $data = CheckList::find($checklist->id);
        $data['status'] = $data->status ? true : false;
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $data = $request->input('data');
        if (!empty($data)) {
            $results = CheckList::where('id', $id)->update($data);
        }
        return response()->json(CheckList::find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CheckList::destroy($id);
    }
}
