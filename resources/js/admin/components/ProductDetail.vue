<template>
  <v-col class="d-flex justify-center align-start flex-wrap pa-0 ma-0 middle-margin" cols="8">
    <v-col class="d-flex justify-center align-start flex-wrap pa-0 ma-0" cols="12" md="4">
      <v-col class="d-flex justify-center align-start pa-0 ma-0" cols="12">
        <template v-if="product.images && product.images.length > 0">
          <v-carousel
            ref="test1"
            hide-delimiters
            hide-delimiter-background
            :show-arrows="false"
            class="pa-0 ma-0"
            height="300px"
            v-model="model"
          >
            <v-carousel-item v-for="image in product.images" :key="image.id" class="pa-0 ma-0">
              <v-img
                :src="image.image"
                height="300px"
                width="400px"
                contain
                @click="toggleModal"
              />
            </v-carousel-item>
          </v-carousel>
        </template>
        <template v-else-if="product.image">
          <v-img
            ref="test2"
            :src="product.image"
            height="300px"
            width="400px"
            contain
          />
        </template>
        <template v-else>
          <v-img
            src="/no-photo.png"
            width="80%;"
            height="80%;"
            contain
          />
        </template>
      </v-col>
      <v-col class="d-flex justify-center align-start pa-0 ma-0" cols="12">
        <v-slide-group
          v-model="model"
          class="pa-4"
          show-arrows
        >
          <v-slide-item
            v-for="image in product.images"
            :key="image.id"
            v-slot="{ toggle }"
          >
            <v-card
              class="ma-4"
              height="50"
              width="50"
              @click="toggle"
            >
              <v-img
                :src="image.image"
                width="50px"
                height="50px"
                contain
              />
            </v-card>
          </v-slide-item>
        </v-slide-group>
      </v-col>
    </v-col>
    <v-col class="d-none d-md-flex justify-center align-start flex-wrap pa-0 ma-0" cols="12" md="1">
    </v-col>
    <v-col class="d-flex justify-center align-start flex-wrap pa-0 ma-0" cols="12" md="7">
      <v-col class="d-flex justify-space-between align-center pa-0 ma-0" cols="12" md="12">
        <v-col class="d-flex justify-start align-center no-gutters px-0" cols="8">
          <h1 class="text-h5">
            {{ product.title }}
          </h1>
        </v-col>
        <v-col class="d-flex justify-end align-center no-gutters" cols="4" v-if="product.vendor">
          <v-img
            :src="product.vendor.image"
            width="40px"
            height="40px"
            contain
          />
        </v-col>
      </v-col>
      <v-col class="d-none d-md-flex justify-end align-start pa-0 ma-0" cols="12" md="12">
        <v-divider/>
      </v-col>
      <v-col class="d-flex justify-space-between align-start flex-wrap pa-0 ma-0 info_gutters" cols="12" md="12">
        <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0" cols="12" md="6">
          <v-col class="d-flex justify-start align-center pa-0 ma-0 ch-title" cols="12" md="12">
            Характеристики
          </v-col>
          <v-col class="d-flex justify-start align-stretch pa-0 ma-0 char_margin ch-info" cols="12" md="12"
                 v-if="product.vendor_code">
            <v-col class="d-flex justify-start align-start pa-0 ma-0" cols="6" md="6">
              Артикул
            </v-col>
            <v-col class="d-flex justify-end align-start pa-0 ma-0 text-end" style="text-align: end" cols="6" md="6">
              {{ product.vendor_code }}
            </v-col>
          </v-col>
          <template v-for="(ch, i) in product.properties">
            <v-col class="d-flex justify-start align-stretch pa-0 ma-0 char_margin ch-info" cols="12" md="12"
                   :key="i">
              <v-col class="d-flex justify-start align-start pa-0 ma-0" cols="6" md="6">
                {{ ch.property.title }}
              </v-col>
              <v-col class="d-flex justify-end align-start pa-0 ma-0" cols="6" md="6">
                {{ ch.value.value }}
              </v-col>
            </v-col>
          </template>
          <template v-if="product.files && product.files.length > 0">
            <v-col class="d-flex justify-start align-center pa-0 ma-0 ch-title mt-4" cols="12" md="12">
              Файлы
            </v-col>
          </template>
          <template v-if="product.files && product.files.length > 0">
            <v-col class="d-flex justify-start align-start flex-column pa-0 ma-0 char_margin ch-info" cols="12" md="12">
              <template v-for="file in product.files">
                <a :href="file.path" style="margin: 8px 0 8px 0" target="_blank" :key="file.id">{{ file.title ? file.title : file.filename }}</a>
              </template>
            </v-col>
          </template>
          <template v-if="product.description">
            <v-col class="d-flex justify-start align-center pa-0 ma-0 ch-title mt-4" cols="12" md="12">
              Описание
            </v-col>
          </template>
          <template v-if="product.description">
            <v-col class="d-flex justify-start align-stretch pa-0 ma-0 char_margin ch-info" cols="12" md="12"
                   v-html="description"></v-col>
          </template>
        </v-col>
        <v-col class="d-flex justify-start align-start pa-0 ma-0" cols="12" md="1"></v-col>
        <v-col class="d-flex justify-end align-start pa-0 ma-0 pr-4" cols="12" md="5">
          <v-card
            tile
            class="buy-card"
          >
            <v-card-title>
              Цена: {{ product.price ? `${product.price} руб` : 'По запросу' }}
            </v-card-title>

            <v-card-text class="pa-0 ma-0">
              <template v-if="product.price">
                <v-btn
                  text
                  tile
                  class="buy-btn"
                  @click="tryBuy"
                >
                  В корзину
                </v-btn>
              </template>
              <template v-else>
                <v-btn
                  text
                  tile
                  class="buy-btn"
                  @click="queryPrice(product)"
                >
                  Запросить
                </v-btn>
              </template>
            </v-card-text>
          </v-card>
        </v-col>
      </v-col>
    </v-col>
    <v-snackbar
      ref="success"
      v-model="snackbar"
      :timeout="timeout"
    >
      Ошибка добавления в корзину

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
    <v-dialog v-model="modal" width="500px" class="d-flex justify-center align-start">
      <v-card>
        <v-card-text class="d-flex justify-center align-start pt-4">
          <template v-if="product.images && product.images.length > 1">
            <v-carousel
              hide-delimiters
              hide-delimiter-background
              :show-arrows="false"
              class="pa-0 ma-0"
              height="400px"
              v-model="model"
            >
              <v-carousel-item v-for="(image) in product.images" :key="image.id" class="pa-0 ma-0">
                <v-img
                  :src="image.image"
                  height="400px"
                  width="500px"
                  contain
                />
              </v-carousel-item>
            </v-carousel>
          </template>
          <template v-else-if="product.image">
            <v-img
              :src="product.image"
              height="400px"
              width="400px"
              contain
            />
          </template>
          <template v-else>
            <v-img
              src="/no-photo.png"
              width="80%;"
              height="80%;"
              contain
            />
          </template>
        </v-card-text>
        <v-card-text>
          <v-col class="d-flex justify-center align-start pa-0 ma-0" cols="12">
            <v-slide-group
              v-model="model"
              class="pa-4"
              show-arrows
            >
              <v-slide-item
                v-for="image in product.images"
                :key="image.id"
                v-slot="{ toggle }"
              >
                <v-card
                  class="ma-4"
                  height="50"
                  width="50"
                  @click="toggle"
                >
                  <v-img
                    :src="image.image"
                    width="50px"
                    height="50px"
                    contain
                  />
                </v-card>
              </v-slide-item>
            </v-slide-group>
          </v-col>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-col>
