<?php

namespace App\Http\Controllers;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ProjectUser;
use App\Models\Project;
use App\Models\User;
use App\Models\Role;
use App\Models\DepartmentUser;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Hash;
use App\Constants\ACTION;
use App\Constants\TYPES;
use App\Models\Notification;
class ProjectUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     * add or remove user in project
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {        
        $user     = Auth::user();
        $author_id  = $user->id; // author add 
        $project_id = $request->input('project_id');
        $user_id = $request->input('user_id');
        $action = $request->input('action');       
        $type = $request->input('type'); 
        $project = Project::find($project_id);
        if ($type == TYPES['access_project']) {
            if ($action == ACTION['remove']) {
                // Xóa user khỏi quản lý project
                $results = ProjectUser::where([
                    ['project_id', '=', $project_id],
                    ['user_id', '=', $user_id],
                    ['action', '=', ACTION['project-management']],
                ])->delete();
                $actionNotification = ACTION['disaccess'];
            }else{
                // add user vào quản lý project
                $project_user = new ProjectUser([
                    'user_id'       => $user_id,
                    'project_id'    => $project_id,
                    'action'        => ACTION[$action],
                ]);
                $project_user->save();
                $results = ProjectUser::with('details_user', 'details_project')->find($project_user->id); 
                $mailTo = $results->details_user->email;
                $emailData['check_user'] = ACTION['access'];
                $actionNotification = ACTION['access'];
            }

            $notification = Notification::where([
                ['project_id','=', $project_id],
                ['user_id','=', $user_id],
                ['type','=', TYPES['project']],
            ])->whereIn('action' , [ACTION['disaccess'], ACTION['access']])
            ->first(); 
            if (!empty($notification)) {
                $data_action = array(
                    'action' => $actionNotification,
                    'status' => 0,
                    'author_id' => $author_id,
                );
                $data_update = Notification::where('id', $notification->id)->update($data_action);
            }  else {
                $notification = new Notification([
                    'project_id' => $project_id,
                    'user_id'    => $user_id,
                    'type'       => TYPES['project'],
                    'action'     => $actionNotification,
                    'author_id' => $author_id,
                ]);                
                $notification->save();
            }
        }else{
            // add user vào project
            if ($action == 'active') {
                $project_user = new ProjectUser([
                    'user_id'       => $user_id,
                    'project_id'    => $project_id,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
                $project_user->save();
                $results =  ProjectUser::with('details_user', 'details_project')->find($project_user->id);
                $mailTo = $results->details_user->email;
            }else{
                // xóa user khỏi project
                $results = ProjectUser::where([
                    ['project_id', '=', $project_id],
                    ['user_id', '=', $user_id],
                ])->delete();
                $users = User::with('detail_user_department')->find($user_id);
                $roles = $users->getRoleNames()->first();
                $users_details = $users->detail_user_department;
                $department_id = $users_details->department_id;
                if ($roles != 'leader') {
                    $tasks = Project::with(["tasks" => function($q) use( $department_id ){
                        $q->where([
                            ['department_id', '=', $department_id],
                            ['list_user_ids', '!=', ''],
                            ['list_user_ids', '!=', NULL],
                        ]);
                    }])->find($project_id);
                    $helpers = new Helpers($tasks->tasks);
                    $removeUsers = $helpers->remoUserInTask($user_id);
                }
            }
        }  
        if (!empty($mailTo)) {
            $mailTo = $mailTo;
            $emailData['slug'] = $project->slug;
            $emailData['name'] = $project->title;
            Mail::to($mailTo)->send(new SendEmail($emailData));
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ProjectUser::destroy($id);
    }
}
