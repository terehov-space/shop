<template>
    <v-col class="d-flex justify-start align-start" cols="12">
        <v-carousel
            cycle
            height="450px"
            hide-delimiter-background
            show-arrows-on-hover
            hide-delimiters
            class="carousel_wrapper"
        >
            <template v-for="(slide, i) in items">
                <template v-if="slide.hasLink">
                    <v-carousel-item
                        :key="i"
                        :src="$device.mobile && slide.mobileImage ? slide.mobileImage : slide.image"
                        :to="slide.link"
                        class="inner-slide"
                    >
                        <div class="carousel-title">
                            {{ slide.title }}
                        </div>
                    </v-carousel-item>
                </template>
                <template v-else>
                    <v-carousel-item
                        :key="i"
                        :src="$device.mobile && slide.mobileImage ? slide.mobileImage : slide.image"
                        class="inner-slide"
                    >
                        <div class="carousel-title">
                            {{ slide.title }}
                        </div>
                    </v-carousel-item>
                </template>
            </template>
        </v-carousel>
    </v-col>
</template>

<script>
export default {
    name: "IndexCarousel",

    props: {
        items: {
            type: Array,
            required: true,
            default: () => [],
        }
    },

    async fetch() {
        const {data} = await this.$axios.get('index/carousel')
        this.items = data.items
    },
}
</script>

<style lang="scss" scoped>
.carousel_wrapper {
    border-radius: 8px;
    width: 100%;

    .inner-slide {
        .v-image.v-responsive {
            .v-responsive__sizer {
                .v-image__image.v-image__image--cover {
                    background-size: 100px !important;
                }
            }
        }

        .carousel-title {
            position: absolute;
            padding: 10px 15px;
            background-color: #A1CB0D;
            color: #FFFFFF;
            bottom: 0;
            right: 0;
            font-size: 24px;
            border-top-left-radius: 8px;
        }
    }
}
</style>
