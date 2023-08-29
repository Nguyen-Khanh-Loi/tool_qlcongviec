<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Tasks;
use App\Models\Project;
use App\Models\Notification;
use App\Models\Log;
use App\Constants\ACTION;
use App\Constants\TYPES;
class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user     = Auth::user();
        $user_id  = $user->id;
        $roles = $user->getRoleNames()->first();
        $results = [];
        $results['status'] = 400;
        $results['messenger'] = 'Error not found';
        $users = User::with('detail_user_department')->find($user_id);
        if ($roles == 'administrator') {
            $department_id = $request->input('department_id') ? $request->input('department_id') : 1;
        }else{
            if (!empty($users)) {
                $details = $users->detail_user_department;
                $department_id = $details->department_id;
            }
        }
        $data = [];
        if (!empty($roles) && in_array($roles,['leader', 'administrator'])) {            
            $tasks = Tasks::with(['projects', 'request_logs' => function($query){
                $query->where([
                    ['status', '=', 0],
                ]);
            }, 'users'])->where([ 
                ['status', '=', 0], 
                ['active', '=', 0], 
                ['project_id', '!=', 0], 
                ['department_id', '=', $department_id], 
            ])->get();
            $data = $tasks;
        } 

        // $tasks = Tasks::with(["projects",'request_logs' => function($query){
        //     $query->where('action', '=', ACTION['approve'])
        //     ->orWhere('action', '=',  ACTION['disapprove']);
        $tasks = Tasks::with(["projects",'request_logs' => function($query){
            $query->whereIn('action', [ACTION['approve'], ACTION['disapprove']])
                  ->where('status', '=', 0);
        }])->where([ 
            ['status', '=', 0], 
            ['active', '=', 1], 
            ['user_id','=', $user_id], 
            ['department_id', '=', $department_id], 
        ])->get();  
        if (!empty($tasks)) {
            foreach ($tasks as $key => $task) {
                if (!empty($task->request_logs->author_id)) {
                    $tasks[$key]['users'] = User::find($task->request_logs->author_id);// get infomation user approve/reject task
                    $data[] = $tasks[$key];
                }
            }
        }      
        $results['data'] = $data;
        $results['status'] = 200;
        $results['messenger'] = 'Successfuly';
        return response()->json($results);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $task_id = $request->input('task_id');
        $author_id = $request->input('author_id'); // user approve task or reject task
        $project_id = $request->input('project_id');
        $check_role = $request->input('check_role'); // check role of user focus task
        $data = array( 'status' => 1);
        // if (empty($check_role)) {
            $results = Notification::where([
                ['project_id', '=', $project_id],
                ['task_id', '=', $task_id],
                ['user_id','=', $user->id], // user id create tasks
                ['author_id','=', $author_id], // user approve task or reject task
                ['action','=', ACTION['approve']],
            ])->update($data);
        // } else {
        //     $dataUpdate = array(
        //         'target_id' => $task_id,
        //         'action' => ACTION['viewed'],
        //         'type' => TYPES['task'],
        //         'user_id' => $user->id, // user id viewed notification
        //     );
        //     $results = new Log($dataUpdate);
        //     $results->save();
        // }
        return $results;
    }
}
