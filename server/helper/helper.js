'user strict';
var slugify = require('slugify');
const DB = require('./db');
const moment = require('moment'); // require
// moment().format(); 

class Helper{

	constructor(app){
		this.db = DB;
	}

	// async addSocketId(userId, userSocketId){
	// 	try {
	// 		return await this.db.query(`UPDATE users SET socket_id = ?, online= ? WHERE id = ?`, [userSocketId,'Y',userId]);
	// 	} catch (error) {
	// 		console.log(error);
	// 		return null;
	// 	}
	// }

	// async logoutUser(userSocketId){
	// 	return await this.db.query(`UPDATE users SET socket_id = ?, online= ? WHERE socket_id = ?`, ['','N',userSocketId]);
	// }

	// getChatList(userId){
	// 	try {
	// 		return Promise.all([
	// 			this.db.query(`SELECT id, name, socket_id, online, updated_at FROM users WHERE id != ?`, [userId])
	// 		]).then( (response) => {
	// 			return {
	// 				chatlist : response[0]
	// 			};
	// 		}).catch( (error) => {
	// 			console.warn(error);
	// 			return (null);
	// 		});
	// 	} catch (error) {
	// 		console.warn(error);
	// 		return null;
	// 	}
	// }

	async insertMessages(params){
		try {
            var sql = "INSERT INTO chattasks (`task_id`, `user_id`, `content`, `created_at`, `updated_at`) values (?,?,?,?,?)"
            var messenger = [params.current_task, params.user_id, params.messenger, params.created_at, params.updated_at];
            const info = await this.getInfoUser(params.user_id);
			const results = await this.db.query(sql, messenger);
            let data = false
            if (results) {
                const id = results.insertId;
                const message = await this.getDataMessenger(id, '');
                data = message;
                data['info_user'] = info;
            }
            return data;
		} catch (error) {
			console.warn(error);
			return null;
		}
	}

	async getMessages(userId, toUserId){
		try {
			return await this.db.query(
				`SELECT id,from_user_id as fromUserId,to_user_id as toUserId,message,time,date,type,file_format as fileFormat,file_path as filePath FROM messages WHERE
					(from_user_id = ? AND to_user_id = ? )
					OR
					(from_user_id = ? AND to_user_id = ? )	ORDER BY id ASC
				`,
				[userId, toUserId, toUserId, userId]
			);
		} catch (error) {
			console.warn(error);
			return null;
		}
	}

    // insert images
    async insertMedia(data){
		try {
            var title = data.originalname;
            var fileName = data.filename;
            fileName = fileName.split('.');
            var type = fileName[fileName.length - 1];
            delete fileName[fileName.length - 1];
            var slug = slugify(fileName.join(" "));
            var dateNow = moment().format();
            var params = [title, slug, data.filename, type, dateNow, dateNow]
            var sql = "INSERT INTO media (`title`, `slug`, `name_file`,`type`, `created_at`, `updated_at`) values (?,?,?,?,?,?)";
			const results = await this.db.query(sql, params);
            let result = false;
            if (results) {
                const id = results.insertId;
                const media = await this.getDataImages(id);
                result = media;
            }           
            return result;
		} catch (error) {
			console.warn(error);
			return null;
		}
	}
	// async mkdirSyncRecursive(directory){
	// 	var dir = directory.replace(/\/$/, '').split('/');
    //     for (var i = 1; i <= dir.length; i++) {
    //         var segment = path.basename('uploads') + "/" + dir.slice(0, i).join('/');
    //         !fs.existsSync(segment) ? fs.mkdirSync(segment) : null ;
    //     }
	// }

