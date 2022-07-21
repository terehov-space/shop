<template>
  <inertia-link :href="`/product/${product.code}`" class="text-decoration-none product_link">
    <v-card class="d-flex flex-column product_card" height="100%" flat>
      <v-card-title class="d-flex justify-center align-start">
        <v-img :src="product.image" width="150px" height="150px" max-width="150px" contain/>
      </v-card-title>
      <v-card-text class="title">
        {{ product.title }}
      </v-card-text>
      <v-spacer />
      <v-card-text class="vendor">
        {{ product.vendor_code }}
      </v-card-text>
      <v-card-text class="price">
        {{ product.price ? product.price : 'По запросу' }}
      </v-card-text>
      <v-card-actions>
        <template v-if="product.price">
        <v-btn class="buy_btn" text>В корзину</v-btn>
        </template>
        <template v-else>
        <v-btn class="buy_btn" text @click.prevent="queryPrice(product)">Запросить</v-btn>
        </template>
      </v-card-actions>
    </v-card>
  </inertia-link>
</template>

<script>
export default {
  name: "ProductCard",
  props: {
    product: {
      type: Object,
      required: true,
    }
  },
  methods: {
    queryPrice(product) {
      this.$store.commit('query/SET_TO_QUERY', product)
      this.$store.commit('query/SHOW_DIALOG')
    },
  }
}
</script>

<style lang="scss" scoped>
.product_link {
  margin-bottom: 40px;
  margin-left: 32px;
  margin-right: 32px;

  .product_card {
    border: 1px solid #e9e9e9;
    border-radius: 8px;
    width: 240px;

    @media only screen and (min-width: 1440px) {
      width: 270px;
    }

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
