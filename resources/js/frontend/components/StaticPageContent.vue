<template>
    <v-col class="d-flex justify-center align-start no--gutters middle-margin" cols="10" md="12" v-html="content">
    </v-col>
</template>

<script>
export default {
    name: "StaticPageContent",

    props: {
        item: {
            type: Object,
            required: true,
            default: {},
        }
    },

    computed: {
        content() {
            return `<div>${this.item.body}</div>`
        },
    },
}
</script>

<style lang="scss" scoped>

</style>
