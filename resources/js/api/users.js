import axios from 'axios';

export default {
    getUser() {
        return axios.get('/api/user');
    },
    meals(id, params) {
        return axios.get(`/api/users/${id}/meals`, params ? { params } : null);
    },
    all(params) {
        return axios.get('/api/users', params ? { params } : null);
    },
    find(id) {
        return axios.get(`/api/users/${id}`);
    },
    update(id, data) {
        return axios.put(`/api/users/${id}`, data);
    },
    create(data) {
        return axios.post(`/api/users`, data);
    },
    delete(id) {
        return axios.delete(`/api/users/${id}`);
    },
};