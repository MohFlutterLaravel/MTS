<template>
    <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 id="mts" class="text-center font-weight-light my-4">Modern-Tech-Services</h3></div>
                                    <div class="card-body">
                                        <form @submit.prevent="login">
                                            <div class="form-floating mb-3">
                                                <input v-model="form.email" class="form-control" id="inputUser" type="text" placeholder="name@example.com" />
                                                <label for="inputUser">Username</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input v-model="form.password" class="form-control" id="inputPassword" type="password" placeholder="Password" />
                                                <label for="inputPassword">Password</label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" />
                                                <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <a class="small" href="#">Forgot Password?</a>
                                                <button class="btn btn-primary">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; MTS 2022</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
    </div>
</template>

<script>
export default{
    beforeCreate(){
      if (this.$store.state.token != '') {
        let token = this.$store.state.token;
        fetch('api/employee/check', {
          method: 'POST',
          headers:{
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
          }
        }).then(res =>{
          if (res.ok) {
            this.$router.push({name: 'dashboard'})
          }
         else{
           this.$store.commit('clearToken');
         }
        }).catch(error =>{
           console.log(error);
        })
        
      }
    },
  data(){
    return{
      form:{
      email: null,
      password: null,
      },
      errors:{}
    }
  },
  methods:{
    login(){
      axios.post('/api/login',this.form)
      .then(response =>{
        if (response.data.success) {
          this.$store.commit('setToken', response.data.access_token);
          this.$store.commit('setUser', response.data.name);
          this.$store.commit('setUserId', response.data.id);
          this.$store.commit('setExpireToken', response.data.expires_in);
          Sweet.fire({ 
          icon: 'success',
          title: 'Signed in successfully'
        })
          this.$router.push('/dashboard') 
        }
      })
      .catch(
        error =>{
          console.log(error);
           Sweet.fire({
              icon: 'warning',
              title: 'Invalid Email or Password'
            }) 
        }
       
        )
      
    },
  },
}
</script>

<style>
</style>