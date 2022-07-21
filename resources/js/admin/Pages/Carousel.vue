<template>
    <v-col cols="12">
        <template v-if="item.id == 0">
            <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
                <div>
                    Добавление слайда
                </div>
            </v-col>
        </template>
        <template v-else>
            <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
                <div>
                    Редактирование слайда
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
                    <v-autocomplete
                        label="Тип связки"
                        v-model="section.model"
                        :items="relations"
                        item-text="title"
                        item-value="value"
                        outlined
                        clearable
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-autocomplete
                        label="Связка"
                        v-model="section.modelId"
                        :items="selectableRelations"
                        item-text="title"
                        item-value="id"
                        outlined
                        clearable
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6 flex-wrap" cols="12">
                    <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                        Картинка для десктопа
                    </v-col>
                    <v-menu
                        absolute
                        offset-y
                        style="max-width: 600px"
                    >
                        <template v-slot:activator="{ on }">
                            <v-card class="d-flex justify-center align-center" v-on="on" height="250px" width="250px"
                                    :loading="uploadImage">
                                <template v-if="section.image && !uploadImage">
                                    <v-img :src="section.image" height="250px" width="250px"/>
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
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6 flex-wrap" cols="12">
                    <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                        Картинка для мобильной версии
                    </v-col>
                    <v-menu
                        absolute
                        offset-y
                        style="max-width: 600px"
                    >
                        <template v-slot:activator="{ on }">
                            <v-card class="d-flex justify-center align-center" v-on="on" height="250px" width="250px"
                                    :loading="uploadImage">
                                <template v-if="section.mobileImagePath && !uploadImage">
                                    <v-img :src="section.mobileImagePath" height="250px" width="250px"/>
                                </template>
                                <template v-else-if="!uploadImage">
                                    <v-icon>
                                        mdi-plus
                                    </v-icon>
                                </template>
                                <input type="file" ref="uploadMobile" hidden @change="uploadMobilePhoto($event)"
                                       accept="image/svg+xml,image/jpeg,image/jpeg,image/png"/>
                            </v-card>
                        </template>
                        <v-list>
                            <v-list-item @click="removeMobileImage">
                                <v-list-item-title>Удалить</v-list-item-title>
                            </v-list-item>
                            <v-list-item @click="addMobileImage">
                                <v-list-item-title>Загрузить</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
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
                <v-btn width="100%" class="mb-4" elevation="0" color="error">
                    Удалить
                </v-btn>
                <v-btn width="100%" class="mb-4" elevation="0" color="error" outlined
                       to="/admin/content/carousels">
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
        sections: {
            required: true,
            type: Array,
            default: () => [],
        },
        products: {
            required: true,
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
        section: {},
        sectionsList: [],
        alerts: [],
        popAlerts: null,
        uploadImage: false,
        relations: [
            {
                title: 'Разделы',
                value: 'App\\Models\\Section',
            },
            {
                title: 'Товары',
                value: 'App\\Models\\Product',
            },
        ],
    }),

    computed: {
        changed() {
            return JSON.stringify(this.item) !== JSON.stringify(this.section)
        },
        selectableRelations() {
            if (this.section.model == 'App\\Models\\Section') {
                return this.sections
            } else if (this.section.model == 'App\\Models\\Product') {
                return this.products
            } else {
                return []
            }
        },
        currentRelation() {
            return this.section.model
        },
    },

    mounted() {
        this.section = JSON.parse(JSON.stringify(this.item))
        this.sectionsList = JSON.parse(JSON.stringify(this.sections))
    },

    watch: {
        currentRelation(newVal, oldVal) {
            if (oldVal) {
                this.section.modelId = null
            }
        },
    },

    methods: {
        trySave(exit) {
            let queryParams = new URLSearchParams(window.location.search)

            if (exit) {
                queryParams.append('e', '1')
            }

            this.$inertia.post(`${window.location.pathname}?${queryParams}`, this.section)
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
                    this.section.imageId = data.image.id
                    this.section.image = data.image.path
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
        uploadMobilePhoto(e) {
            this.uploadImage = true
            const file = e.target.files[0]
            this.removeMobileImage()
            axios.post('/admin/upload/image', {image: file}, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
                .then(({data}) => {
                    this.section.mobileImage = data.image.id
                    this.section.mobileImagePath = data.image.path
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
        removeImage() {
            this.section.imageId = null
            this.section.image = null
        },
        removeMobileImage() {
            this.section.mobileImage = null
            this.section.mobileImagePath = null
        },
        addImage() {
            this.$refs.upload.click()
        },
        addMobileImage() {
            this.$refs.uploadMobile.click()
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
