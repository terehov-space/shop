<template>
    <v-app>
        <v-navigation-drawer v-model="navigation" class="drawer-wrapper pa-0 ma-0" app>
            <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0" cols="11">
                <v-col v-if="!search" class="d-flex justify-end align-center top_second_action_wrapper" cols="12">
                    <v-btn text icon @click="search = !search">
                        <v-icon>
                            mdi-magnify
                        </v-icon>
                    </v-btn>
                    <v-btn text icon to="/basket" @click.native="navigation = !navigation">
                        <v-badge
                            color="green"
                            overlap
                            v-model="basketHasProducts"
                        >
                            <v-icon>
                                mdi-cart-outline
                            </v-icon>
                        </v-badge>
                    </v-btn>
                </v-col>
                <v-col v-if="search" class="d-flex justify-end align-center top_second_action_wrapper" cols="12">
                    <v-text-field
                        outlined
                        hide-details
                        dense
                        placeholder="Поиск"
                        background-color="#f7f7f7"
                        append-icon="mdi-magnify"
                        class="search_field"
                        v-model="searchPhrase"
                        @keydown.enter="goSearch"
                    />
                    <v-btn text @click="goSearch">Найти</v-btn>

                    <v-btn text icon @click="search = !search">
                        <v-icon>
                            mdi-close
                        </v-icon>
                    </v-btn>
                </v-col>
                <v-col class="d-flex justify-start align-center navi-row" cols="12">
                    Каталог
                </v-col>
                <v-col class="d-flex justify-start align-center pa-0 ma-0" cols="12">
                    <v-divider/>
                </v-col>
                <v-col class="d-flex justify-start align-center flex-wrap pa-0 ma-0" cols="12">
                    <v-expansion-panels accordion flat tile>
                        <v-expansion-panel
                            v-for="(section, sIdx) in sectionLinks"
                            :key="sIdx"
                            class="pa-0 ma-0"
                        >
                            <v-expansion-panel-header>{{ section.title }}</v-expansion-panel-header>
                            <v-expansion-panel-content>
                                <v-list>
                                    <v-list-item :to="`/catalog/${section.code}`"
                                                 @click.native="navigation = !navigation">
                                        <v-list-item-title>{{ section.title }}</v-list-item-title>
                                    </v-list-item>
                                    <template v-for="(sub, ssIdx) in section.sections">
                                        <template v-if="sub.sections">
                                            <v-expansion-panels accordion flat tile>
                                                <v-expansion-panel class="pa-0 ma-0">
                                                    <v-expansion-panel-header>{{ sub.title }}</v-expansion-panel-header>
                                                    <v-expansion-panel-content>
                                                        <v-list>
                                                            <v-list-item :to="`/catalog/${sub.code}`"
                                                                         @click.native="navigation = !navigation">
                                                                <v-list-item-title>{{ sub.title }}</v-list-item-title>
                                                            </v-list-item>
                                                            <template v-for="(subSub, ssbIdx) in sub.sections">
                                                                <v-list-item :to="`/catalog/${subSub.code}`"
                                                                             @click.native="navigation = !navigation">
                                                                    <v-list-item-title>{{
                                                                            subSub.title
                                                                        }}
                                                                    </v-list-item-title>
                                                                </v-list-item>
                                                            </template>
                                                        </v-list>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>
                                            </v-expansion-panels>
                                        </template>
                                        <template v-else>
                                            <v-list-item :to="`/catalog/${sub.code}`"
                                                         @click.native="navigation = !navigation">
                                                <v-list-item-title>{{ sub.title }}</v-list-item-title>
                                            </v-list-item>
                                        </template>
                                    </template>
                                </v-list>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-col>
                <v-col class="d-flex justify-start align-center pa-0 ma-0" cols="12">
                    <v-divider/>
                </v-col>
                <v-col class="d-flex justify-start align-start flex-wrap pa-0 ma-0" cols="12" style="width: 100%">
                    <v-list>
                        <inertia-link href="/about" class="text-decoration-none" @click="navigation = !navigation">
                            <v-list-item>
                                <v-list-item-title>О Компании</v-list-item-title>
                            </v-list-item>
                        </inertia-link>
                        <inertia-link href="/digital" class="text-decoration-none" @click="navigation = !navigation">
                            <v-list-item>
                                <v-list-item-title>Электронные каталоги</v-list-item-title>
                            </v-list-item>
                        </inertia-link>
                        <inertia-link href="/contacts" class="text-decoration-none" @click="navigation = !navigation">
                            <v-list-item>
                                <v-list-item-title>Контакты</v-list-item-title>
                            </v-list-item>
                        </inertia-link>
                        <inertia-link href="/delivery" class="text-decoration-none" @click="navigation = !navigation">
                            <v-list-item>
                                <v-list-item-title>Доставка и оплата</v-list-item-title>
                            </v-list-item>
                        </inertia-link>
                        <v-expansion-panels tile flat style="width: 100%">
                            <v-expansion-panel>
                                <v-expansion-panel-header class="pa-5 ma-0">
                                    Услуги
                                </v-expansion-panel-header>
                                <v-expansion-panel-content class="pa-0 ma-0">
                                    <v-list links>
                                        <v-list-item to="/services/remont-oborudovaniya"
                                                     @click.native="navigation = !navigation" class="pa-0 ma-0">
                                            <v-list-item-title>Ремонт оборудования</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item to="/services/prokat-oborudovaniya"
                                                     @click.native="navigation = !navigation" class="pa-0 ma-0">
                                            <v-list-item-title>Прокат оборудования</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-list>
                </v-col>
                <v-col class="d-flex justify-start align-center pa-0 ma-0" cols="12">
                    <v-divider/>
                </v-col>
                <template v-if="settings">
                    <v-list>
                        <a :href="`tel:${settings.phone}`" class="text-decoration-none">
                            <v-list-item>
                                <v-list-item-title>{{ settings.phone }}</v-list-item-title>
                            </v-list-item>
                        </a>
                    </v-list>
                </template>
            </v-col>
            <v-col class="d-flex justify-end align-center top_action_wrapper pa-0 ma-0" cols="1">
                <v-btn text icon @click="navigation = !navigation">
                    <v-icon>
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-col>
        </v-navigation-drawer>
        <v-main class="d-flex justify-start align-start pa-0 ma-0">
            <v-col class="justify-center align-center pa-0 header_wrapper" cols="12">
                <v-col class="d-flex justify-start align-center content_wrapper pa-0 my-4" cols="12">
                    <v-col class="justify-start align-center mobile-menu" cols="3">
                        <v-btn text icon @click="navigation = !navigation">
                            <v-icon>
                                mdi-menu
                            </v-icon>
                        </v-btn>
                    </v-col>
                    <v-col class="d-flex align-center justify-center justify-md-start logo_wrapper" cols="6" md="6">
                        <inertia-link href="/"
                                      class="d-flex justify-center flex-column align-center text-decoration-none text-link_wrap">
                            <v-img src="/images/logo.svg" height="50px" width="50px" max-width="50px" contain/>
                            CarWashZone
                        </inertia-link>

                        <template v-if="settings">
                            <v-col class="d-none d-md-flex justify-start align-center" cols="6">
                                <a :href="`tel:${settings.phone}`" class="phone_link">{{ settings.phone }}</a>
                                <a title="Whatsapp" :href="`whatsapp://send?phone=${settings.whatsPhone}`"
                                   target="_blank"
                                   class="whats_link ml-4">
                                    <v-img src="/images/whats.svg" max-height="25px" max-width="25px" contain/>
                                </a>
                            </v-col>
                        </template>
                    </v-col>
                    <v-col class="d-flex align-center justify-end" cols="3" md="6">
                        <transition name="slide-fade">
                            <v-text-field
                                outlined
                                hide-details
                                dense
                                placeholder="Поиск"
                                background-color="#f7f7f7"
                                class="search_field"
                                v-model="searchPhrase"
                                @keydown.enter="goSearch"
                                clearable
                                v-if="search"
                            />
                        </transition>
                        <v-btn text icon @click="goSearch" class="d-none d-md-flex">
                            <v-icon>mdi-magnify</v-icon>
                        </v-btn>
                        <v-btn text icon to="/basket">
                            <v-badge
                                color="green"
                                overlap
                                v-model="basketHasProducts"
                            >
                                <v-icon>
                                    mdi-cart-outline
                                </v-icon>
                            </v-badge>
                        </v-btn>
                        <v-btn text icon class="d-md-none d-block"
                               @click="navigation = !navigation; $nextTick(() => {search = !search})">
                            <v-icon>
                                mdi-magnify
                            </v-icon>
                        </v-btn>
                    </v-col>
                </v-col>
            </v-col>
            <v-col class="d-flex justify-center align-center pa-0 ma-0 mobile_phone_wrapper" cols="12">
                <template v-if="settings">
                    <v-col class="d-flex d-md-none justify-center align-center" cols="6">
                        <a :href="`tel:${settings.phone}`" class="phone_link">{{ settings.phone }}</a>
                    </v-col>
                </template>
            </v-col>
            <v-col class="justify-center align-center pa-0 ma-0 menu_wrapper" cols="12">
                <v-col class="d-flex justify-start align-center py-0 content_wrapper" cols="12">
                    <div
                        class="pa-0 ma-0 dropdown_wrapper"
                        @mouseover="showCatalog = true"
                        @mouseleave="showCatalog = false"
                    >
                        <v-btn to="/catalog" class="menu_link" text>
                            <v-icon color="#ffffff">
                                mdi-menu
                            </v-icon>
                            Каталог
                        </v-btn>
                        <transition name="fade">
                            <div
                                class="main-nav__dropdown-menu"
                                v-if="showCatalog"
                            >
                                <div
                                    v-for="link in sectionLinks"
                                    :key="link.code"
                                    class="pa-0 ma-0 dropdown_wrapper"
                                    @mouseover="showSubCatalog = link.code"
                                    @mouseleave="showSubCatalog = null"
                                >
                                    <inertia-link
                                        class="main-nav__dropdown-link"
                                        :href="`/catalog/${link.code}`"
                                        :title="link.title"
                                    >
                                        {{ link.title }}
                                    </inertia-link>
                                    <transition name="fade">
                                        <div
                                            class="main-nav__dropdown-sub_menu"
                                            v-if="showSubCatalog == link.code"
                                        >
                                            <div
                                                v-for="subLink in link.sections"
                                                :key="subLink.code"
                                                @mouseover="showSubSubCatalog = subLink.code"
                                                @mouseleave="showSubSubCatalog = null"
                                            >
                                                <inertia-link
                                                    class="main-nav__dropdown-link"
                                                    :href="`/catalog/${subLink.code}`"
                                                    :title="subLink.title"
                                                >
                                                    {{ subLink.title }}
                                                </inertia-link>
                                                <transition name="fade">
                                                    <div
                                                        class="main-nav__dropdown-sub_sub_menu"
                                                        v-if="showSubSubCatalog == subLink.code"
                                                    >
                                                        <div
                                                            v-for="subSubLink in subLink.sections"
                                                            :key="subSubLink.code"
                                                        >
                                                            <inertia-link
                                                                class="main-nav__dropdown-link"
                                                                :href="`/catalog/${subSubLink.code}`"
                                                                :title="subSubLink.title"
                                                            >
                                                                {{ subSubLink.title }}
                                                            </inertia-link>
                                                        </div>
                                                    </div>
                                                </transition>
                                            </div>
                                        </div>
                                    </transition>
                                </div>
                            </div>
                        </transition>
                    </div>
                    <inertia-link href="/about" class="menu_link">О компании</inertia-link>
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <a
                                class="text-decoration-none mr-6"
                                style="color: #FFFFFF"
                                v-bind="attrs"
                                v-on="on"
                            >
                                Услуги
                            </a>
                        </template>
                        <v-list links>
                            <v-list-item to="/services/remont-oborudovaniya">
                                <v-list-item-title>Ремонт оборудования</v-list-item-title>
                            </v-list-item>
                            <v-list-item to="/services/prokat-oborudovaniya">
                                <v-list-item-title>Прокат оборудования</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <inertia-link href="/digital" class="menu_link">Электронные каталоги</inertia-link>
                    <inertia-link href="/contacts" class="menu_link">Контакты</inertia-link>
                    <inertia-link href="/delivery" class="menu_link">Доставка и оплата</inertia-link>
                </v-col>
            </v-col>
            <v-container class="d-flex justify-center align-start pa-0 ma-0" fluid>
                <v-col class="d-flex justify-start align-start content_wrapper" cols="12">
                    <slot />
                </v-col>
                <v-dialog v-model="showDialog" width="350px" style="z-index: 1500;">
                    <v-card width="350px">
                        <v-card-title>Запросить стоимость</v-card-title>
                        <v-divider v-if="product"/>
                        <v-card-text class="py-4 dialog_product_title" v-if="product">
                            {{ product.title }}
                        </v-card-text>
                        <v-divider/>
                        <v-card-text class="py-4">
                            <v-form
                                ref="form"
                                lazy-validation
                            >
                                <v-text-field
                                    outlined
                                    dense
                                    placeholder="Имя/Название компании"
                                    v-model="formData.name"
                                />
                                <v-text-field
                                    outlined
                                    dense
                                    placeholder="Телефон"
                                    v-model="formData.phone"
                                />
                                <v-text-field
                                    outlined
                                    dense
                                    placeholder="Почта"
                                    v-model="formData.email"
                                />
                                <v-textarea
                                    placeholder="Ваш вопрос"
                                    v-model="formData.comment"
                                    outlined
                                />
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn color="success" @click="sendForm">
                                Отправить
                            </v-btn>
                            <v-btn color="error" @click="$store.commit('RESET_PRODUCT')">
                                Отмена
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-container>
        </v-main>
        <v-footer class="d-flex justify-center align-start footer_wrapper" color="#173211">
            <v-col class="d-flex justify-start align-start content_wrapper flex-wrap" cols="12">
                <v-col class="d-flex align-center justify-center justify-md-start logo_wrapper" cols="12" md="2">
                    <inertia-link href="/"
                                  class="d-flex justify-center flex-column align-center text-decoration-none text-link_wrap">
                        <v-img src="/images/logo.svg" height="50px" width="50px" max-width="50px" contain/>
                        CarWashZone
                    </inertia-link>
                </v-col>
                <v-col class="d-flex align-start justify-start flex-column links_wrapper" cols="12" md="4">
                    <inertia-link href="/about" class="menu_link">О компании</inertia-link>
                    <inertia-link href="/services/remont-oborudovaniya" class="menu_link">Ремонт оборудования</inertia-link>
                    <inertia-link href="/services/prokat-oborudovaniya" class="menu_link">Прокат оборудования</inertia-link>
                    <inertia-link href="/digital" class="menu_link">Электронные каталоги</inertia-link>
                    <inertia-link href="/contacts" class="menu_link">Контакты</inertia-link>
                    <inertia-link href="/delivery" class="menu_link">Доставка и оплата</inertia-link>
                </v-col>
                <v-col class="d-flex align-start justify-start flex-column links_wrapper" cols="12" md="4">
                    <inertia-link href="/catalog" class="menu_link">Каталог</inertia-link>
                    <template v-for="section in sectionLinks">
                        <inertia-link :href="`/catalog/${section.code}`" class="menu_link" :key="section.id">{{ section.title }}</inertia-link>
                    </template>
                </v-col>
            </v-col>
        </v-footer>
    </v-app>
