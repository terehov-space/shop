<template>
    <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0 mb-4" cols="12">
        <v-col class="d-flex justify-start align-start flex-column flex-wrap px-5" cols="12">
            <h1 class="text-h5">Заказ: {{ basket.id }} {{ basket.status }}</h1>
        </v-col>
        <template v-if="loading">

        </template>
        <template v-else-if="!basket && !items">
            <v-col class="d-flex justify-space-between justify-md-end align-start flex-wrap pa-0 ma-0" cols="12"
                   md="12">
                <v-card width="100%">
                    <v-card-text>
                        Такого заказа не существует
                    </v-card-text>
                </v-card>
            </v-col>
        </template>
        <template v-else-if="basket">
            <v-col class="d-flex justify-space-between justify-md-start align-start flex-wrap pa-0 ma-0 px-2" cols="12"
                   md="12">
                <v-col class="d-flex justify-space-between justify-md-start align-start flex-wrap pa-0 ma-0" cols="12"
                       md="8">
                    <v-card width="100%">
                        <v-card-title>Товары в корзине</v-card-title>
                        <v-divider/>
                        <template v-for="item in products">
                            <v-card-text :key="item.id">
                                <v-col class="d-flex justify-start align-start ma-0 pa-0" cols="12">
                                    <v-col cols="2">
                                        <v-card width="75px" height="75px">
                                            <v-img :src="item.image"
                                                   width="75px" height="75px" contain/>
                                        </v-card>
                                    </v-col>
                                    <v-col class="d-flex flex-column" cols="6">
                                        <v-col class="pa-0 ma-0 mb-4 product_title" cols="12">{{
                                                item.title
                                            }}
                                        </v-col>
                                        <v-col class="pa-0 ma-0 mb-4 product_vendor_code" cols="12">
                                            {{ item.vendorCode }}
                                        </v-col>
                                    </v-col>
                                    <v-col class="d-flex flex-column align-end pr-0 mr-0" cols="4">
                                        <v-col class="pa-0 ma-0 mb-4 product_price" cols="12">{{ item.count}} x {{ item.price }} руб.
                                        </v-col>
                                    </v-col>
                                </v-col>
                            </v-card-text>
                        </template>
                    </v-card>
                </v-col>
            </v-col>
        </template>
    </v-col>
</template>

<script>
export default {
    name: "BasketPage",
    props: {
        item: {
            type: Object,
            required: true,
            default: () => {
            },
        },
        items: {
            type: Array,
            required: true,
            default: () => [],
        },
    },

    data: () => ({
        basket: {},
        products: [],
        loading: false,
    }),

    async mounted() {
        this.basket = JSON.parse(JSON.stringify(this.item))
        this.products = JSON.parse(JSON.stringify(this.items))
    },
}
</script>

<style lang="scss" scoped>
.product_title, .product_price {
    font-size: 20px;
    color: #000000;
    line-height: 22px;
}

.product_vendor_code {
    font-size: 16px;
    color: #000000;
    line-height: 18px;
}
</style>
