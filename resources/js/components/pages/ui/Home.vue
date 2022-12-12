<template>
    <h1>Admin Panel</h1>
    </template>
    
<script>
export default{
    created(){
    if (this.$store.state.token != '') {
        let token = this.$store.state.token
        fetch('api/employee/check', {
          method: 'POST',
          headers:{
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
          }
        }).then(res =>{
         if (res.ok) {
            console.log('token not expired yet!');
          }else{
             console.log('token expired ');
           this.$store.commit('clearToken');
           Notification.session_expired();
           this.$router.push({name: 'login'})
          }
        }).catch(error =>{
           console.log(error);
        })
      }else{ 
        Notification.session_expired();
        this.$router.push({name: 'login'})
        }
  },
}
</script>
    
<style>
</style>