</template>

<script>
import {InertiaProgress} from '@inertiajs/progress'
import axios from 'axios'

InertiaProgress.init()

export default {
    props: {
        sectionLinks: {
            type: Array,
            required: true,
            default: () => [],
        },
        settings: {
            type: Object,
            required: true,
            default: () => {
            },
        },
        basket: {
            required: true,
            default: () => {
            },
        },
    },

    data: () => ({
        showCatalog: false,
        showSubCatalog: null,
        showSubSubCatalog: null,
        navigation: false,
        search: false,
        searchPhrase: '',
        sections: [],

        formData: {
            productId: null,
            phone: null,
            email: null,
            name: null,
            comment: null,
        },

        formDataClean: {
            productId: null,
            phone: null,
            email: null,
            name: null,
            comment: null,
        },
    }),

    computed: {
        basketHasProducts() {
            return this.basket && this.basket.products && this.basket.products.length
        },
        showDialog: {
            get() {
                return this.$store.state.showDialog
            },
            set() {
                this.$store.commit('RESET_PRODUCT')
            },
        },
        product() {
            return this.$store.state.product
        },
    },

    methods: {
        async sendForm() {
            await axios.post('/form', this.formData)
                .then(({data}) => {
                    this.$store.commit('RESET_PRODUCT')
                })
        },
        goSearch() {
            if (this.searchPhrase && this.searchPhrase.length > 2) {
                window.location.href = `/search?q=${this.searchPhrase}`
                this.searchPhrase = null
            } else if (!this.searchPhrase) {
                this.search = !this.search;
            }
        },
    },

    watch: {
        product(newVal) {
            if (newVal) {
                this.formData.productId = newVal.id
            } else {
                this.formData = JSON.parse(JSON.stringify(this.formDataClean))
            }
        },
    },
}
</script>

