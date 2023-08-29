const express = require("express");
var cors = require('cors')
const app = express();
const http = require("http");
var path = require('path');
const server = http.createServer(app);
const { Server } = require("socket.io");
const fs = require('fs');
const helper = require("./helper/helper");
app.use(cors())
const io = new Server(server, {
    cors: {
        origin: "http://localhost:8000",
    },
});
const indexRouter = require('./routers/index');
class ServerChat{   
    constructor(){
        this.connectSocket();
        this.connectServer();
        this.createFolderImages();
        this.routerApp();        
    }
    // connect socket
    connectSocket(){
        io.on("connection", async (socket) => {
            socket.join('room user');
           
            socket.on("join room", (data) => {
                console.log('join room', data); 
                socket.join(data);         
            });
            
            socket.on("join user", (user_id) => {
                console.log('user_id', user_id);
                socket.join(user_id);
            });
            // add messenger
            socket.on("send messenger", async function (data) { 
                const result = await helper.insertMessages(data);
                if (result) {
                    io.in(data['room']).emit("listen messenger", result);
                }
            });
            // remove messenger
            socket.on("remove messenger", async function (data) {    
                const result = await helper.removeMessenger(parseInt(data['id']));
                if (result) {
                    io.in(data['room']).emit("listen remove messenger", data);
                }
            });
            // edit messenger
            socket.on("edit messenger", async function (data) {   
                const result = await helper.editMessenger(data);
                if (result) {
                    io.in(data['room']).emit("listen edit messenger", data);
                }
            });
            // add remove user in task
            socket.on("user in tasks", async (data) => {
                let result = await helper.actionUserInTask(data);
                if (result) {
                    result['author'] = data['data_author'];
                    /**
                     *  id user id add in task
                     *  author data
                     */
                    // socket.broadcast.to(data['id']).emit('listen notifications', 'this is a test');
                    socket.to(data['id']).emit('listen notifications', result);
                    // io.in(data['id']).emit('listen notifications', result);
                    // socket.broadcast.to('room user').emit('listen notifications', result);
                }
            })

            // request task
            socket.on("request tasks", async (data) => {
                console.log('data create task', data.data.user_id);
                // io.in(data.data.user_id).emit("notification task", data.data);
                io.in(data.projects.slug).emit("notification task", data.data);//
            });

            socket.on("request create tasks", async (data) => {
                // send notification for leader when create task
                var id_leader = data.leader[0].id;
                io.in(id_leader).emit("notification task", data.tasks);
            });

            socket.on("disconnect", (socket) => {
                console.log("disconnect");
            });
        });
    }
    // connect server
    connectServer(){
        server.listen(3000, () => {
            console.log("listening on *:3000");
        });
    }
    // create folder
    createFolderImages(){
        var dir2 = './images';
        if (!fs.existsSync(dir2)){
            fs.mkdirSync(dir2);
        }
        var dir = './images/uploads';
        if (!fs.existsSync(dir)){
            fs.mkdirSync(dir);
        }
    }
    //router
    routerApp(){
        app.set('views', path.join(__dirname, 'views'));
        app.set('view engine', 'ejs');
        app.use('/', indexRouter);
    }
}
new ServerChat();