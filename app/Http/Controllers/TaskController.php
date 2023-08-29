<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\DepartmentUser;
use App\Models\Project;
use App\Models\Card;
use App\Models\Tasks;
use App\Models\Label;
use App\Models\ChatTasks;
use App\Models\TaskUser;
use App\Models\Log;
use App\Models\Notification;
use App\Http\Traits\TaskProject;
use App\Constants\ACTION;
use App\Constants\TYPES;

class TaskController extends Controller
{
    use TaskProject;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user     = Auth::user();
        $user_id  = $user->id;
        $roles    = $user->getRoleNames()->first();
        $users    = User::with('detail_user_department')->find($user_id);
        $cards    = Card::all();
        $labels   = Label::all();
        $results  = [];
        $data     = [];
        $slug = $request->input('slug');
        $project = Project::where('slug',$slug)->first();
        $project_id = $project->id;        
        // check admin
        if ($roles == 'administrator') {
            $department_id = $request->input('department_id') ? $request->input('department_id') : 1;
        }else{
            if (!empty($users)) {
                $details = $users->detail_user_department;
                $department_id = $details->department_id;
            }
        }
        // $dataTasks = Tasks::with('projects', 'messengers', 'task_users')->where([
        //     ['project_id', '=', $project_id],
        //     ['department_id', '=', $department_id],
        // ])->get();
        foreach ($cards as $key => $card) {            
            $card_id = $card->id;
            $query = [
                ['card_id', '=', $card_id],
                ['project_id', '=', $project_id],
                ['department_id', '=', $department_id]
            ];
            if ($roles == 'administrator') {
                $query = [
                    ['card_id', '=', $card_id],
                    ['project_id', '=', $project_id],
                ];
            }
            $list_tasks = Tasks::with('projects')->where($query)->get();
            $list_draggable[$card_id] = [];
            if (!empty($list_tasks)) {
                foreach ($list_tasks as $key => $tasks) {
                    $data_chat = Tasks::with('messengers')->find($tasks->id);
                    $results[$tasks->id] = $tasks;
                    $results[$tasks->id]['messengers'] = $data_chat->messengers;
                    foreach ($data_chat->messengers  as $k => $v) {
                        $results[$tasks->id]['messengers'][$k]['info_user'] = User::find($v->user_id);
                    }
                    $list_draggable[$card_id][$key] = $tasks->id;
                    // get list member add in task
                    $user_in_task = Tasks::with('task_users')->find($tasks->id);
                    if (!empty($user_in_task->task_users()->get())) {
                        $list_tasks[$key]['members'] = $this->listMembers($user_in_task->task_users()->get());
                    }
                    // get list labels add in task
                    if (!empty($tasks->labels)) {
                        $list_tasks[$key]['task_labels'] = $this->listLabels($tasks->labels);
                    }
                    // get works to do in current task
                    $list_tasks[$key]['works'] = $this->listWorks($tasks->id);
                    // get list files in task
                    $list_tasks[$key]['list_files'] = $this->listFiles($tasks->list_files);
                }                
            }
        }

        if (!empty($list_draggable)) {
            $data['list_draggable'] = $list_draggable;
            $data['list_task'] = $results;
        }else{
            $data['list_draggable'] = [];
            $data['list_task'] = [];
        }
        
        
        // get user in project
        $user_project = Project::with('projectuser')->find($project_id);  
        $data['project_users'] = [];
        if (!empty($user_project->projectuser)) { 
            $arr_user = [];      
            foreach ($user_project->projectuser as $key => $user) {
                $arr_user[$user->user_id] = User::find($user->user_id);
            }
            $data['project_users'] = $arr_user;
        }

        // get list user of department
        $data['list_user'] = [];
        $list_user = DepartmentUser::where('department_id', '=', $department_id)->get();
        if (!empty($list_user)) {
            $arr = [];      
            foreach ($list_user as $key => $user) {
                $dataUser = User::find($user->user_id);
                $arr[$user->user_id] = $dataUser;
                $arr[$user->user_id]['roles'] = $dataUser->getRoleNames()->first();
            }
            $data['list_user'] = $arr;
        }

        $data['labels'] = $labels;
        $data['cards'] = $cards;
        $data['project'] = $project;
        // $data['dataTasks'] = $dataTasks;
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user       = Auth::user();
        $user_id    = $user->id;
        $roles      = $user->getRoleNames()->first();
        $card_id    = $request->input('card_id');
        $postion    = $request->input('postion'); 
        $title      = $request->input('title_'.$postion); 
        $project_id = $request->input('project_id'); 
        $action     = $request->input('action'); 
        $slug       = Str::slug($title);
        if (Tasks::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . uniqid();
        }
        $users = User::with('detail_user_department')->find($user_id);
        
        if ($roles == 'administrator') {
            $department_id = $request->input('department_id') ? $request->input('department_id') : 1;
        }else{
            if (!empty($users)) {
                $details = $users->detail_user_department;
                $department_id = $details->department_id;
            }
        }

