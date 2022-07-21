<template>
    <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0 mb-4" cols="12">
        <v-col class="d-flex justify-start align-start flex-column flex-wrap px-5" cols="12">
            <h1 class="text-h5">Оформление заказа</h1>
        </v-col>
        <v-col class="d-flex justify-start align-start flex-column flex-wrap px-5" cols="12">
            <v-form>
                <v-col class="d-flex justify-start align-start flex-column flex-wrap px-5" cols="12">
                    <v-text-field
                        v-model="order.phone"
                        label="Телефон"
                        outlined
                        placeholder="Телефон"
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start flex-column flex-wrap px-5" cols="12">
                    <v-text-field
                        v-model="order.email"
                        label="Email"
                        placeholder="Email"
                        :rules="emailRules"
                        outlined
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start flex-column flex-wrap px-5" cols="12">
                    <v-btn @click="createOrder" name="createOrderBtn">Закончить оформление</v-btn>
                </v-col>
            </v-form>
        </v-col>
        <v-snackbar
            ref="success"
            v-model="snackbar"
            :timeout="1500"
        >
            Заполните все поля
            <template v-slot:action="{ attrs }">
                <v-btn
                    color="blue"
                    text
                    v-bind="attrs"
                    @click="snackbar = false"
                >
                    Закрыть
                </v-btn>
            </template>
        </v-snackbar>
        <v-snackbar
            ref="success"
            v-model="success"
            :timeout="1500"
        >
            Заполните все поля
            <template v-slot:action="{ attrs }">
                <v-btn
                    color="blue"
                    text
                    v-bind="attrs"
                    @click="snackbar = false"
                >
                    Закрыть
                </v-btn>
            </template>
        </v-snackbar>
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
        quantity: 1,
        order: {
            email: null,
            phone: null,
        },
        emailRules: [
            v => !!v || 'E-mail is required',
            v => /.+@.+/.test(v) || 'E-mail must be valid',
        ],
        snackbar: false,
        success: false,
    }),

    mounted() {
        this.basket = JSON.parse(JSON.stringify(this.item))
        this.products = JSON.parse(JSON.stringify(this.items))
    },

    methods: {
        addQuantity(item) {
            item.quantity++
        },
        remQuantity(item) {
            item.quantity--
        },
        changeQuantityPrevent(e) {
            e.preventDefault()
        },
        deleteFromBasket(e) {
            this.quantity = 0
        },
        async createOrder() {
            if (!this.order.phone || !this.order.email) {
                this.snackbar = true
            } else {
                await axios.post(`/new-order`, this.order)
                    .then(({data}) => {
                        this.$inertia.get(`/orders?order=${data.item.id}`)
                    })
            }
        },
    },

    computed: {
        basketCode() {
            return this.$store.state.basket.initialData;
        },
        totalPrice() {
            let totalPrice = 0

            if (this.basket.items) {
                this.basket.items.forEach(item => {
                    totalPrice += item.price * item.quantity
                })
            }

            return totalPrice
        }
    }
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
