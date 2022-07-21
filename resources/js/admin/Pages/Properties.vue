<template>
    <v-col cols="12">
        <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
            <div>
                Свойства раздела: {{ section.title }}
            </div>
            <div>
                <v-btn tile text to="/admin/catalog/sections">
                    <v-icon>
                        mdi-arrow-left
                    </v-icon>
                    Назад
                </v-btn>
                <v-btn tile text @click="showDialog = true">
                    <v-icon>
                        mdi-plus
                    </v-icon>
                    Добавить
                </v-btn>
            </div>
        </v-col>
        <v-col cols="12">
            <v-text-field
                v-model="search"
                outlined
                placeholder="Поиск"
                hide-details
                dense
                @keydown.enter="trySearch"
                class="mb-4"
            />
            <v-btn tile text :disabled="!search || search.length < 3" @click="trySearch">
                Найти
            </v-btn>
            <v-btn tile text :disabled="!search || search.length < 3" @click="clearSearch">
                Сбросить
            </v-btn>
        </v-col>
        <v-col cols="12">
            <v-data-table
                :headers="headers"
                :items.sync="properties"
                :loading="loading"
                hide-default-footer
            >
                <template v-slot:item.actions="{item}">
                    <div class="text-end">
                        <v-btn tile text @click="tryDelete(item.id)">
                            <v-icon>
                                mdi-delete
                            </v-icon>
                        </v-btn>
                        <v-btn tile text :to="`/admin/catalog/sections/${section.id}/props/${item.id}`" :disabled="item.realType == 'model'">
                            <v-icon>
                                mdi-eye
                            </v-icon>
                        </v-btn>
                    </div>
                </template>
            </v-data-table>
        </v-col>
        <template v-if="lastPage > 1">
            <v-col cols="12">
                <v-pagination v-model="page" :length="pageCount"/>
            </v-col>
        </template>
        <v-dialog v-model="showDialog" width="400">
            <v-card width="400px" height="500px">
                <v-card-title>Добавить свойство</v-card-title>
                <v-card-text>
                    <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0 py-2" cols="12">
                        <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
                            <v-text-field
                                v-model="property.title"
                                label="Название"
                                class="mb-4"
                                outlined
                                hide-details
                            />
                        </v-col>
                        <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
                            <v-autocomplete
                                v-model="property.type"
                                label="Тип"
                                :items="types"
                                item-text="title"
                                item-value="val"
                                class="mb-4"
                                outlined
                                hide-details
                            />
                        </v-col>
                        <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
                            <v-autocomplete
                                v-model="property.sectionIds"
                                label="Разделы"
                                :items="sections"
                                item-text="title"
                                item-value="id"
                                multiple
                                outlined
                                hide-details
                            />
                        </v-col>
                    </v-col>
                </v-card-text>
                <v-card-actions>
                    <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0 py-2" cols="12">
                        <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
                            <v-btn
                                outlined
                                @click="cancelSaving"
                            >
                                Отмена
                            </v-btn>
                            <v-btn
                                outlined
                                @click="trySave"
                                class="ml-4"
                            >
                                Сохранить
                            </v-btn>
                        </v-col>
                    </v-col>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-col>
</template>

<script>
import headers from '../static/propertyHeaders.json'

export default {
    props: {
        items: {
            required: true,
            type: Array,
            default: () => [],
        },
        section: {
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
        types: {
            required: true,
            type: Array,
            default: () => [],
        },
        lastPage: {
            required: true,
            type: Number,
            default: () => 0,
        },
        currentPage: {
            required: true,
            type: Number,
            default: () => 0,
        },
    },

    data: () => ({
        showDialog: false,
        search: null,
        headers,
        page: 0,
        pageCount: -1,
        loading: false,
        property: {},
        tmpProperty: {
            title: '',
            sectionIds: [],
            type: 'string,',
        }
    }),

    computed: {
        properties() {
            return this.items
        },
    },

    mounted() {
        this.page = this.currentPage
        this.pageCount = this.lastPage

        const queryParams = new URLSearchParams(window.location.search);
        this.search = queryParams.get('q')
        this.tmpProperty.sectionIds.push(this.section.id)

        this.property = JSON.parse(JSON.stringify(this.tmpProperty))
    },

    watch: {
        page(newVal, oldVal) {
            if (oldVal !== 0) {
                this.loading = true

                let searchQuery = `page=${this.page}`

                if (this.search) {
                    searchQuery += `&q=${this.search}`

                    if (this.searchSub) {
                        searchQuery += `&s=1`
                    }
                }

                this.$inertia.get(`${window.location.pathname}?${searchQuery}`)
            }
        },
    },

    methods: {
        trySave() {
            this.$inertia.post(window.location.pathname, this.property)
            this.clearProperty()
        },
        tryDelete(id) {
            this.$inertia.delete(`${window.location.pathname}/${id}`)
        },
        cancelSaving() {
            this.showDialog = false
            this.clearProperty()
        },
        trySearch() {
            let searchQuery = `q=${this.search}`

            if (this.searchSub) {
                searchQuery += `&s=1`
            }

            this.$inertia.get(`${window.location.pathname}?${searchQuery}`)
        },
        clearSearch() {
            this.$inertia.get(`${window.location.pathname}`)
        },
        clearProperty() {
            this.showDialog = false
            this.property = JSON.parse(JSON.stringify(this.tmpProperty))
        }
    },
}
</script>

<style scoped>

</style>
