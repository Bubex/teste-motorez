import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import toastr from 'toastr';
import 'toastr/build/toastr.min.css';
window.toastr = toastr;

import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';
window.noUiSlider = noUiSlider;

import Pusher from 'pusher-js';
window.Pusher = Pusher;

import Echo from 'laravel-echo';
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: false,
    encrypted: true,
    disableStats: true,
});

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();