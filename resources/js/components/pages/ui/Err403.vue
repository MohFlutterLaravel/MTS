<template>
    <div class="text-center">
        <h3 class="text-gray-800 font-weight-bold">Comming soon !</h3>
        <p class="lead text-gray-800 mx-auto">Cette page est en train de developmenet</p>
        <router-link :to="{name:'home'}" class="collapse-item">&larr; Back to Dashboard</router-link>
    </div>
</template>
<script>
export default{
    name: 'Permission',
}
</script>
<style>
</style>