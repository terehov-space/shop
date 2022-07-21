<template>
    <v-app app>
        <v-navigation-drawer app permanent>
            <v-col cols="12">
                <v-list>
                    <v-list-item disabled>
                        <v-list-item-title>
                            Каталог
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/catalog/sections">
                        <v-list-item-title>
                            Разделы
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/catalog/products">
                        <v-list-item-title>
                            Товары
                        </v-list-item-title>
                    </v-list-item>
                    <v-divider/>
                    <v-list-item disabled>
                        <v-list-item-title>
                            Заказы и формы
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/orders">
                        <v-list-item-title>
                            Заказы
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/forms">
                        <v-list-item-title>
                            Обратная связь
                        </v-list-item-title>
                    </v-list-item>
                    <v-divider/>
                    <v-list-item disabled>
                        <v-list-item-title>
                            Контент
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/content/vendors">
                        <v-list-item-title>
                            Производители
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/content/digitals">
                        <v-list-item-title>
                            Электронные каталоги
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/content/pages">
                        <v-list-item-title>
                            Страницы
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/content/carousels">
                        <v-list-item-title>
                            Слайдер
                        </v-list-item-title>
                    </v-list-item>
                    <v-divider/>
                    <v-list-item disabled>
                        <v-list-item-title>
                            Настройки
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/admin/settings">
                        <v-list-item-title>
                            Настройки сайта
                        </v-list-item-title>
                    </v-list-item>
                    <v-divider/>
                    <v-list-item>
                        <v-list-item-title>
                            Выйти
                        </v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-col>
        </v-navigation-drawer>
        <v-main>
            <v-container class="d-flex justify-center align-start pa-0 ma-0" fluid>
                <v-col class="d-flex justify-start align-start" cols="12">
                    <slot/>
                </v-col>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
export default {

}
</script>