<style lang="scss">
.mobile_phone_wrapper {
    .phone_link {
        text-decoration: none;
        color: #000000;
        font-size: 18px;
        font-weight: 600;
        line-height: 20px;
    }
}
.drawer-wrapper {
    z-index: 10;
    width: 100% !important;
    height: 100%;
    overflow: auto;

    .v-navigation-drawer__content {
        display: flex;
    }

    .top_action_wrapper {
        background-color: #ededed;
        height: 75px;
    }

    .top_second_action_wrapper {
        background-color: #f7f7f7;
        height: 75px;
    }

    .navi-row {
        height: 60px;
    }

    .catalog_link_wrapper {
        width: 100%;
    }
}

@media only screen and (min-width: 769px) {
    .drawer-wrapper {
        display: none;
    }
}

.top_header {
    display: flex;
    height: 35px;
    background-color: #e9e9e9;
}

@media only screen and (max-width: 768px) {
    .top_header {
        display: none !important;
    }
}

.header_wrapper {
    display: flex;
    height: 75px;
    margin: 15px 0 !important;

    .mobile-menu {
        display: flex;
    }

    @media only screen and (min-width: 769px) {
        .mobile-menu {
            display: none !important;
        }
    }

    .logo_wrapper {
        justify-content: center;

        .text-link_wrap {
            color: #173211 !important;
        }

        .phone_link, .top_header-link {
            text-decoration: none;
            color: #000000;
            font-weight: 600;
            font-size: 16px;

            &:hover {
                opacity: .7;
            }
        }
    }

    @media only screen and (min-width: 769px) {
        .logo_wrapper {
            justify-content: flex-start !important;
        }
    }

    .search_wrapper {
        .search_field {
            * {
                border: none !important;
            }
        }
    }

    @media only screen and (max-width: 768px) {
        .search_wrapper {
            display: none !important;
        }
    }
}

