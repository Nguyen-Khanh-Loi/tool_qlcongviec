// import { store } from '../store/store';// không thể dùng nhiều store
import moment from "moment";
export const taskHelper = {
    isEmptyObject,
    convertToObject,
    updateDataTask,
    addWorkTodo,
    addcheckLists,
    calculateListWorkTodo,
    removeCheckListTask,
    updatedDataChecklist,
    calculateDate,
    convertDate,
    validateUrl,
    uploadFilesTask,
    countFileTasks,
    addRemoveUserInProject, 
};

function isEmptyObject(obj) {
    return Object.keys(obj).length === 0;
}

function convertToObject(obj) {
    if (typeof obj != 'undefined' && obj.length > 0) {
        var object = obj.reduce(
        (obj, item) => Object.assign(obj, { [item.id]: item.id }), {});
        return object;
    }else{
        return {};
    }
}
 /**
 * add and remove use,label in current task
 * @param {*} obj in obj key: action, id, data, key, field, task_id
 * @param {*} action active or deactive
 * @param {*} id user id, label id
 * @param {*} data data of user or label
 * @param {*} key key get data of current task use key
 * @param {*} field field data task by field
 */
async function updateDataTask( obj , $store ) { 
    // action, id_data, data, key, field, task_id
    var dataUpdated = $store.getters.currentTask[obj['key']];
    var task_id = $store.getters.currentTask.id;
    
    if (!dataUpdated) {
        dataUpdated = {};
    }

    if ( obj['action'] == 'deactive' ) {
        delete dataUpdated[obj['id']];
    }else{
        if (obj['action'] == 'active') {
            dataUpdated[obj['id']] = obj['data'];
        }else{
            dataUpdated = obj['data']
        }        
    }     
    var listArray = Object.keys(dataUpdated); // convert data Obj to array by key dataupdated
    var fields = {};
    var data = {};
    if (typeof obj['action'] != 'undefined') {
        fields[obj['field']] = listArray ? listArray.join(",") : "";
        if (obj['types'] == 'add_remove_user'){
            data["user_id"] = obj['id']; 
            data["action"] = obj['action']; 
        }
    }else{
        fields[obj['field']] = dataUpdated ? moment(dataUpdated).format('YYYY-MM-DD HH:mm:ss') : null  // update date deadline in task
    }
        
    
    data["task_id"] = task_id;
    data["info_task"] = fields; 
    // update task
    await $store.dispatch( 'updateTask', data ); // callback function

    // update data in task
    if (typeof $store.getters.listTasks[task_id] != 'undefined') {
        if ($store.getters.currentTask[obj['field']]) {
            $store.getters.listTasks[task_id][obj['key']] = $store.getters.currentTask[obj['key']];
        } else {
            $store.getters.listTasks[task_id][obj['key']] = false;
        }
    }
}

 /**
 * add work todo
 * @param {*} data
 */
async function addWorkTodo( data, $store ) {
    
    // create
    if ($store.getters.currentTask.works.length == 0) {
        $store.getters.currentTask.works = {}
    }
    var results = await $store.dispatch( 'createWorkTodo', data ); // callback function 
    if (typeof results != 'undefined') {
        $store.getters.currentTask.works[results.id] = results;
    }
}

 /**
 * add check list todo
 * @param {*} data key=> id of work todo
 * 
 */
async function addcheckLists( data, $store ) {
    var task_id = store.getters.currentTask.id;
    if ($store.getters.currentTask.works.length == 0) {
        $store.getters.currentTask.works = {} // add works todo in currents
    }

    if ($store.getters.listTasks[task_id].works.length == 0) {
        $store.getters.listTasks[task_id].works = {} // add works todo in list task
    }
    var results = await $store.dispatch( 'addCheckList', data );
    if (typeof results != 'undefined') {
        if ($store.getters.currentTask.works[data.id]['check_list'].length == 0) {
            $store.getters.currentTask.works[data.id]['check_list'] = {};
        }
        if (typeof $store.getters.listTasks[task_id].works[data.id] == 'undefined') {
            $store.getters.listTasks[task_id].works[data.id] = {}
        }
        if (typeof $store.getters.listTasks[task_id].works[data.id]['check_list'] == 'undefined') {
            $store.getters.listTasks[task_id].works[data.id]['check_list'] = {};
        }
        $store.getters.currentTask.works[data.id]['check_list'][results.id] = results;
        $store.getters.listTasks[task_id].works[data.id]['check_list'][results.id] = results;
    }
}

 /**
 * add check list todo
 * @param {*} data key=> id of work todo
 * 
 */
