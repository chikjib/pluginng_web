import axios from 'axios';
import { reactive } from 'vue';

export class AuthSession {
    constructor(storage = window.localStorage) {
        this.storage = storage;
        this.token = storage.getItem('token');
        try {
            this.user = JSON.parse(storage.getItem('user') || 'null');
        } catch {
            this.user = null;
        }
        if (this.token && this.user && typeof this.user === 'object') {
            axios.defaults.headers.common.Authorization = `Bearer ${this.token}`;
        } else {
            this.logout();
        }
    }
    login(token, user) {
        this.storage.setItem('token', token);
        this.storage.setItem('remember_token', user.remember_token);
        this.storage.setItem('user', JSON.stringify(user));
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
        this.token = token;
        this.user = user;
    }
    check() {
        return Boolean(this.token && this.user);
    }
    logout() {
        for (const key of ['token', 'remember_token', 'user']) this.storage.removeItem(key);
        delete axios.defaults.headers.common.Authorization;
        this.token = null;
        this.user = null;
    }
}

export default reactive(new AuthSession());
