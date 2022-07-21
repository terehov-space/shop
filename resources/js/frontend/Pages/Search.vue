<template>
    <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
        <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0 my-4" cols="12">
            <h1 class="text-h5">Поиск</h1>
        </v-col>
        <v-col class="d-flex justify-start justify-md-start align-stretch flex-wrap pa-0 ma-0" cols="12">
            <template v-for="product in products">
                <product-card :product="product" :key="product.id"/>
            </template>
        </v-col>
        <v-col class="d-flex justify-center align-center flex-wrap loader--wrapper mb-8" cols="12" md="10">
            <v-progress-circular
                v-if="loading"
                indeterminate
                color="amber"
            />
        </v-col>
    </v-col>
</template>

<script>
import ProductCard from "../components/ProductCard"
import axios from "axios"

export default {
    name: "CatalogDetailPage",
    components: {ProductCard},

    props: {
        items: {
            type: Array,
            required: true,
            default: () => [],
        },
        currentPage: {
            type: Number,
            required: true,
            default: () => 0,
        },
        lastPage: {
            type: Number,
            required: true,
            default: () => 0,
        },
    },

    data: () => ({
        products: [],
        loading: false,
        curPage: -1,

        snackbar: false,
        timeout: 1000,
    }),

    methods: {
        async loadMore() {
            if (this.curPage <= this.lastPage && !this.loading) {
                this.loading = true

                const queryParams = new URLSearchParams(window.location.search)

                const searchPhrase = queryParams.get('q')

                await axios.get(`/search?q=${searchPhrase}&page=${this.curPage}`)
                    .then(({data}) => {

                        this.products.push(...data.items)
                        this.curPage += 1
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }
        },
        async handleScroll() {
            const rect = document.querySelector('.loader--wrapper').getBoundingClientRect()
            const elemTop = rect.top
            const elemBottom = rect.bottom

            if (elemTop < window.innerHeight && elemBottom >= 0) {
                await this.loadMore()
            }
        },
        async tryBuy(productId) {
            const buyData = {
                product_id: productId,
                quantity: 1,
            }

            await this.$axios.put(`basket/${this.basketCode}`, buyData)
                .finally(() => {
                    this.snackbar = true
                })
        },
    },

    mounted() {
        window.addEventListener('scroll', this.handleScroll)

        this.products = JSON.parse(JSON.stringify(this.items))
        this.curPage = JSON.parse(JSON.stringify(this.currentPage)) + 1
    },

    destroyed() {
        window.removeEventListener('scroll', this.handleScroll)
    },

    computed: {
        basketCode() {
            return this.$store.state.basket.initialData;
        },
    },
}
</script>

<style scoped>

</style>
