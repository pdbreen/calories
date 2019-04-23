import axios from 'axios';

export default {
    all(page) {
        const params = page ? { page } : null;
        return axios.get('/api/meals', params ? { params } : null);
    },
    find(id) {
        return axios.get(`/api/meals/${id}`);
    },
    update(id, data) {
        return axios.put(`/api/meals/${id}`, data);
    },
    create(data) {
        return axios.post(`/api/meals`, data);
    },
    delete(id) {
        return axios.delete(`/api/meals/${id}`);
    },
};