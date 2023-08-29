const state = {
    allCodes: [],
};

const getters = {
    allCodes: state => state.allCodes,
};
const actions = { 
    async getAllCodes({commit}){
        let res = await axios.post('/api/codes/index');
        if (res.status == 200) {
            commit('setAllCodes', res.data); 
            return res.data           
        }
    }
};

const mutations = {
    setAllCodes: (state, payload) => (state.loadingState = payload),
};
export default {
    state,
    getters,
    actions,
    mutations
};