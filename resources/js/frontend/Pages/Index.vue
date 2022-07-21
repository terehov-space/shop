<template>
    <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
        <index-carousel :items="carousel.items || []" />
        <index-section-tiles :sections="sections.items || []"/>
        <index-popular-products :products="products.items || []"/>

        <inertia-head>
            <title>Продажа оборудования для автомоечного гидравлического коммунального пищевого производственного и с х сегмента купить по лучшей цене интернет магазин</title>
            <meta name="title" content="Продажа оборудования для автомоечного гидравлического коммунального пищевого производственного и с х сегмента купить по лучшей цене интернет магазин" />
            <meta name="description" content="Компания CarWashZona предлагает купить оборудование для автомоечного, гидравлического, коммунального, пищевого, производственного и с/х сегмента. " />
            <meta name="og:description" content="Компания CarWashZona предлагает купить оборудование для автомоечного, гидравлического, коммунального, пищевого, производственного и с/х сегмента. " />
            <meta name="og:title" content="Продажа оборудования для автомоечного гидравлического коммунального пищевого производственного и с х сегмента купить по лучшей цене интернет магазин" />
        </inertia-head>
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
