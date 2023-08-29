import moment from "moment";
import { staticProject } from "@/js/helpers/statisproject";
const state = {
    listProjects: [],
    projectData:{},
    loadingState: false,
    currentProject: {},
    paginateProject:"",
    totalPageProject:"",
    totalProject: 0,
    projectSiderBar:[], // show list project in siderbar
    titleProject:"",
    breadcrumbProject:[],
    dataChartProject:[],
};

const getters = {
    listProjects: state => state.listProjects,
    projectData: state => state.projectData,
    currentProject: state => state.currentProject,
    paginateProject: state => state.paginateProject,
    totalPageProject: state => state.totalPageProject,
    totalProject: state => state.totalProject,
    projectSiderBar: state => state.projectSiderBar,
    titleProject: state => state.titleProject,
    breadcrumbProject: state => state.breadcrumbProject,
    dataChartProject: state => state.dataChartProject,
};
const actions = {  

    /**
     * get all project 
     * @param {*} param0 
     */
    async getProjects({ commit }) {
        commit('loadingState', true);
        let res = await axios.post('/api/project/index');
        if (res.status == 200) {
            if (res.data.data) {
                commit('addItemProjects', res.data.data);
            }
            commit('setPaginateProject', res.data.current_page);
            commit('setTotalPageProject', res.data.last_page);
            commit('setTotalProject', res.data.total);
            commit('loadingState', false);
            return res.data;
        }
    },
     /**
     * create project
     * @param {*} param0 
     */
    async createProject({commit}, data) {
        var config = "";
        if (data['file']) {
            let formData = new FormData();
            formData.append('file', data['file']);
            formData.append('end_time', data['end_time']);
            formData.append('description', data['description']);
            formData.append('start_time', data['start_time']);
            formData.append('title', data['title']);
            formData.append('code', data['code']);
            formData.append('code_id', data['code_id']);// mã khu mực dự án
            formData.append('code_company_center', data['code_company_center']);
            formData.append('type', 'projects');
            config = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }
            let res = await axios.post(`/api/project/create`, formData, config);
            if (res.status == 200) {
                return res.data;
            }
        }else{
            let res = await axios.post(`/api/project/create`, data);
            if (res.status == 200) {
                return res.data;
            }
        }
    },
    /**
     * update project
     * @param {data} 
     */
    async updateProject({commit}, data){
        var config = "";
        if (data['file']) {
            let formData = new FormData();
            formData.append('file', data['file']);
            formData.append('id', data['id']);
            formData.append('title', data['title']);
            formData.append('start_time', data['start_time']);
            formData.append('end_time', data['end_time']);
            formData.append('description', data['description']);
            formData.append('type', 'projects');
            config = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }
            let res = await axios.post(`/api/project/update`, formData, config);
            
            if (res.status == 200) {
                return res.data;
            }
        }else{
            const dataUpdate = {
                'title': data['title'],
                'start_time': data['start_time'],
                'end_time': data['end_time'],
                'description': data['description'] ? data['description'] : '',
                'url_image': data['url_image'] ? data['url_image'] : '',
            }
            var _dataUpdate = {
                'id': data['id'],
                'data': dataUpdate,
            }
            if (data['action'] == 'remove') {
                _dataUpdate['data'] = {
                    'active': 1
                }
            }
            let res = await axios.post(`/api/project/update`, _dataUpdate);
            if (res.status == 200) {
                return res.data;
            }
        }
        console.log('res', res);
    },
    /**
     * remove or add user in project
     * @param {data} data => project id, user ud, action 
     */
    async addRemoveUserInProject({ commit }, data){
        let res = await axios.post(`/api/project/adduser`, data);
        return res.data;
    },

    /**
     * get current project 
     * @param {data} data => project id, user ud, action 
     */
    async getProject( {commit}, data ){
        commit('loadingState', true);
        const resultData = await axios.post('/api/project/show', data)
        .then(res => {
            if (res.status == 200) {
                if (data['slug']) {
                    commit('setCurrentProject', res.data);
                    commit('setTitleProject', res.data.title);
                    var breadcrumb = [
                        {
                            text: "Gosu",
                            href: "/",
                        },
                        {
                            text: res.data.title,
                            active: true,
                        },
                    ]
                    commit('setBreadcrumbProject', breadcrumb);
                    var resultDataChart = staticProject.statisticalProject(res.data.departments);
                    commit('setDataChartProject', resultDataChart);
                    return res.data;
                }
                return res.data;
            }
        });
        return resultData;
    },
    /**
     * add title
     */    
    addTitleProject({commit},title){
        commit('setTitleProject', title);
    },
    /**
     * add breadcrumb
     */    
    addBreadcrumbProject({commit}, breadcrumb){
        commit('setBreadcrumbProject', breadcrumb);
    },
    /**
     * add current project
     */    
    addCurrentProject({commit}, data){
        commit('setCurrentProject', data);
    },
    /**
     * add addDataChartProject
     */    
    addDataChartProject({commit}, data){
        commit(' setDataChartProject', data);
    },    
    /**
     * get project show siderbar
     */
    async getProjectSiderBar({commit}){
        var params = {'number': 12}
        let res = await axios.post('/api/project/index', params);
        if (res.status == 200) {
            commit('setProjectSiderBar', res.data); 
            return res.data           
        }
    },
    /**
     * add remove deparrtment in project
     */
    async departmentInProject({commit},data){
        let res = await axios.post('/api/project/department', data);
        if (res.status == 200) {
            return res.data           
        }
    },
};

const mutations = {
    loadingState: (state, payload) => (state.loadingState = payload),
    addItemProjects: (state, payload) => (state.listProjects = payload),
    setCurrentProject: (state, payload) => (state.currentProject = payload),
    setPaginateProject: (state, payload) => (state.paginateProject = payload),
    setTotalPageProject: (state, payload) => (state.totalPageProject = payload),
    setTotalProject: (state, payload) => (state.totalProject = payload),
    setProjectSiderBar: (state, payload) => (state.projectSiderBar = payload),
    setTitleProject: (state, payload) => (state.titleProject = payload),
    setBreadcrumbProject: (state, payload) => (state.breadcrumbProject = payload),
    setDataChartProject: (state, payload) => (state.dataChartProject = payload),
};
export default {
    state,
    getters,
    actions,
    mutations
};