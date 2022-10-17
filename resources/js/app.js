import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'
import VueQrcode from '@chenfengyuan/vue-qrcode';
import VueSweetalert2 from 'vue-sweetalert2'
import QrReader from 'vue3-qr-reader';
import 'sweetalert2/dist/sweetalert2.min.css'
import store from './Store'
import VueHtmlToPaper from './Plugins/VueHtmlToPaper';

import { vfmPlugin } from 'vue-final-modal'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(store)
            .use(plugin)
            .use(VueSweetalert2)
            .use(QrReader)
            .use(VueHtmlToPaper)
            .use(vfmPlugin({
                key: '$vfm',
                componentName: 'VueFinalModal',
                dynamicContainerName: 'ModalsContainer'
            }))
            .use(ZiggyVue, Ziggy)
            .component(VueQrcode.name, VueQrcode)
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
