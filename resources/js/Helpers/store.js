import { createStore } from 'vuex'
export const store = createStore({
    state:{
        token: localStorage.getItem('token') || '',
        user: localStorage.getItem('user') || '',
        id: localStorage.getItem('id') || '',
        expires_in: localStorage.getItem('expires_in') || '',
    },
    mutations:{
        setToken(state, token){
            localStorage.setItem('token', token);
            state.token = token;    
        },
        setUser(state, user){
            localStorage.setItem('user', user);
            state.user = user;    
        },
        setUserId(state, id){
            localStorage.setItem('id', id);
            state.id = id;    
        },
        setExpireToken(state, expires_in){
            localStorage.setItem('expires_in', expires_in);
            state.expires_in = expires_in;    
        },
        clearToken(state){
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('id');
            localStorage.removeItem('expires_in');
            state.token = '';
            state.user = '';
            state.id = '';
            state.surface_controle = '';
            state.expires_in = '';
            
        },
    }
  });
