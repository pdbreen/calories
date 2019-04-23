import Vue from 'vue'
import VueRouter from 'vue-router'

import store from './store/index';

Vue.use(VueRouter)

function requireAuth(to, from, next) {

    function proceed() {
        if (store.getters.loggedIn) {
            if ((to.meta.permission === 'user_manager' && store.getters.user.role < 1) ||
                (to.meta.permission === 'admin' && store.getters.user.role < 2) ||
                (to.meta.permission === 'guest')) {
                next('/')
            } else {
                next()
            }
        } else {
            if (to.meta.permission !== 'guest') {
                next('/login')
            } else {
                next()
            }
        }
    }

    if (store.getters.userLoadStatus === 0) {
        store.dispatch('loadUser');
        store.watch((state, getters) => getters.userLoadStatus, () => { proceed() })
    } else {
        proceed()
    }
}

import Login from './views/Login'
import Register from './views/Register'
import MealsIndex from './views/meals/MealsIndex'
import MealsEdit from './views/meals/MealsEdit'
import UsersIndex from './views/users/UsersIndex'
import UsersEdit from './views/users/UsersEdit'

export default new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/login',
            name: 'login',
            component: Login,
            beforeEnter: requireAuth,
            meta: {
                permission: 'guest'
            }
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
            beforeEnter: requireAuth,
            meta: {
                permission: 'guest'
            }
        },
        {
            path: '/',
            name: 'meals.index',
            component: MealsIndex,
            beforeEnter: requireAuth,
            meta: {
                permission: 'user'
            }
        },
        {
            path: '/meals/:meal_id/edit',
            name: 'meals.edit',
            component: MealsEdit,
            beforeEnter: requireAuth,
            meta: {
                permission: 'user'
            }
        },
        {
            path: '/users/:user_id/add-meal',
            name: 'meals.create',
            component: MealsEdit,
            beforeEnter: requireAuth,
            meta: {
                permission: 'user'
            }
        },
        {
            path: '/account/:user_id/edit',
            name: 'account',
            component: UsersEdit,
            beforeEnter: requireAuth,
            meta: {
                permission: 'user'
            }
        },
        {
            path: '/users/:user_id/meals',
            name: 'users.meals',
            component: MealsIndex,
            beforeEnter: requireAuth,
            meta: {
                permission: 'admin'
            }
        },
        {
            path: '/users/:user_id/edit',
            name: 'users.edit',
            component: UsersEdit,
            beforeEnter: requireAuth,
            meta: {
                permission: 'user_manager'
            }
        },
        {
            path: '/users',
            name: 'users.index',
            component: UsersIndex,
            beforeEnter: requireAuth,
            meta: {
                permission: 'user_manager'
            }
        },
        {
            path: '/users/create',
            name: 'users.create',
            component: UsersEdit,
            beforeEnter: requireAuth,
            meta: {
                permission: 'user_manager'
            }
        },
        {
            path: '*',
            redirect: '/'
        }
    ],
});
