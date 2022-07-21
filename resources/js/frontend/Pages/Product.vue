<template>
    <v-col class="d-flex justify-space-between align-start flex-wrap pa-0 ma-0" cols="12">
        <v-col class="d-flex justify-start align-start flex-wrap px-5" cols="12">
            Главная
            <inertia-link :href="`/catalog`" class="bread_link">/ Каталог
            </inertia-link>
            <template v-if="product.sections.length > 0">
                <template v-for="section in product.sections">
                    <inertia-link :href="`/catalog/${section.code}`" :key="section.id" class="bread_link">/
                        {{ section.title }}
                    </inertia-link>
                </template>
            </template>
        </v-col>
        <ProductDetail :product="product" />
        <inertia-head v-if="product">
            <title>{{ seoTitle }}</title>
            <meta name="title" :content="seoTitle" />
            <meta name="description" :content="seoDescription" />
            <meta name="og:description" :content="seoDescription" />
            <meta name="og:title" :content="seoTitle" />
        </inertia-head>
    </v-col>
</template>

<script>
import ProductDetail from "../components/ProductDetail";

export default {
    name: 'ProductsDetailPage',

    components: {ProductDetail},

    props: {
        product: {
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

    computed: {
        seoTitle() {
            if (this.product && this.product.seoTitle) {
                return this.product.seoTitle
            }

            return this.settings.indexTitle
        },
        seoDescription() {
            if (this.product && this.product.seoDescription) {
                return this.product.seoDescription
            }

            return this.settings.indexDescription
        },
    },
}
</script>

<style lang="scss" scoped>
.bread_link {
    text-decoration: none;
    color: #000000;
}
</style>
