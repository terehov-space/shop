<template>
    <div>
        <inertia-head :title="title"/>
        <h1>{{ title }}</h1>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            required: true,
            type: String,
        },
    },
}
</script>
