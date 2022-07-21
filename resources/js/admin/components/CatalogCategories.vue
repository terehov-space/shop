<template>
    <v-col
        class="d-flex justify-center justify-md-space-between align-stretch flex-wrap no--gutters bottom-margin ma-0 pa-0"
        cols="12"
        md="12"
    >
        <v-col
            class="d-flex justify-start justify-md-space-between align-stretch flex-wrap ma-0 pa-0 px-3 mb-3"
            cols="12"
            md="12"
            v-if="selectedCategory"
        >
            <a @click="selectedCategory = null">
                <v-icon>mdi-chevron-left</v-icon>
                Назад
            </a>
        </v-col>
        <template v-for="(category, i) in sections">
            <inertia-link
                :href="`/catalog/${category.code}`"
                :event="!selectedCategory && category.sections.length !== 0 ? 'click.prevent': 'click'"
                :key="i"
                @click.prevent="selectCategory(category)"
                class="justify-center align-stretch catalog_link text-decoration-none disabled"
                :class="[
            {
              'd-flex': !selectedCategory
            },
            {
              'd-none': selectedCategory
            }
          ]"
            >
                <v-card
                    tile
                    flat
                    class="category-card"
                >
                    <v-card-text class="d-flex justify-center align-center">
                        <v-img
                            :src="category.image"
                            contain
                            height="75px"
                            width="175px"
                        />
                    </v-card-text>
                    <v-spacer/>
                    <v-card-text class="pop_title_cat text-center pa-0 ma-0">
                        {{ category.title }}
                    </v-card-text>
                </v-card>
            </inertia-link>
        </template>
        <v-col
            class="d-flex justify-center justify-md-start align-stretch flex-wrap ma-0 pa-0"
            cols="12"
            md="12"
            v-if="selectedCategory"
        >
            <inertia-link
                :href="`/catalog/${selectedCategory.code}`"
                @click="selectCategory(selectedCategory)"
                class="justify-center align-stretch catalog_sub_link text-decoration-none"
            >
                <v-card
                    tile
                    flat
                    class="d-flex justify-center align-center category-card"
                >
                    <v-card-text class="d-flex justify-center align-center pop_title_cat text-center pa-0 ma-0">
                        {{ selectedCategory.title }}
                    </v-card-text>
                </v-card>
            </inertia-link>
            <template v-for="(category, i) in selectedCategory.sections">
                <inertia-link
                    :href="`/catalog/${category.code}`"
                    :key="i"
                    @click="selectCategory(category)"
                    class="justify-center align-stretch catalog_sub_link text-decoration-none"
                >
                    <v-card
                        tile
                        flat
                        class="d-flex justify-center align-center category-card"
                    >
                        <v-card-text class="d-flex justify-center align-center pop_title_cat text-center pa-0 ma-0">
                            {{ category.title }}
                        </v-card-text>
                    </v-card>
                </inertia-link>
            </template>
        </v-col>
    </v-col>
</template>

<script>
export default {
    name: "CatalogCategories",

    props: {
        sections: {
            required: true,
            type: Array,
        }
    },

    data: () => ({
        selectedCategory: null,
    }),

    methods: {
        selectCategory(item) {
            this.selectedCategory = item
        }
    },
}
</script>

<style lang="scss" scoped>
.catalog_link {
    padding: 10px 20px;
    width: 30%;
    margin: 10px;
    font-weight: 400;
    color: #000000;
    border: 1px solid #C4C4C4;
    border-radius: 4px;
    transition: .5s;

    &.selected {
        @media only screen and (max-width: 480px) {
            width: 90%;
        }
    }

    @media only screen and (max-width: 480px) {
        margin: 5px;
        width: 45%;
    }

    .category-card {
        width: 100%;
        min-height: 210px;
    }

    .pop_title_cat {
        color: #000000;
        font-size: 18px;
        line-height: 20px;
    }

    &:hover {
        box-shadow: 0 3px 1px -2px rgb(0 0 0 / 20%), 0px 2px 2px 0px rgb(0 0 0 / 14%), 0px 1px 5px 0px rgb(0 0 0 / 12%);
    }
}

.catalog_sub_link {
    padding: 10px 20px;
    width: 30%;
    margin: 10px;
    font-weight: 400;
    color: #000000;
    border: 1px solid #C4C4C4;
    border-radius: 4px;
    transition: .5s;

    &.selected {
        @media only screen and (max-width: 480px) {
            width: 90%;
        }
    }

    @media only screen and (max-width: 480px) {
        margin: 5px;
        width: 90%;
    }

    .category-card {
        width: 100%;
        min-height: 50px;
    }

    .pop_title_cat {
        color: #000000;
        font-size: 18px;
        line-height: 20px;
    }

    &:hover {
        box-shadow: 0 3px 1px -2px rgb(0 0 0 / 20%), 0px 2px 2px 0px rgb(0 0 0 / 14%), 0px 1px 5px 0px rgb(0 0 0 / 12%);
    }
}
</style>
