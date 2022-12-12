<template>

    <div class="d-flex justify-content-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Produits</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Produits</h6>
            <div>
                <button @click="getResults('api/employee/products')" type="button" class="btn btn-primary me-2"><i
                        class="fa-solid fa-arrows-rotate"></i></button>
                <button @click="openDialogEditProduit" v-if="showEditButton" type="button" class="btn btn-dark me-2">
                    <i class="fa-solid fa-edit"></i>
                </button>
                <button @click="deleteProduit" v-if="showDeleteButton" type="button" class="btn btn-danger me-2">
                    <i class="fa-solid fa-trash"></i>
                </button>
                <button type="button" class="btn btn-primary" @click="openDialogProduit"><i
                        class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-items-center table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>CODE BARE</th>
                        <th>DESIGNATION</th>
                        <th>FAMILLE</th>
                        <th>STOCK</th>
                        <th>PRIX ACHAT</th>
                        <th>PRIX VENTE</th>
                        <th>TVA</th>
                        <th>TYPE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr :id="'produit' + produit.id" style="cursor: pointer" v-for="produit in produits"
                        @click="showAction(produit)">

                        <td>{{ produit.barecode }}</td>
                        <td>{{ produit.designation }}</td>
                        <td><span class="badge bg-success">{{ produit.category.designation }}</span></td>
                        <td>{{ produit.qte }}</td>
                        <td>{{ produit.pa }}</td>
                        <td>{{ produit.pv }}</td>
                        <td>{{ produit.tva }}</td>
                        <td><span class="badge bg-success">{{ produit.type.type_name }}</span></td>
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

    <!-- Dialog create produit open -->
    <Dialog v-model:visible="showCreateProduitDialog" :style="{ width: '750px' }" header="Ajouter Produit" :modal="true"
        class="p-fluid">
        <form @submit="storeProduit" enctype="multipart/form-data">
            <div class="row">
                <div class="col-4">
                    <div class="field">
                        <label for="codebare">Code bare</label>
                        <input class="form-control" id="codebare" v-model.trim="form.barecode" type="text" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="designation">Designation</label>
                        <input class="form-control" id="designation" v-model.trim="form.designation" type="text"
                            required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="qte">Quantité initial</label>
                        <input class="form-control" id="qte" v-model.trim="form.qte" type="number" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="pa">Prix D'achat</label>
                        <input class="form-control" id="pa" v-model.trim="form.pa" type="number" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="pv">Prix De Vente</label>
                        <input class="form-control" id="pv" v-model.trim="form.pv" type="number" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="tva">TVA</label>
                        <input class="form-control" id="tva" v-model.trim="form.tva" type="number">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 d-flex">
                        <div class="form-floating pb-4 mt-4" style="width: 75%!important;">
                            <v-select placeholder="Type de produit" v-model="form.type_id" :options="optionType"
                                :reduce="type_name => type_name.id" label="type_name"></v-select>
                        </div>
                        <div>
                            <button style="
                            --bs-btn-padding-y: .39em;
                            --bs-btn-padding-x: .7em;
                            margin-top: 24px;
                            margin-left: 9px;" type="button" class="btn btn-sm btn-primary" @click="openDialogType"><i
                                    class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-6 d-flex">
                        <div class="form-floating pb-4 mt-4" style="width: 75%!important;">
                            <v-select placeholder="Famille de produit" v-model="form.category_id"
                                :options="optionCategory" :reduce="designation => designation.id" label="designation">
                            </v-select>
                        </div>
                        <div>
                            <button style="
                            --bs-btn-padding-y: .39em;
                            --bs-btn-padding-x: .7em;
                            margin-top: 24px;
                            margin-left: 9px;" type="button" class="btn btn-sm btn-primary"
                                @click="openDialogFamille"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Ajouter" type="submit" class="btn btn-primary">
            </div>
        </form>
    </Dialog>
    <!-- Dialog create produit close -->

    <!-- Dialog edit produit open -->
    <Dialog v-model:visible="showEditProduitDialog" :style="{ width: '750px' }" header="Editer Produit" :modal="true"
        class="p-fluid">
        <form @submit="editProduit" enctype="multipart/form-data">
            <div class="row">
                <div class="col-4">
                    <div class="field">
                        <label for="codebare">Code bare</label>
                        <input class="form-control" id="codebare" v-model.trim="produit.barecode" type="text" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="designation">Designation</label>
                        <input class="form-control" id="designation" v-model.trim="produit.designation" type="text"
                            required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="qte">Quantité initial</label>
                        <input class="form-control" id="qte" v-model.trim="produit.qte" type="number" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="pa">Prix D'achat</label>
                        <input class="form-control" id="pa" v-model.trim="produit.pa" type="number" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="pv">Prix De Vente</label>
                        <input class="form-control" id="pv" v-model.trim="produit.pv" type="number" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="tva">TVA</label>
                        <input class="form-control" id="tva" v-model.trim="produit.tva" type="number">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 d-flex">
                        <div class="form-floating pb-4 mt-4" style="width: 75%!important;">
                            <v-select placeholder="Type de produit" v-model="produit.type_id" :options="optionType"
                                :reduce="type_name => type_name.id" label="type_name"></v-select>
                        </div>
                        <div>
                            <button style=" 
                            --bs-btn-padding-y: .39em;
                            --bs-btn-padding-x: .7em;
                            margin-top: 24px;
                            margin-left: 9px;" type="button" class="btn btn-sm btn-primary" @click="openDialogType"><i
                                    class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-6 d-flex">
                        <div class="form-floating pb-4 mt-4" style="width: 75%!important;">
                            <v-select placeholder="Famille de produit" v-model="produit.category_id"
                                :options="optionCategory" :reduce="designation => designation.id" label="designation">
                            </v-select>
                        </div>
                        <div>
                            <button style="
                            --bs-btn-padding-y: .39em;
                            --bs-btn-padding-x: .7em;
                            margin-top: 24px;
                            margin-left: 9px;" type="button" class="btn btn-sm btn-primary"
                                @click="openDialogFamille"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Editer" type="submit" class="btn btn-primary">
            </div>
        </form>
    </Dialog>
    <!-- Dialog edit produit close -->

    <!-- Dialog create type produit open -->
    <Dialog v-model:visible="typeDialog" :style="{ width: '600px' }" header="Type produit" :modal="true"
        class="p-fluid">
        <form @submit="storeType" enctype="multipart/form-data">
            <div class="field">
                <label for="type_name">Designation</label>
                <input class="form-control" id="type_name" v-model.trim="type.type_name" type="text" required>
            </div>
            <div class="field">
                <label for="type_name">Type produit</label>
                <Checkbox class="form-control" id="type_name" v-model="type.stock_touch" :binary="true" />
            </div>

            <div class="form-group m-4 d-flex justify-content-end">
                <input value="Ajouter" type="submit" class="btn btn-primary">
            </div>
        </form>
    </Dialog>
    <!-- Dialog create type produit close -->

    <!-- Dialog create type produit open -->
    <Dialog v-model:visible="familleDialog" :style="{ width: '600px' }" header="Famille produit" :modal="true"
        class="p-fluid">
        <form @submit="storeFamille" enctype="multipart/form-data">
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
                <input value="Ajouter" type="submit" class="btn btn-primary">
            </div>
        </form>
    </Dialog>
    <!-- Dialog create type produit close -->