.footer_wrapper {
    display: flex;
    color: #ffffff !important;

    .mobile-menu {
        display: flex;
    }

    @media only screen and (min-width: 769px) {
        .mobile-menu {
            display: none !important;
        }
    }

    .links_wrapper {
        .menu_link {
            color: #ffffff;
            text-decoration: none;
            margin-top: 10px;
            font-size: 18px;
            font-weight: 500;
            line-height: 20px;
        }
    }

    .logo_wrapper {
        justify-content: center;

        .text-link_wrap {
            color: #FFFFFF !important;
        }

        .phone_link, .top_header-link {
            text-decoration: none;
            color: #000000;
            font-weight: 600;
            font-size: 16px;

            &:hover {
                opacity: .7;
            }
        }
    }

    @media only screen and (min-width: 769px) {
        .logo_wrapper {
            justify-content: flex-start !important;
        }
    }

    .search_wrapper {
        .search_field {
            * {
                border: none !important;
            }
        }
    }

    @media only screen and (max-width: 768px) {
        .search_wrapper {
            display: none !important;
        }
    }
}

.slide-fade-enter-active {
    transition: all .2s ease;
}

.slide-fade-leave-active {
    transition: all .2s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}

.slide-fade-enter, .slide-fade-leave-to
    /* .slide-fade-leave-active до версии 2.1.8 */
{
    transform: translateX(10px);
    opacity: 0;
}