async function updatedDataChecklist( data, $store ) {
    var results = await $store.dispatch( 'updatedChecklist', data );
    return results;
}

 /**
 * add check list todo
 * @param {*} data key=> id of work todo
 * 
 */
function calculateListWorkTodo(data) {
    var total = 0;
    var done  = 0;
    var percentTask = 0;
    var percent = {};
    for (const key in data) {
        var listCheck = data[key];
        total = total + Object.keys(listCheck['check_list']).length;
        var number = 0;
        var done_check = 0;
        for (const id in listCheck['check_list']) {
            var check = listCheck['check_list'][id]
            if (check.status) {
                done++;
                done_check++;
            }
            number++;
        }
        if (number > 0) {
            percent[key] = Math.round(100/number * done_check)
        }else{
            percent[key] = 0
        }
    }
    if (done > 0) {
        percentTask = Math.round(100/total * done)
    }
    var results = {
        'total': total,
        'done': done,
        'percent' : percent,
        'percentTask': percentTask
    };
    return results;
}
/**
 * remove check list
 * @param array data key => id of check list, word_toto_id
 */
async function removeCheckListTask(data, $store){
    var task_id = $store.getters.currentTask.id;
    var work_id = data['work_id'];
    var status = await $store.dispatch( 'removeCheckList', data['id'] );
    if (status == 200) {
        delete $store.getters.currentTask.works[work_id].check_list[data['id']];
        delete  $store.getters.listTasks[task_id].works[work_id]['check_list'][data['id']];
    }
}
/**
 * 
 * @param {*} dateTasks 
 * @returns 
 */
function calculateDate(dateTasks){
    var today    = moment(new Date());
    dateTasks    = moment(new Date(dateTasks))
    var duration = moment.duration(dateTasks.diff(today));
    var days     = Math.round(duration.asDays());
    var timeTask = ' [at] '+moment(dateTasks).format('HH:ss');
    var danger = "[<span class='danger' style='background:#ffedeb'>out of date</span>]";
    var warning = "[<span class='warning' style='background:#e2b203;color:#000'>near due</span>]";
    var results = moment().add(days, 'days').calendar({
        sameDay: '[Today]'+ timeTask,
        nextDay: '[Tomorrow]' + timeTask + warning,
        nextWeek: 'dddd'+ timeTask,
        lastDay: '[Yesterday]' + timeTask + danger,
        lastWeek: '[Last] dddd'+ timeTask + danger,
        sameElse: function (now) {
            if (this.isBefore(now)) {
                return 'DD/MM/YYYY'+ timeTask + danger;
            } else {
                return 'DD/MM/YYYY'+ timeTask;
            }
        }
    });
    return results;
}
/**
 * 
 * @param {*} dateFiles 
 * @returns 
 */
function convertDate(dateFiles){
    var today    = moment(new Date());
    dateFiles    = moment(new Date(dateFiles))
    var duration = moment.duration(dateFiles.diff(today));
    var days     = Math.round(duration.asDays());
    var results = moment().add(days, 'days').calendar();
    return results;
}
/**
 * 
 * @param {*} url 
 * @returns 
 */
function validateUrl(url) {
    try {
        return new URL(url);
    } catch (error) {
        return false;
    }
}

/**
 * 
 * @param {*} data 
 * @returns 
 */
async function uploadFilesTask(data, $store){
    let formData = new FormData();
    formData.append('file', data['file']);
    formData.append('task_id', data['task_id']);
    var results = await $store.dispatch( 'uploadFile', formData );
    if (results) {
        $store.getters.listTasks[data['task_id']]['list_files'] = results.list_files
    }
    return results;
}

/**
 * 
 * @param {*} data 
 * @returns 
 */
function countFileTasks(data){
    var count = 0;
    if (data) {
        count = Object.keys(data).length;
    }
    return count;
}

/**
 *  function add or remove user in project
 *  @param {*} data key action: active or deactive, user_id, project id
 */
async function addRemoveUserInProject(data, $store){
    var results =  await $store.dispatch( 'addRemoveUserInProject', data );
    return results;
}