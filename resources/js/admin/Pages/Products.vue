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
            <v-autocomplete
                v-model="section"
                :items="sectionList"
                item-text="title"
                item-value="id"
                outlined
                placeholder="Раздел"
                dense
                hide-details
                class="mb-4"
                :disabled="Boolean(noSection)"
            />
            <v-checkbox
                label="Товары без разделов"
                v-model="noSection"
                :value="1"
                class="mb-4"
                :disabled="Boolean(section)"
            />
            <v-btn tile text :disabled="(!search || search.length < 3) && !this.section && !noSection"
                   @click="trySearch">
                Найти
            </v-btn>
            <v-btn tile text :disabled="(!search || search.length < 3) && !this.section && !noSection"
                   @click="clearSearch">
                Сбросить
            </v-btn>
        </v-col>
        <v-col class="d-flex justify-end align-center" cols="12">
            <template v-if="!allSelected && this.groupProducts.length === 0">
                <v-btn outlined @click="allSelected = true">Выделить все</v-btn>
            </template>
            <template v-else>
                <v-btn outlined @click="deselectAll">Снять выделение</v-btn>
            </template>
        </v-col>
        <v-col cols="12">
            <v-data-table
                :headers="headers"
                :items="products"
                :loading="loading"
                hide-default-footer
                :item-class="hasError"
                :items-per-page.sync="showPerPage"
            >
                <template v-slot:item.actions="{item}">
                    <div class="text-end">
                        <v-btn tile text :to="`/admin/catalog/products/${item.id}`">
                            <v-icon>
                                mdi-pencil
                            </v-icon>
                        </v-btn>
                    </div>
                </template>
                <template v-slot:item.check="{item}">
                    <div class="text-end">
                        <v-checkbox
                            v-model="groupProducts"
                            :value="item.id"
                        />
                    </div>
                </template>
            </v-data-table>
        </v-col>
        <template v-if="lastPage > 1">
            <v-col cols="12">
                <v-pagination v-model="page" :length="pageCount"/>
            </v-col>
        </template>
        <v-select
            v-model="showPerPage"
            :items="showPerPageList"
        />
        <v-btn @click="setPerPage" outlined>Применить</v-btn>

        <v-speed-dial
            v-model="fab"
            bottom
            right
            fixed
        >
            <template v-slot:activator>
                <v-btn
                    v-model="fab"
                    color="blue darken-2"
                    dark
                    fab
                >
                    <v-icon v-if="fab">
                        mdi-close
                    </v-icon>
                    <v-icon v-else>
                        mdi-group
                    </v-icon>
                </v-btn>
            </template>
            <v-btn
                fab
                dark
                small
                color="green"
                title="Производители"
                @click="showVendorDialog = !showVendorDialog"
            >
                <v-icon>mdi-cogs</v-icon>
            </v-btn>
            <v-btn
                fab
                dark
                small
                color="indigo"
                title="Разделы"
                @click="changeSectionsDialog = !changeSectionsDialog"
            >
                <v-icon>mdi-format-section</v-icon>
            </v-btn>
        </v-speed-dial>
        <v-dialog v-model="changeSectionsDialog" width="500px">
            <v-card width="500px">
                <v-card-title>
                    Групповой перенос товаров({{ groupProducts.length }})
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
                        @click="trySaveSections"
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
        <v-dialog v-model="showVendorDialog" width="500px">
            <v-card width="500px">
                <v-card-title>
                    Групповой перенос товаров({{ groupProducts.length }})
                </v-card-title>
                <v-card-text>
                    <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                        <v-autocomplete
                            label="Производитель"
                            v-model="vendorId"
                            :items="vendorsList"
                            item-text="title"
                            item-value="id"
                            outlined
                            clearable
                        />
                    </v-col>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        @click="trySaveVendor"
                        outlined
                        color="success"
                        :loading="loading"
                    >
                        Применить
                    </v-btn>
                    <v-btn
                        @click="cancelSavingVendor"
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
import headers from '../static/productHeaders.json'
import axios from 'axios'

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
        perPage: {
            required: false,
            type: Number,
            default: () => 10,
        },
        sections: {
            type: Array,
            require: true,
            default: () => [],
        },
        vendors: {
            type: Array,
            require: true,
            default: () => [],
        },
    },

    data: () => ({
        allSelected: false,

        // dialog to change sections
        changeSectionsDialog: false,
        sectionId: null,
        secondIdsList: null,
        thirdIdsList: null,

        // dialog to change vendor
        showVendorDialog: false,
        vendorId: null,

        vendorsList: [],

        fab: null,
        groupProducts: [],
        search: null,
        section: null,
        sectionList: [],
        products: [],
        noSection: null,
        headers,
        page: 0,
        pageCount: -1,
        loading: false,
        showPerPage: 10,
        showPerPageList: [
            {
                text: 10,
                value: 10,
            },
            {
                text: 20,
                value: 20,
            },
            {
                text: 50,
                value: 50,
            },
            {
                text: 100,
                value: 100,
            },
        ],
    }),

    mounted() {
        this.products = this.items
        this.sectionList = this.sections
        this.page = this.currentPage
        this.pageCount = this.lastPage

        const queryParams = new URLSearchParams(window.location.search);
        this.search = queryParams.get('q')
        this.section = parseInt(queryParams.get('section')) || null
        this.noSection = parseInt(queryParams.get('n')) || null
        this.showPerPage = this.perPage

        if (queryParams.get('perPage')) {
            this.showPerPage = parseInt(queryParams.get('perPage'))
        }

        this.vendorsList = JSON.parse(JSON.stringify(this.vendors))
    },

    computed: {
        subSections() {
            const sections = JSON.parse(JSON.stringify(this.sections))

            if (this.sectionId) {
                return sections.filter((item) => item.sectionId === this.sectionId)
            } else {
                return sections
            }
        },
        subSubSections() {
            const sections = JSON.parse(JSON.stringify(this.sections))

            if (this.sectionId) {
                return sections.filter((item) => this.secondIdsList === item.sectionId)
            } else {
                return []
            }
        },
        rootSections() {
            const sections = JSON.parse(JSON.stringify(this.sections))

            return sections.filter((item) => !item.sectionId)
        },
    },

    watch: {
        allSelected(newVal) {
            if (newVal) {
                console.log('test')
                this.products.forEach((item) => {
                    this.groupProducts.push(item.id)
                })
            } else {
                this.groupProducts = []
            }
        },
        page(newVal, oldVal) {
            if (oldVal !== 0) {
                this.loading = true

                const queryParams = new URLSearchParams();

                queryParams.append('page', this.page)

                if (this.search) {
                    queryParams.append('q', this.search)
                }

                if (this.section) {
                    queryParams.append('section', this.section)
                } else if (this.noSection) {
                    queryParams.append('n', '1')
                }

                if (this.showPerPage) {
                    queryParams.append('perPage', this.showPerPage)
                }

                this.$inertia.get(`${window.location.pathname}?${queryParams.toString()}`)
            }
        },

    },

    methods: {
        deselectAll() {
            this.allSelected = false
            this.groupProducts = []
        },
        cancelSaving() {
            this.sectionId = null
            this.secondIdsList = null
            this.thirdIdsList = null
            this.changeSectionsDialog = null
        },
        trySaveSections() {
            this.loading = true

            const data = {
                items: this.groupProducts,
                sectionId: this.sectionId,
                secondIds: this.secondIdsList,
                thirdIds: this.thirdIdsList,
            }

            axios.post('/admin/catalog/group/sections', data)
                .then(() => {
                    this.changeSectionsDialog = false
                    this.loading = false
                    window.location.reload()
                })
        },
        cancelSavingVendor() {
            this.vendorId = null
            this.showVendorDialog = false
        },
        async trySaveVendor() {
            this.loading = true

            const data = {
                items: this.groupProducts,
                vendorId: this.vendorId
            }

            axios.post('/admin/catalog/group/vendors', data)
                .then(() => {
                    this.showVendorDialog = false
                    this.loading = false
                    window.location.reload()
                })
        },
        setPerPage() {
            const queryParams = new URLSearchParams();

            if (this.search) {
                queryParams.append('q', this.search)
            }

            if (this.section) {
                queryParams.append('section', this.section)
            } else if (this.noSection) {
                queryParams.append('n', '1')
            }

            if (this.showPerPage) {
                queryParams.append('perPage', this.showPerPage)
            }

            this.$inertia.get(`${window.location.pathname}?${queryParams.toString()}`)
        },
        hasError(item) {
            return item.hasErrors ? 'tr-error-class' : '';
        },
        trySearch() {
            const queryParams = new URLSearchParams();

            if (this.search) {
                queryParams.append('q', this.search)
            }

            if (this.section) {
                queryParams.append('section', this.section)
            } else if (this.noSection) {
                queryParams.append('n', '1')
            }

            if (this.showPerPage) {
                queryParams.append('perPage', this.showPerPage)
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
