<template>
    <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
        <index-carousel/>
        <index-section-tiles :sections="sections.items || []"/>
        <index-popular-products :products="products.items || []"/>
    </v-col>
</template>

<script>
import IndexCarousel from "../components/IndexCarousel";
import IndexPopularProducts from "../components/IndexPopularProducts";
import IndexSectionTiles from "../components/IndexSectionTiles";

export default {
    components: {IndexPopularProducts, IndexSectionTiles, IndexCarousel},
    props: {
        carousel: {
            type: Object,
            required: true,
        },
        sections: {
            type: Object,
            required: true,
        },
        products: {
            type: Object,
            required: true,
        },
    }
}
</script>

<style lang="scss" scoped>

</style>
