import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        product: null,
        showDialog: false,
        isOrder: false,
    },
    mutations: {
        INIT_PRODUCT_ORDER({state}, product) {
            this.state.product = product
            this.state.showDialog = true
            this.state.isOrder = true
        },
        INIT_PRODUCT_QUERY({state}, product) {
            this.state.product = product
            this.state.showDialog = true
            this.state.isOrder = false
        },
        RESET_PRODUCT({state}) {
            this.state.product = null
            this.state.showDialog = false
            this.state.isOrder = false
        },
    },
})
