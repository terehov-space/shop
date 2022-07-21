<template>
    <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0 mb-4" cols="12">
        <v-col class="d-flex justify-start align-start flex-column flex-wrap px-5" cols="12">
            <h1 class="text-h5">Корзина</h1>
        </v-col>
        <template v-if="loading">

        </template>
        <template v-else-if="!items || !items.length">
            <v-col class="d-flex justify-space-between justify-md-end align-start flex-wrap pa-0 ma-0" cols="12"
                   md="12">
                <v-card width="100%">
                    <v-card-text>
                        В Корзине пусто
                    </v-card-text>
                </v-card>
            </v-col>
        </template>
        <template v-else-if="items.length">
            <v-col class="d-flex justify-space-between justify-md-start align-start flex-wrap pa-0 ma-0 px-2" cols="12"
                   md="12">
                <v-col class="d-flex justify-space-between justify-md-start align-start flex-wrap pa-0 ma-0" cols="12"
                       md="12">
                    <v-card width="100%">
                        <v-card-title>Товары в корзине</v-card-title>
                        <v-divider/>
                        <template v-for="item in products">
                            <v-card-text :key="item.id">
                                <v-col class="d-flex justify-start align-start flex-wrap ma-0 pa-0" cols="12">
                                    <v-col cols="12" md="1">
                                        <v-card width="75px" height="75px">
                                            <v-img :src="item.image"
                                                   width="75px" height="75px" contain/>
                                        </v-card>
                                    </v-col>
                                    <v-col class="d-flex flex-column" cols="12" md="6">
                                        <v-col class="pa-0 ma-0 mb-4 product_title" cols="12">{{
                                                item.title
                                            }}
                                        </v-col>
                                        <v-col class="pa-0 ma-0 mb-4 product_vendor_code" cols="12">
                                            {{ item.vendorCode }}
                                        </v-col>
                                    </v-col>
                                    <v-col class="d-flex flex-column align-end pr-0 mr-0" cols="12" md="2">
                                        <v-col class="pa-0 ma-0 mb-4 product_price" cols="12">{{
                                                Math.round(item.price * item.count)
                                            }} руб.
                                        </v-col>
                                    </v-col>
                                    <v-col class="d-flex flex-column justify-end align-end pr-0 mr-0" cols="12" md="3">
                                        <v-col class="pa-0 ma-0 mb-4 product_quantity" cols="12">
                                            <v-item-group class="toggle-group">
                                                <v-btn @click="remQuantity(item)" name="minusQuantityBtn">
                                                    <v-icon>mdi-minus</v-icon>
                                                </v-btn>

                                                <v-btn disabled
                                                       style="background-color: transparent!important; color: #000000!important;">
                                                    {{ item.count }}
                                                </v-btn>

                                                <v-btn @click="addQuantity(item)" name="plusQuantityBtn">
                                                    <v-icon>mdi-plus</v-icon>
                                                </v-btn>

                                                <v-btn @click="deleteFromBasket(item)" name="removeBtn">
                                                    <v-icon>mdi-delete</v-icon>
                                                </v-btn>
                                            </v-item-group>
                                        </v-col>
                                    </v-col>
                                </v-col>
                            </v-card-text>
                            <v-divider />
                        </template>
                    </v-card>
                </v-col>
                <v-col class="d-flex justify-space-between justify-md-end align-start flex-wrap pa-0 ma-0 my-4"
                       cols="12"
                       md="12">
                    <v-col class="d-flex justify-end align-start pa-0 ma-0 flex-wrap" cols="12" md="4">
                        <v-col class="d-flex justify-end align-start" cols="12">Общая стоимость: {{ Math.round(basket.totalPrice) }} руб.</v-col>
                        <v-col class="d-flex justify-end align-start" cols="12">
                            <v-btn to="/new-order" text>Оформить заказ</v-btn>
                        </v-col>
                    </v-col>
                </v-col>
            </v-col>
        </template>
    </v-col>
</template>

<script>
import axios from 'axios'

export default {
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
            default: () => {
            },
        },
    },

    data: () => ({
        basket: {},
        products: [],
        loading: false,
        localBasketCode: null,
    }),

    mounted() {
        this.basket = JSON.parse(JSON.stringify(this.item))
        this.products = JSON.parse(JSON.stringify(this.items))
    },

    methods: {
        async remQuantity(product) {
            await axios.post('/basket', {
                productId: product.id,
                count: product.count - 1,
            })
                .then(({data}) => {
                    this.basket = data.item
                    this.products = data.items
                })
        },
        async addQuantity(product) {
            await axios.post('/basket', {
                productId: product.id,
                count: product.count + 1,
            })
                .then(({data}) => {
                    this.basket = data.item
                    this.products = data.items
                })
        },
        async deleteFromBasket(product) {
            await axios.post('/basket', {
                productId: product.id,
                count: 0,
            })
                .then(({data}) => {
                    this.basket = data.item
                    this.products = data.items
                })
        },
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

.toggle-group {
    border-radius: 4px;
    display: inline-flex;
    max-width: 100%;

    .v-btn.v-btn {
        border-radius: 0;
        border-style: solid;
        border-width: thin;
        box-shadow: none;
        opacity: 0.8;
        padding: 0 12px;
        min-width: 48px;
        min-height: 48px;
        border-color: #c4c4c4;

        &:first-child {
            border-top-left-radius: inherit;
            border-bottom-left-radius: inherit;
        }


        &:last-child {
            border-top-right-radius: inherit;
            border-bottom-right-radius: inherit;
        }
    }
}
</style>
