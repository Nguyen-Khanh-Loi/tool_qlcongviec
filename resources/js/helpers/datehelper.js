import moment from "moment";
export const dateHelper = {
    formatDate,
    calculateDays,
    calculateMonthTask, 
    showDateDuration
};
function formatDate(data){
    var format = data['format'] ? data['format'] : 'YYYY-MM-DD HH::mm:ss'
    return moment(data['date']).format(format);
}
function calculateDays(dateTasks){
    var today    = moment(new Date());
    dateTasks    = moment(new Date(dateTasks))
    var duration = moment.duration(dateTasks.diff(today));
    var days     = Math.round(duration.asDays());
    return days;
}
function calculateMonthTask(dateTasks){
    const date = moment().add(1, 'month'); // current date
    const firstDayOfMonth = date.startOf('month');
    const currentDateTasks = moment(new Date(dateTasks));
    const firstDayDeadline = currentDateTasks.startOf('month');
    var result = false;
    if (firstDayDeadline.unix() == firstDayOfMonth.unix()) {
        result = true;
    }
    return result
}

function showDateDuration(dateMessenger){
    var today     = moment(new Date());
    dateMessenger = moment(new Date(dateMessenger))
    var duration = moment.duration(dateMessenger.diff(today));
    var days     = Math.round(duration.asDays());
    var timeMessenger = ' [at] '+moment(dateMessenger).format('HH:ss');
    var results = moment().add(days, 'days').calendar({
        sameDay: '[Today]'+ timeMessenger,
        nextDay: '[Tomorrow]' + timeMessenger,
        nextWeek: 'dddd'+ timeMessenger,
        lastDay: '[Yesterday]' + timeMessenger ,
        lastWeek: '[Last] dddd'+ timeMessenger,
        sameElse: function (now) {
            if (this.isBefore(now)) {
                return 'DD/MM/YYYY'+ timeMessenger;
            } else {
                return 'DD/MM/YYYY'+ timeMessenger;
            }
        }
    });
    return results;
}