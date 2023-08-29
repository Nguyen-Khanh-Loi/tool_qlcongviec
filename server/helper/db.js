var mysql = require('mysql');
const dotenv = require('dotenv');
dotenv.config();
console.log('process', process.env.DB_HOST);
const hostMySQL = process.env.DB_HOST;
const DBMySQL = process.env.DB_NAME;
const userDB = process.env.DB_USER;
const passDB = process.env.DB_PASS;
// var con;
class Db {
	constructor() {
		this.connection = mysql.createConnection({
            host: hostMySQL,
            user: userDB,
            password: passDB,
            database: DBMySQL
        });
	}
	query(sql, args) {
		return new Promise((resolve, reject) => {
			this.connection.query(sql, args, (err, rows) => {
				if (err)
					return reject(err);
				resolve(rows);
			});
		});
	}
	close() {
		return new Promise((resolve, reject) => {
			this.connection.end(err => {
				if (err)
					return reject(err);
				resolve();
			});
		});
	}
}
module.exports = new Db();
// class connectDatabase {
//     constructor() {
//         this.connectMySql()
//     }
//      /**
//     * connect database mysql 
//     */
//     connectMySql(){
//         try {
//             con = mysql.createConnection({
//                 host: hostMySQL,
//                 user: userDB,
//                 password: passDB,
//                 database: DBMySQL
//             });
//             con.connect(function(err) {
//                 if (err) throw err;
//                     console.log("Connected DB Mysql!");
//                 }
//             )
//         } catch (error) {
//             console.log("Failed to connect to mysql: " + error);
//         }
//     }
//     /**
//      * messenger chat, info user
//      * @param {*} data 
//      */
//     insertData(data){
//         try {
//             var dataInsert = [data['current_task'], data['user_id'], data['messenger']];
//             console.log('dataInsert', dataInsert);
//             var sql = `INSERT INTO chattasks ( task_id, user_id, content ) VALUES ( ${data['current_task']}, ${data['user_id']}, ${data['messenger']}  )`;
//             con.query(sql, function (err, result, fields) {
//                 if (err) throw err;
//                 console.log(result);
//             });
//         } catch (error) {
//             console.log("Failed to connect to mysql: " + error);
//         }
//     }
// }
// module.exports = new connectDatabase()