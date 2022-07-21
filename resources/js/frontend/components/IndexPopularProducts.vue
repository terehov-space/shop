<template>
    <v-col class="d-flex justify-start align-stretch flex-wrap" cols="12">
        <v-col class="d-flex justify-start align-stretch flex-wrap popular_title" cols="12">
            Популярные товары
        </v-col>
        <v-col class="d-flex justify-start align-stretch flex-wrap pa-0 ma-0" cols="12">
            <template v-for="(product, idx) in products">
                <product-card :product="product" :key="idx"/>
            </template>
        </v-col>
    </v-col>
</template>

<script>
import ProductCard from "./ProductCard";

export default {
    name: "IndexPopularProducts",

    components: {ProductCard},

    props: {
        products: {
            required: true,
            type: Array,
        },
    },
}
</script>

<style lang="scss" scoped>
.popular_title {
    font-size: 20px;
    font-weight: 500;
}
</style>
