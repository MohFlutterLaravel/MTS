<template>
    <div class="d-flex justify-content-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Clients</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Clients</h6>
            <div>
                <button @click="getResults('api/employee/clients')" type="button" class="btn btn-primary me-2"><i
                        class="fa-solid fa-arrows-rotate"></i></button>
                <button v-if="showFilterButton" @click="openDialogFilter" type="button" class="btn btn-dark me-2"><i
                        class="fas fa-filter"></i></button>
                <button v-if="showDetteButton" @click="openDialogDette" type="button" class="btn btn-warning me-2"><i
                        class="fa-solid fa-money-bill-wave"></i></button>
                <button v-if="showEditButton" type="button" class="btn btn-dark me-2" @click="editDialogSource"><i
                        class="fas fa-edit"></i></button>
                <button v-if="showDeleteButton" type="button" class="btn btn-danger me-2" @click="deleteClient"><i
                        class="fas fa-trash"></i></button>
                <button type="button" class="btn btn-primary" @click="openDialogSource"><i
                        class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-items-center table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Mobile</th>
                        <th>DETTE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr :id="'client' + client.id" @click="showAction(client)" style="cursor: pointer"
                        v-for="client in clients">

                        <td>{{ client.id }}</td>
                        <td><span class="badge bg-success">{{ client.name }}</span></td>
                        <td>{{ client.phone }}</td>
                        <td>{{ client.dette }}</td>
                    </tr>
                    <tr v-if="spinner">
                        <td colspan="8" class="text-center">
                            <div class="p-4">
                                <ProgressSpinner />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-md-8">
                    <nav>
                        <ul class="pagination">
                            <li v-bind:class="{ disabled: !pagination.first_link }" class="page-item"><a href="#"
                                    @click="getResults(pagination.first_link)" class="page-link">&laquo;</a></li>
                            <li v-bind:class="{ disabled: !pagination.prev_link }" class="page-item"><a href="#"
                                    @click="getResults(pagination.prev_link)" class="page-link">&lt;</a></li>
                            <li v-for="n in pagination.last_page" v-bind:key="n"
                                v-bind:class="{ active: pagination.current_page == n }" class="page-item"><a href="#"
                                    @click="getResults(pagination.path_page + n)" class="page-link">{{ n }}</a></li>
                            <li v-bind:class="{ disabled: !pagination.next_link }" class="page-item"><a href="#"
                                    @click="getResults(pagination.next_link)" class="page-link">&gt;</a></li>
                            <li v-bind:class="{ disabled: !pagination.last_link }" class="page-item"><a href="#"
                                    @click="getResults(pagination.last_link)" class="page-link">&raquo;</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-md-4">
                    Page: {{ pagination.from_page }} - {{ pagination.to_page }}
                    Total: {{ pagination.total_page }}
                </div>
            </div>
        </div>
    </div>

    <!-- Dialog create open -->
    <Dialog v-model:visible="createDialog" :style="{ width: '450px' }" header="Fournisseur formulaire" :modal="true"
        class="p-fluid">
        <form @submit="store" enctype="multipart/form-data">
            <div class="field">
                <label for="nameClient">Nom et Prenom</label>
                <input class="form-control" id="nameClient" v-model.trim="form.name" type="text" required>
            </div>
            <div class="field">
                <label for="mobile">Mobile</label>
                <input class="form-control" id="mobile" name="mobile" v-model.trim="form.phone" type="text">
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Ajouter" type="submit" class="btn btn-primary">
            </div>
        </form>

    </Dialog>
    <!-- Dialog create close -->

    <!-- Dialog edit open -->
    <Dialog v-model:visible="editSourceDialog" :style="{ width: '450px' }" header="Fournisseur formulaire" :modal="true"
        class="p-fluid">
        <form @submit="editSource" enctype="multipart/form-data">
            <div class="field">
                <label for="nameClient">Nom et Prenom</label>
                <input class="form-control" id="nameClient" v-model.trim="client.name" type="text" required>
            </div>
            <div class="field">
                <label for="mobile">Mobile</label>
                <input class="form-control" id="mobile" name="mobile" v-model.trim="client.phone" type="text">
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Editer" type="submit" class="btn btn-primary">
            </div>
        </form>

    </Dialog>
    <!-- Dialog edit close -->

    <!-- Dialog filter open -->
    <Dialog v-model:visible="filterDialog" :style="{ width: '600px' }" header="Filter" :modal="true" class="p-fluid">
        <div class="row">
            <div class="col-12">
                <div class="form-floating pb-4">
                    <v-select placeholder="Nom et prenom" v-model="form.client_id" :options="options"
                        :reduce="name => name.id" label="name"></v-select>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <button class="btn btn-dark btn-lg" @click="filter"
                style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: 1.5rem; --bs-btn-font-size: 1.25rem;">Filter</button>
        </div>
    </Dialog>
    <!-- Dialog filter close -->

    <!-- Dialog dette open -->
    <Dialog v-model:visible="detteClientDialog" :style="{ width: '450px' }" header="Pay Dette" :modal="true"
        class="p-fluid">
        <form @submit="payedette" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="field">
                        <label for="mantant">Mantant</label>
                        <input class="form-control" id="mantant" v-model.trim="mantant" type="number" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field">
                        <label for="observation">Observation</label>
                        <input class="form-control" id="observation" v-model.trim="observation" type="text">
                    </div>
                </div>
            </div>
            <div class="row d-flex justify-content-center mt-4">
                <div class="col-6">
                        <div class="form-floating">
                            <v-select placeholder="Caisse" v-model="caisse_id" :options="optionsCaisses"
                                :reduce="label => label.id" label="label"></v-select>
                        </div>
                </div>
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Pay" type="submit" class="btn btn-primary">
            </div>
        </form>

    </Dialog>
    <!-- Dialog dette close -->

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
            form: {
                user_id: null,
                name: null,
                phone: null,
                client_id: null,
            },
            mantant: null,
            observation: null,
            caisse_id: null,
            clients: [],
            client: null,
            options: [],
            optionsCaisses: [],
            clientOption: [],
            loading: false,
            spinner: true,
            pagination: {},
            createDialog: false,
            editSourceDialog: false,
            detteClientDialog: false,
            showDeleteButton: false,
            showEditButton: false,
            filterDialog: false,
            showFilterButton: true,
            showRefreshButton: false,
            showDetteButton: false
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

            // filter api/employee/clients-filter
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.get('api/employee/clients-filter', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.options.push({ 'id': response[index].id, 'name': response[index].name, })
                        // console.log(response[index].designation.fr);

                    }
                    console.log(this.options);
                })
                .catch(err => {
                    console.log(err);
                })
            // get caisses
            axios.get('api/employee/caisses-filter', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.optionsCaisses.push({ 'id': response[index].id, 'label': response[index].label, })
                        // console.log(response[index].designation.fr);

                    }
                })
                .catch(err => {
                    console.log(err);
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
        showAction(client) {
            this.showDeleteButton = true
            this.showDetteButton = true
            this.showEditButton = true
            let id = client.id
            this.client = client
            const elements = document.querySelectorAll('.client');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            var element = document.getElementById('client' + id);
            element.classList.add("client");
            element.classList.add("table-dark");
            element.classList.add("font-weight-bold");
        },
        getResults(page) {
            const elements = document.querySelectorAll('.client');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            this.mantant = null
            this.caisse_id = null
            this.showFilterButton = true
            this.spinner = true
            this.showDeleteButton = false
            this.showEditButton = false
            this.showDetteButton = false
            page = page || 'api/employee/clients'
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
                    self.clients = res.data;
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
        editDialogSource() {
            this.editSourceDialog = true
        },
        openDialogFilter() {
            this.filterDialog = true
        },
        openDialogSource() {
            this.createDialog = true
        },
        store(e) {
            e.preventDefault();
            this.spinner = true
            this.loading = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/clients', this.form, {
                headers: headers,
            })
                .then(res => {
                    self.form.name = null;
                    self.form.phone = null;
                    self.createDialog = false;
                    Notification.success();
                    self.getResults();
                    this.spinner = false
                    this.loading = false
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })
        },
        deleteClient() {
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
                    var self = this
                    let token = this.$store.state.token

                    const headers = {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                    axios.delete('api/employee/clients/' + self.client.id, { headers: headers })
                        .then(res => {
                            if (res.data.success) {
                                Notification.success();
                                self.getResults();
                                Swal.fire(
                                    'Supprimé!',
                                    'La famille a été supprimé.',
                                    'success'
                                )
                                this.spinner = false
                                this.loading = false
                            } else {
                                this.spinner = false
                                this.loading = false
                                Swal.fire(
                                    'Attention !',
                                    'Vous ne pouvez pas supprimer un client qui a un dette',
                                    'error'
                                )
                            }
                        })
                        .catch(err => {
                            console.log(err);
                        })
                }
            })
        },
        editSource(e) {
            e.preventDefault();
            this.loading = true
            var self = this
            let token = this.$store.state.token

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.put('api/employee/clients/' + self.client.id, self.client, { headers: headers })
                .then(res => {
                    Notification.success()
                    self.getResults();
                    this.editSourceDialog = false
                })
                .catch(err => {
                    console.log(err);
                })
        },
        filter() {
            this.showDeleteButton = false
            this.showEditButton = false
            this.clients.forEach(client => {
                if (client.id == this.form.client_id) {
                    this.clients = []
                    this.clients = [client]
                    this.filterDialog = false
                    this.form.client_id = null
                    this.showFilterButton = false
                    this.showRefreshButton = true
                }
            });
        },
        openDialogDette() {
            this.detteClientDialog = true
        },
        payedette(e) {
            e.preventDefault();
            this.loading = true
            var self = this
            let token = this.$store.state.token

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('api/employee/payedettes',
                {
                    'user_id': self.form.user_id,
                    'caisse_id': self.caisse_id,
                    'client_id': self.client.id,
                    'mantant': self.mantant,
                    'observation': self.observation
                }, { headers: headers })
                .then(res => {
                    this.detteClientDialog = false
                    Notification.success()
                    self.getResults();
                    this.editSourceDialog = false
                })
                .catch(err => {
                    console.log(err);
                })
        },

    },
}
</script>

<style>

</style>