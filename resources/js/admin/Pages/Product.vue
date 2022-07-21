<template>
    <v-col cols="12">
        <template v-if="item.id == 0">
            <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
                <div>
                    Добавление товара
                </div>
            </v-col>
        </template>
        <template v-else>
            <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
                <div>
                    Редактирование товара
                </div>
            </v-col>
        </template>
        <v-col class="d-flex justify-start align-start ma-0 pa-0" cols="12">
            <v-col class="d-flex justify-start flex-wrap align-start pa-0 ma-0" cols="10">
                <v-col class="d-flex justify-start flex-wrap align-start" cols="12">
                    <v-tabs v-model="tab">
                        <v-tab>Основное</v-tab>
                        <v-tab>Разделы</v-tab>
                        <v-tab>Свойства</v-tab>
                        <v-tab>Галерея и файлы</v-tab>
                        <v-tab>Цены</v-tab>
                        <v-tab>SEO</v-tab>
                    </v-tabs>
                </v-col>
                <v-col class="d-flex justify-start flex-wrap align-start" cols="12">
                    <v-tabs-items v-model="tab" style="width: 100%">
                        <v-tab-item class="pa-2">
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-text-field
                                    label="Название"
                                    v-model="product.title"
                                    outlined
                                    :error="errors && Boolean(errors['product.title'])"
                                    :error-messages="errors['product.title']"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-text-field
                                    label="ЧПУ товара"
                                    v-model="product.code"
                                    outlined
                                    :error="errors && Boolean(errors['product.code'])"
                                    :error-messages="errors['product.code']"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-text-field
                                    label="Артикул"
                                    v-model="product.vendorCode"
                                    outlined
                                    :error="errors && Boolean(errors['product.vendorCode'])"
                                    :error-messages="errors['product.vendorCode']"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <editor v-model="product.description"/>
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-checkbox
                                    label="Показать на главной"
                                    v-model="product.showMain"
                                    :value="1"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-autocomplete
                                    label="Производитель"
                                    v-model="product.vendorId"
                                    :items="vendors"
                                    item-text="title"
                                    item-value="id"
                                    outlined
                                    clearable
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-menu
                                    absolute
                                    offset-y
                                    style="max-width: 600px"
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-card class="d-flex justify-center align-center" v-on="on" height="250px"
                                                width="250px" :loading="uploadImage">
                                            <template v-if="product.image && !uploadImage">
                                                <v-img :src="product.image" height="250px" width="250px"/>
                                            </template>
                                            <template v-else-if="!uploadImage">
                                                <v-icon>
                                                    mdi-plus
                                                </v-icon>
                                            </template>
                                            <input type="file" ref="upload" hidden @change="uploadPhoto($event)"
                                                   accept="image/svg+xml,image/jpeg,image/jpeg,image/png"/>
                                        </v-card>
                                    </template>
                                    <v-list>
                                        <v-list-item @click="removeImage">
                                            <v-list-item-title>Удалить</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item @click="addImage">
                                            <v-list-item-title>Загрузить</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </v-col>
                        </v-tab-item>
                        <v-tab-item class="pa-2">
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-autocomplete
                                    label="Основной раздел"
                                    v-model="product.sectionId"
                                    :items="rootSections"
                                    item-text="title"
                                    item-value="id"
                                    outlined
                                    clearable
                                    :error="sectionError"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-autocomplete
                                    label="Дополнительные разделы"
                                    v-model="secondIdsList"
                                    :items="subSections"
                                    item-text="title"
                                    item-value="id"
                                    multiple
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
                                    multiple
                                    outlined
                                    clearable
                                />
                            </v-col>
                        </v-tab-item>
                        <v-tab-item class="pa-2">
                            <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
                                <template
                                    v-if="!itemCurrentSection || itemCurrentSection.sectionId || currentSection != itemCurrentSection">
                                    <v-alert color="red" border="left" dark>Для доступа к редактированию свойств
                                        требуется
                                        выбрать основным разделом новый корневой раздел и сохранить товар
                                    </v-alert>
                                </template>
                                <template v-else>
                                    <template v-for="property in propertyList">
                                        <v-col
                                            class="d-flex justify-space-between align-start flex-wrap font-weight-bold pa-0 ma-0 mb-4"
                                            cols="12"
                                            :key="property.id"
                                        >
                                            <v-autocomplete
                                                v-model="property.value"
                                                :items="property.options"
                                                item-text="value"
                                                item-value="id"
                                                outlined
                                                :label="property.title"
                                                clearable
                                            />
                                        </v-col>
                                    </template>
                                </template>
                            </v-col>
                        </v-tab-item>
                        <v-tab-item class="pa-2">
                            <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
                                <v-col
                                    class="d-flex justify-space-between align-start flex-wrap font-weight-bold pa-0 ma-0 mb-4"
                                    cols="12">
                                    <div>
                                        Изображения галереи
                                    </div>
                                    <div>
                                        <template v-if="!imageList || imageList.length < 10">
                                            <v-btn text outlined color="success" @click="addGallery">
                                                <v-icon>
                                                    mdi-plus
                                                </v-icon>
                                            </v-btn>
                                        </template>
                                    </div>
                                    <input type="file" ref="gallery" hidden @change="uploadGallery($event)"
                                           accept="image/svg+xml,image/jpeg,image/jpeg,image/png"/>
                                </v-col>
                                <template v-for="(image, gIdx) in imageList">
                                    <v-menu
                                        absolute
                                        offset-y
                                        style="max-width: 600px"
                                        :key="gIdx"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-card class="d-flex justify-center align-center" v-on="on" height="250px"
                                                    width="250px">
                                                <v-img :src="image.image" height="250px" width="250px"/>
                                            </v-card>
                                        </template>
                                        <v-list>
                                            <v-list-item @click="removeGallery(image.image)">
                                                <v-list-item-title>Удалить</v-list-item-title>
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </template>
                            </v-col>
                            <v-col class="d-flex justify-start align-start flex-wrap" cols="12">
                                <v-col
                                    class="d-flex justify-space-between align-start flex-wrap font-weight-bold pa-0 ma-0 mb-4"
                                    cols="12">
                                    <div>
                                        Документы и файлы
                                    </div>
                                    <div>
                                        <template v-if="!fileList || fileList.length < 5">
                                            <v-btn text outlined color="success" @click="addFile">
                                                <v-icon>
                                                    mdi-plus
                                                </v-icon>
                                            </v-btn>
                                        </template>
                                    </div>
                                    <input type="file" ref="file" hidden @change="uploadFile($event)"
                                           accept="application/pdf"/>
                                </v-col>
                                <template v-for="(file, fIdx) in fileList">
                                    <v-card class="d-flex justify-center align-center" width="100%">
                                        <v-card-text style="width: 100%">
                                            <v-col class="d-flex justify-start align-center pa-0 ma-0 py-4" cols="12">
                                                <v-col class="d-flex justify-center align-center pa-0 ma-0 fill-height"
                                                       cols="2">
                                                    <a :href="file.file" target="_blank"
                                                       class="d-flex justify-center align-center text-decoration-none">
                                                        открыть файл
                                                    </a>
                                                </v-col>
                                                <v-col class="d-flex justify-start align-start pa-0 ma-0 px-4" cols="9">
                                                    <v-text-field
                                                        v-model="file.title"
                                                        outlined
                                                        hide-details
                                                        label="Название ссылки"
                                                    />
                                                </v-col>
                                                <v-col class="d-flex justify-end align-start pa-0 ma-0 px-4" cols="1">
                                                    <v-btn tile text @click="removeFile(file.file)">
                                                        <v-icon>
                                                            mdi-delete
                                                        </v-icon>
                                                    </v-btn>
                                                </v-col>
                                            </v-col>
                                        </v-card-text>
                                    </v-card>
                                </template>
                            </v-col>
                        </v-tab-item>
                        <v-tab-item class="pa-2">
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-text-field
                                    label="Стоимость (рубли)"
                                    v-model="product.price"
                                    step=".01"
                                    outlined
                                    :error="errors && Boolean(errors.title)"
                                    :error-messages="errors.title"
                                    hide-spin-buttons
                                    type="number"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-checkbox
                                    label="Показать цену на сайте"
                                    v-model="product.showPrice"
                                    :value="1"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-checkbox
                                    label="Под заказ"
                                    v-model="product.onOrder"
                                    :value="1"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-text-field
                                    label="Стоимость (доллары)"
                                    v-model="product.priceUsd"
                                    step=".01"
                                    outlined
                                    :error="errors && Boolean(errors.title)"
                                    :error-messages="errors.title"
                                    hide-spin-buttons
                                    type="number"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-text-field
                                    label="Стоимость (евро)"
                                    v-model="product.priceEur"
                                    step=".01"
                                    outlined
                                    :error="errors && Boolean(errors.title)"
                                    :error-messages="errors.title"
                                    hide-spin-buttons
                                    type="number"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-checkbox
                                    label="Обновлять цену по курсу доллара"
                                    v-model="product.updateUsd"
                                    :value="1"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-checkbox
                                    label="Обновлять цену по курсу евро"
                                    v-model="product.updateEur"
                                    :value="1"
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-text-field
                                    label="Ссылка синхронизации с comet-a.ru"
                                    v-model="product.syncCometa"
                                    outlined
                                />
                            </v-col>
                        </v-tab-item>
                        <v-tab-item class="pa-2">
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-text-field
                                    label="title postfix"
                                    v-model="product.seoTitle"
                                    outlined
                                    :error="errors && Boolean(errors.title)"
                                    :error-messages="errors.title"
                                    counter
                                    hide-spin-buttons
                                />
                            </v-col>
                            <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                                <v-textarea
                                    label="description"
                                    v-model="product.seoDescription"
                                    outlined
                                    counter
                                    :error="errors && Boolean(errors.title)"
                                    :error-messages="errors.title"
                                    hide-spin-buttons
                                />
                            </v-col>
                        </v-tab-item>
                    </v-tabs-items>
                </v-col>
            </v-col>
            <v-col class="d-flex justify-start align-start flex-wrap" cols="2">
                <v-btn width="100%" class="mb-4" elevation="0" color="success" @click="trySave(false)"
                       :disabled="!changed">
                    Сохранить
                </v-btn>
                <v-btn width="100%" class="mb-4" elevation="0" color="success" @click="trySave(true)"
                       :disabled="!changed">
                    Сохранить и выйти
                </v-btn>
                <v-btn width="100%" class="mb-4" elevation="0" color="error" @click="deleteItem">
                    Удалить
                </v-btn>
                <v-btn width="100%" class="mb-4" elevation="0" color="error" outlined
                       to="/admin/catalog/products">
                    Назад
                </v-btn>
            </v-col>
        </v-col>
        <v-col class="d-flex align-end flex-column" style="position: absolute; right: 0; bottom: 0;"
               v-if="alerts.length > 0">
            <template v-for="(alert, aDx) in alerts">
                <v-alert border="left" :color="alert.type == 'error'? 'red' : 'green'" dark width="250px" :key="aDx">
                    {{ alert.text }}
                </v-alert>
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
        properties: {
            required: true,
            type: Array,
            default: () => [],
        },
        sections: {
            required: true,
            type: Array,
            default: () => [],
        },
        secondIds: {
            required: true,
            type: Array,
            default: () => [],
        },
        thirdIds: {
            required: true,
            type: Array,
            default: () => [],
        },
        images: {
            required: false,
            type: Array,
            default: () => [],
        },
        files: {
            required: false,
            type: Array,
            default: () => [],
        },
        vendors: {
            required: false,
            type: Array,
            default: () => [],
        },
        errors: {
            required: false,
            type: Object,
            default: () => {
            },
        }
    },

    data: () => ({
        tab: null,
        product: {},
        sectionsList: [],
        propertyList: [],
        imageList: [],
        fileList: [],
        alerts: [],
        popAlerts: null,
        uploadImage: false,
        uploadingFile: false,
        secondIdsList: [],
        thirdIdsList: [],
    }),

    mounted() {
        this.product = JSON.parse(JSON.stringify(this.item))
        this.sectionsList = JSON.parse(JSON.stringify(this.sections))
        this.imageList = JSON.parse(JSON.stringify(this.images))
        this.fileList = JSON.parse(JSON.stringify(this.files))
        this.secondIdsList = JSON.parse(JSON.stringify(this.secondIds))
        this.thirdIdsList = JSON.parse(JSON.stringify(this.thirdIds))
        this.propertyList = JSON.parse(JSON.stringify(this.properties))
    },

    computed: {
        subSections() {
            const sections = JSON.parse(JSON.stringify(this.sectionsList))

            if (this.product.sectionId) {
                return sections.filter((item) => item.sectionId === this.product.sectionId)
            } else {
                return sections
            }
        },
        subSubSections() {
            const sections = JSON.parse(JSON.stringify(this.sectionsList))

            if (this.product.sectionId) {
                return sections.filter((item) => this.secondIdsList.includes(item.sectionId))
            } else {
                return []
            }
        },
        rootSections() {
            return this.sectionsList.filter((item) => !item.sectionId)
        },
        currentSection() {
            return this.sectionsList.find((item) => item.id === this.product.sectionId)
        },
        itemCurrentSection() {
            return this.sectionsList.find((item) => item.id === this.item.sectionId)
        },
        changed() {
            return JSON.stringify(this.item) !== JSON.stringify(this.product) ||
                JSON.stringify(this.imageList) !== JSON.stringify(this.images) ||
                JSON.stringify(this.fileList) !== JSON.stringify(this.files) ||
                JSON.stringify(this.secondIdsList) !== JSON.stringify(this.secondIds) ||
                JSON.stringify(this.thirdIdsList) !== JSON.stringify(this.thirdIds) ||
                JSON.stringify(this.propertyList) !== JSON.stringify(this.properties)
        },
        sectionError() {
            return (Boolean(this.currentSection) && Boolean(this.currentSection.sectionId)) || !Boolean(this.currentSection)
        },
    },

    methods: {
        deleteItem() {
            this.$inertia.delete(window.location.pathname);
        },
        trySave(exit) {
            let queryParams = new URLSearchParams(window.location.search)

            if (exit) {
                queryParams.append('e', '1')
            }

            this.$inertia.post(`${window.location.pathname}?${queryParams}`, {
                product: this.product,
                images: this.imageList,
                files: this.fileList,
                sections: [...this.secondIdsList, ...this.thirdIdsList],
                properties: this.propertyList,
            })
        },
        uploadPhoto(e) {
            this.uploadImage = true
            const file = e.target.files[0]
            this.removeImage()
            axios.post('/admin/upload/image', {image: file}, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
                .then(({data}) => {
                    this.product.imageId = data.image.id
                    this.product.image = data.image.path
                    this.alerts.push({text: 'Файл загружен на сервер', type: 'success'})
                })
                .catch((error) => {
                    this.alerts.push({text: 'Ошибка загрузки файла', type: 'error'})
                })
                .finally(() => {
                    this.clearAlerts()
                    this.uploadImage = false
                })
        },
        uploadGallery(e) {
            if (e.target.files) {
                this.uploadImage = true
                const file = e.target.files[0]
                axios.post('/admin/upload/image', {image: file}, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
                    .then(({data}) => {
                        this.imageList.push({
                            id: data.image.id,
                            image: data.image.path,
                        })
                        this.alerts.push({text: 'Файл загружен на сервер', type: 'success'})
                    })
                    .catch((error) => {
                        this.alerts.push({text: 'Ошибка загрузки файла', type: 'error'})
                    })
                    .finally(() => {
                        this.clearAlerts()
                        this.uploadImage = false
                    })

                this.$refs.gallery.value = ''
            }
        },
        uploadFile(e) {
            if (e.target.files) {
                this.uploadingFile = true
                const file = e.target.files[0]
                axios.post('/admin/upload/file', {file: file}, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
                    .then(({data}) => {
                        this.fileList.push({
                            id: data.file.id,
                            file: data.file.path,
                            title: data.file.title,
                        })
                        this.alerts.push({text: 'Файл загружен на сервер', type: 'success'})
                    })
                    .catch((error) => {
                        this.alerts.push({text: 'Ошибка загрузки файла', type: 'error'})
                    })
                    .finally(() => {
                        this.clearAlerts()
                        this.uploadingFile = false
                    })

                this.$refs.file.value = ''
            }
        },
        removeImage() {
            this.product.imageId = null
            this.product.image = null
        },
        removeGallery(path) {
            this.imageList = this.imageList.filter((item) => item.image !== path)
        },
        removeFile(path) {
            this.fileList = this.fileList.filter((item) => item.file !== path)
        },
        addImage() {
            this.$refs.upload.click()
        },
        addGallery() {
            this.$refs.gallery.click()
        },
        addFile() {
            this.$refs.file.click()
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
        },
    },

    watch: {
        currentSection(newVal, oldVal) {
            if (newVal && oldVal) {
                this.secondIdsList = []
                this.thirdIdsList = []
            }
        },
    },
}
</script>

<style scoped>

</style>
