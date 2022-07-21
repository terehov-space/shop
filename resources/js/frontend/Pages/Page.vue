<template>
    <v-col class="d-flex justify-center align-start flex-wrap no--gutters" md="12">
        <StaticPageContent :item="item" />

        <inertia-head v-if="item">
            <title>{{ item.seoTitle }}</title>
            <meta name="title" :content="item.seoTitle" />
            <meta name="description" :content="item.seoDescription" />
            <meta name="og:description" :content="item.seoDescription" />
            <meta name="og:title" :content="item.seoTitle" />
        </inertia-head>
    </v-col>
</template>

<script>
import StaticPageContent from "../components/StaticPageContent";

export default {
    components: {StaticPageContent},

    props: {
        item: {
            type: Object,
            required: true,
            default: {},
        }
    },
}
</script>
