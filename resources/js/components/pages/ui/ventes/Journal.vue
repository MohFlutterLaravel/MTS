<template>
    <div class="d-flex justify-content-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Journal de vente</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Bon de livraison <span v-if="affich"><b> total{{journné}}</b></span></h6>
            <div>
                <button v-tooltip.top="'Actualiser'" @click="getResults('api/employee/ventes')" type="button"
                    class="btn btn-primary me-2"><i class="fa-solid fa-arrows-rotate"></i></button>
                <button v-tooltip.top="'Filtrer'" v-if="showFilterButton" @click="openDialogFilter" type="button"
                    class="btn btn-dark me-2"><i class="fas fa-filter"></i></button>
                <button v-tooltip.top="'Imprimer'" v-if="showActionButtons" type="button" class="btn btn-dark me-2"><i
                        class="fas fa-print"></i></button>
                <button v-tooltip.top="'Supprimer'" v-if="showActionButtons" type="button" class="btn btn-danger me-2"
                    @click="deleteAchat"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-items-center table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ETAT</th>
                        <th>N°</th>
                        <th>Client</th>
                        <th>PAIEMENT</th>
                        <th>CAISSE</th>
                        <th>Mantant</th>
                        <th>UTILISATEUR</th>
                        <th>OBSERVATION</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr :id="'vente' + vente.id" @click="showAction(vente)" style="cursor: pointer"
                        v-for="vente in ventes">

                        <td>
                            <span :class="vente.isvalid == 1 ? 'badge bg-success' : 'badge bg-danger'">{{
                                    vente.isvalid
                                        == 1 ? 'VALIDE' : 'INVALIDE'
                            }}</span>
                        </td>
                        <td>{{ vente.id }}</td>
                        <td>{{ vente.client.name }}</td>
                        <td>{{ vente.payemode.mode }}</td>
                        <td>{{ vente.caisse.label }}</td>
                        <td>{{ vente.mantant }}</td>
                        <td>{{ vente.user.name }}</td>
                        <td>{{ vente.observation != null ? vente.observation : 'Aucune observation' }}</td>
                        <td>{{ new Date(vente.updated_at).toLocaleString('en-GB') }}</td>
                    </tr>
                    <tr v-if="spinner">
                        <td colspan="9" class="text-center">
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

    <!-- Dialog filter open -->
    <Dialog v-model:visible="filterDialog" :style="{ width: '750px' }" header="Filtre journal" :modal="true"
        class="p-fluid">
        <form @submit="filterJournal" enctype="multipart/form-data">
            <div class="d-flex justify-content-between m-4">
                <div>
                    <Calendar v-model="selectedDated" :showButtonBar="true" :showIcon="true" />
                </div>
                <div>
                    <Calendar v-model="selectedDatef" :showButtonBar="true" :showIcon="true" />
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <input type="submit" value="Valider" class="btn btn-dark btn-lg"
                    style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: 1.5rem; --bs-btn-font-size: 1.25rem;">
            </div>
        </form>
    </Dialog>
    <!-- Dialog filter close -->
</template>
<script>
import Dialog from 'primevue/dialog';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import ProgressSpinner from 'primevue/progressspinner';
import Calendar from 'primevue/calendar';
import Tooltip from 'primevue/tooltip';
export default {
    components: {
        Dialog,
        Loading,
        ProgressSpinner,
        Calendar,
    },
    directives: {
        'tooltip': Tooltip
    },
    data() {
        return {
            form: {
                user_id: null
            },
            ventes: [],
            filterDialog: null,
            selectedDated: null,
            selectedDatef: null,
            vente: null,
            noventes: false,
            produits: [],
            produit: null,
            pagination: {},
            loading: false,
            spinner: false,
            source_id: null,
            caisse_id: null,
            payemode_id: null,
            optionClient: [],
            optionProduits: [],
            optionCaisses: [],
            optionPayemode: [],
            journné: null,
            affich: false,
            //buttons
            showFilterButton: true,
            showActionButtons: false,

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

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            // get clients
            axios.get('api/employee/clients-filter', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.optionClient.push({ 'id': response[index].id, 'name': response[index].name, })
                        // console.log(response[index].designation.fr);

                    }
                })
                .catch(err => {
                    console.log(err);
                })
            // get produits
            axios.get('api/employee/produits-filter', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.optionProduits.push({
                            'id': response[index].id,
                            'barecode': response[index].barecode,
                            'designation': response[index].designation,
                            'pa': response[index].pa,
                            'pv': response[index].pv,
                            'tva': response[index].tva,
                        })
                    }
                })
                .catch(err => {
                    console.log(err);
                })

            // get payemode
            axios.get('api/employee/payemode-filter', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.optionPayemode.push({ 'id': response[index].id, 'mode': response[index].mode, })
                        // console.log(response[index].designation.fr);

                    }
                })
                .catch(err => {
                    console.log(err);
                })

            // get caisses
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
        getResults(page) {

            const elements = document.querySelectorAll('.achat');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            this.showFilterButton = true
            this.client_id = null
            this.payemode_id = null
            this.caisse_id = null
            this.ventes = []
            this.noventes = false
            this.spinner = true
            this.showActionButtons = false
            page = page || 'api/employee/ventes'
            var self = this
            let token = this.$store.state.token

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.get(page, { headers: headers, })
                .then(response => {
                    let res = response.data
                    console.log(res.data);
                    this.ventes = res.data;
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
        showAction(vente) {
            this.showActionButtons = true
            let id = vente.id
            this.vente = vente
            this.produits = []
            const elements = document.querySelectorAll('.vente');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            var element = document.getElementById('vente' + id);
            element.classList.add("vente");
            element.classList.add("table-dark");
            element.classList.add("font-weight-bold");
        },
        openDialogFilter() {
            this.filterDialog = true
        },
        filterJournal(e) {
            e.preventDefault();
            this.spiner = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('api/employee/ventes-filter',
                {
                    'dated': self.selectedDated,
                    'datef': self.selectedDatef
                }, { headers: headers })
                .then(res => {
                    self.filterDialog = false
                    console.log(res.data)
                    self.ventes = res.data.ventes;
                    self.journné = res.data.mantant;
                    self.affich = true
                    self.spinner = false
                    self.selectedDated = null
                    self.selectedDatef = null
                    if (self.ventes.length == 0) {
                        self.noventes = true
                    }
                })
                .catch(err => {
                    console.log(err);
                })
        },
    }
}
</script>
<style>

</style>