<template>
    <div class="d-flex justify-content-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Charges</li>
            </ol>
        </nav>
    </div>
    <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Charges</h6>
            <div>
                <button v-if="showRefreshButton" @click="getResults('api/employee/charges')" type="button"
                    class="btn btn-primary me-2"><i class="fa-solid fa-arrows-rotate"></i></button>
                <button v-if="showFilterButton" @click="openDialogFilter" type="button" class="btn btn-dark me-2"><i
                        class="fas fa-filter"></i></button>
                <button v-if="showDeleteButton" type="button" class="btn btn-danger me-2" @click="deleteCharge"><i
                        class="fas fa-trash"></i></button>
                <button type="button" class="btn btn-primary" @click="ajouteDialogCharge"><i
                        class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-items-center table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>TYPE</th>
                        <th>CAISSE</th>
                        <th>UTILISATEUR</th>
                        <th>DESIGNATION</th>
                        <th>MANTANT</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr :id="'charge' + charge.id" style="cursor: pointer" v-for="charge in charges"
                        @click="showAction(charge)">

                        <td>{{ charge.id }}</td>
                        <td><span class="badge bg-success">{{ charge.typecharge.designation }}</span></td>
                        <td><span class="badge bg-success">{{ charge.caisse.label }}</span></td>
                        <td><span class="badge bg-success">{{ charge.user.name }}</span></td>
                        <td>{{ charge.designation }}</td>
                        <td>{{ charge.mantant }}</td>
                        <td>{{ new Date(charge.created_at).toLocaleString('en-GB') }}</td>
                    </tr>
                    <tr v-if="spinner">
                        <td colspan="8" class="text-center">
                            <div class="p-4">
                                <ProgressSpinner />
                            </div>
                        </td>
                    </tr>
                    <tr v-if="nocharges">
                        <td colspan="8" class="text-center">
                            <div class="p-4">
                                Aucune data pour s'afficher ! 
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

    <!-- Dialog ajouter charge open -->
    <Dialog v-model:visible="chargeDialog" :style="{ width: '750px' }" header="Nouveau Charge" :modal="true"
        class="p-fluid">
        <form @submit="ajoutercharge" enctype="multipart/form-data">
            <div class="row">
                <div class="col-6">
                    <div class="field input-group mb-4">
                        <span class="input-group-text">Mantant</span>
                        <input type="number" v-model.trim="form.mantant" class="form-control">
                    </div>
                </div>
                <div class="col-6">
                    <div class="field input-group mb-4">
                        <span class="input-group-text">Designation</span>
                        <input type="text" v-model.trim="form.designation" class="form-control">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-floating pb-4">
                        <v-select placeholder="Caisse" v-model="form.caisse_id" :options="optionCaisses"
                            :reduce="label => label.id" label="label"></v-select>
                    </div>
                </div>
                <div class="col-6 d-flex">
                    <div class="form-floating pb-4 me-2" style="width: 75%!important;">
                        <v-select placeholder="Type" v-model="form.typecharge_id" :options="optionTypecharges"
                            :reduce="designation => designation.id" label="designation"></v-select>
                    </div>
                    <div>
                        <button style="--bs-btn-padding-y: .39em; --bs-btn-padding-x: .7em;" type="button"
                            class="btn btn-sm btn-primary" @click="ajouteDialogType"><i
                                class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <input type="submit" value="Valider" class="btn btn-dark btn-lg"
                    style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: 1.5rem; --bs-btn-font-size: 1.25rem;">
            </div>
        </form>
    </Dialog>
    <!-- Dialog ajouter charge close -->


    <!-- Dialog ajouter type open -->
    <Dialog v-model:visible="typeDialog" :style="{ width: '750px' }" header="Nouveau Type" :modal="true"
        class="p-fluid">
        <form @submit="ajouterType" enctype="multipart/form-data">
            <div class="row d-flex justify-content-center">
                <div class="col-8">
                    <div class="field input-group mb-4">
                        <span class="input-group-text">Designation</span>
                        <input type="text" v-model.trim="form.designation" class="form-control">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <input type="submit" value="Valider" class="btn btn-dark btn-lg"
                    style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: 1.5rem; --bs-btn-font-size: 1.25rem;">
            </div>
        </form>
    </Dialog>
    <!-- Dialog ajouter type close -->

    <!-- Dialog filter open -->
    <Dialog v-model:visible="filterDialog" :style="{ width: '750px' }" header="Nouveau Type" :modal="true"
        class="p-fluid">
        <form @submit="filterCharge" enctype="multipart/form-data">
            <div class="row d-flex justify-content-center">
                <div class="col-8">
                    <Calendar v-model="selectedDate" :showButtonBar="true" :showIcon="true" />
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <input type="submit" value="Valider" class="btn btn-dark btn-lg"
                    style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: 1.5rem; --bs-btn-font-size: 1.25rem;">
            </div>
        </form>
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
            form: {
                user_id: null,
                typecharge_id: null,
                caisse_id: null,
                designation: null,
                mantant: null,
            },
            chargeDialog: false,
            typeDialog: false,
            filterDialog: false,
            charges: [],
            charge: null,
            nocharges: false,
            selectedDate: null,
            optionCaisses: [],
            optionTypecharges: [],
            pagination: {},
            loading: false,
            spinner: false,
            showDeleteButton: false,
            showFilterButton: true,
            showRefreshButton: true,
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
            // get type charges
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.get('api/employee/typecharges', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.optionTypecharges.push({ 'id': response[index].id, 'designation': response[index].designation, })
                    }
                })
                .catch(err => {
                    console.log(err);
                })
            axios.get('api/employee/caisses-filter', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.optionCaisses.push({ 'id': response[index].id, 'label': response[index].label, })
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
        showAction(charge) {
            this.showDeleteButton = true
            let id = charge.id
            this.charge = charge
            const elements = document.querySelectorAll('.charge');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            var element = document.getElementById('charge' + id);
            element.classList.add("charge");
            element.classList.add("table-dark");
            element.classList.add("font-weight-bold");
        },
        getResults(page) {
            const elements = document.querySelectorAll('.charge');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            this.charge = null
            this.showDeleteButton = false
            this.spinner = true
            page = page || 'api/employee/charges'
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
                    self.charges = res.data;
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
        filterCharge(e) {
            e.preventDefault();
            this.loading = true
            this.spiner = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('api/employee/charges-filter', {'date': self.selectedDate}, {headers:headers})
            .then(res=>{
                self.filterDialog = false
                self.charges = res.data[0]
                self.spinner = false
                self.loading = false
                if (self.charges.length == 0) {
                    self.nocharges = true
                }
            })
            .catch(err=>{
                console.log(err);
            })
        },
        deleteCharge(e) {
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
                    e.preventDefault();
                    var self = this
                    let token = this.$store.state.token
                    const headers = {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                    axios.delete('/api/employee/charges/' + this.charge.id, {
                        headers: headers,
                    })
                        .then(res => {
                            Notification.success();
                            self.getResults();
                        })
                        .catch(err => {
                            Notification.error();
                            console.log(err);
                        })
                }
            })
        },
        ajouteDialogCharge() {
            this.chargeDialog = true
        },
        ajouteDialogType() {
            this.typeDialog = true
        },
        ajoutercharge(e) {
            e.preventDefault();
            this.spinner = true
            this.loading = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/charges', this.form, {
                headers: headers,
            })
                .then(res => {
                    self.form.caisse_id = null;
                    self.form.typecharge_id = null;
                    self.form.designation = null;
                    self.form.mantant = null;
                    self.chargeDialog = false;
                    Notification.success();
                    self.getResults();
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })
        },
        ajouterType(e) {
            e.preventDefault();
            this.loading = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/typecharges', {
                'user_id': self.form.user_id,
                'designation': self.form.designation,
            }, {
                headers: headers,
            })
                .then(res => {
                    console.log(res);
                    this.loading = false
                    self.typeDialog = false,
                        self.optionTypecharges.push({ 'id': res.data.id, 'designation': res.data.designation, })
                    self.form.designation = null
                    Notification.success();
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