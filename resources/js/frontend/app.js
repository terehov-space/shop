import Vue from 'vue'
import {createInertiaApp, Head, Link} from '@inertiajs/inertia-vue'
import Layout from './Shared/Layout'
import vuetify from './plugins/vuetify'
import device from 'vue-device-detector'
import { store } from "./store";

createInertiaApp({
    resolve: name => {
        const page = require(`.//Pages/${name}`).default
        page.layout = page.layout || Layout

        return page
    },
    setup({ el, App, props, plugin }) {
        Vue.use(plugin)
        Vue.use(device)
        Vue.component('inertia-link', Link)
        Vue.component('inertia-head', Head)

        Vue.component('router-link', {
            functional: true,
            render(h, context) {
                const data = { ...context.data }
                delete data.nativeOn
                const props = data.props
                props.href = props.to /// v-btn passes `to` prop but inertia-link requires `href`, so we just copy it
                return h('inertia-link', data, context.children)
            },
        })

        new Vue({
            vuetify,
            store,
            render: h => h(App, props),
        }).$mount(el)
    },
})
