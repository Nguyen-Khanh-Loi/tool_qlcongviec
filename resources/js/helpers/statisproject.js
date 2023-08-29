export const staticProject = {
    statisticalTasks,
    statisticalProject
};

/**
 *   function calculate percent of tasks
 *  @param {*} data list tasks in project
 */
function statisticalTasks(data){
    var totalTask     = 0;
    var totalTaskDone = 0;
    var percent       = 0;
    if (data.length > 0) {
        totalTask = data.length;        
        var taskDone = data.filter((item)=>{
            return item.card_id === 4
        });
        totalTaskDone = taskDone.length;
        percent = Math.round(100/totalTask * totalTaskDone);
    }
    return percent;
}

/**
 *  function calculate percent of project
 *  @param {*} data list tasks in department
 */
function statisticalProject(data){
    var countDepart  = data.length;
    var perNumber    = Math.round(100/countDepart);
    var totalPercent = 0;
    var results      = {};
    var result       = {};
    for (const key in data) {
        const depart = data[key];
        var percent = statisticalTasks(depart.tasks);
        result[key] = {
            'title': depart.title,
            'id': depart.id,
            'percent' : percent
        }
        totalPercent = totalPercent + calculatePercent(percent, perNumber)
    }
    var results = {
        'results' : result,
        'total'   : totalPercent
    }
    return results;
}

var calculatePercent = (x, y) => Math.round((x * y)/100);