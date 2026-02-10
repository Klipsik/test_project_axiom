import './bootstrap';
import { createApp } from 'vue';
import { createI18n } from 'vue-i18n';
import App from './App.vue';
import ru from './locales/ru.js';
import en from './locales/en.js';

// Get saved locale or default to 'en'
const savedLocale = localStorage.getItem('locale') || 'en';
const defaultLocale = ['en', 'ru'].includes(savedLocale) ? savedLocale : 'en';

const i18n = createI18n({
    locale: defaultLocale,
    fallbackLocale: 'en',
    messages: {
        en,
        ru,
    },
    legacy: false,
});

const app = createApp(App);
app.use(i18n);
app.mount('#app');