    async getInfoUser(userId){
        var sql = `SELECT * from users WHERE id = ?`
        const result = await this.db.query(sql, userId);
        var data = false;
        if (result) {
            data = result[0]
        }
        return data;
    }
    // get data messener
    async getDataMessenger(id){
        var sql = `SELECT * from chattasks WHERE id = ?`
        const result = await this.db.query(sql, id);
        var data = false;
        if (result) {
            data = result[0]
        }
        return data;
    }
    // getDataImages
    async getDataImages(id){
        var sql = `SELECT * from media WHERE id = ?`
        const result = await this.db.query(sql, id);
        var data = false;
        if (result) {
            data = result[0]
        }
        return data;
    }
    // remove
    async removeMessenger(id){
        var sql = `DELETE from chattasks WHERE id = ?`
        const result = await this.db.query(sql, id);
        var data = false;
        if (result) {
            data = true
        }
        return data;
    }
    //
    async editMessenger(data){
        var sql = `UPDATE chattasks SET ? WHERE id = ?`
        var update = {
            'content': data['content'],
            'status_edit' : 1,
            'updated_at' : data['updated_at']
        }
        var params = [update, data['id']]
        const result = await this.db.query(sql, params);
        var data = false;
        if (result) {
            data = true
        }
        return data;
    }
    // get list user in project by project id
    async getListUserInProject(params){
        try {
            const id = parseInt(params.project_id)
            var sql = `SELECT user_id from project_users WHERE project_id = ?`
            const result = await this.db.query(sql, id);
            return result;
        } catch (error) {
            console.log('error', error)
        }
    }
    // insert status notifications
    async insertNotifications(params){
        const userIds = await this.getListUserInProject(params);
        var results = false;
        if (userIds) {
            let data = [];
            for (const key in userIds) {
                const users = userIds[key];
                data[key] = [
                    parseInt(params.current_task), 
                    parseInt(users.user_id), 
                    0, 
                    0, 
                    params.created_at,
                    null,
                    0,
                    params.created_at,
                    params.created_at, 
                    params.updated_at
                ]                               
            }

            // var sql = "INSERT INTO notifications ( `task_id`, `user_id`, `status`, `status_add_user`, `date_add_user`, `date_remove_user`, `status_messenger`, `create_at_messenger` , `created_at`, `updated_at` ) values ?";
            // this.db.query(sql, [data], function (err, result) {
            //     if (err) throw err;
            //     console.log("Number of records inserted: " + result.affectedRows);
            // });
            results = {}
            // for (const key in userIds) {
            //     const users  = userIds[key];
            //     const result = await this.getNotifications(users.user_id);
            //     if (result.length > 0) {
            //         if (typeof results[users.user_id] == 'undefined') {
            //             results[users.user_id] = result;
            //         }
            //     }
            // }
            return results;
        }
    }
    // insert user notifications
    async actionUserInTask(params){
        try {            
            var results = await this.getDataNotificationByUserID(params);
            var id = false;
            if (results) {
                var data = await this.updatedDataNotifications(results.id, params['action']);
                id = data ? results.id : false
            }else{
                var data = await this.insertDataNotifications(params);
                id = data ? data : false
            }
            var dataNotice = false;
            if (id) {
                dataNotice = await this.getNotifications(id);
                return dataNotice[0];
            }
        } catch (error) {
            console.log('error actionUserInTask', error);
        }
    }
    // insert data notification when add user in tasks
    async insertDataNotifications(params){
        var dateNow = moment().format();
        var data = [
            parseInt(params.task_id), 
            parseInt(params.user_id), 
            parseInt(params.data_author.id), 
            0, 
            0, 
            dateNow,
            null,
            dateNow, 
            dateNow,
        ];
        var sql = "INSERT INTO notifications ( `task_id`, `user_id`, `author_id`, `status`, `status_add_user`, `date_add_user`, `date_remove_user`, `created_at`, `updated_at` ) values (?,?,?,?,?,?,?,?,?)";
        var results = await this.db.query(sql, data); 
        let id = false
        if (results) {
            id = results.insertId
        } 
        return id;
        
    }
    // change data notification when add user in tasks
    async updatedDataNotifications(id, action){
        var dateNow = moment().format();
        var sql = `UPDATE notifications SET ? WHERE id = ?`
        var update = {
            'status': 0,
            'status_add_user' : 0,
        }
        action === 'deactive' ? update['date_remove_user'] = dateNow : update['date_add_user'] = dateNow;
        var updateData = [update, id];
        const result = await this.db.query(sql, updateData);
        var data = false;
        if (result) {
            data = true
        }
        return data;
    }
    // check record exist
    /**
     * task_id is current task, id => user add id, author_id => author add user in task
     * @param {*} params 
     * @returns 
     */ 
    async getDataNotificationByUserID(params){
        try {
            var where = [params['task_id'], params['user_id'], params['data_author']['id']]
            var sql = `SELECT id FROM notifications WHERE task_id = ? AND user_id = ? AND author_id = ?`;
            const result = await this.db.query(sql, where);
            var data = false;
            if (result.length > 0) {
                data = result[0]
            }
            return data;
        } catch (error) {
            console.log('error', error);
        }  
    }
    // get notifications
    async getNotifications(id){
        let data = false;
        try {
            // const id = parseInt(id)
            // var sql = `SELECT n.id AS id ,n.task_id AS task_id, n.user_id AS user_id, n.status AS status, n.status_add_user AS status_add_user, n.date_add_user AS date_add_user, n.date_remove_user AS date_remove_user, n.status_messenger AS status_messenger, n.create_at_messenger AS create_at_messenger, n.created_at AS created_at, n.updated_at AS updated_at, t.slug AS slug_tasks, p.slug as slug_project FROM notifications n JOIN tasks t ON t.id = n.task_id JOIN projects p ON p.id = t.project_id WHERE n.user_id = ? AND n.status != 1`;
            // var sql = `SELECT n.id AS id ,n.task_id AS task_id, n.user_id AS user_id, n.status AS status, n.status_add_user AS status_add_user, n.date_add_user AS date_add_user, n.date_remove_user AS date_remove_user, n.created_at AS created_at, n.updated_at AS updated_at, t.slug AS slug_tasks, p.slug as slug_project FROM notifications n JOIN tasks t ON t.id = n.task_id JOIN projects p ON p.id = t.project_id WHERE n.id = ?`;
            var sql = `SELECT n.id AS id ,n.task_id AS task_id, n.user_id AS user_id, n.author_id AS author_id, n.status AS status, n.status_add_user AS status_add_user, n.date_add_user AS date_add_user, n.date_remove_user AS date_remove_user, n.created_at AS created_at, n.updated_at AS updated_at, t.slug AS slug_task, t.title AS name_task, p.slug as slug_project, p.title as name_project FROM notifications n JOIN tasks t ON t.id = n.task_id JOIN projects p ON p.id = t.project_id WHERE n.id = ?`;
            const result = await this.db.query(sql, id);
            if (result) {
                data = result
            }
            return data;
        } catch (error) {
            console.log('error', error)
        }
    }
    // get data user id

}
module.exports = new Helper();