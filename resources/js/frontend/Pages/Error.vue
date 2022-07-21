<template>
    <div>
        <H1>{{ title }}</H1>
        <div>{{ description }}</div>
        <div>
            <inertia-link href="/">Вернуться на главную</inertia-link>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        status: Number,
    },
    computed: {
        title() {
            return {
                500: '500: Ошибка на сервере',
                404: '404: Страница не найдена',
            }[this.status]
        },
        description() {
            return {
                500: 'Упс, что-то пошло не так.',
                404: 'Кажется у нас нет такой страницы.',
            }[this.status]
        },
    },
}
</script>
