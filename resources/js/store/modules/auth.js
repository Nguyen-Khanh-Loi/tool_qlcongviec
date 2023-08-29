import Router from '../../router';

const state = {
    loginResponse: {}, // check login 
    authUser: {}, // get user data
    authUserData: {}, // user data
    dataNotifications: [], // notification data by user
    departmentId: false// 
};

const getters = {
    getLoginResponse: state => state.loginResponse,
    getAuthUser: state => state.authUser,
    authUserData: state => state.authUserData,
    dataNotifications: state => state.dataNotifications,
    departmentId: state => state.departmentId,
};

const actions = {
    login({ commit, getters }, loginData) {
        axios.get('/sanctum/csrf-cookie').then(() => {
            axios
                .post('/api/login', {
                    email: loginData.email,
                    password: loginData.password,
                })
                .then(response => {
                    commit('mutateLoginResponse', response.data);
                    sessionStorage.setItem(
                        'loginResponse',
                        JSON.stringify(response.data)
                    );
                    if (getters.getLoginResponse.response_type == 'success') {
                        axios.get('/api/user').then(response => {
                            if (response.status == 200) {
                                commit('mutateAuthUser', response.data);
                                sessionStorage.setItem(
                                    'authUser',
                                    JSON.stringify(response.data)
                                );
                                // Router.push('/dashboard');
                                var redirect = sessionStorage.getItem('redirected');
                                if (redirect) {
                                    redirect = JSON.parse(redirect);
                                    window.location.replace(redirect.href);
                                    sessionStorage.removeItem('redirected');
                                    return
                                }
                                window.location.replace('/dashboard');
                            }
                        });
                    } else {
                        alert(getters.getLoginResponse.response_data[0]);
                    }
                });
        });
    },
    firstLogin({ commit, getters }, loginData) {
        axios.get('/sanctum/csrf-cookie').then(() => {
            axios
                .post('/api/first-login', {
                    email: loginData.email,
                    password: loginData.password,
                    repassword: loginData.repassword,
                    is_first_login: loginData.is_first_login
                })
                .then(response => {
                    commit('mutateLoginResponse', response.data);
                    sessionStorage.setItem(
                        'loginResponse',
                        JSON.stringify(response.data)
                    );
                    if (getters.getLoginResponse.response_type == 'success') {
                        axios.get('/api/user').then(response => {
                            if (response.status == 200) {
                                commit('mutateAuthUser', response.data);
                                sessionStorage.setItem(
                                    'authUser',
                                    JSON.stringify(response.data)
                                );
                                // Router.push('/dashboard');
                                var redirect = sessionStorage.getItem('redirected');
                                if (redirect) {
                                    redirect = JSON.parse(redirect);
                                    window.location.replace(redirect.href);
                                    sessionStorage.removeItem('redirected');
                                    return
                                }
                                window.location.replace('/dashboard');
                            }
                        });
                    } else {
                        alert(getters.getLoginResponse.response_data[0]);
                    }
                });
        });
    },

    auth({ commit }) {
        commit('mutateAuthUserData', JSON.parse(sessionStorage.getItem('authUser')));
    },

    logout() {
        document.getElementById("preloader").style.display = "block";
        document.getElementById("status").style.display = "block";
        axios.get('/api/logout').then((response) => {
            sessionStorage.removeItem('loginResponse');
            sessionStorage.removeItem('authUser');
            Router.push('/');
            setTimeout(() => {
                window.location.reload('/')
            }, 100);

        });
    },

    storeUpdateUser({ commit, getters }) {
        axios.get('/sanctum/csrf-cookie').then(() => {
            axios.get('/api/user').then(response => {
                if (response.status === 200) {
                    commit('mutateAuthUser', response.data);
                    commit('mutateAuthUserData', response.data);
                    sessionStorage.setItem(
                        'authUser',
                        JSON.stringify(response.data)
                    );
                }
            });
        });
    },

    //add department id when role admin
    department({ commit }, id) {
        commit('mutateDepartmentId', id);
        sessionStorage.setItem(
            'departmentId', id
        );
    },

    // notification
    notifications({ commit }){
        axios.post('/api/notifications')
        .then(response => {
            if (response.status == 200) {
                if (response.data.status == 200) {
                    commit('mutateDataNotifications', response.data.data);
                }
                
            }
        });
    },
    // upload change avata
    // upload file 
    async uploadAvatar({commit}, data){
        var config = {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        }
        let res = await axios.post(`/api/user/upload`, data, config);
        let results = false;
        if (res.status == 200 && res.data) {
            commit('mutateAuthUser', res.data.data);
            commit('mutateAuthUserData', res.data.data);
            sessionStorage.setItem(
                'authUser',
                JSON.stringify(res.data.data)
            );
            results = res.data.data;
        }
        return results;
    },
    // lấy tất cả user quản lý dự án
    async getListUserProjectManager({commit}){
        let res = await axios.post(`/api/user/project-manager`);
        if (res.status == 200) {
            return res.data;
        }
    },    
};

const mutations = {
    mutateLoginResponse: (state, payload) => (state.loginResponse = payload),
    mutateAuthUser: (state, payload) => (state.authUser = payload),
    mutateAuthUserData: (state, payload) => (state.authUserData = payload),
    mutateDepartmentId: (state, payload) => (state.departmentId = payload),
    mutateDataNotifications: (state, payload) => (state.dataNotifications = payload),
};

export default {
    state,
    getters,
    actions,
    mutations,
};
