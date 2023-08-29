const state = {
    listTasks: {}, // get all task of project
    listCard: {}, // get all cart
    currentTask: {}, // get current task of project
    listUsers: {}, // get list user of department
    listTaskDraggable: {}, // get list task add drag,
    taskDraggableStore: {}, // get list task add drag,
    listUserProject: {}, // get list user of project,
    listItemLabels: {}, // get list label of task,
    projectInfo: false,
    loadingStatus: false,
    myTasks:[],
    listCardsInMyTasks: {},
};
const getters = {
    listTasks: state => state.listTasks,
    listCard: state => state.listCard,
    currentTask: state => state.currentTask,
    listUsers: state => state.listUsers,
    listTaskDraggable: state => state.listTaskDraggable,
    taskDraggableStore: state => state.taskDraggableStore,
    projectUsers: state => state.listUserProject,
    listItemLabels: state => state.listItemLabels,
    projectInfo: state => state.projectInfo,
    loadingStatus: state => state.loadingStatus,
    myTasks: state => state.myTasks,
    listCardsInMyTasks: state => state.listCardsInMyTasks,
};
const actions = {   
    // get all task of user
    async getListMyTasks({commit}){
        let res = await axios.post(`/api/my-tasks/index`);
        if (res.status == 200) {
            commit('setMyTasks', res.data.data);
            if (res.data.data.length > 0) {
                commit('setTask', res.data.data); 
            }           
            commit('setLabels', res.data.labels);
            commit('setCard', res.data.cards);
            return res.data.data;
        }
    },
    // get all task
    async getListTasks({commit}, data) {        
        let res = await axios.post(`/api/tasks/index`, data) 
        if (res.status == 200) {
            commit('setTask', res.data.list_task); 
            commit('setTaskDraggable', res.data.list_draggable);
            commit('setTaskDraggableStore', res.data.list_draggable);
            commit('setCard', res.data.cards);
            commit('setListUsers', res.data.list_user);
            commit('setLabels', res.data.labels); 
            commit('setProjectInfo', res.data.project); 
            if (res.data.project_users.length == 0) {
                commit('setListProjectUsers', {});
            } else{
                commit('setListProjectUsers', res.data.project_users);
            }
             
        }
    },
    // get list user in projects
    //get data current tasks
    getListUsersInProject({commit}, data) {
        commit('setListProjectUsers', data);
    },
    // create new task task
    async createNewTask({ commit }, data){
        let res = await axios.post(`/api/tasks/create`, data); 
        if (res.status == 200 ) {
            return res.data;
        }
    },
    // update data task
    async updateTask({ commit }, data){
        if (!state.loadingStatus) {
            commit('loadingStatus', true);
            let res = await axios.post(`/api/tasks/update`, data);
            if (res.status == 200) { 
                commit('setCurrentTask', res.data);  
                commit('loadingStatus', false);           
            }
            return res;
        }        
    },

    // add new work to do
    async createWorkTodo({ commit }, data){
        let res = await axios.post(`/api/todo/create`, data);
        if (res.status == 200) { 
            return res.data;
        }
    },

    // remove work to do
    async removeWorkTodo({ commit }, id){
        let res = await axios.post(`/api/todo/remove/${id}`);
        return res.status;
    },

    // add check list
    async addCheckList({ commit }, data){
        let res = await axios.post(`/api/checklist/create`, data);
        if (res.status == 200) { 
            return res.data;
        }
    },
    // update check list
    async updatedChecklist({ commit }, data){
        let res = await axios.post(`/api/checklist/update/${data['id']}`, data);
        return res.data;
    },
    // remove check in work todo
    async removeCheckList({commit}, id){
        let res = await axios.post(`/api/checklist/remove/${id}`);
        return res.status;
    },

    // upload file 
    async uploadFile({commit}, data){
        var config = "";
        if (typeof data['url'] == 'undefined') {
            config = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }
        }
        let res = await axios.post(`/api/tasks/store`, data, config);
        return res.data;
    },
    // remove File upload
    async removeFilesMedia({commit}, data){
        let res = await axios.post(`/api/media/remove/${data['media_id']}`, data);
    },
    //get data current tasks
    getCurrentTask({commit}, data) {
        commit('setCurrentTask', data);
    },
    //get list card in my task
    getListCardsInMyTasks({commit}, data) {
        commit('setListCardsInMyTasks', data);
    },
};
const mutations = {
    setCard(state, payload){
        state.listCard = payload;
    },
    setTask(state, payload){       
        state.listTasks = payload;
    },
    setTaskDraggable(state, payload){       
        state.listTaskDraggable = payload;
    },
    setCurrentTask(state, payload){       
        state.currentTask = payload;
    },
    setListUsers(state, payload){       
        state.listUsers = payload;
    },
    setListProjectUsers(state, payload){       
        state.listUserProject = payload;
    },
    setLabels(state, payload){
        state.listItemLabels = payload;
    },
    setMyTasks(state, payload){
        state.myTasks = payload;
    },
    setListCardsInMyTasks(state, payload){
        state.listCardsInMyTasks = payload;
    },
    loadingStatus: (state, payload) => (state.loadingStatus = payload),
    setProjectInfo: (state, payload) => (state.projectInfo = payload),
    setTaskDraggableStore: (state, payload) => (state.taskDraggableStore = payload),
};

export default {
    state,
    getters,
    actions,
    mutations,
};