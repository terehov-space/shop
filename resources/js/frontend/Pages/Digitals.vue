<template>
    <v-col class="d-flex justify-space-between align-start flex-wrap no--gutters" cols="12">
        <v-col class="d-none d-md-flex justify-start align-start text-h5" cols="10" md="12">
            Электронные каталоги
        </v-col>
        <v-col class="d-flex justify-center align-start flex-wrap no--gutters" cols="12" md="2">
            <DigitalFilters @filter="filterCatalog" :items="vendors"/>
        </v-col>
        <v-col class="d-none d-md-flex justify-end align-start flex-wrap no--gutters" cols="12" md="1">
        </v-col>
        <v-col class="d-flex justify-center justify-md-end align-start flex-wrap no--gutters" cols="12" md="9">
            <DigitalCatalog :filter.sync="selectedVendor" :items="items"/>
        </v-col>
    </v-col>
</template>

<script>
import DigitalFilters from "../components/DigitalFilters";
import DigitalCatalog from "../components/DigitalCatalog";

export default {
    components: {DigitalCatalog, DigitalFilters},

    props: {
        items: {
            required: true,
            type: Array,
            default: () => [],
        },
        vendors: {
            required: true,
            type: Array,
            default: () => [],
        },
        settings: {
            type: Object,
            required: true,
            default: () => {
            },
        },
    },

    data: () => ({
        selectedVendor: null,
    }),

    methods: {
        filterCatalog(selectedVendor) {
            this.selectedVendor = selectedVendor
        }
    }
}
</script>
