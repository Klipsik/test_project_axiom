import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Set locale header from localStorage or default to 'en'
const savedLocale = localStorage.getItem('locale') || 'en';
const defaultLocale = ['en', 'ru'].includes(savedLocale) ? savedLocale : 'en';
window.axios.defaults.headers.common['X-Locale'] = defaultLocale;