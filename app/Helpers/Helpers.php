<?php
namespace App\Helpers;
use App\Models\Tasks;
use App\Http\Traits\TaskProject;
class Helpers
{
    private $data;
    use TaskProject;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * remove user in tasks
     */
    public function remoUserInTask($user_id)
    {
        $result = false;
        if (!empty($this->data)) {
            foreach ($this->data as $key => $value) {
                $task_id = $value->id;
                $user_in_task = explode(',',$value->list_user_ids);
                if (in_array($user_id, $user_in_task)) {
                    $result = array_diff($user_in_task, [$user_id]);
                    $update = ['list_user_ids' => implode(',',$result)];
                    Tasks::where('id', $task_id)->update($update);
                }
            }
        }
        return $this->data;
    }
    /**
     * xử lý thêm user leader của bộ phận vào dự án vào dự án 
     *  
     * */   
    public function addRemoveUserInProject($array){
        dd($array);
    } 
}