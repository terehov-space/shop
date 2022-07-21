<template>
    <v-col cols="12">
        <template v-if="currentSection">
            <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
                <div>
                    Подразделы: {{ currentSection.title }}
                </div>
                <div>
                    <v-btn tile text
                        :to="currentSection.sectionId ? `/admin/catalog/sections/${currentSection.sectionId}` : '/admin/catalog/sections'">
                        <v-icon>
                            mdi-arrow-left
                        </v-icon>
                        назад
                    </v-btn>
                    <v-btn tile text
                        :to="currentSection ? `/admin/catalog/sections/0/edit?parent=${currentSection.id}` : '/admin/catalog/sections/0/edit'">
                        <v-icon>
                            mdi-plus
                        </v-icon>
                        Добавить
                    </v-btn>
                </div>
            </v-col>
        </template>
        <template v-else>
            <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
                <div>
                    Разделы
                </div>
                <div>
                    <v-btn tile text to="/admin/catalog/sections/0/edit">
                        <v-icon>
                            mdi-plus
                        </v-icon>
                        Добавить
                    </v-btn>
                </div>
            </v-col>
        </template>
        <template v-if="!currentSection">
            <v-col cols="12">
                <v-text-field v-model="search" outlined placeholder="Поиск" hide-details dense
                    @keydown.enter="trySearch" class="mb-4" />
                <v-checkbox v-model="searchSub" label="Искать в подразделах" :value="1" hide-details class="mb-4" />
                <v-btn tile text :disabled="!search || search.length < 3" @click="trySearch">
                    Найти
                </v-btn>
                <v-btn tile text :disabled="!search || search.length < 3" @click="clearSearch">
                    Сбросить
                </v-btn>
            </v-col>
        </template>
        <v-col cols="12">
            <v-data-table :headers="headers" :items="sections" :loading="loading" hide-default-footer>
                <template v-slot:item.actions="{ item }">
                    <div class="text-end">
                        <v-btn tile text :disabled="!item.hasSections || item.level > 2" :to="`/admin/catalog/sections/${item.id}`" title="Список подразделов">
                            <v-icon>
                                mdi-file-tree
                            </v-icon>
                        </v-btn>
                        <v-btn tile text :disabled="item.level > 2" :to="`/admin/catalog/sections/0/edit?parent=${item.id}`" title="Добавить подраздел">
                            <v-icon>
                                mdi-plus
                            </v-icon>
                        </v-btn>
                        <v-btn tile text :to="`/admin/catalog/sections/${item.id}/props`" v-if="!currentSection" title="Свойства">
                            <v-icon>
                                mdi-form-textbox
                            </v-icon>
                        </v-btn>
                        <v-btn tile text :to="`/admin/catalog/sections/${item.id}/edit`" title="редактировать">
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
                <v-pagination v-model="page" :length="pageCount" />
            </v-col>
        </template>
    </v-col>
</template>

<script>
import headers from '../static/sectionHeaders.json'

export default {
    props: {
        items: {
            required: true,
            type: Array,
            default: () => [],
        },
        currentPage: {
            required: true,
            type: Number,
            default: () => 0,
        },
        lastPage: {
            required: true,
            type: Number,
            default: () => 0,
        },
        item: {
            type: Object,
            require: false,
            default: () => {
            },
        },
    },

    data: () => ({
        search: null,
        searchSub: null,
        sections: [],
        currentSection: {},
        headers,
        page: 0,
        pageCount: -1,
        loading: false,
    }),

    mounted() {
        this.sections = this.items
        this.currentSection = this.item
        this.page = this.currentPage
        this.pageCount = this.lastPage

        const queryParams = new URLSearchParams(window.location.search);
        this.search = queryParams.get('q')
        this.searchSub = parseInt(queryParams.get('s')) || null
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
    },
}
</script>

<style scoped>
</style>
