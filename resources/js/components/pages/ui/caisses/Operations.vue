<template>
    <div class="d-flex justify-content-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Operations</li>
            </ol>
        </nav>
    </div>
    <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Operations</h6>
            <div>
                <button @click="getOperation('api/employee/operations')" type="button"
                    class="btn btn-primary me-2"><i class="fa-solid fa-arrows-rotate"></i></button>
                <button @click="openDialogFilter" class="btn btn-dark me-2"><i class="fas fa-filter"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Caisse</th>
                        <th>Encaissement</th>
                        <th>Decaissement</th>
                        <th>Observation</th>
                        <th>Solde initial</th>
                        <th>Ancien solde</th>
                        <th>Nouveau solde</th>
                        <th>Utilisateur</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody v-if="operations != []">
                    <tr v-for="operation in operations">
                        <td>{{ operation.id }}</td>
                        <td v-if="operation.caisse">{{ operation.caisse.label }}</td>
                        <td>{{ operation.enc }}</td>
                        <td>{{ operation.dec }}</td>
                        <td>{{ operation.observation }}</td>
                        <td v-if="operation.caisse">{{ operation.caisse.solde_ini }}</td>
                        <td>{{ operation.ancien_solde }}</td>
                        <td>{{ operation.nv_solde }}</td>
                        <td v-if="operation.user">{{ operation.user.name }}</td>
                        <td>{{ new Date(operation.created_at).toLocaleString('en-GB') }}</td>
                    </tr>
                    <tr v-if="spinner">
                        <td colspan="10" class="text-center">
                            <div class="p-4">
                                <ProgressSpinner />
                            </div>
                        </td>
                    </tr>

                    <tr v-if="operations.length == 0">
                        <td colspan="10" class="text-center">
                            <div class="p-4">
                                No Data! ...
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
                                    @click="getOperation(pagination.first_link)" class="page-link">&laquo;</a>
                            </li>
                            <li v-bind:class="{ disabled: !pagination.prev_link }" class="page-item"><a href="#"
                                    @click="getOperation(pagination.prev_link)" class="page-link">&lt;</a></li>
                            <li v-for="n in pagination.last_page" v-bind:key="n"
                                v-bind:class="{ active: pagination.current_page == n }" class="page-item"><a href="#"
                                    @click="getOperation(pagination.path_page + n)" class="page-link">{{ n }}</a></li>
                            <li v-bind:class="{ disabled: !pagination.next_link }" class="page-item"><a href="#"
                                    @click="getOperation(pagination.next_link)" class="page-link">&gt;</a></li>
                            <li v-bind:class="{ disabled: !pagination.last_link }" class="page-item"><a href="#"
                                    @click="getOperation(pagination.last_link)" class="page-link">&raquo;</a>
                            </li>
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

    <!-- Dialog filter open -->
    <Dialog v-model:visible="filterDialog" :style="{ width: '600px' }" header="Filter" :modal="true" class="p-fluid">
        <div class="row">
            <div class="col-6">
                <div class="form-floating">
                    <select v-model="selectedCaisse" class="form-select" id="caisseSelect"
                        aria-label="Floating label select example">
                        <option v-for="caisse in caisses" :value="caisse.id" selected>{{ caisse.label }}</option>
                    </select>
                    <label for="caisseSelect">Caisse</label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-floating">
                    <select v-model="selectedUser" class="form-select" id="userSelect"
                        aria-label="Floating label select example">
                        <option v-for="user in users" :value="user.id" selected>{{ user.name }}</option>
                    </select>
                    <label for="userSelect">Utilisateur</label>
                </div>
            </div>
            <div class="col-12 mt-2 mb-4">
                <Calendar placeholder="Superieur a" v-model="selectedDateDebut" :showButtonBar="true" :showIcon="true" />
            </div>
            <div class="col-12 mt-2 mb-4">
                <Calendar placeholder="Inferieur a" v-model="selectedDateFin" :showButtonBar="true" :showIcon="true" />
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <button class="btn btn-dark btn-lg" @click="filter"
                style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: 1.5rem; --bs-btn-font-size: 1.25rem;">Filter</button>
        </div>
    </Dialog>
    <!-- Dialog filter close -->
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
import Calendar from 'primevue/calendar';
export default {
    components: {
        Dialog,
        Loading,
        ProgressSpinner,
        Calendar,
    },
    data() {
        return {
            operations: [],
            operation: null,
            caisses: [],
            selectedCaisse: null,
            users: [],
            selectedUser: null,
            selectedDateDebut: null,
            selectedDateFin: null,
            loading: true,
            spinner: true,
            pagination: {},
            filterDialog: false,
            form: {
                user_id: null,
            }
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

            // get caisse and users for filter api/employee/operations-filter
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.get('api/employee/operations-filter', { headers: headers })
                .then(res => {
                    console.log(res.data);
                    this.caisses = res.data.caisses
                    this.users = res.data.users
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
        this.getOperation();
    },
    methods: {
        getOperation(page) {
            this.spinner = true
            page = page || 'api/employee/operations'
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
                    self.operations = res.data;
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
        openDialogFilter() {
            this.filterDialog = true
        },
        filter() {
            this.spinner = true
            var self = this
            let token = this.$store.state.token

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('api/employee/result-filter-operations',{
                'caisse_id': self.selectedCaisse,
                'user_id': self.selectedUser,
                'dated': self.selectedDateDebut,
                'datef': self.selectedDateFin
            }, {headers:headers})
            .then(res=>{
                this.filterDialog = false
                self.operations = res.data[0];
                self.spinner = false
                self.selectedDateDebut = null
                self.selectedDateFin = null
            })
            .catch(err=>{
                console.log(err);
            })
        }
    }
}
</script>

<style>

</style>