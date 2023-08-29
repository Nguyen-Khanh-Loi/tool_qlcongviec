import { dateHelper } from "@/js/helpers/datehelper";
import { forIn } from "lodash";
export const filterDataProject = {
    filterTaskProject
}
// function return data by member;
const dataInUser = (dataUser, dataTask) => {
    var results
    for (const key in dataUser) {
        const idUser = dataUser[key];
        if (dataTask.members[idUser]) {
            results = dataTask
        }
    }
    return results;
}

/**
 * filter user
 * data[date] => checck dealine date of task => 
 * value[no]: not deadline in task
 * value[out_date]: expiration date in task
 * value[week_date]: task deadline in next week
 * value[month_date]: task deadline in next month
 * @param {*} data 
 * @returns 
 */
function filterTaskProject(data){
    var tasks = data['tasks'];
    var types = data['types'];
    if (tasks) {  
        if (types['search']) {
            var results = {};
            tasks.filter((e)=>{
                var check = types['search'].toLowerCase().split(' ').every( v => e.title.toLowerCase().includes(v) )
                if (check) {
                    results[e.id] = e
                }
            })
        }else{
             // filter task by user           
            if (types['users']) {
                var results = {};           
                if (types['users']['no']) {
                    tasks.filter((e) => {
                        if (!e.members) {
                            results[e.id] = e
                        }
                    })
                }
                if (types['users']['list_user']) {
                    tasks.filter((e) => {
                        if (e.members) {
                            results[e.id] = dataInUser(types['users']['list_user'], e);
                        }
                    })
                }
            }

            // filter task by date
            if (types['date']) {
                if(results && Object.keys(results).length > 0) tasks = Object.values(results);
                var results = {}
                switch (types['date']) { 
                    case 'no':
                        tasks.filter((e) => {
                            if (e && !e.deadline) {
                                results[e.id] = e
                            }
                        })
                        break;
                    case 'out_date':
                        tasks.filter((e) => {
                            if (e && dateHelper.calculateDays(e.deadline) < 0 && e.deadline) {
                                results[e.id] = e
                            }  
                        });
                        break;
                    case 'day_date':
                        tasks.filter((e) => {
                            if (e && e.deadline && dateHelper.calculateDays(e.deadline) > 0 && dateHelper.calculateDays(e.deadline) <= 1) {
                                results[e.id] = e
                            }  
                        });
                        break;
                    case 'week_date': 
                        tasks.filter((e) => {
                            if (e && e.deadline && dateHelper.calculateDays(e.deadline) > 6 && dateHelper.calculateDays(e.deadline) <= 14) {
                                results[e.id] = e
                            }  
                        });         
                    case 'month_date':
                        tasks.filter((e) => {
                            if (e && e.deadline && dateHelper.calculateMonthTask(e.deadline)) {
                                results[e.id] = e
                            }  
                        });
                        break;
                    // default:
                    //     results = tasks
                    //     break;
                }
            }

            //filter task by labels
            if (types['label']) {
                if(results && Object.keys(results).length > 0) tasks = Object.values(results);
                var results = {}
                if (types['label']['no']) {
                    tasks.filter((e) => {
                        if (!e.task_labels) {
                            results[e.id] = e
                        }
                    })
                }

                if (types['label']['id']) {
                    tasks.filter((e) => {
                        console.log(e.id, e.task_labels)
                        if (e.task_labels) {
                            for (const key in types['label']['id']) {
                                const idLabel = types['label']['id'][key];
                                if (e.task_labels[idLabel]) {
                                    results[e.id] = e
                                }
                            }
                        }
                    })
                }
            }
        }
        return results;
    }
}