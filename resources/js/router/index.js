import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/layouts/Dashboard.vue';
import AllUser from '../components/layouts/AllUser.vue';

import Profile from '../components/layouts/profile/Profile.vue';
import ChangePasswordProfile from '../components/layouts/profile/ChangePassword.vue';
import Single from '../components/layouts/single-project.vue';
import Login from '../components/account/Login.vue';
import LoginIdentifier from '../components/account/LoginIdentifier.vue';

import LoginChallengeComponent from '../components/account/LoginChallengeComponent.vue';
import ViewProject from '../components/projects/ViewProject.vue';
import Analytics from '../components/projects/Analytics.vue';
import Create from '@/js/components/projects/Create.vue';
import Mytask from '@/js/components/mytask/Index.vue';
import Projects from '@/js/components/projects/ListProjects.vue';

const ErrorPage = {
    template: '<div>403 - Access denied</div>'
};
export const routes = [
    {
        name: 'login',
        path: '/login',
        component: LoginIdentifier,
    },
    {
        path: "/login-challenge/:email/:is_first_login",
        name: "Login Challenge",
        component: LoginChallengeComponent
    },
    {
        name: 'dashboard',
        path: '/dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },

    {
        name: 'alluser',
        path: '/alluser',
        component: AllUser,
        meta: { requiresAuth: true, roles: ['administrator', 'leader'] }
    },
    {
        name: 'home',
        path: '/',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    // {
    //     name: 'dashboard',
    //     path: '/',
    //     component: Dashboard,
    //     meta: { requiresAuth: true }
    // },
    {
        path: '/analytics/:slug',
        name: 'analytics',
        component: Analytics,
        meta: { requiresAuth: true }
    },

    {
        path: '/project/:slug',
        name: 'project',
        component: ViewProject,
        meta: { requiresAuth: true }
    },
    {
        path: '/project/:slug/:name',
        name: 'viewtask',
        component: ViewProject,
        meta: { requiresAuth: true }
    },
    {
        name: 'single',
        path: '/single',
        component: Single,
        meta: { requiresAuth: true }
    },
    {
        name: 'Profile User',
        path: '/profile-user',
        component: Profile,
        meta: { requiresAuth: true }
    },
    {
        name: 'Change Password',
        path: '/change-pasword',
        component: ChangePasswordProfile,
        meta: { requiresAuth: true }
    },
    {
        path: '/error',
        name: 'error',
        component: ErrorPage
    },
    {
        path: '/create-project',
        name: 'create-project',
        meta: { requiresAuth: true, roles: ['administrator', 'manager'] },
        component: Create
    },    
    {
        path: '/my-task',
        name: 'my-task',
        meta: { requiresAuth: true },
        component: Mytask
    },
    {
        path: '/list-projects',
        name: 'list-projects',
        component: Projects
    },

];
const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    if (to.redirectedFrom) {
        if(!isAuthenticated()){
            sessionStorage.setItem(
                'redirected',
                JSON.stringify(to.redirectedFrom)
            );
        }
    }
    const roles = to.meta.roles;
    let authUser = undefined;
    authUser = JSON.parse(sessionStorage.getItem('authUser'))
    if (roles && roles.length > 0 && authUser) {
        const userRoles = authUser.roles;
        const hasAccess = userRoles.some(role => roles.includes(role.name));
        if (!hasAccess) {
            next('/error');
            return;
        }
    }
    if (isAuthenticated() || to.name === 'login' || to.name === 'Login Challenge') {
        if (to.name === 'project' || to.name === 'viewtask') {
            var data = {'slug': to.params.slug}
            axios
            .post('/api/project/show', data)
            .then((response) => {
                if (response.status == 200) {
                    const checkUserProject = response.data.check_user;
                    if (!checkUserProject) {
                        next('/error');
                        return;
                    }
                    next();
                }                        
            })
            .catch((error) => {
                console.log('error', error)
            })
            .finally();
            return
        }
        next();
    } else {
        // Not logged in, redirect to login.        
        next({ name: 'login' })
    }
});

function isAuthenticated() {
    // Check if the user is authenticated
    let output = undefined;

    if (sessionStorage.getItem('loginResponse')) {
        output = true;
    } else {
        output = false
    }
    return output;
}

export default router