</template>

<script>
export default {
  name: "ProductDetail",

    props: {
        product: {
            type: Object,
            required: true,
        },
    },

  data: () => ({
    modal: false,

    snackbar: false,
    timeout: 1000,

    model: null,

    tab: null,
    items: [
      'Описание',
    ],
    text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
  }),

  computed: {
    description() {
      if (this.product.description) {
        return `<div>${this.product.description}</div>`
      }

      return null
    },
    basketCode() {
      return this.$store.state.basket.initialData;
    },
    seoDescription() {
      const regex = /( |<([^>]+)>)/ig

      return this.product.description ? this.product.description.replace(regex, '') : 'Широкий выбор моек , насосов и каналопромывочного оборудования'
    },
  },

  head() {
    return {
      title: this.product.seo_title ? this.product.seo_title : `${this.product.title} - Заказывайте у нас с доставкой по всей России`,
      meta: [
        {
          name: 'description', content: this.product.seo_description ? this.product.seo_description : this.seoDescription,
        },
        {
          name: 'og:title', content: this.product.seo_title ? this.product.seo_title : `${this.product.title} - Заказывайте у нас с доставкой по всей России`,
        },
        {
          name: 'og:description', content: this.product.seo_description ? this.product.seo_description : this.seoDescription,
        },
      ],
    }
  },

  async fetch() {
    const productSlug = this.$router.currentRoute.params.id

    const {data} = await this.$axios.get(`product/${productSlug}`)

    data.item.realTitle = data.item.title

    this.product = data.item
  },

  methods: {
    queryPrice(product) {
      this.$store.commit('query/SET_TO_QUERY', product)
      this.$store.commit('query/SHOW_DIALOG')
    },
    async tryBuy() {
      const buyData = {
        product_id: this.product.id,
        quantity: 1,
      }

      await this.$axios.put(`basket/${this.basketCode}`, buyData)
        .finally(() => {
          this.snackbar = true
        })
    },

    toggleModal() {
      this.modal = !this.modal
    }
  }
}
</script>

<style lang="scss" scoped>
.info_gutters {
  margin-top: 15px !important;

  .ch-title {
    font-size: 18px;
    font-weight: 500;
  }

  .ch-info {
    font-size: 14px;
  }

  .char_margin {
    margin-top: 10px !important;
  }

  .buy-card {
    width: 100%;

    .buy-btn {
      width: 100%;
      padding: 20px 0 !important;
    }
  }
}

.div-margin {
  margin-top: 10px !important;
  margin-bottom: 10px !important;
}

.tab_info {
  min-height: 200px;
}

.selectedImage {
  border: 1px solid #A1CB0D;
}
</style>
