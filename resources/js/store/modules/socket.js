import { io } from "socket.io-client";
console.log('process.env.NODE_ENV', process.env.NODE_ENV);
const URL = process.env.NODE_ENV === "development" ? 'ws://localhost:3000' : "wss://base.nemosoftware.nets:7979";
export const socket = io(URL);
const state = {
    loadChat: false,
    connectSocket: false,
    listMessengers: [],
};

const getters = {
    loadChat: state => state.loadChat,
    connectSocket: state => state.connectSocket,
    listMessengers: state => state.listMessengers,
};

const actions = {
    connect: ({commit}, slug) => {
        socket.on("connect", () => {
            console.log('connect', socket);
            commit('setConnect', true);
        });
    },
    disconnect: ({commit}) => {
        socket.on("disconnect", () => {
            console.log('disconnect', socket);
            commit('setConnect', false);
        });
    },
    joinRoom({commit}, id){
        socket.emit('join room', id);
    },
    // user id join in room
    joinUser({commit}, id){
        socket.emit('join user', id);
    },
    onEventChat({commit}, data){
        console.log('event', data);
        socket.emit(data['event'], data);
    },
    addDataMessenger({commit}, data){
        commit('setListMessengers', data);
    }
};

const mutations = {
    setConnect: (state, payload) => (state.connectSocket = payload),
    setListMessengers: (state, payload) => (state.listMessengers.unshift(payload)),
};

export default {
    state,
    getters,
    actions,
    mutations,
};