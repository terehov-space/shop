<template>
    <v-col cols="12">
        <template v-if="item.id == 0">
            <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
                <div>
                    Добавление страницы
                </div>
            </v-col>
        </template>
        <template v-else>
            <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
                <div>
                    Редактирование страницы
                </div>
            </v-col>
        </template>
        <v-col class="d-flex justify-start align-start ma-0 pa-0" cols="12">
            <v-col class="d-flex justify-start flex-wrap align-start" cols="10">
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="Название"
                        v-model="section.title"
                        outlined
                        :error="errors && Boolean(errors.title)"
                        :error-messages="errors.title"
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="ЧПУ раздела"
                        v-model="section.code"
                        outlined
                        :error="errors && Boolean(errors.code)"
                        :error-messages="errors.code"
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-autocomplete
                        label="Родительская страница"
                        v-model="section.pageId"
                        :items="sections"
                        item-text="title"
                        item-value="id"
                        outlined
                        clearable
                        hide-details
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <editor v-model="section.body"/>
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="title"
                        v-model="section.seoTitlePostfix"
                        outlined
                        counter
                        hide-spin-buttons
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-textarea
                        label="description"
                        v-model="section.seoDescription"
                        outlined
                        counter
                        hide-spin-buttons
                    />
                </v-col>
            </v-col>
            <v-col class="d-flex justify-start align-start flex-wrap" cols="2">
                <v-btn width="100%" class="mb-4" elevation="0" color="success" @click="trySave(false)" :disabled="!changed">
                    Сохранить
                </v-btn>
                <v-btn width="100%" class="mb-4" elevation="0" color="success" @click="trySave(true)" :disabled="!changed">
                    Сохранить и выйти
                </v-btn>
                <v-btn width="100%" class="mb-4" elevation="0" color="error">
                    Удалить
                </v-btn>
                <v-btn width="100%" class="mb-4" elevation="0" color="error" outlined
                       to="/admin/content/pages">
                    Назад
                </v-btn>
            </v-col>
        </v-col>
        <v-col class="d-flex align-end flex-column" style="position: absolute; right: 0; bottom: 0;" v-if="alerts.length > 0">
            <template v-for="(alert, aDx) in alerts">
                <v-alert border="left" :color="alert.type == 'error'? 'red' : 'green'" dark width="250px" :key="aDx">{{ alert.text }}</v-alert>
            </template>
        </v-col>
    </v-col>
</template>

<script>
import axios from 'axios'

export default {
    props: {
        item: {
            required: true,
            type: Object,
            default: () => {
            },
        },
        sections: {
            required: true,
            type: Array,
            default: () => [],
        },
        errors: {
            required: false,
            type: Object,
            default: () => {},
        }
    },

    data: () => ({
        section: {},
        sectionsList: [],
        alerts: [],
        popAlerts: null,
        uploadImage: false,
    }),

    computed: {
        changed() {
            return JSON.stringify(this.item) !== JSON.stringify(this.section)
        },
    },

    mounted() {
        this.section = JSON.parse(JSON.stringify(this.item))
        this.sectionsList = JSON.parse(JSON.stringify(this.sections))
    },

    methods: {
        trySave(exit) {
            let queryParams = new URLSearchParams(window.location.search)

            if (exit) {
                queryParams.append('e', '1')
            }

            this.$inertia.post(`${window.location.pathname}?${queryParams}`, this.section)
        },
        clearAlerts() {
            if (!this.popAlerts) {
                setInterval(() => {
                    if (this.alerts.length > 0) {
                        this.alerts.shift()
                    } else {
                        clearTimeout(this.popAlerts)
                    }
                }, 1000)
            }
        }
    },
}
</script>

<style scoped>

</style>
