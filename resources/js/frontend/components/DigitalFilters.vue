<template>
    <v-col class="d-flex justify-center align-start flex-wrap pa-0 ma-0 middle-margin" cols="12">
        <v-col class="d-none d-md-flex justify-start align-start pa-0 ma-0" cols="10" md="12">
            <v-card
                width="100%"
            >
                <v-card-title class="text-h6">
                    Фильтр
                </v-card-title>
                <v-card-text>
                    <v-list
                        dense
                        class="pa-0 ma-0"
                        itemid="id"
                    >
                        <v-list-item-group
                            v-model="selectedPartner"
                            color="primary"
                            class="pa-0 ma-0"
                        >
                            <v-list-item
                                v-for="item in partners"
                                :key="item.id"
                                class="pa-0 ma-0"
                                :value="item.id"
                            >
                                <v-list-item-content class="pa-0 ma-0">
                                    <v-list-item-title v-text="item.title" class="pa-0 ma-0"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item
                                class="pa-0 ma-0"
                                :value="-1"
                            >
                                <v-list-item-content class="pa-0 ma-0">
                                    <v-list-item-title class="pa-0 ma-0">Прочие</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col class="d-flex d-md-none justify-start align-start pa-0 ma-0" cols="10" md="12">
            <v-select
                v-model="selectedPartner"
                :items="partners"
                item-text="title"
                item-value="id"
                outlined
                hide-details
                placeholder="Выбрать парнёра"
                clearable
            />
        </v-col>
    </v-col>
</template>

<script>
export default {
    name: "DigitalFilters",

    props: {
        items: {
            type: Array,
            required: true,
            default: () => [],
        }
    },

    data: () => ({
        selectedPartner: null,
        partners: [],
    }),

    watch: {
        selectedPartner(newVal) {
            this.$emit('filter', newVal)
        }
    },

    mounted() {
        this.partners = JSON.parse(JSON.stringify(this.items))
    }
}
</script>

<style scoped>

</style>
