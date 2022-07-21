<template>
  <v-col class="d-flex justify-space-between justify-md-start align-start flex-wrap no--gutters middle-margin" cols="10"
         md="12">
    <template v-for="item in filteredItems">
      <a
        :href="item.file"
        :key="item.id"
        target="_blank"
      >
        <v-card
          class="digital_card mx-1 mb-8"
        >
          <v-img
            :src="item.image"
            class="digital_img"
          />
        </v-card>
      </a>
    </template>
  </v-col>
</template>

<script>
export default {
  name: "DigitalCatalog",

  props: {
    filter: {
      required: false,
      default: null,
      type: Number,
    },
    items: {
      required: true,
      default: () => {},
      type: Array,
    },
  },

  computed: {
    filteredItems() {
      if (this.filter === -1 || this.filter > 0) {
        return this.items.filter(item => {
          if (this.filter === -1) {
            return item.vendorId === null
          } else {
            return item.vendorId === this.filter
          }
        })
      }

      return this.items
    }
  },
}
</script>

<style lang="scss" scoped>
.digital_card {
  width: 184px;
  height: 275px;

  @media only screen and (max-width: 480px) {
    width: 145px;
    height: 225px;
  }

  .digital_img {
    width: 184px;
    height: 275px;

    @media only screen and (max-width: 480px) {
      width: 145px;
      height: 225px;
    }
  }
}
</style>
