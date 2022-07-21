import Vue from 'vue'
import {createInertiaApp, Head, Link} from '@inertiajs/inertia-vue'
import Layout from './Shared/Layout'
import vuetify from './plugins/vuetify'
import YimoVueEditor from 'yimo-vue-editor'
import langYimo from './static/langYimo.json'

function getCookie() {
    let cookie = {};
    document.cookie.split(';').forEach(function(el) {
        let [key,value] = el.split('=');
        cookie[key.trim()] = value;
    })
    return cookie;
}

createInertiaApp({
    resolve: name => {
        const page = require(`.//Pages/${name}`).default
        page.layout = page.layout || Layout

        return page
    },
    setup({ el, App, props, plugin }) {
        Vue.use(plugin)
        Vue.use(YimoVueEditor, {
            name: 'editor',
            config: {
                uploadImgUrl: '/admin/upload/string',
                printLog: false,
                lang: langYimo,
            },
        })
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
            render: h => h(App, props),
        }).$mount(el)
    },
})
