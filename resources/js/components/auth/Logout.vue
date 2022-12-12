<template>
</template>

<script type="text/javascript">
export default {
   created(){
    this.$store.commit('clearToken');
    Notification.custom_notification("bye ;) ♥♥")
   this.$router.push({name: 'login'}) 
   },
 }
</script>

<style type="text/css">
</style>