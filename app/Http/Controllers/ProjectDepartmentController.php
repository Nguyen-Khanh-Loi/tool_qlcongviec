<?php

namespace App\Http\Controllers;
use DB;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Constants\ACTION;
use App\Constants\TYPES;
use App\Models\Department;
use App\Models\Tasks;
use App\Models\User;
use App\Models\DepartmentUser;
use App\Models\ProjectUser;
use App\Models\Project;
use App\Models\ProjectDepartment;
use App\Models\Notification;
use App\Helpers\Helpers;
class ProjectDepartmentController extends Controller
{ 

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
        $department_id = $request->input('department_id');
        $action = $request->input('action');
        $department = ProjectDepartment::where([
            ['project_id','=', $project_id],
            ['department_id','=', $department_id]
        ])->first();
        if (!empty($department->id)) {
            $data_action = array(
                'action' => $action,
            );
            $data_update = ProjectDepartment::where('id', $department->id)->update($data_action);
        }else{
            $department = new ProjectDepartment([
                'project_id' => $project_id,
                'department_id' => $department_id,
                'action' => $action,
            ]);
            $department->save();
        }        
        $userLeaders = User::role('leader')->pluck('id');
        $project = Project::find($project_id);
        $listLeader = DepartmentUser::with('user')
        ->where([
            ['department_id', '=', 1],
        ])
        ->whereIn('user_id', $userLeaders)
        ->get()->toArray();        
        if (ACTION[$action] == 'approve') {
            $mailTo = [];
            $dataInsert = [];
            foreach ($listLeader as $key => $user) {
                $useremail = $user['user']['email'];
                $mailTo[] = $useremail;
                // lưu dữ lthoong tin dự án thông báo cho leader 
                $notification = Notification::where([
                    ['project_id','=', $project_id],
                    ['user_id','=', $user['user']['id']],
                    ['type','=', TYPES['project']],
                    ['action','=', ACTION['approve']],
                    ['author_id','=', $author_id],
                ])->first();
                if (!empty($notification)) {
                    $data_action = array(
                        'status' => 0,
                        'author_id' => $author_id,
                    );
                    $data_update = Notification::where('id', $notification->id)->update($data_action);
                }  else {
                    $notification = new Notification([
                        'project_id' => $project_id,
                        'user_id'    => $user['user']['id'],
                        'type'       => TYPES['project'],
                        'action'     => ACTION['approve'],
                        'author_id' => $author_id,
                    ]);                
                    $notification->save();
                }

                // insert leader in project 
                $leader_user_project = ProjectUser::where([
                    ['project_id','=', $project_id],
                    ['user_id','=', $user['user']['id']],
                ])->whereIn('action',[ACTION['disapprove'], ACTION['approve']] )->first(); 
                if (!empty($leader_user_project)) {
                    $data_action = array(
                        'action' => ACTION['approve'],
                    );
                    $data_update = ProjectUser::where('id', $leader_user_project->id)->update($data_action);
                }  else {
                    $leader_user_project = new ProjectUser([
                        'project_id' => $project_id,
                        'user_id'    => $user['user']['id'],
                        'action'     => ACTION['approve'],
                    ]);                
                    $leader_user_project->save();
                }
            }
            if (!empty($mailTo)) {
                $emailData = [
                    'slug'=> $project->slug,
                    'name'=> $project->title,
                ];
                Mail::to($mailTo)->send(new SendEmail($emailData));
            }
        }else{
            // xử lý ẩn user leader khi ẩn bộ phận khỏi project
            foreach ($listLeader as $key => $user) {
                $leader_user_project = ProjectUser::where([
                    ['project_id','=', $project_id],
                    ['user_id','=', $user['user']['id']],
                ])->whereIn('action',[ACTION['disapprove'], ACTION['approve']] )->first(); 
                if (!empty($leader_user_project)) {
                    $data_action = array(
                        'action' => ACTION['disapprove'],
                    );
                    $data_update = ProjectUser::where('id', $leader_user_project->id)->update($data_action);
                }
            }
        }        
        $result = Department::with(["tasks" => function($q) use( $project_id ){
            $q->where('project_id', '=', $project_id)->whereIn('card_id', [2,3,4]);
        }])->find($department_id);
        return response()->json($result);
    }
}