.menu_wrapper {
    display: flex;
    background-color: #173211;
    height: 50px;

    .menu_link {
        height: 100%;
        padding: 0;
        color: #FFFFFF;
        text-decoration: none;
        display: flex;
        align-content: center;
        justify-content: flex-start;
        margin-right: 25px;
        font-weight: 400;
        text-transform: none;
        font-size: 16px;
        line-height: 20px;
    }
}

@media only screen and (max-width: 768px) {
    .menu_wrapper {
        display: none !important;
    }
}

.content_wrapper {
    max-width: 1300px;
    width: 100%;
}

.dropdown_wrapper {
    z-index: 150;
    position: relative;
    font-size: 13px;
    font-weight: bold;
    cursor: pointer;

    .main-nav__link {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        height: 100%;

        &:hover {
            color: #ffffff;
        }
    }
}

.main-nav__icon {
    margin-right: 10px;
}

.main-nav__dropdown-menu {
    background-color: #ffffff;
    position: absolute;
    left: 0;
    top: 36px;
    box-shadow: 0 6px 12px rgb(0 0 0 / 18%);
    border-radius: 0 0 3px 3px;
    min-width: 250px;

    .main-nav__dropdown-link {
        text-decoration: none;
        padding: 14px 27px 13px 20px;
        text-transform: capitalize;
        font-size: 14px;
        color: #000000;
        display: block;
        max-width: 250px;
        width: 100%;
        font-weight: normal;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        &:hover {
            color: #000000;
            background-color: #fafafa;
        }
    }
}

