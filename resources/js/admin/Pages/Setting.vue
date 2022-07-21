<template>
    <v-col cols="12">
        <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
            <div>
                Настройки сайта
            </div>
        </v-col>
        <v-col class="d-flex justify-start align-start ma-0 pa-0" cols="12">
            <v-col class="d-flex justify-start flex-wrap align-start" cols="10">
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="Title основной"
                        v-model="item.indexTitle"
                        outlined
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="Description основной"
                        v-model="item.indexDescription"
                        outlined
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="Телефон"
                        v-model="item.phone"
                        outlined
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="Номер whatsapp"
                        v-model="item.whatsPhone"
                        outlined
                    />
                </v-col>
            </v-col>
            <v-col class="d-flex justify-start align-start flex-wrap" cols="2">
                <v-btn width="100%" class="mb-4" elevation="0" color="success" @click="trySave(false)"
                       :disabled="!changed">
                    Сохранить
                </v-btn>
                <v-btn width="100%" class="mb-4" elevation="0" color="error">
                    Удалить
                </v-btn>
                <v-btn width="100%" class="mb-4" elevation="0" color="error" outlined
                       :to="item.sectionId ? `/admin/catalog/sections/${item.sectionId}` : '/admin/catalog/sections'">
                    Назад
                </v-btn>
            </v-col>
        </v-col>
    </v-col>
</template>

<script>
import axios from 'axios'

export default {
    props: {
        setting: {
            required: true,
            type: Object,
            default: () => {
            },
        },
    },

    data: () => ({
        item: {},
    }),

    computed: {
        changed() {
            return JSON.stringify(this.item) !== JSON.stringify(this.setting)
        },
    },

    mounted() {
        this.item = JSON.parse(JSON.stringify(this.setting))
    },

    methods: {
        trySave() {
            this.$inertia.post(window.location.pathname, this.item)
        },
    },
}
</script>

<style scoped>

</style>