        if (!empty($action) && $action == 'my-task') {
            $title   = $request->input('title');
            $expDate = null;
            if (!empty($request->input('date'))) {
                $expDate = date('Y-m-d H:i:s', strtotime($request->input('date')));
            }            
            $slug    = Str::slug($title);  
            if (empty($project_id)) {
                $project_id = 0;
            }          
            if (Tasks::where('slug', $slug)->exists()) {
                $slug = $slug . '-' . uniqid();
            }
            $tasks = new Tasks([
                'title'         => $title,
                'project_id'    => $project_id,
                'card_id'       => 1,
                'department_id' => $department_id,
                'slug'          => $slug,
                'user_id'       => $user_id,
                'deadline'      => $expDate,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);            
        }else{
            $tasks = new Tasks([
                'title'         => $title,
                'project_id'    => $project_id,
                'card_id'       => $card_id,
                'department_id' => $department_id,
                'slug'          => $slug,
                'user_id'       => $user_id,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }        
        
        $tasks->save();
        $position = 1;  
        // insert data in log
        $log = new Log([
            'target_id' => $tasks->id,            
            'action' => ACTION['create'],
            'type' => TYPES['task'],
            'user_id' => $user_id,
        ]); 
        $log->save();
        // insert data notification
        $notification = new Notification([
            'project_id' => $project_id,            
            'task_id' => $tasks->id,            
            'action' => ACTION['create'],
            'type' => TYPES['task'],
            'author_id' => $user_id, // user create tasks
        ]); 
        $notification->save();
        $tasks = Tasks::with('projects', 'users', 'request_logs')->find($tasks->id);
        if (!empty($request->input('position'))) {
            $position = $request->input('position');
        }  
        // save task of user in list my task
        $userInTasks = new TaskUser([
            'task_id' => $tasks->id,
            'user_id' => $user_id,
            'position' => $position
        ]);
        $userInTasks->save();

        $user_in_task = Tasks::with('task_users')->find($tasks->id);        
        if (!empty($user_in_task->task_users()->get())) {
            $tasks['members'] = $this->listMembers($user_in_task->task_users()->get());
        }
        // get list labels add in task
        if (!empty($tasks->labels)) {
            $tasks['task_labels'] = $this->listLabels($tasks->labels);
        }
        // get works to do in current task
        $tasks['works'] = $this->listWorks($tasks->id);
        // get list files in task
        $tasks['list_files'] = $this->listFiles($tasks->list_files);
        $tasks['messengers'] = [];
        $results = [];
        $results['data'] = $tasks;
        if ($project_id != 0) {
            $results['leaders'] = $this->showUsersLeader($department_id);  
        };
        return response()->json($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $task_id = $request->input('task_id');
        $task = Tasks::find($task_id);
        $media = $this->upLoadFiles($request);
        $id = $media->id;
        if ($id) {
            if (!empty($task->list_files)) {
                $files = explode(',',$task->list_files);
                $files[] = $id;
            }else{
                $files = [];
                $files[] = $id;
            }
            $data = array(
                'list_files' => implode(',', $files)
            );
            Tasks::where('id', $task_id)->update($data);
            $task = Tasks::find($task_id);
            $task['list_files'] = $this->listFiles($task->list_files);
        }
        return response()->json($task);
    }

    /**
     * Display the specified resource. 04005224
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $results = Tasks::findOrFail($id);
        // if (!empty($results->list_user_ids)) {
        //     $results['members'] = $this->listMembers($results->list_user_ids);            
        // }
        $user_in_task = Tasks::with('task_users')->find($id);
        if (!empty($user_in_task->task_users()->get())) {
            $results['members'] = $this->listMembers($user_in_task->task_users()->get());
        }
        // get list labels add in task
        if (!empty($results->labels)) {
            $results['task_labels'] = $this->listLabels($results->labels);
        }
        return response()->json($results);     
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
        $current_user = Auth::user();
        $task_id  = $request->input('task_id');
        $data     = $request->input('info_task');
        $action   = $request->input('action');
        $user_id  = $request->input('user_id');// user create task       
        if (!empty($action)) {
            switch ($action) {
                case 'active':
                    $userInTasks = new TaskUser([
                        'task_id' => $task_id,
                        'user_id' => $user_id,
                    ]);
                    $userInTasks->save();
                    break;
                case 'deactive':
                    $results = TaskUser::where([
                        ['task_id', '=', $task_id],
                        ['user_id', '=', $user_id],
                    ])->delete();
                    break;
                default:
                    // $auth_id = $request->input('auth_id');
                    $log = new Log([
                        'target_id' => $task_id,            
                        'action' => ACTION[$action],
                        'type' => TYPES['task'],
                        'user_id' => $current_user->id,
                    ]); 
                    $log->save();
                    $project_id  = $request->input('project_id');
                    // update notification
                    $notification = new Notification([
                        'project_id' => $project_id,            
                        'task_id' => $task_id,            
                        'action' => ACTION[$action],
                        'type' => TYPES['task'],
                        'user_id' => $user_id,
                        'author_id' => $current_user->id, // user approve task // or user reject task
                    ]); 
                    $notification->save();
                    // add user in task 
                    if (ACTION[$action] == 'approve') {                      
                        $userInTasks = new TaskUser([
                            'task_id' => $task_id,
                            'user_id' => $user_id,
                        ]);
                        $userInTasks->save();
                    }
                    break;
            }
        }
        if (!empty($data)) {
            Tasks::where('id', $task_id)->update($data);            
        }
        $task = Tasks::find($task_id);
        $user_in_task = Tasks::with('task_users')->find($task_id);
        if (!empty($user_in_task->task_users()->get())) {
            $task['members'] = $this->listMembers($user_in_task->task_users()->get());
        }
        // if (!empty($task->list_user_ids)) {
        //     $task['members'] = $this->listMembers($task->list_user_ids);
        // }
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
        return response()->json($task);
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
}
