<template>
    <v-col cols="12">
        <v-col class="d-flex justify-space-between align-center font-weight-bold" cols="12">
            <div>
                Обработка заказа: #{{ section.id }}
            </div>
        </v-col>
        <v-col class="d-flex justify-start align-start ma-0 pa-0" cols="12">
            <v-col class="d-flex justify-start flex-wrap align-start" cols="10">
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="Идентификатор заказа для отслеживания"
                        v-model="section.code"
                        outlined
                        disabled
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-autocomplete
                        label="Статус"
                        v-model="section.status"
                        :items="statuses"
                        item-text="title"
                        item-value="val"
                        outlined
                        hide-details
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="Телефон"
                        v-model="section.phone"
                        outlined
                        hide-details
                        disabled
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6" cols="12">
                    <v-text-field
                        label="Email"
                        v-model="section.email"
                        outlined
                        hide-details
                        disabled
                    />
                </v-col>
                <v-col class="d-flex justify-start align-start pa-0 ma-0 mb-6 flex-wrap" cols="12">
                    <template v-for="item in items">
                        <v-card-text :key="item.id">
                            <v-col class="d-flex justify-start align-start ma-0 pa-0" cols="12">
                                <v-col cols="2">
                                    <v-card width="75px" height="75px">
                                        <v-img :src="item.image"
                                               width="75px" height="75px" contain/>
                                    </v-card>
                                </v-col>
                                <v-col class="d-flex flex-column" cols="6">
                                    <v-col class="pa-0 ma-0 mb-4 product_title" cols="12">{{
                                            item.title
                                        }}
                                    </v-col>
                                    <v-col class="pa-0 ma-0 mb-4 product_vendor_code" cols="12">
                                        {{ item.vendorCode }}
                                    </v-col>
                                </v-col>
                                <v-col class="d-flex flex-column align-end pr-0 mr-0" cols="4">
                                    <v-col class="pa-0 ma-0 mb-4 product_price" cols="12">{{ item.count}} x {{ item.price }} руб.
                                    </v-col>
                                </v-col>
                            </v-col>
                        </v-card-text>
                    </template>
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
                       to="/admin/orders">
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
        items: {
            required: true,
            type: Array,
            default: () => [],
        },
    },

    data: () => ({
        section: {},
        alerts: [],
        popAlerts: null,
        uploadImage: false,
        statuses: [
            {
                'title': 'Новый',
                'val': 'new',
            },
            {
                'title': 'В обработке',
                'val': 'process',
            },
            {
                'title': 'Готов',
                'val': 'ready',
            },
        ],
    }),

    computed: {
        changed() {
            return JSON.stringify(this.item) !== JSON.stringify(this.section)
        },
    },

    mounted() {
        this.section = JSON.parse(JSON.stringify(this.item))
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
        removeImage() {
            this.section.imageId = null
            this.section.image = null
        },
        addImage() {
            this.$refs.upload.click()
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
