<template>
    <v-col cols="12">
        <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
            <div>
                Занчения свойства: {{ property.section.title }} - {{ property.title }}({{ property.type }})
            </div>
            <div>
                <v-btn tile text :to="`/admin/catalog/sections/${property.section.id}/props`">
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
                :items.sync="options"
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
                        <v-btn tile text @click="tryEdit(item)">
                            <v-icon>
                                mdi-pencil
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
                        <template v-if="property.realType === 'string'">
                            <v-text-field
                                v-model="option.value"
                                label="Значение"
                                class="mb-4"
                                outlined
                                hide-details
                            />
                        </template>
                        <template v-else-if="property.realType === 'text'">
                            <v-textarea
                                v-model="option.value"
                                label="Значение"
                                class="mb-4"
                                outlined
                                hide-details
                            />
                        </template>
                        <template v-else-if="property.realType === 'number'">
                            <v-text-field
                                v-model="option.value"
                                label="Значение"
                                class="mb-4"
                                type="number"
                                outlined
                                hide-details
                            />
                        </template>
                        <template v-else-if="property.realType === 'float'">
                            <v-text-field
                                v-model="option.value"
                                label="Значение"
                                class="mb-4"
                                type="number"
                                step=".1"

                                outlined
                                hide-details
                            />
                        </template>
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
import headers from '../static/optionHeaders.json'

export default {
    props: {
        items: {
            required: true,
            type: Array,
            default: () => [],
        },
        property: {
            required: true,
            type: Object,
            default: () => {
            },
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
        loading: false,
        search: null,
        page: 0,
        pageCount: -1,
        headers,
        option: {},
        tmpOption: {
            value: '',
            propertyId: null,
            sectionId: null,
        }
    }),

    computed: {
        options() {
            return this.items
        },
    },

    mounted() {
        this.tmpOption.sectionId = this.property.sectionId
        this.tmpOption.propertyId = this.property.id
        this.option = JSON.parse(JSON.stringify(this.tmpOption))
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
        tryDelete(id) {
            this.$inertia.delete(`${window.location.pathname}/${id}`)
        },
        trySave() {
            this.$inertia.post(window.location.pathname, this.option)
            this.cancelSaving()
        },
        tryEdit(item) {
            this.option = JSON.parse(JSON.stringify(item))
            this.showDialog = true
        },
        cancelSaving() {
          this.showDialog = false
          this.clearOption()
        },
        trySearch() {
            let searchQuery = `q=${this.search}`

            if (this.searchSub) {
                searchQuery += `&s=1`
            }

            this.$inertia.get(`${window.location.pathname}?${searchQuery}`)
        },
        clearSearch() {
            this.search = null
        },
        clearOption() {
            this.option = JSON.parse(JSON.stringify(this.tmpOption))
        },
    },
}
</script>

<style scoped>

</style>
