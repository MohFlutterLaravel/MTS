<template>
    <div class="d-flex justify-content-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Trésorerie</li>
            </ol>
        </nav>
    </div>
    <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Caisses</h6>
            <div>
                <button @click="getResults('api/employee/caisses')" type="button" class="btn btn-primary me-2"><i
                        class="fa-solid fa-arrows-rotate"></i></button>
                <button @click="encaisserDialog" v-if="showCaissButton" type="button" class="btn btn-success me-2">
                    <i class="fa-solid fa-download"></i>
                </button>
                <button @click="decaisserDialog" v-if="showCaissButton" type="button" class="btn btn-dark me-2">
                    <i class="fa-solid fa-upload"></i>
                </button>
                <button type="button" class="btn btn-primary" @click="openDialogCaisse"><i
                        class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-items-center table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>LABEL</th>
                        <th>TOT-ENC</th>
                        <th>TOT-DEC</th>
                        <th>UTILISATEUR</th>
                        <th>SOLDE</th>
                        <th>DATE-MODIF</th>
                    </tr>
                </thead>
                <tbody>
                    <tr :id="'caisse' + caisse.id" style="cursor: pointer" v-for="caisse in caisses"
                        @click="showAction(caisse)">

                        <td>{{ caisse.id }}</td>
                        <td><span class="badge bg-success">{{ caisse.label }}</span></td>
                        <td>{{ caisse.tot_enc }}</td>
                        <td>{{ caisse.tot_dec }}</td>
                        <td><span class="badge bg-success">{{ caisse.user.name }}</span></td>
                        <td>{{ caisse.solde }}</td>
                        <td>{{ new Date(caisse.updated_at).toLocaleString('en-GB') }}</td>
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
    <Dialog v-model:visible="createDialog" :style="{ width: '450px' }" header="Nouvelle Caisse" :modal="true"
        class="p-fluid">
        <form @submit="store" enctype="multipart/form-data">
            <div class="field">
                <label for="label">Label</label>
                <input class="form-control" id="label" v-model.trim="form.label" type="text" required>
            </div>
            <div class="field">
                <label for="solde_ini">Solde initial</label>
                <input class="form-control" id="solde_ini" v-model.trim="form.solde_ini" type="number">
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Ajouter" type="submit" class="btn btn-primary">
            </div>
        </form>

    </Dialog>
    <!-- Dialog create close -->

    <!-- Dialog encaisser open -->
    <Dialog v-model:visible="showEncaisserDialog" :style="{ width: '450px' }" header="Encaissement" :modal="true"
        class="p-fluid">
        <form @submit="encaisser" enctype="multipart/form-data">
            <div class="field">
                <label for="encaissement">Somme de</label>
                <input class="form-control" id="encaissement" v-model.trim="encaissement" type="number">
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Ajouter" type="submit" class="btn btn-primary">
            </div>
        </form>
    </Dialog>
    <!-- Dialog encaisser close -->

    <!-- Dialog decaisser open -->
    <Dialog v-model:visible="showDecaisserDialog" :style="{ width: '450px' }" header="Decaissement" :modal="true"
        class="p-fluid">
        <form @submit="decaisser" enctype="multipart/form-data">
            <div class="field">
                <label for="decaissement">Somme de</label>
                <input class="form-control" id="decaissement" v-model.trim="decaissement" type="number">
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Ajouter" type="submit" class="btn btn-primary">
            </div>
        </form>
    </Dialog>
    <!-- Dialog decaisser close -->
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
                label: null,
                solde_ini: null,
            },
            pagination: {},
            caisses: [],
            caisse: null,
            spinner: true,
            loading: true,
            showCaissButton: false,
            createDialog: false,
            showEncaisserDialog: false,
            showDecaisserDialog: false,
            encaissement: null,
            decaissement: null,
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
            const elements = document.querySelectorAll('.caisse');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            this.spinner = true
            this.showCaissButton = false
            this.caisse = null
            page = page || 'api/employee/caisses'
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
                    self.caisses = res.data;
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
        showAction(caisse) {
            this.showCaissButton = true
            let id = caisse.id
            this.caisse = caisse
            const elements = document.querySelectorAll('.caisse');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            var element = document.getElementById('caisse' + id);
            element.classList.add("caisse");
            element.classList.add("table-dark");
            element.classList.add("font-weight-bold");
        },
        openDialogCaisse() {
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
            axios.post('/api/employee/caisses', this.form, {
                headers: headers,
            })
                .then(res => {
                    self.form.label = null;
                    self.form.solde_ini = null;
                    self.createDialog = false;
                    Notification.success();
                    self.getResults();
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })
        },
        encaisserDialog() {
            this.showEncaisserDialog = true
        },
        decaisserDialog() {
            this.showDecaisserDialog = true
        },
        encaisser(e) {
            e.preventDefault();
            this.spinner = true
            this.loading = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/encaisser', {
                'encaissement': self.encaissement,
                'user_id': self.form.user_id,
                'caisse_id': self.caisse.id
            }, {
                headers: headers,
            })
                .then(res => {
                    self.encaissement = null;
                    self.showEncaisserDialog = false;
                    Notification.success();
                    self.getResults();
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })
        },

        decaisser(e) {
            e.preventDefault();
            this.spinner = true
            this.loading = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/decaisser', {
                'decaissement': self.decaissement,
                'user_id': self.form.user_id,
                'caisse_id': self.caisse.id
            }, {
                headers: headers,
            })
                .then(res => {
                    self.decaissement = null;
                    self.showDecaisserDialog = false;
                    Notification.success();
                    self.getResults();
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })
        },
    },
}
</script>

<style>

</style>