</template>

<script>
import Dialog from 'primevue/dialog';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import ProgressSpinner from 'primevue/progressspinner';
import Checkbox from 'primevue/checkbox';
export default {
    components: {
        Dialog,
        Loading,
        ProgressSpinner,
        Checkbox,
    },
    data() {
        return {
            form: {
                user_id: null,
                category_id: null,
                type_id: null,
                barecode: null,
                designation: null,
                qte: null,
                pa: null,
                pv: null,
                image: null,
                tva: null,
            },
            type: {
                type_name: null,
                stock_touch: false,
            },
            famille: {
                designation: null,
                description: null,
            },
            showEditProduitDialog: false,
            typeDialog: false,
            familleDialog: false,
            pagination: {},
            produits: [],
            produit: null,
            spinner: true,
            loading: true,
            showCreateProduitDialog: false,
            showDeleteButton: false,
            showEditButton: false,
            createDialog: false,
            showEditDialog: false,
            optionType: [],
            optionCategory: [],
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

            // get api/employee/categories-select
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.get('api/employee/categories-select', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.optionCategory.push({ 'id': response[index].id, 'designation': response[index].designation, })
                    }
                })
                .catch(err => {
                    console.log(err);
                })
            // get api/employee/types-select
            axios.get('api/employee/types-select', { headers: headers })
                .then(res => {
                    let response = res.data[0]
                    for (let index = 0; index < response.length; index++) {
                        this.optionType.push({ 'id': response[index].id, 'type_name': response[index].type_name, })
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
            const elements = document.querySelectorAll('.produit');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            this.form.category_id = null
            this.form.type_id = null
            this.form.barecode = null
            this.form.designation = null
            this.form.qte = null
            this.form.pa = null
            this.form.pv = null
            this.form.image = null
            this.form.tva = null
            this.spinner = true
            this.produit = null
            page = page || 'api/employee/products'
            var self = this
            let token = this.$store.state.token

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.get(page, { headers: headers, })
                .then(response => {
                    self.showEditButton = false
                    self.showDeleteButton = false
                    let res = response.data[0]
                    self.produits = res.data;
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

        showAction(produit) {
            this.showEditButton = true
            this.showDeleteButton = true
            let id = produit.id
            this.produit = produit
            const elements = document.querySelectorAll('.produit');
            elements.forEach(el => {
                el.removeAttribute('class');
            });
            var element = document.getElementById('produit' + id);
            element.classList.add("produit");
            element.classList.add("table-dark");
            element.classList.add("font-weight-bold");
        },
        openDialogProduit() {
            this.showCreateProduitDialog = true
        },
        openDialogEditProduit() {
            this.showEditProduitDialog = true
        },
        openDialogType() {
            this.typeDialog = true
        },
        openDialogFamille() {
            this.familleDialog = true
        },
        storeProduit(e) {
            e.preventDefault();
            this.spinner = true
            this.loading = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/products', this.form, {
                headers: headers,
            })
                .then(res => {
                    self.form.category_id = null;
                    self.form.type_id = null;
                    self.form.barecode = null;
                    self.form.designation = null;
                    self.form.pa = null;
                    self.form.pv = null;
                    self.form.image = null;
                    self.form.tva = null;
                    self.showCreateProduitDialog = false;
                    Notification.success();
                    self.getResults();
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })
        },
        editProduit(e) {
            e.preventDefault();
            this.spinner = true
            this.loading = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.put('/api/employee/products/' + this.produit.id, this.produit, {
                headers: headers,
            })
                .then(res => {
                    self.showEditProduitDialog = false;
                    Notification.success();
                    self.getResults();
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })
        },
        storeType(e) {
            e.preventDefault();
            this.loading = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/types', {
                'type_name': self.type.type_name,
                'stock_touch': self.type.stock_touch,
                'user_id': self.form.user_id,
            }, {
                headers: headers,
            })
                .then(res => {
                    this.loading = false
                    self.typeDialog = false,
                        self.optionType.push({ 'id': res.data.id, 'type_name': res.data.type_name, })
                    self.type.type_name = null
                    self.type.stock_touch = null
                    Notification.success();
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })
        },
        storeFamille(e) {
            e.preventDefault();
            this.spinner = true
            var self = this
            let token = this.$store.state.token
            const headers = {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
            axios.post('/api/employee/categories', {
                'designation': self.famille.designation,
                'description': self.famille.description,
                'user_id': self.form.user_id
            }, {
                headers: headers,
            })
                .then(res => {
                    self.famille.designation = null;
                    self.famille.description = null;
                    self.familleDialog = false;
                    self.optionCategory.push({ 'id': res.data.id, 'designation': res.data.designation, })
                    Notification.success();
                })
                .catch(err => {
                    Notification.error();
                    console.log(err);
                })

        },
        editerDialog() {
            this.showEditDialog = true
        },
        deleteProduit() {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer!'
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
                    axios.delete('api/employee/products/' + self.produit.id, { headers: headers })
                        .then(res => {
                            if (res.data.success) {
                                Notification.success();
                                self.getResults();
                                Swal.fire(
                                    'Supprimé!',
                                    'Le produit a été supprimé.',
                                    'success'
                                )
                            } else {
                                this.spinner = false
                                this.loading = false
                                Swal.fire(
                                    'Attention !',
                                    'Vous ne pouvez pas supprimer un produit non solder',
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
    },
}
</script>

<style>

</style>