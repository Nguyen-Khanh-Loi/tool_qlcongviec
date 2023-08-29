<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Tasks;
use App\Models\TaskUser;
use App\Models\Label;
use App\Models\Card;
use App\Models\ProjectUser;
use App\Models\Project;
use App\Models\DepartmentUser;
use App\Http\Traits\TaskProject;

class TaskUserController extends Controller
{
    use TaskProject;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user    = Auth::user();
        $mytasks = User::with('mytasks')->find($user->id);
        $labels  = Label::all();
        $cards   = Card::all();
        $data    = [];
        $results = [];
        if ($mytasks->mytasks()->get()) {
            foreach ($mytasks->mytasks()->get() as $key => $task) {
                $results[$task->task_id] = self::showDataTask($task);
            }
        }
        $data['labels'] = $labels;
        $data['data'] = $results;
        $data['cards'] = $cards;
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

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
    public function update(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $task_id = $request->input('task_id');
        $data    = $request->input('data_update');
        $result  = TaskUser::where([
            ['task_id', '=', $task_id],
            ['user_id', '=', $user_id],
        ])->update($data);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function users(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $roles    = $user->getRoleNames()->first();
        $project_id = $request->input('project_id');
        $department = $request->input('department_id');
        if (empty($department)) {
            $department_id = 1; // set default department (after update)
        }
        $users    = User::with('detail_user_department')->find($user_id);
        if (!in_array($roles, ['manager','administrator']) && empty($department)) {
            $details = $users->detail_user_department;
            $department_id = $details->department_id;
        }
        $users_leader = User::whereHas("roles", function($q){ $q->where("name", "leader"); })->get();
        $user_in_project = [];
        if (!empty($users_leader)) {
            foreach ($users_leader as $key => $leader) {
                $result_user = DepartmentUser::with('user')->where([
                    ['department_id', '=', $department_id],
                    ['user_id', '=', $leader->id]
                ])->first();
                if (!empty($result_user)) {
                    $user_in_project[] = $result_user->user;
                }                
            }
        }
        $user_project = Project::with('projectuser')->find($project_id);  
        if (!empty($user_project->projectuser)) { 
            foreach ($user_project->projectuser as $key => $user) {
                $user_in_project[] = User::find($user->user_id);
            }
        }        
        return response()->json($user_in_project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    /**
     * show task
     */
    public function showDataTask($data){
        $task_id = $data->task_id;
        $position = $data->position;
        $task = Tasks::find($task_id);
        $user_in_task = Tasks::with('task_users')->find($task_id);
        if (!empty($user_in_task->task_users()->get())) {
            $task['members'] = $this->listMembers($user_in_task->task_users()->get());
        }
        // get list labels add in task
        if (!empty($task->labels)) {
            $task['task_labels'] = $this->listLabels($task->labels);
        }
        // get works to do in current task
        $task['works'] = $this->listWorks($task->id);
        // get list files in task
        $task['list_files'] = $this->listFiles($task->list_files);
        $data_chat = Tasks::with('messengers')->find($task->id);
        $task['messengers'] = $data_chat->messengers;
        foreach ($data_chat->messengers  as $k => $v) {
            $task['messengers'][$k]['info_user'] = User::find($v->user_id);
        }  
        $project = Tasks::with('projects')->find($task_id);
        $task['projects'] = $project->projects;
        $task['position'] = $position;
        return $task; 
    }
}
