<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Project;
use App\Models\Card;
use App\Models\Tasks;
use App\Models\ProjectUser;
use App\Models\DepartmentUser;
use App\Models\CodeProject;
use App\Models\Department;
use App\Models\ProjectDepartment;
use Illuminate\Support\Str;
use App\Http\Traits\TaskProject;
use App\Constants\ACTION;
use App\Constants\TYPES;

class ProjectController extends Controller
{
    use TaskProject;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $number = 100;
        if (!empty($request->input('number'))) {
            $number = $request->input('number');
        }
        $user     = Auth::user();
        $user_id  = $user->id;
        $roles    = $user->getRoleNames()->first();
        $users    = User::with('detail_user_department')->find($user_id);
        
        if ($roles !== 'manager' && $roles !== 'administrator') {
            if (!empty($users)) {
                $details = $users->detail_user_department;
                $department_id = $details->department_id;
            }
        }
        
        if ( in_array($roles, ['administrator', 'supper_administrator', 'project_manager']) ) {
            $projects = Project::with("tasks")->where('active','=',0)->latest()->paginate($number);
        } else if (in_array($roles, ['manager'])) {
            $projects = Project::with("tasks")->where([
                ['active','=',0],
                ['user_id','=',$user_id],
            ])->latest()->paginate($number);
        } else {
            if ( in_array($roles, ['leader']) ) {
                $list_project = ProjectDepartment::where('department_id', $department_id)->pluck('project_id');
            }else{
                $list_project = ProjectUser::where('user_id', $user_id)->pluck('project_id');
            }

            $projects = Project::with(["tasks" => function($q) use( $department_id ){
                $q->where('department_id', '=', $department_id);
            }])
            ->where('active','=',0)
            ->whereIn('id', $list_project)->latest()->paginate($number);
        }
        // if (!empty($projects)) {
        //     foreach ($projects as $key => $project) {
        //         if ($roles === 'leader') {
        //             $tasks = Project::with(["tasks" => function($q) use( $department_id ){
        //                 $q->where('department_id', '=', $department_id);
        //             }])->find($project->id);
        //         }else{
        //             $tasks = Project::with('tasks')->find($project->id);
        //         }
        //         if ($roles === 'user') {
        //             $projects[$key]->data_task = $tasks->tasks;
        //         }else{
        //             $projects[$key]['data_task'] = $tasks->tasks;
        //         }
                
        //     }
        // }
        return response()->json($projects);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user        = Auth::user();
        $user_id     = $user->id;
        $title       = $request->input('title');
        $description = "";
        if (!empty($request->input('description')) && $request->input('description')!== null && $request->input('description')!== 'undefined') {
            $description = $request->input('description');
        }
        $starttime    = $request->input('start_time');
        $code_center  = $request->input('code_company_center');
        $endtime      = $request->input('end_time');
        $code_id      = $request->input('code_id'); // mã khu vực dự án
        $code_project = $request->input('code');
        $slug         = Str::slug($title);
        if (Project::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . uniqid();
        }
        $images = $this->upLoadFiles($request);
        $url_image = null;
        if (!empty($images)) {
            $url_image = $images->name_file;
        }
        $data        = array(  
            'code'        => $code_project,        
            'code_company_center' => $code_center,        
            'user_id'     => $user_id,        
            'title'       => $title,
            'slug'        => $slug,
            'url_image'   => $url_image,
            'description' => $description,
            'status'      => ACTION['pending'],
            'start_time'  => date('Y-m-d H:i:s', strtotime($starttime)),
            'end_time'    => date('Y-m-d H:i:s', strtotime($endtime)),
        );        
        $project = new Project($data);
        $project->save();
        $results = Project::findOrFail($project->id);
        $dataCodeProject = array(
            'code_id'      => (int) $code_id, // code mã đầu game ví dụ mobile việt nam/ pc việt năm
            'project_id'   => $project->id, // id của dự án
            'code_project' => $code_project, // code của dự án game
        );
        $saveDataCode = new CodeProject($dataCodeProject);
        $saveDataCode->save();
        $code_id      = $request->input('code_id');
        $code_project = $request->input('code');
        $project['tasks'] = [];
        return response()->json($project);
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
    public function show(Request $request)
    {
        $user        = Auth::user();
        if ($request->input('slug')) {
            $slug        = $request->input('slug');
            // get current project
            $current_project = Project::where('slug', $slug)->firstOrFail();

            $listDepartments = ProjectDepartment::where([
                ['action', ACTION['approve']],
                ['project_id', $current_project->id],
            ])->pluck('department_id');

            $project     = Project::with(['tasks' => function($q) use($listDepartments) {
                $q->whereIn('department_id', $listDepartments);
            }, 'projectuser' => function($query){
                $query->where('action', '=', ACTION['project-management']); // xửa lý lấy dữ liệu user được quản lý thêm vào quản lý dự án 
            }])->find($current_project->id);
            // lấy tất cả các bộ phận thuộc dự án
            $departments = Department::with(["tasks" => function($q) use( $project ){
                $q->where('project_id', '=', $project->id)->whereIn('card_id', [2,3,4]);
            }])->whereIn('id', $listDepartments)->get();

            $project['departments'] = $departments;

            $project['check_user'] = true;
            if (!in_array($user->getRoleNames()->first(), ['administrator', 'leader'])) {
                $user_in_project = ProjectUser::where([
                    ['project_id', '=', $project->id],
                    ['user_id', '=', $user->id],
                ])->first();
                if (empty($user_in_project)) {
                    $project['check_user'] = false;
                }
            }

        }else{
            $project_id = $request->input('project_id');
            $project = Project::with('tasks')->find($project_id);
        }
       
        return response()->json($project);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $user     = Auth::user();
        $user_id  = $user->id;
        $roles    = $user->getRoleNames()->first();
        $users    = User::with('detail_user_department')->find($user_id);
        
        if ($roles !== 'manager' && $roles !== 'administrator') {
            if (!empty($users)) {
                $details = $users->detail_user_department;
                $department_id = $details->department_id;
            }else{
                $department_id = 2;
            }
        }  
        if ($request->has('keySearch')) {
            $keySearch = $request->input('keySearch');
            if ( $roles === 'administrator' || $roles === 'leader' ) {
                // $projects = Project::all();
                $projects = Project::where('title','LIKE','%'.$keySearch.'%')->get();
            } elseif ($roles === 'manager') {
                $user     = User::with('projects')->find($user_id);
                $projects = $user->projects()->where('title','LIKE','%'.$keySearch.'%')->get();
            } else {
                $projects = DB::table('project_users')
                ->join('projects', 'projects.id', '=', 'project_users.project_id')
                ->select('projects.*')
                ->where([
                    ['project_users.user_id', '=', $user_id],
                    ['projects.title', 'LIKE', '%'.$keySearch.'%'],
                ])->get();
            }
        }
        return response()->json($projects);
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
        
        $id = $request->input('id');
        $images = $this->upLoadFiles($request);
        if ( !empty($images) ) {            
            $data['url_image'] = $images->name_file;
            $data['title'] =  $request->input('title');
            $data['description'] =  $request->input('description');
            $data['end_time'] =  $request->input('end_time');
            $data['start_time'] =  $request->input('start_time');
        } else {
            $data = $request->input('data');
        }

        $result = Project::where('id', $id)->update($data);
        $project = Project::with('tasks')->find($id);
        return response()->json($project);
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
