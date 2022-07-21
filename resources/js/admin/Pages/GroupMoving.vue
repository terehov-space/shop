<template>
    <v-col cols="12">
        <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
            <div>
                Товары
            </div>
            <div>
                <v-btn tile text
                       to="/admin/catalog/products/0">
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

            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                <v-autocomplete
                    label="Основной раздел"
                    v-model="filterSectionId"
                    :items="filterRootSections"
                    item-text="title"
                    item-value="id"
                    outlined
                    clearable
                />
            </v-col>
            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                <v-autocomplete
                    label="Дополнительные разделы"
                    v-model="filterSecondIdsList"
                    :items="filterSubSections"
                    item-text="title"
                    item-value="id"
                    outlined
                    clearable
                />
            </v-col>
            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                <v-autocomplete
                    label="Дополнительные вложенные разделы"
                    v-model="filterThirdIdsList"
                    :items="filterSubSubSections"
                    item-text="title"
                    item-value="id"
                    :disabled="!filterSubSubSections.length"
                    outlined
                    clearable
                />
            </v-col>
            <v-btn tile text @click="trySearch">
                Найти
            </v-btn>
            <v-btn tile text :disabled="(!search || search.length < 3)" @click="clearSearch">
                Сбросить
            </v-btn>
        </v-col>
        <v-col cols="12">
            <v-data-table
                :headers="headers"
                :items="products"
                :loading="loading"
                hide-default-footer
                :item-class="hasError"
                :items-per-page="products.length"
            >
                <template v-slot:item.actions="{item}">
                    <div class="text-end">
                        <v-checkbox
                            v-model="toMoveIds"
                            :value="item.id"
                            label=""
                        />
                    </div>
                </template>
            </v-data-table>
        </v-col>
        <v-fab-transition>
            <v-btn
                color="success"
                dark
                fixed
                bottom
                right
                fab
                @click="showGroupDialog = !showGroupDialog"
            >
                <v-icon>mdi-group</v-icon>
            </v-btn>
        </v-fab-transition>
        <v-dialog v-model="showGroupDialog" width="500px">
            <v-card width="500px">
                <v-card-title>
                    Групповой перенос товаров({{ toMoveIds.length }})
                </v-card-title>
                <v-card-text>
                    <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                        <v-autocomplete
                            label="Основной раздел"
                            v-model="sectionId"
                            :items="rootSections"
                            item-text="title"
                            item-value="id"
                            outlined
                            clearable
                        />
                    </v-col>
                    <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                        <v-autocomplete
                            label="Дополнительные разделы"
                            v-model="secondIdsList"
                            :items="subSections"
                            item-text="title"
                            item-value="id"
                            outlined
                            clearable
                        />
                    </v-col>
                    <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                        <v-autocomplete
                            label="Дополнительные вложенные разделы"
                            v-model="thirdIdsList"
                            :items="subSubSections"
                            item-text="title"
                            item-value="id"
                            :disabled="!subSubSections.length"
                            outlined
                            clearable
                        />
                    </v-col>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        @click="trySave"
                        outlined
                        color="success"
                        :loading="loading"
                    >
                        Применить
                    </v-btn>
                    <v-btn
                        @click="cancelSaving"
                        outlined
                        color="error"
                    >
                        Отмена
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-col>
</template>

<script>
import headers from '../static/groupHeaders.json'

export default {
    props: {
        items: {
            required: true,
            type: Array,
            default: () => [],
        },
        sections: {
            type: Array,
            require: true,
            default: () => [],
        },
    },

    data: () => ({
        search: null,
        sectionId: null,
        sectionList: [],
        products: [],
        headers,
        loading: false,
        toMoveIds: [],
        showGroupDialog: false,
        secondIdsList: [],
        thirdIdsList: [],
        filterSectionId: null,
        filterSecondIdsList: null,
        filterThirdIdsList: null,
    }),

    computed: {
        subSections() {
            const sections = JSON.parse(JSON.stringify(this.sectionList))

            if (this.sectionId) {
                return sections.filter((item) => item.sectionId === this.sectionId)
            } else {
                return sections
            }
        },
        subSubSections() {
            const sections = JSON.parse(JSON.stringify(this.sectionList))

            if (this.sectionId) {
                return sections.filter((item) => this.secondIdsList === item.sectionId)
            } else {
                return []
            }
        },
        rootSections() {
            return this.sectionList.filter((item) => !item.sectionId)
        },
        filterSubSections() {
            const sections = JSON.parse(JSON.stringify(this.sectionList))

            if (this.filterSectionId) {
                return sections.filter((item) => item.sectionId === this.filterSectionId)
            } else {
                return sections
            }
        },
        filterSubSubSections() {
            const sections = JSON.parse(JSON.stringify(this.sectionList))

            if (this.filterSectionId) {
                return sections.filter((item) => this.filterSecondIdsList === item.sectionId)
            } else {
                return []
            }
        },
        filterRootSections() {
            return this.sectionList.filter((item) => !item.filterSectionId)
        },
    },

    mounted() {
        this.loading = false
        this.products = this.items
        this.sectionList = this.sections

        const queryParams = new URLSearchParams(window.location.search);
        this.search = queryParams.get('q')
        this.filterSectionId = Number(queryParams.get('sectionId')) || null
        this.filterSecondIdsList = Number(queryParams.get('secondIds')) || []
        this.filterThirdIdsList = Number(queryParams.get('thirdIds')) || []
    },

    methods: {
        trySave() {
            this.loading = true

            const data = {
                items: this.toMoveIds,
                sectionId: this.sectionId,
                secondIds: this.secondIdsList,
                thirdIds: this.thirdIdsList,
            }

            this.$inertia.post(window.location.href, data)
            this.showGroupDialog = false
            this.loading = false
        },
        cancelSaving() {
            this.sectionId = null
            this.secondIdsList = null
            this.thirdIdsList = null
            this.showGroupDialog = false
        },
        hasError(item) {
            return item.hasErrors ? 'tr-error-class' : '';
        },
        trySearch() {
            const queryParams = new URLSearchParams();

            if (this.search) {
                queryParams.append('q', this.search)
            }

            if (this.filterSectionId) {
                queryParams.append('sectionId', this.filterSectionId)
            }

            if (this.filterSecondIdsList) {
                queryParams.append('secondId', this.filterSecondIdsList)
            }

            if (this.filterThirdIdsList) {
                queryParams.append('thirdId', this.filterThirdIdsList)
            }

            this.$inertia.get(`${window.location.pathname}?${queryParams.toString()}`)
        },
        clearSearch() {
            this.$inertia.get(`${window.location.pathname}`)
        },
    },
}
</script>

<style lang="scss">
.tr-error-class {
    background-color: #ff5252;
}
</style>