.main-nav__dropdown-sub_menu {
    background-color: #ffffff;
    position: absolute;
    left: 250px;
    top: 0;
    box-shadow: 0 6px 12px rgb(0 0 0 / 18%);
    border-radius: 0 0 3px 3px;
    min-width: 250px;

    .main-nav__dropdown-link {
        text-decoration: none;
        padding: 14px 27px 13px 20px;
        text-transform: capitalize;
        font-size: 14px;
        color: #000000;
        display: block;
        max-width: 250px;
        width: 100%;
        font-weight: normal;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        &:hover {
            color: #000000;
            background-color: #fafafa;
        }
    }
}

.main-nav__dropdown-sub_sub_menu {
    background-color: #ffffff;
    position: absolute;
    left: 250px;
    top: 0;
    box-shadow: 0 6px 12px rgb(0 0 0 / 18%);
    border-radius: 0 0 3px 3px;
    min-width: 250px;

    .main-nav__dropdown-link {
        text-decoration: none;
        padding: 14px 27px 13px 20px;
        text-transform: capitalize;
        font-size: 14px;
        color: #000000;
        display: block;
        max-width: 250px;
        width: 100%;
        font-weight: normal;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        &:hover {
            color: #000000;
            background-color: #fafafa;
        }
    }
}

.fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
}

.fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */
{
    opacity: 0;
}

.dialog_product_title {
    color: #000000 !important;
    font-size: 18px;
    font-weight: 600;
}
</style>
