import api from '../../api/users';
import axios from "axios";

const state = {
    user: {},
    userLoadStatus: 0,
    accessToken: null,
};

// getters
const getters = {
    user: state => state.user,
    userLoadStatus: state => state.userLoadStatus,
    loggedIn: state => state.userLoadStatus === 2,
    accessToken: state => state.accessToken,
};

const actions = {
    login({ commit }, data) {
        return axios.post(`/api/login`, data).then(response => {
            commit('setAccessToken', response.data.token);
            commit('setUserLoadStatus', 0);
        });
    },
    register( {commit}, data) {
        return axios.post(`/api/register`, data).then(response => {
            commit('setAccessToken', response.data.token);
            commit('setUserLoadStatus', 0);
        });
    },
    loadUser( { state, commit } ){
        commit( 'setUserLoadStatus', 1 );
        if (!state.accessToken) {
            commit('setAccessToken', localStorage.getItem('accessToken'));
        }
        api.getUser()
            .then( function( response ){
                commit( 'setUser', response.data.data );
                commit( 'setUserLoadStatus', 2 );
            })
            .catch( function(){
                commit( 'setUser', {} );
                commit( 'setUserLoadStatus', 3 );
            });
    },

    logout( { commit } ){
        commit( 'setAccessToken', null );
        commit( 'setUserLoadStatus', 0 );
        commit( 'setUser', {} );
    }
};

const mutations = {
    setUserLoadStatus( state, status ){
        state.userLoadStatus = status;
    },

    setUser( state, user ){
        state.user = user;
    },

    setAccessToken( state, token){
        if (token) {
            localStorage.setItem('accessToken', token);
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        } else {
            localStorage.removeItem('accessToken');
            delete axios.defaults.headers.common['Authorization'];
        }
    }
};

export default {
    state,
    getters,
    actions,
    mutations
}