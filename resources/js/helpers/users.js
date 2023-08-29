export const userHelper = {
    fullName,
    avatar,
    checkRoles,
    checkUserInProjects
};
function fullName(data){
    if (data.first_name || data.last_name){
        if (data.first_name && !data.last_name) {
            return data.first_name
        }else if(!data.first_name && data.last_name){
            return data.last_name
        }else{
            return data.first_name+' '+data.last_name
        }
    }else{
        return data.name
    };
};
function avatar(url){
    var avatar = 'images/avatar.png';
    var path   = process.env.PUBLIC_URL;
    var src    = path+avatar
    if (url) {
        src = path+'users/'+url;
    }
    return src;
}
function checkRoles(roleName) {
    const listRoles = ['leader', 'administrator'];
    return listRoles.some((role) => listRoles.includes(roleName));
}
// check user in project
function checkUserInProjects(data){
    let result = false;
    if (data['listUsers']) {
        for (const key in data['listUsers']) {
            const item = data['listUsers'][key];
            if (item.user_id == data.user.id) {
                result = true;
                break;
            }
        }
    }
    return result;
}