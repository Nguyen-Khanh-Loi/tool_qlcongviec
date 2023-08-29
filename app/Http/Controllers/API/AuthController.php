<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DepartmentUser;
use App\Models\ProjectUser;
use App\Models\Department;
use App\Models\Role;
use App\Models\Media;
use App\Http\Traits\TaskProject;

class AuthController extends Controller
{
    use TaskProject;
    /**
     * Xử lý yêu cầu đăng nhập.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkLogin(Request $request)
    {
        $email = $request->input('email');
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if($validator->fails()) {
            $msg = [];

            foreach(array_values($validator->errors()->toArray()) as $val) {
                foreach($val as $error) {
                    $msg[] = $error;
                }

            }

            $res = [
                'status' => 200,
                'success'=>false,
                'message' => $msg
            ];

            return response()->json($res);
        }
        $user  = User::where('email', $email)->get()->first();
        if($user) {
            $res = [
                'status' => 200,
                'success'=>true,
                'message' => "success",
                'data' => ['is_user'=>true,'is_first_login'=>$user->first_login,'res'=>$user]
            ];

            return response()->json($res); 
        }

        else {
            $res = [
                'status' => 200,
                'success'=>false,
                'message' => "User not found"
            ];

            return response()->json($res); 

        }
    }

    /**
     * Xử lý yêu cầu đăng nhập.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function firstLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'repassword' => 'required',
        ]);

        if($validator->fails()) {
            $msg = [];

            foreach(array_values($validator->errors()->toArray()) as $val) {
                foreach($val as $error) {
                    $msg[] = $error;
                }

            }

            $res = [
                'response_index' => true,
                'response_type' => 'error',
                'response_data' => $msg,
                'authenticated' => false,
            ];

            return response()->json($res, 200);
        }
        $user  = User::where('email', $request->email)->get()->first();
        if(!$user || $user->first_login !=1) {
            $res = [
                'response_index' => true,
                'response_type' => 'error',
                'response_data' => ["Check first login false",$user->firstLogin],
                'authenticated' => false,
            ];

            return response()->json($res); 
        }
        $user->password = Hash::make($request->password);
        $user->first_login = 0;
        $user->save();
        if(Auth::attempt($request->only('email', 'password'))) {
            $res = [
                'response_index' => true,
                'response_type' => 'success',
                'response_data' => ['You Have Logged In Successfully'],
                'authenticated' => true,
            ];
            return response()->json($res, 200); 
        }

        else {
            $res = [
                'response_index' => true,
                'response_type' => 'error',
                'response_data' => ['Given Credentials Does Not Match Our Record'],
                'authenticated' => false,
            ];

            return response()->json($res, 200);
        }
    }

    /**
     * Xử lý yêu cầu đăng nhập.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            $msg = [];

            foreach(array_values($validator->errors()->toArray()) as $val) {
                foreach($val as $error) {
                    $msg[] = $error;
                }

            }

            $res = [
                'response_index' => true,
                'response_type' => 'error',
                'response_data' => $msg,
                'authenticated' => false,
            ];

            return response()->json($res, 200);
        }

        if(Auth::attempt($request->only('email', 'password'))) {
            $res = [
                'response_index' => true,
                'response_type' => 'success',
                'response_data' => ['You Have Logged In Successfully'],
                'authenticated' => true,
            ];
            return response()->json($res, 200); 
        }

        else {
            $res = [
                'response_index' => true,
                'response_type' => 'error',
                'response_data' => ['Given Credentials Does Not Match Our Record'],
                'authenticated' => false,
            ];            
            return response()->json($res, 200);
        }
    }
     /**
     * lây thông tin user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logged(Request $request)
    {
        
        $req = $request->user();
        $req['roles'] = $req->getRoleNames()->first();
        $department = DepartmentUser::where('user_id', $req->id)->first();
        if (!empty($department)) {
            $req['department_id'] = $department->department_id;
        }
        return response()->json($req, 200); 
    }

     /**
     * logout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        return response()->json('logout successfull');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user     = Auth::user();
        $user_id  = $user->id;
        $isAdmin  = $user->hasAnyRole('administrator');
        $users    = [];
        if($isAdmin){
            $users = User::with(['roles' => function ($query) {
                $query->where('status', 1);
            }])
            ->get()->toArray();
        }else{
            $deparmentUser = DepartmentUser::where('user_id', $user_id)->get()->first();
            if($deparmentUser){
                $listUser = DepartmentUser::where('department_id', $deparmentUser->department_id)
                ->pluck('user_id')
                ->toArray();
                $users = User::with(['roles' => function ($query) {
                    $query->where('status', 1);
                }])
                ->whereIn('id', $listUser)
                ->get()->toArray();
            }
        }
        $roles = Role::where('status', 1)->get()->toArray();
        $deparments = Department::all();
        $responseData = [
            'status' => 200,
            'success'=>true,
            'message' => 'success',
            'data' => ['users'=>$users,'roles'=>$roles, 'deparments'=> $deparments ]
        ];
        return response()->json($responseData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $role = Role::where('id', $request->input('role'))->get()->first();
        $oldUser = User::where('email', $request->input('email'))->get()->first();
        if($oldUser){
            $responseData = [    'status' => 200,'success'=>true,    'message' => 'This account already exists'];
            return response()->json($responseData);
        }
        if ($role){            
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make(""),
                'first_login'=> 1
            ]);
            $user->assignRole($role->name);
            $user->load([
                'roles' => function ($query) {
                    $query->where('status', 1);
                },
                'roles.permissions' => function ($query) {
                    $query->where('status', 1);
                }
            ]);
            $responseData = [
                'status' => 200,
                'success'=>true,
                'message' => 'The user successfully created',
                'data'=>['user_created'=>$user]
            ];
            if ($request->input('deparment')) {
                $id_deparment = (int) $request->input('deparment');
                $user_id = $user->id; 
                $pepartmentUser = DepartmentUser::create([
                    'user_id' => $user_id,
                    'department_id' => $id_deparment,
                ]);
            }
            return response()->json($responseData);
        }else{
            $responseData = [
                'status' => 200,
                'success'=>false,
                'message' => 'Role not found'
            ];
            return response()->json($responseData);
        }
    }

     /**
     * upload avatar user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        //        
        $result = $this->upLoadFiles($request);
        $user     = Auth::user();
        $user_id  = $user->id;
        $user->avatar = $result->name_file;
        $user->save();
        $user['roles'] = $user->getRoleNames()->first();
        $responseData = [
            'message' => 'Change avatar successfully',
            'data'    => $user
        ];
        return response()->json($responseData);
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
    public function update(Request $request)
    {
        $userUpdate     = Auth::user();
        $userUpdate->last_name = $request->input('last_name');
        $userUpdate->first_name = $request->input('first_name');
        $userUpdate->phone = $request->input('phone');
        $userUpdate->address = $request->input('address');
        $userUpdate->save();
        $responseData = [
            'status' => 200,
            'success'=>true,
            'message' => 'The user successfully updated',
            'data' => $userUpdate,
        ];
        return response()->json($responseData);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeRoleUser($id, Request $request)
    {
        $user = User::find($id);
        if($user){
            $user->load([
                'roles' => function ($query) {
                    $query->where('status', 1);
                }
            ]);
            $roles = Role::where('status', 1)->get();
            foreach ($roles as $role) {
                if ($request->has('role') && $request->input('role') == $role->id){
                    $user->assignRole($role->name);
                }else{
                    $user->removeRole($role->name);
                }
            }
            $user->load([
                'roles' => function ($query) {
                    $query->where('status', 1);
                },
            ]);
            $responseData = [
                'status' => 200,
                'success'=>true,
                'message' => 'The user successfully updated',
                'data'=>['user_update'=>$user]
            ];
            return response()->json($responseData);
        }else{
            $responseData = [
                'status' => 200,
                'success'=>true,
                'message' => 'The user not found'
            ];
        return response()->json($responseData);
        }
    }

    // update user
    public function changePasswordUser($id, Request $request)
    {
        $user = User::find($id);
        $password = $request->input('password');
        $repassword = $request->input('repassword');
        if($password !== $repassword or !$password){
            $responseData = ['status' => 200,'success'=>true,    'message' => 'The user false to updated'];
            return response()->json($responseData);
        }
        $user->password = Hash::make($password);
        $user->save();
        $responseData = [
            'status' => 200,'success'=>true,
            'message' =>
            'The user successfully updated'];
        return response()->json($responseData);
    }
    /**
     * Xử lý yêu cầu đăng nhập.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();
        $userUpdate = User::find($user->id);
        // return response()->json($user->password);
        if (!Hash::check($request->input('password'), $user->password)) {
            $responseData = [   'status' => 200,'success'=>true,     'message' => 'Password false'];
            return response()->json($responseData);
            
        }
        $passwordNew = $request->input('password_new');
        $repasswordNew = $request->input('repassword_new');
        if($passwordNew !== $repasswordNew or !$passwordNew){
            $responseData = [   'status' => 200,'success'=>true,     'message' => 'The user false to updated'];
            return response()->json($responseData);
        }
        $user->password = Hash::make($passwordNew);
        $user->save();
        $responseData = [   'status' => 200,'success'=>true,     'message' => 'The user successfully updated'];
        return response()->json($responseData);
    }
    /**
     *  xử lý lấy dữ liệu user quản lý dự án và tất cả các bộ phận
     */
    public function storeUserProjectManager(Request $request){
        $users = User::role('project_manager')->get();
        $department = Department::all();
        $user_in_project = 
        $results = [];       
        $results['status'] = 200;       
        $results['project_manager'] = $users;       
        $results['department'] = $department;       
        // $results['user_in_project'] = $department;       
        return response()->json($results);
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

    
    
