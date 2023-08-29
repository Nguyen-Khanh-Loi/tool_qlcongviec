<?php
namespace App\Http\Traits;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Label;
use App\Models\Tasks;
use App\Models\WorkToDo;
use App\Models\Media;
use App\Models\DepartmentUser;

trait TaskProject {

    /**
     * show list member in task
     * @param string $data list user id in task 
     */
    public function listMembers($data) 
    {
        $members = [];
        // $lists = explode(",", $data);
        foreach ($data as $k => $v) {
            $members[$v->user_id] = User::find($v->user_id);
        }
        return $members;
    }

    /**
     * show list labels in task
     * @param string $data list label id in task 
     */
    public function listLabels($data) 
    {
        $labels = [];
        $lists = explode(",", $data);
        foreach ($lists as $k => $id) {
            $labels[$id] = Label::find($id);
        }
        return $labels;
    }

    /**
     * show list work to do in task
     * @param number id of task 
     */
    public function listWorks($id) 
    {
        $listWorks = [];
        $work = Tasks::with('worktodo')->find($id);
        if (!empty($work->worktodo)) {
            foreach ($work->worktodo as $key => $v) {
                $listWorks[$v->id] = $v;
                $listWorks[$v->id]['check_list'] = self::checkLists($v->id);
            }
        }
        return $listWorks;
    }

    /**
     * show check list in work todo
     * @param number id of work todo
     */
    public function checkLists($id) 
    {
        $checkLists = [];
        $works = WorkToDo::with('checklist')->find($id);
        if (!empty($works->checklist)) {
            foreach ($works->checklist as $key => $v) {
                $checkLists[$v->id] = $v;
                $checkLists[$v->id]['status'] = $v->status ? true : false;
            }
        }
        return $checkLists;
    }

    /**
     * upload files in task
     */
    public function upLoadFiles($request){
        $folderName = 'uploads';
        $path = public_path() . '/' . $folderName;
        if (!is_dir($path)) {
            mkdir($path, 0755);
        }
        $result = false;
        if ($request->input('url')) {
            $title     = $request->input('name');
            $url       = $request->input('url');
            $extension = 'link';
            $slug      = Str::slug($title);
            if (Media::where('slug', $slug)->exists()) {
                $slug = $slug . '-' . uniqid();
            }
            $media = new Media([
                'title'         => $title,
                'name_file'     => $url,
                'type'          => $extension,
                'slug'          => $slug,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $media->save();
            $result = $media;
        }else{
            if ($request->hasFile('file')) {
                $file       = $request->file('file');
                $fullName   = $file->getClientOriginalName();
                $checkFiles = explode(".", $fullName);
                $title      = current( $checkFiles );
                $extension  = end( $checkFiles );
                $fileName   = Str::slug($title.time()).'.'.$extension;

                if ($request->input('type') == 'avatar') {
                    $folderName = 'users';
                    $path = public_path() . '/' . $folderName;
                    if (!is_dir($path)) {
                        mkdir($path, 0755);
                    }
                    $file->move(public_path('users'), $fileName); 
                } elseif ($request->input('type') == 'projects') {
                    $folderName = 'projects';
                    $path = public_path() . '/' . $folderName;
                    if (!is_dir($path)) {
                        mkdir($path, 0755);
                    }
                    $file->move(public_path('projects'), $fileName);
                } else{
                    $file->move(public_path('uploads'), $fileName);  
                    $url = public_path('uploads').'/'.$fileName;   
                }     
                $slug      = Str::slug($title);
                if (Media::where('slug', $slug)->exists()) {
                    $slug = $slug . '-' . uniqid();
                }
                $media = new Media([
                    'title'         => $title,
                    'name_file'     => $fileName,
                    'type'          => $extension,
                    'slug'          => $slug,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
                $media->save();
                $result = $media;
            }    
        }
        return $result;
    }

    /**
     * show list file in task
     * @param number id of media
     */
    public function listFiles($arr) 
    {
        $listFiles = false;
        if (!empty($arr) && isset($arr)) {
            $arr = explode(',',$arr);
            foreach ($arr as $key => $v) {
                $listFiles[$v] = Media::find($v);
            }
        }
        return $listFiles;
    }

    /**
     * get user leader
     */
    public function showUsersLeader($department_id){
        $users_leader = User::whereHas("roles", function($q){ $q->where("name", "leader"); })->get();
        $leaders = false;
        if (!empty($users_leader)) {
            $leaders = [];
            foreach ($users_leader as $key => $leader) {
                $result_user = DepartmentUser::with('user')->where([
                    ['department_id', '=', $department_id],
                    ['user_id', '=', $leader->id]
                ])->first();
                if (!empty($result_user)) {
                    $leaders[] = $result_user->user;
                }                
            }
        }
        return $leaders;
    }
    
}