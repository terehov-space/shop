<template>
    <v-col class="d-flex justify-start align-start flex-column flex-wrap pa-0 ma-0" cols="12">
        <v-col class="d-flex justify-start align-start flex-column flex-wrap px-5" cols="12">
            <h1 class="text-h5">Каталог</h1>
        </v-col>
        <v-col class="d-flex justify-start align-start flex-column flex-wrap ma-0 pa-0" cols="12">
            <CatalogCategories :sections="sections.items"/>
        </v-col>
        <inertia-head>
            <title>Каталог оборудования для автомоечного гидравлического коммунального пищевого производственного и с х сегмента купить цена продажа интернет магазин</title>
            <meta name="title" content="Каталог оборудования для автомоечного гидравлического коммунального пищевого производственного и с х сегмента купить цена продажа интернет магазин" />
            <meta name="description" content="Компания CarWashZona предлагает купить оборудование для автомоечного, гидравлического, коммунального, пищевого, производственного и с/х сегмента. В каталоге представлен широкий ассортимент товаров по оптимальным ценам. Гарантия на продукцию, техническое обслуживание и сопутствующие услуги. Доставка по Москве и другим городам России." />
            <meta name="og:description" content="Компания CarWashZona предлагает купить оборудование для автомоечного, гидравлического, коммунального, пищевого, производственного и с/х сегмента. В каталоге представлен широкий ассортимент товаров по оптимальным ценам. Гарантия на продукцию, техническое обслуживание и сопутствующие услуги. Доставка по Москве и другим городам России." />
            <meta name="og:title" content="Каталог оборудования для автомоечного гидравлического коммунального пищевого производственного и с х сегмента купить цена продажа интернет магазин" />
        </inertia-head>
    </v-col>
</template>

<script>
import CatalogCategories from "../components/CatalogCategories";

export default {
    name: 'CatalogIndexPage',
    components: {CatalogCategories},

    props: {
        sections: {
            required: true,
            type: Object,
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
            title: 'Каталог - project.ru',
        }
    },
}
</script>

<style scoped>

</style>
