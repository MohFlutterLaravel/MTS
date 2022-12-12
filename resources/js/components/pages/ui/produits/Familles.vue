<template>

    <div class="d-flex justify-content-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Familles</li>
            </ol>
        </nav>
    </div>

    <section class="sec">
        <div>
            <div class="d-flex justify-content-between mb-4">
                <h3>List des familles</h3>
                <button @click="openNew" class="btn btn-primary"><i class="fas fa-plus"></i></button>
            </div>
            <table class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:9%">ID</th>
                        <th style="width:15%">Designation</th>
                        <th style="width:30%">Description</th>
                        <th style="width:15%">Utilisateur</th>
                        <th style="width:16%">Action</th>
                        <th style="width:15%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="famille in familles">
                        <td>{{ famille.id }}</td>
                        <td>{{ famille.designation }}</td>
                        <td>{{ famille.description }}</td>
                        <td>{{ famille.user.name }}</td>
                        <td>{{ new Date(famille.created_at).toLocaleString('en-GB') }}</td>
                        <td>
                            <div style="margin-bottom: 4px;">
                                <button @click="confirmUpdateFamille(famille)" class="btn btn-primary btn-block">
                                    <i class="far fa-edit"></i>
                                </button>
                            </div>
                            <div>
                                <button @click="deleteFamille(famille.id)" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="spinner">
                        <td colspan="8" class="text-center">
                            <div class="p-4">
                                <ProgressSpinner />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th style="width:9%">ID</th>
                        <th style="width:15%">Designation</th>
                        <th style="width:30%">Description</th>
                        <th style="width:15%">Utilisateur</th>
                        <th style="width:16%">Action</th>
                        <th style="width:15%">Date</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <!-- Dialog create open -->
    <Dialog v-model:visible="createDialog" :style="{ width: '450px' }" header="Famille Details" :modal="true"
        class="p-fluid">
        <form @submit="store" enctype="multipart/form-data">
            <div class="field">
                <label for="designationFr">Designation</label>
                <input class="form-control" id="designationFr" name="designationFr" v-model.trim="form.designation"
                    type="text" required>
            </div>
            <div class="field input-group mt-4">
                <span class="input-group-text">Description</span>
                <textarea v-model.trim="form.description" class="form-control" aria-label="With textarea"></textarea>
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Ajouter" type="submit" class="btn btn-primary">
            </div>
        </form>

    </Dialog>
    <!-- Dialog create close -->

    <!-- Dialog update open -->
    <Dialog v-model:visible="UpdateDialog" :style="{ width: '450px' }" header="Famille Details" :modal="true"
        class="p-fluid">
        <form @submit="update" enctype="multipart/form-data">
            <div class="field">
                <label for="designationFr">Designation</label>
                <input class="form-control" id="designationFr" name="designationFr" v-model.trim="famille.designation"
                    type="text" required>
            </div>
            <div class="field input-group mt-4">
                <span class="input-group-text">Description</span>
                <textarea v-model.trim="famille.description" class="form-control" aria-label="With textarea"></textarea>
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Modifier" type="submit" class="btn btn-primary">
            </div>
        </form>

    </Dialog>
    <!-- Dialog update close -->

    <!-- loading overlay open -->
    <loading v-model:active="loading" :can-cancel="true" :is-full-page="true" :background-color="'#001eff38'"
        :color="'#001eff'" :loader="'dots'" />

    <!-- loading overlay close -->
</template>

<script>
import Dialog from 'primevue/dialog';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import ProgressSpinner from 'primevue/progressspinner';
export default {
    components: {
        Dialog,
        Loading,
        ProgressSpinner,
    },
    data() {
        return {
            createDialog: false,
            UpdateDialog: false,
            form: {
                designation: null,
                description: null,
                user_id: null,
            },
            familles: [],
            famille: null,
            pagination: {},
            loading: true,
            spinner: true,
        }
    },
    created() {
        if (this.$store.state.token != '') {
            let token = this.$store.state.token
            fetch('api/employee/check', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                }
            }).then(res => {
                if (res.ok) {
                    this.form.user_id = this.$store.state.id
                    console.log('token not expired yet!');
                } else {
                    console.log('token expired ');
                    this.$store.commit('clearToken');
                    Notification.session_expired();
                    this.$router.push({ name: 'login' })
                }
            }).catch(error => {
                console.log(error);
            })
        } else {
            Notification.session_expired();
            this.$router.push({ name: 'login' })
        }
    },
    mounted() {
        this.getResults();
    },
    methods: {
        getResults(page) {
            this.spinner = true
            page = page || 'api/employee/categories'
            var self = this
            let token = this.$store.state.token

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.get(page, { headers: headers, })
                .then(response => {
                    let res = response.data[0]
                    self.familles = res.data;
                    console.log(res);
                    self.pagination = {
                        current_page: res.current_page,
                        last_page: res.last_page,
                        from_page: res.from,
                        to_page: res.to,
                        total_page: res.total,
                        path_page: res.path + "?page=",
                        first_link: res.first_page_url,
                        last_link: res.last_page_url,
                        prev_link: res.prev_page_url,
                        next_link: res.next_page_url,
                    }
                    this.spinner = false;
                    this.loading = false;
                }).catch(err => {
                    if (err.response) {
                        if (err.response.status == 403) {
                            this.$router.push({ name: '403' })
                        }
                    }
                });

        },
        openNew() {
            this.createDialog = true;
        },
        store(e) {
            e.preventDefault();
            this.spinner = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/categories', this.form, {
                headers: headers,
            })
                .then(res => {
                    self.form.designation = null;
                    self.form.description = null;
                    self.createDialog = false;
                    Notification.success();
                    self.getResults();
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })

        },
        update(e) {
            e.preventDefault();
            this.loading = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/categories/' + self.famille.id + '?_method=PUT',
                {
                    designation: self.famille.designation,
                    description: self.famille.description,
                    user_id: self.form.user_id
                },
                { headers: headers, })
                .then(function (res) {
                    self.UpdateDialog = false;
                    Notification.success();
                    self.getResults();
                }).catch(function (error) {
                    console.log(error);
                })
        },
        deleteFamille(id){
            var self = this
            let token = this.$store.state.token
            const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
            }
            Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Vous ne pourrez pas revenir en arrière !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
            if (result.isConfirmed) {
                this.loading = true
                axios.delete('/api/employee/categories/'+id+'' ,{headers: headers,}
                ).then(res=>{
                  this.loading = false
                  if (res.data.success) {
                    Notification.success();
                    self.getResults();
                    Swal.fire(
                    'Supprimé!',
                    'La famille a été supprimé.',
                    'success'
                    )
                  }else{
                    Notification.error();
                    Swal.fire(
                    'Attention !',
                    'Ce famille contient des produits online.',
                    'error'
                    )
                }
                })
                .catch(err=>{
                  console.log(err);
                })
            }
            })
        },
        confirmUpdateFamille(famille) {
            this.famille = famille
            this.UpdateDialog = true
        }
    },

}
</script>

<style>

</style>