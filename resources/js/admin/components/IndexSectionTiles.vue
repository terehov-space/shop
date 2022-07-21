<template>
    <v-col class="d-flex justify-space-between align-stretch flex-wrap" cols="12">
        <template v-for="category in sections">
            <inertia-link :href="`/catalog/${category.code}`" class="section_link_wrapper" :key="category.id">
                <v-card flat class="d-flex justify-start align-center flex-column section_card">
                    <v-card-text class="image_wrapper">
                        <v-img :src="category.image" width="250px" max-width="250px" contain/>
                    </v-card-text>
                    <v-card-title class="title_wrapper">
                        {{ category.title }}
                    </v-card-title>
                </v-card>
            </inertia-link>
        </template>
    </v-col>
</template>

<script>
export default {
    name: "IndexSectionTiles",

    props: {
        sections: {
            required: true,
            type: Array,
        },
    },
}
</script>

<style lang="scss" scoped>
.section_link_wrapper {
    text-decoration: none;
    margin-bottom: 20px;

    .section_card {
        height: 100%;
        width: 360px;
        border: 1px solid #e9e9e9;

        .image_wrapper {
            display: flex;
            justify-content: center;
            height: 250px;
        }

        .title_wrapper {
            white-space: pre-line;
            word-break: break-word;
            text-align: center;
        }
    }
}

@media only screen and (max-width: 769px) {
    .section_link_wrapper {
        width: 100% !important;

        .section_card {
            width: 100% !important;
        }
    }
}
</style>
