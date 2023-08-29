import { createStore } from 'vuex';
import Roles from './modules/roles';
import Permissions from './modules/permissions';
import Auth from './modules/auth';
import Project from './modules/project';
import Task from './modules/task';
import Socket from './modules/socket';
import Codes from './modules/codes';
export const store = createStore({
    modules: {
        Roles,
        Permissions,
        Auth,
        Project,
        Task,
        Socket,
        Codes
    },
});