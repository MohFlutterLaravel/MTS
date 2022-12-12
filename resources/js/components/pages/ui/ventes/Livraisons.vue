<template>

    <div class="card p-2" style="height: 78vh; overflow-y: auto; background-color: rgb(241 241 241);">
        <div class="d-flex justify-content-between">
            <div class="d-flex justify-content-between" style="width: 70%;">
                <div class="field input-group me-2" style="width: 55%;">
                    <span class="input-group-text">Codebare</span>
                    <input v-on:keyup.native.enter="getProduit" type="text" v-model.trim="form.barecode"
                        class="form-control" autofocus>
                </div>
                <div class="form-floating w-50" style="background-color: white;">
                    <v-select placeholder="Produit" v-model="produit" :options="optionProduits"
                        :reduce="designation => designation" label="designation"></v-select>
                </div>
            </div>
            <div v-if="showValideButton" class="d-flex justify-content-end">
                <div class="ms-2">
                    <button class="btn btn-danger" @click="vider" v-tooltip.top="'Vider'"
                        style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: 1.5rem;"><i
                            class="fa-solid fa-trash-can-arrow-up"></i></button>
                </div>
                <div class="ms-2">
                    <button class="btn btn-primary" v-tooltip.top="'Imprimer'"
                        style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: 1.5rem;"><i
                            class="fa-solid fa-print"></i></button>
                </div>
                <div class="ms-2">
                    <button class="btn btn-success" v-tooltip.top="'Valider'" @click="openDialogFinal"
                        style="--bs-btn-padding-y: .5rem; --bs-btn-padding-x: .9rem; --bs-btn-font-size: 1.5rem;"><i
                            class="fa-solid fa-check"></i></button>
                </div>

            </div>
        </div>
        <div class="card mt-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Produits</h6>
                <div v-if="showActionProduitButton">
                    <button @click="EditProduit" type="button" class="btn btn-dark me-2"><i
                            class="fas fa-edit"></i></button>
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
                            <td>{{ produit.pa }}</td>
                            <td>{{ produit.prix }}</td>
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
    </div>
    <!-- Dialog qte prix open -->
    <Dialog v-model:visible="showQtePrixDialog" :style="{ width: '500px' }" header="Vente" :modal="true"
        class="p-fluid">
        <div>
            <h5>{{ produit.designation }}</h5>
        </div>
        <div class="d-flex justify-content-arrownd mb-4">
            <div class="field input-group me-2" style="width: 55%;">
                <span class="input-group-text">Quantité</span>
                <input @keyup.enter="getProduit()" type="number" v-model="qte" class="form-control" autofocus>
            </div>
            <div class="field input-group me-2" style="width: 55%;">
                <span class="input-group-text">Prix</span>
                <input type="number" v-model.trim="produit.pv" class="form-control">
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <div>
                <button @click="getProduit" class="btn btn-primary ms-2">Ajouter</button>
            </div>
        </div>
    </Dialog>
    <!-- Dialog qte prix close -->

    <!-- Dialog final open -->
    <Dialog v-model:visible="showPayemodeDialog" :style="{ width: '500px' }" header="Payemode" :modal="true"
        class="p-fluid">
        <div class="d-flex justify-content-center">
            <div class="form-floating pb-4" style="width: 80%;">
                <v-select placeholder="Payemode" v-model="payemode_id" :options="optionPayemode"
                    :reduce="mode => mode.id" label="mode"></v-select>
            </div>
            <div>
                <button @click="storeVente" class="btn btn-primary ms-2">Select</button>
            </div>
        </div>
    </Dialog>
    <!-- Dialog final close -->

    <!-- Dialog client open -->
    <Dialog v-model:visible="showClientDialog" :style="{ width: '500px' }" header="Client" :modal="true"
        class="p-fluid">
        <div class="d-flex justify-content-center">
            <div class="form-floating pb-4" style="width: 80%;">
                <v-select placeholder="Client" v-model="client_id" :options="optionClient" :reduce="name => name.id"
                    label="name"></v-select>
            </div>
            <div>
                <button @click="closeDialogClient" class="btn btn-primary ms-2">Select</button>
            </div>
        </div>
    </Dialog>
    <!-- Dialog client close -->
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
    data() {
        return {
            form: {
                user_id: null,
                produit_id: null,
                produit: null,
                barecode: null,
            },
            optionProduits: [],
            optionCaisses: [],
            optionPayemode: [],
            optionClient: [],
            payemode_id: 1,
            client_id: null,
            caisse_id: null,
            produits: [],
            produit: null,
            spinner: false,
            loading: false,
            qte: null,
            //button
            showActionProduitButton: false,
            showValideButton: false,
            // dialog 
            showQtePrixDialog: false,
            showPayemodeDialog: false,
            showClientDialog: true
        }
    },
    directives: {
        'tooltip': Tooltip
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
                    this.client_id = 1
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
    watch: {
        produits: {
            deep: true,
            handler() {
                if (this.produits.length == 0) {
                    this.showValideButton = false
                    this.showActionProduitButton = false
                } else {
                    this.showValideButton = true
                }
            }
        },
        produit: function () {
            if (this.produit != null) {
                this.openQtePrixDialog()
            }
        },
    },
    methods: {
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
        openQtePrixDialog() {
            this.qte = null
            this.showQtePrixDialog = true
        },
        getProduit() {
            let commit = null
            if (this.qte != null && this.produit.pv != "") {

                if (this.form.barecode != null) {

                } else {
                    for (let index = 0; index < this.produits.length; index++) {
                        if (this.produits[index].barecode == this.produit.barecode) {
                            Swal.fire(
                                'Produit exist?',
                                'Le produit est exist dans le bon',
                                'warning'
                            )
                            this.showQtePrixDialog = false
                            this.produit = null
                            this.qte = null
                            return;
                        }

                    }
                    this.produits.push({
                        'id': this.produit.id,
                        'barecode': this.produit.barecode,
                        'designation': this.produit.designation,
                        'pa': this.produit.pa,
                        'prix': this.produit.pv,
                        'tva': this.produit.tva,
                        'qte': this.qte,
                        'total': this.qte * this.produit.pv,
                    })
                }
                this.showQtePrixDialog = false
                this.produit = null
                this.qte = null
            } else {
                Swal.fire(
                    'Obligatoire!',
                    'Les champs ne doivent pas être laissés vides',
                    'warning'
                )
            }


        },
        EditProduit() {
            this.produit = this.form.produit
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
                    const elements = document.querySelectorAll('.produit');
                    elements.forEach(el => {
                        el.removeAttribute('class');
                    });
                    this.showActionProduitButton = false
                }
            })
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
        openDialogFinal() {
            this.showPayemodeDialog = true
        },
        storeVente() {
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
                axios.post('/api/employee/ventes', {
                    'user_id': self.form.user_id,
                    'client_id': self.client_id || 1,
                    'caisse_id': self.caisse_id || 1,
                    'payemode_id': self.payemode_id,
                    'products': self.produits
                }, {
                    headers: headers,
                })
                    .then(res => {
                        self.showAchatDialog = false
                        self.client_id = null;
                        self.caisse_id = null;
                        self.payemode_id = null;
                        self.produits = [];
                        self.showValideButton = false
                        Notification.success();
                        this.spinner = false
                        this.loading = false
                        this.showClientDialog = true
                    })
                    .catch(err => {
                        Notification.error();
                        console.log(err);
                        this.spinner = false
                        this.loading = false
                    })
            } else {
                Swal.fire(
                    'Attention!',
                    'Les champs ne doivent pas être laissés vides',
                    'warning'
                )
            }
        },
        closeDialogClient() {
            this.showClientDialog = false
        }
    }
}
</script>

<style>
.form-floating.w-50 {
    background-color: white;
    border-radius: 7px;
}

div#vs1__combobox,
div#vs2__combobox,
div#vs3__combobox {
    border: none;
}

.vs__actions {
    margin-top: 18px;
}

input.vs__search {
    margin-top: 12px;
}
</style>