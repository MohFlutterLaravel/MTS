<template>
    <div class="d-flex justify-content-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Bon de reception</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Bon de reception</h6>
            <div>
                <button @click="getResults('api/employee/achats')" type="button" class="btn btn-primary me-2"><i
                        class="fa-solid fa-arrows-rotate"></i></button>
                <button v-if="showFilterButton" @click="openDialogFilter" type="button" class="btn btn-dark me-2"><i
                        class="fas fa-filter"></i></button>
                <button v-if="showDeleteButton" type="button" class="btn btn-danger me-2" @click="deleteAchat"><i
                        class="fas fa-trash"></i></button>
                <button type="button" class="btn btn-primary" @click="openDialogAchat"><i
                        class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-items-center table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ETAT</th>
                        <th>N°</th>
                        <th>FOURNISSEUR</th>
                        <th>PAIEMENT</th>
                        <th>CAISSE</th>
                        <th>Mantant</th>
                        <th>UTILISATEUR</th>
                        <th>OBSERVATION</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr :id="'achat' + achat.id" @click="showAction(achat)" style="cursor: pointer"
                        v-for="achat in achats">

                        <td>
                            <span :class="achat.isvalid == 1 ? 'badge bg-success' : 'badge bg-danger'">{{
                                    achat.isvalid
                                        == 1 ? 'VALIDE' : 'INVALIDE'
                            }}</span>
                        </td>
                        <td>{{ achat.id }}</td>
                        <td>{{ achat.source.name }}</td>
                        <td>{{ achat.payemode.mode }}</td>
                        <td>{{ achat.caisse.label }}</td>
                        <td>{{ achat.mantant }}</td>
                        <td>{{ achat.user.name }}</td>
                        <td>{{ achat.observation }}</td>
                        <td>{{ new Date(achat.updated_at).toLocaleString('en-GB') }}</td>
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

    <!-- Dialog Achat open -->
    <Dialog position="top" v-model:visible="showAchatDialog" :style="{ width: '1000px' }" header="Ajouter Achat"
        :modal="true" class="p-fluid">

        <!--Button Action-->
        <div class="d-flex justify-content-between">
            <div class="d-flex justify-content-start">
                <div class="me-2">
                    <button type="button" @click="openDialogFinal" v-if="showValideButton"
                        class="btn btn-outline-success" v-tooltip.top="'Valider'"
                        style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: 1.5rem;"><i
                            class="fa-solid fa-check"></i></button>
                </div>
                <div class="me-2">
                    <button type="button" @click="openDialogFinalEdit" v-if="showModifierButton"
                        class="btn btn-outline-success" v-tooltip.top="'Editer'"
                        style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: 1.5rem;"><i
                            class="fa-solid fa-edit"></i></button>
                </div>
                <div class="me-2">
                    <button type="button" @click="vider" v-if="showValideButton" class="btn btn-outline-danger"
                        v-tooltip.top="'Vider'"
                        style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: 1.5rem;"><i
                            class="fa-solid fa-trash-can-arrow-up"></i></button>
                </div>
                <div class="me-2">
                    <button type="button" @click="openDialogProduits" class="btn btn-outline-primary"
                        v-tooltip.top="'Insérer'"
                        style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: 1.5rem;"><i
                            class="fa-solid fa-cubes"></i></button>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <div class="ms-2">
                    <button class="btn btn-dark" v-tooltip.top="'Imprimer'"
                        style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: 1.5rem;"><i
                            class="fa-solid fa-print"></i></button>
                </div>
                <div class="ms-2">
                    <button class="btn btn-dark" v-tooltip.top="'Exporter'"
                        style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: 1.5rem;"><i
                            class="fa-regular fa-file-excel"></i></button>
                </div>
            </div>
        </div>
        <!--Produits-->
        <div class="card mt-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Produits</h6>
                <div v-if="showActionProduitButton">
                    <button type="button" class="btn btn-dark me-2"><i class="fas fa-edit"></i></button>
                    <button @click="deleteProduit" type="button" class="btn btn-danger me-2"><i
                            class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-items-center table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>CODEBARE</th>
                            <th>DESIGNATION</th>
                            <th>PA</th>
                            <th>PV</th>
                            <th>TVA</th>
                            <th>QTE</th>
                            <th>Mantant</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr :id="'produit' + produit.id" @click="showActionProduit(produit)" style="cursor: pointer"
                            v-for="produit in produits">
                            <td>{{ produit.barecode }}</td>
                            <td>{{ produit.designation }}</td>
                            <td>{{ produit.prix }}</td>
                            <td>{{ produit.pv }}</td>
                            <td>{{ produit.tva }}</td>
                            <td>{{ produit.qte }}</td>
                            <td>{{ produit.total }}</td>
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
        </div>


    </Dialog>
    <!-- Dialog Achat close -->

    <!-- Dialog produits open -->
    <Dialog v-model:visible="showProduitDialog" :style="{ width: '500px' }" header="Produits" :modal="true"
        class="p-fluid">
        <div class="form-floating pb-4" style="width: 100%;">
            <v-select placeholder="Produit" v-model="produit" :options="optionProduits"
                :reduce="designation => designation" label="designation"></v-select>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="field input-group w-100">
                    <span class="input-group-text">PA</span>
                    <input type="number" v-model.trim="paa" class="form-control" required>
                </div>
            </div>
            <div class="col-6">
                <div class="field input-group w-100">
                    <span class="input-group-text">QTE</span>
                    <input type="number" v-model.trim="qte" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            <button @click="showvalid" class="btn btn-primary">Ajouter</button>
        </div>
        <div class="mt-4 d-flex justify-content-center">
            <button @click="closeProduitDialog" class="btn btn-primary">Terminer</button>
        </div>


    </Dialog>
    <!-- Dialog produits close -->

    <!-- Dialog fournisseur et caisse open -->
    <Dialog v-model:visible="showFourniCaisseDialog" :style="{ width: '500px' }" header="Produits" :modal="true"
        class="p-fluid">
        <div class="d-flex justify-content-between">
            <div class="form-floating pb-4" style="width: 40%;">
                <v-select placeholder="Fournisseur" v-model="source_id" :options="optionSource"
                    :reduce="name => name.id" label="name"></v-select>
            </div>
            <div class="form-floating pb-4" style="width: 40%;">
                <v-select placeholder="Caisse" v-model="caisse_id" :options="optionCaisses" :reduce="label => label.id"
                    label="label"></v-select>
            </div>
            <div>
                <button @click="secondDialog" class="btn btn-primary">Ajouter</button>
            </div>
        </div>
    </Dialog>
    <!-- Dialog fournisseur et caisse close -->

    <!-- Dialog final open -->
    <Dialog v-model:visible="showPayemodeDialog" :style="{ width: '500px' }" header="Payemode" :modal="true"
        class="p-fluid">
        <div class="d-flex justify-content-center">
            <div class="form-floating pb-4" style="width: 80%;">
                <v-select placeholder="Payemode" v-model="payemode_id" :options="optionPayemode"
                    :reduce="mode => mode.id" label="mode"></v-select>
            </div>
            <div>
                <button @click="storeAchat" class="btn btn-primary ms-2">Select</button>
            </div>
        </div>
    </Dialog>
    <!-- Dialog final close -->

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
                user_id: null,
                isvalid: null,
                produit: null,
            },
            produits: [],
            loading: false,
            produit: null,
            qte: null,
            paa: null,
            source_id: null,
            caisse_id: null,
            payemode_id: null,
            optionSource: [],
            optionProduits: [],
            optionCaisses: [],
            optionPayemode: [],
            achats: [],
            achat: null,
            pagination: {},
            spinner: false,
            action: null,
            //button
            showDeleteButton: false,
            showFilterButton: false,
            showValideButton: false,
            showModifierButton: false,
            showActionProduitButton: false,
            //dialog
            showAchatDialog: false,
            showProduitDialog: false,
            showFourniCaisseDialog: false,
            showPayemodeDialog: false,
            showEditDialog: false,

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

            // get fournisseurs
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.get('api/employee/sources-filter', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.optionSource.push({ 'id': response[index].id, 'name': response[index].name, })
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
    watch: {
        produits: {
            deep: true,
            handler() {
                if (this.produits.length == 0) {
                    this.showValideButton = false
                    this.showActionProduitButton = false
                    this.showModifierButton = false
                }
            }
        }
    },
    methods: {
        showAction(achat) {
            this.showDeleteButton = true
            let id = achat.id
            this.achat = achat
            this.produits = []
            const elements = document.querySelectorAll('.achat');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            var element = document.getElementById('achat' + id);
            element.classList.add("achat");
            element.classList.add("table-dark");
            element.classList.add("font-weight-bold");
        },
        showActionProduit(produit) {
            this.showActionProduitButton = true
            let id = produit.id
            this.form.produit = produit
            const elements = document.querySelectorAll('.produit');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            var element = document.getElementById('produit' + id);
            element.classList.add("produit");
            element.classList.add("table-dark");
            element.classList.add("font-weight-bold");
        },
        getResults(page) {

            const elements = document.querySelectorAll('.achat');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            this.isvalid = null
            this.source_id = null
            this.payemode_id = null
            this.caisse_id = null
            this.produits = []
            this.spinner = true
            this.showDeleteButton = false
            this.showValideButton = false
            page = page || 'api/employee/achats'
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
                    this.achats = res.data;
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
        // dialog
        openDialogFilter() {

        },
        openDialogAchat() {
            this.showFourniCaisseDialog = true
            this.showModifierButton = false
        },
        secondDialog() {
            if (this.source_id != null && this.caisse_id != null) {
                this.showFourniCaisseDialog = false
                this.showAchatDialog = true
            } else {
                Swal.fire(
                    'Attention!',
                    'Les champs ne doivent pas être laissés vides',
                    'warning'
                )
            }
        },
        openDialogProduits() {
            this.showProduitDialog = true
        },
        openDialogSources() {
            this.showFourniDialog = true
        },
        openDialogFinal() {
            this.showPayemodeDialog = true
        },
        openDialogFinalEdit() {

        },
        showvalid() {
            if (this.produit != null && this.paa != null && this.qte != null) {
                if (this.paa > this.produit.pa) {
                    Swal.fire(
                        'Difference?',
                        'ancien prix: ' + this.produit.pa + ' nouveau prix: ' + this.paa,
                        'question'
                    )
                }
                for (let index = 0; index < this.produits.length; index++) {
                    if (this.produits[index].barecode == this.produit.barecode) {
                        Swal.fire(
                            'Produit exist?',
                            'Le produit est exist dans le bon',
                            'warning'
                        )
                        return;
                    }

                }
                this.produits.push({
                    'id': this.produit.id,
                    'barecode': this.produit.barecode,
                    'designation': this.produit.designation,
                    'prix': this.paa,
                    'pv': this.produit.pv,
                    'tva': this.produit.tva,
                    'qte': this.qte,
                    'total': this.qte * this.paa,
                })
                this.produit = null
                this.qte = null
                this.paa = null
            } else {
                Swal.fire(
                    'Attention!',
                    'Les champs ne doivent pas être laissés vides',
                    'warning'
                )
            }
        },
        closeProduitDialog() {
            if (this.produits.length != 0) {
                if (this.action == 'edit') {
                this.showModifierButton = true
                this.showValideButton = false
            } else {
                this.showModifierButton = false
                this.showValideButton = true
            }
            this.showProduitDialog = false 
            }else {
                Swal.fire(
                    'Attention!',
                    'Les champs ne doivent pas être laissés vides',
                    'warning'
                )
            }
        },
        vider() {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Vous vous voulez vider le bon ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, Vider tout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.produits = []
                    this.showValideButton = false
                }
            })
        },
        //---
        storeAchat() {
            if (this.payemode_id != null) {
                this.showPayemodeDialog = false
                this.spinner = true
                this.loading = true
                var self = this
                let token = this.$store.state.token
                const headers = {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
                axios.post('/api/employee/achats', {
                    'user_id': self.form.user_id,
                    'source_id': self.source_id,
                    'caisse_id': self.caisse_id,
                    'payemode_id': self.payemode_id,
                    'products': self.produits
                }, {
                    headers: headers,
                })
                    .then(res => {
                        self.showAchatDialog = false
                        self.source_id = null;
                        self.caisse_id = null;
                        self.payemode_id = null;
                        self.produits = [];
                        self.showValideButton = false
                        self.payemode_id = null;
                        self.payemode_id = null;
                        self.payemode_id = null;
                        Notification.success();
                        self.getResults();
                    })
                    .catch(err => {
                        Notification.error();
                        console.log(err);
                    })
            } else {
                Swal.fire(
                    'Attention!',
                    'Les champs ne doivent pas être laissés vides',
                    'warning'
                )
            }
        },
        deleteAchat() {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Vous vous voulez supprimer le bon ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, Supprimer tout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.loading = true
                    this.spinner = true
                    var self = this
                    let token = this.$store.state.token
                    const headers = {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                    axios.delete('api/employee/achats/' + self.achat.id, { headers: headers })
                        .then(res => {
                            if (res.data.success) {
                                Swal.fire(
                                    'Supprimé!',
                                    'L\'achat a été supprimé.',
                                    'success'
                                )
                                this.spinner = false
                                this.loading = false
                                Notification.success()
                                this.getResults();
                            } else {
                                this.getResults();
                                Notification.error();
                                Swal.fire(
                                    'Attention !',
                                    'Ce Bon n\'est pas vide.',
                                    'error'
                                )
                            }
                        })
                        .catch(err => {
                            Notification.error();
                            console.log(err);
                        })
                }
            })
        },
        deleteProduit() {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Vous vous voulez annuler le produit ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, annuler!'
            }).then((result) => {
                if (result.isConfirmed) {
                    for (let index = 0; index < this.produits.length; index++) {
                        if (this.produits[index] === this.form.produit) {
                            this.produits.splice(index, 1)

                        }
                    }
                }
            })
        },

    }
}
</script>

<style>

</style>