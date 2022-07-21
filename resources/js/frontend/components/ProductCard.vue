<template>
    <inertia-link :href="`/product/${product.code}`" class="text-decoration-none product_link">
        <v-card class="d-flex flex-column product_card" height="100%" flat>
            <v-card-title class="d-flex justify-center align-start">
                <v-img :src="product.image" width="150px" height="150px" max-width="150px" contain/>
            </v-card-title>
            <v-card-text class="title">
                {{ product.title }}
            </v-card-text>
            <v-spacer/>
            <v-card-text class="vendor">
                {{ product.vendorCode }}
            </v-card-text>
            <v-card-text class="price">
                {{ product.price ? `${product.price} ₽` : 'По запросу' }}
            </v-card-text>
            <v-card-actions>
                <template v-if="product.onOrder">
                    <v-btn class="buy_btn" text @click.prevent="queryPrice(product, true)" name="buyBtn">Под заказ</v-btn>
                </template>
                <template v-else-if="product.price">
                    <v-btn class="buy_btn" text @click.prevent="addToBasket(product)" name="buyBtn">В корзину</v-btn>
                </template>
                <template v-else>
                    <v-btn class="buy_btn" text @click.prevent="queryPrice(product)" name="queryBtn">Запросить</v-btn>
                </template>
            </v-card-actions>
        </v-card>
    </inertia-link>
</template>

<script>
import axios from 'axios'

export default {
    name: "ProductCard",
    props: {
        product: {
            type: Object,
            required: true,
        }
    },
    methods: {
        queryPrice(product, onOrder) {
            if (onOrder) {
                this.$store.commit('INIT_PRODUCT_ORDER', product)
            } else {
                this.$store.commit('INIT_PRODUCT_QUERY', product)
            }
        },
        async addToBasket(product) {
            await axios.post('/basket', {
                productId: product.id,
            })
                .then(() => {
                    window.location.reload()
                })
        },
    },
}
</script>

<style lang="scss" scoped>
.product_link {
    margin-bottom: 10px;
    width: 25%;
    border-radius: 8px;

    @media screen and (min-width: 1024px) {
        width: calc(25% - 10px);
        margin-left: 5px;
        margin-right: 5px;
    }

    &:hover {
        box-shadow: 0 3px 3px -1px rgb(0 0 0 / 20%), 0 2px 2px 0 rgb(0 0 0 / 14%), 0 1px 5px 0 rgb(0 0 0 / 12%);
    }

    .product_card {
        border: 1px solid #e9e9e9;
        border-radius: 8px;
        width: 100%;

        .title {
            font-size: 18px;
            font-weight: 500;
            color: #000000;
        }

        .vendor {
            font-size: 16px;
            font-weight: 400;
            color: #000000;
        }

        .price {
            font-size: 24px;
            font-weight: 600;
            color: #000000;
        }

        .buy_btn {
            background-color: #A1CB0D;
            color: #FFFFFF;
            width: 100%;
        }
    }
}

@media only screen and (max-width: 768px) {
    .product_link {
        margin-left: 0;
        margin-right: 0;
        width: 100% !important;

        .product_card {
            width: 100% !important;
        }
    }
}
</style>
