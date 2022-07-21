<template>
    <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
        <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0" cols="12">
            Главная
            <inertia-link :href="`/catalog`" class="bread_link">/ Каталог
            </inertia-link>
            <template v-if="section && section.section">
                <inertia-link :href="`/catalog/${section.section.code}`" class="bread_link">/ {{ section.section.title }}
                </inertia-link>
            </template>
            <template v-if="section">
                <inertia-link :href="`/catalog/${section.code}`" class="bread_link">/ {{ section.title }}</inertia-link>
            </template>
        </v-col>
        <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0 my-4" cols="12" v-if="section">
            <h1 class="text-h5">Категория: {{ section.title }}</h1>
        </v-col>
        <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0" cols="12" md="3"
               v-if="section &&  (section.sections || (section.section && section.section.sections))">
            <v-list class="sections_wrapper">
                <template v-if="section && section.sections && section.sections.length > 0">
                    <template v-for="subSection in section.sections">
                        <v-list-item :key="subSection.id" :to="`/catalog/${subSection.code}`"
                                     class="subsection-wrapper">
                            <v-list-item-title>
                                {{ subSection.title }}
                            </v-list-item-title>
                        </v-list-item>
                    </template>
                </template>
                <template v-else-if="section && section.section && section.section.sections && section.section.sections.length > 0">
                    <template v-for="subSection in section.section.sections">
                        <v-list-item :key="subSection.id" :to="`/catalog/${subSection.code}`"
                                     class="subsection-wrapper">
                            <v-list-item-title>
                                {{ subSection.title }}
                            </v-list-item-title>
                        </v-list-item>
                    </template>
                </template>
                <template v-else>
                    <v-list-item :to="`/catalog/${section.code}`" class="subsection-wrapper">
                        <v-list-item-title>
                            {{ section.title }}
                        </v-list-item-title>
                    </v-list-item>
                </template>
            </v-list>
        </v-col>
        <v-col class="d-flex justify-start align-stretch flex-wrap pa-0 ma-0 px-4 pt-2" cols="12" md="9">
            <template v-for="product in products">
                <product-card :product="product" :key="product.id" />
            </template>
        </v-col>
        <v-col class="d-flex justify-center align-center flex-wrap loader--wrapper mb-8" cols="12" md="10">
            <v-progress-circular
                v-if="loading"
                indeterminate
                color="amber"
            />
        </v-col>
        <inertia-head v-if="section">
            <title>{{ seoTitle }}</title>
            <meta name="title" :content="seoTitle" />
            <meta name="description" :content="seoDescription" />
            <meta name="og:description" :content="seoDescription" />
            <meta name="og:title" :content="seoTitle" />
        </inertia-head>
    </v-col>
</template>

<script>
import ProductCard from "../components/ProductCard";
import axios from "axios";

export default {
    name: "CatalogDetailPage",
    components: {ProductCard},

    data: () => ({
        section: null,
        parentSection: null,
        products: [],
        loading: false,
        currentPage: 1,
        lastPage: 1,
    }),

    props: {
        data: {
            type: Object,
            required: true,
        },
        settings: {
            type: Object,
            required: true,
            default: () => {
            },
        },
    },
    head() {
        return {
            title: `Каталог - project.ru`,
        }
    },

    mounted() {
        const data = JSON.parse(JSON.stringify(this.data))

        this.section = JSON.parse(JSON.stringify(data.item))
        this.products = JSON.parse(JSON.stringify(data.items))
        this.currentPage = JSON.parse(JSON.stringify(data.currentPage)) + 1
        this.lastPage = JSON.parse(JSON.stringify(data.totalPages))

        window.addEventListener('scroll', this.handleScroll)
    },

    computed: {
        seoTitle() {
            if (this.section && this.section.seoTitle) {
                return this.section.seoTitle
            }

            return this.settings.indexTitle
        },
        seoDescription() {
            if (this.section && this.section.seoDescription) {
                return this.section.seoDescription
            }

            return this.settings.indexDescription
        },
    },

    destroyed() {
        window.removeEventListener('scroll', this.handleScroll)
    },

    methods: {
        async loadMore() {
            if (this.currentPage <= this.lastPage && !this.loading) {
                this.loading = true

                await axios.get(`/catalog/${this.section.code}?page=${this.currentPage}`)
                    .then(({data}) => {

                        this.products.push(...data.items)
                        this.currentPage += 1
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }
        },
        changeCategory(item) {
            this.section = item
        },
        async handleScroll() {
            const rect = document.querySelector('.loader--wrapper').getBoundingClientRect()
            const elemTop = rect.top
            const elemBottom = rect.bottom

            if (elemTop < window.innerHeight && elemBottom >= 0) {
                await this.loadMore()
            }
        },
    },
}
</script>

<style lang="scss">
.bread_link {
    color: #000000 !important;
    text-decoration: none;
    margin-left: 5px;
}

.sections_wrapper {
    width: 100%;

    .subsection-wrapper {
        border: 1px solid #e9e9e9;
        width: 100%;
    }
}
</style>
