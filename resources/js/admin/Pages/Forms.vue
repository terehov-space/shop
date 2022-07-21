<template>
    <v-col cols="12">
        <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
            <div>
                Форма обратной связи
            </div>
        </v-col>
        <v-col cols="12">
            <v-data-table
                :headers="headers"
                :items="pages"
                :loading="loading"
                hide-default-footer
            >
                <template v-slot:item.product="{item}">
                    <div class="text-end">
                        <a :href="`/admin/catalog/products/${item.product}`" target="_blank">Товар</a>
                    </div>
                </template>
                <template v-slot:item.actions="{item}">
                    <div class="text-end">
                        <v-btn tile text :to="`/admin/orders/${item.id}`">
                            <v-icon>
                                mdi-eye
                            </v-icon>
                        </v-btn>
                    </div>
                </template>
            </v-data-table>
        </v-col>
        <template v-if="lastPage > 1">
            <v-col cols="12">
                <v-pagination v-model="page" :length="pageCount"/>
            </v-col>
        </template>
    </v-col>
</template>

<script>
import headers from '../static/formHeaders.json'

export default {
    props: {
        items: {
            required: true,
            type: Array,
            default: () => [],
        },
        currentPage: {
            required: true,
            type: Number,
            default: () => 0,
        },
        lastPage: {
            required: true,
            type: Number,
            default: () => 0,
        },
    },

    data: () => ({
        search: null,
        pages: [],
        headers,
        page: 0,
        pageCount: -1,
        loading: false,
    }),

    mounted() {
        this.pages = this.items
        this.page = this.currentPage
        this.pageCount = this.lastPage

        const queryParams = new URLSearchParams(window.location.search);
        this.search = queryParams.get('q')
    },

    watch: {
        page(newVal, oldVal) {
            if (oldVal !== 0) {
                this.loading = true

                let searchQuery = `page=${this.page}`

                if (this.search) {
                    searchQuery += `&q=${this.search}`
                }

                this.$inertia.get(`${window.location.pathname}?${searchQuery}`)
            }
        },
    },

    methods: {
        trySearch() {
            let searchQuery = `q=${this.search}`

            this.$inertia.get(`${window.location.pathname}?${searchQuery}`)
        },
        clearSearch() {
            this.$inertia.get(`${window.location.pathname}`)
        },
    },
}
</script>

<style scoped>

</style>
