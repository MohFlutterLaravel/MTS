<template>
    <div id="app-main" tabindex="-1" @keyup.alt.enter="admin">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <router-link :to="{ name: 'home' }" style="color:white" id="mts" class="navbar-brand ps-3">MOD TECH SERV
            </router-link>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                    class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..."
                        aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i
                            class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="#!">
                                <i style="margin-right: 8px;" class="fas fa-cog fa-sm fa-fw mr-2 text-primary"></i>
                                Paramètre
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#!">
                                <i style="margin-right: 8px;" class="fas fa-database fa-sm fa-fw mr-2 text-primary"></i>
                                Database
                            </a>
                        </li>
                        <li>
                            <hr style="color: #f3f3f3;" class="dropdown-divider" />
                        </li>
                        <li>
                            <router-link :to="{ name: 'logout' }" class="dropdown-item">
                                <i style="margin-right: 8px;" class="fa-solid fa-power-off fa-sm fa-fw text-danger"></i>
                                Quitter
                            </router-link>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="index.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Surface</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseProduit" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-cubes"></i></div>
                                Produits
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseProduit" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <router-link :to="{ name: 'produits' }" class="nav-link"><i class="fas fa-list"></i>
                                        &nbsp List des produits</router-link>
                                    <router-link :to="{ name: 'familles' }" class="nav-link"><i class="fas fa-tags"></i>
                                        &nbsp
                                        Familles</router-link>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseStock" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-layer-group"></i></div>
                                Stock
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseStock" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html"><i class="fas fa-folder"></i> &nbsp
                                        Etat
                                        de Stock</a>
                                    <a class="nav-link" href="layout-sidenav-light.html"><i class="fas fa-eye"></i>
                                        &nbsp
                                        Mouvement</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseAchat" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-basket-shopping"></i></div>
                                Achats
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseAchat" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <router-link :to="{ name: 'receptions' }" class="nav-link">
                                        <i class="fa-solid fa-cash-register"></i>
                                        &nbsp
                                        Reception
                                    </router-link>
                                    <router-link :to="{ name: '403' }" class="nav-link">
                                        <i class="fa-solid fa-trash-can-arrow-up"></i>
                                        &nbsp
                                        Retour
                                    </router-link>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseComptoir" aria-expanded="false"
                                aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-money-bill-wave-alt"></i></div>
                                Comptoir
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseComptoir" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <router-link :to="{ name: 'ventes' }" class="nav-link"><i
                                            class="fas fa-barcode"></i>
                                        &nbsp
                                        Vente</router-link>
                                    <router-link :to="{ name: 'journal' }" class="nav-link"><i
                                            class="fas fa-file-alt"></i>
                                        &nbsp
                                        Journal</router-link>
                                </nav>
                            </div>

                            <a v-if="showadmin" class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapsetresore" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-hdd"></i></div>
                                Trésorerie
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsetresore" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <router-link :to="{ name: 'caisses' }" class="nav-link">
                                        <i class="fas fa-search-dollar"></i> &nbsp
                                        Caisses
                                    </router-link>
                                    <router-link :to="{ name: 'operations' }" class="nav-link">
                                        <i class="far fa-file-alt"></i> &nbsp
                                        Operation
                                    </router-link>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseFournis" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-dolly"></i></div>
                                Fournisseurs
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseFournis" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <router-link :to="{ name: 'sources' }" class="nav-link">
                                        <i class="fas fa-list"></i> &nbsp
                                        List fourni
                                    </router-link>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseClients" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Clients
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseClients" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <router-link :to="{ name: 'clients' }" class="nav-link">
                                        <i class="fas fa-list"></i> &nbsp
                                        List Clients
                                    </router-link>
                                </nav>
                            </div>

                            <div class="sb-sidenav-menu-heading">Technique</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseIntervention" aria-expanded="false"
                                aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-wrench"></i></div>
                                Interventions
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseIntervention" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html"><i class="fas fa-plus"></i> &nbsp
                                        Ajouter
                                    </a>
                                    <a class="nav-link" href="layout-sidenav-light.html"><i class="fas fa-check"></i>
                                        &nbsp
                                        Valider</a>
                                </nav>
                            </div>

                            <div class="sb-sidenav-menu-heading">Dépenses</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseCharge" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-chart-area"></i></div>
                                Charges
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseCharge" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <router-link :to="{ name: 'charges' }" class="nav-link">
                                        <i class="fas fa-search-dollar"></i> &nbsp Consulter
                                    </router-link>
                                </nav>
                            </div>
                            <div class="sb-sidenav-menu-heading">Addons</div>
                            <a class="nav-link" href="charts.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Statistiques
                            </a>
                            <a class="nav-link" href="tables.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Inventaire
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        {{ login != '' ? login : 'errorToken' }}
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <router-view></router-view>
                    </div>
                </main>
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
    </div>

</template>
    
<script>
export default {
    data() {
        return {
            login: '',
            showadmin: false,
        }
    },
    created() {
        if (this.$store.state.user != '') {
            this.login = this.$store.state.user
        }
        this.$router.push({ name: 'home' })
    },
    methods: {
        admin() {
            this.showadmin = !this.showadmin
            this.$router.push({ name: 'home' })
        }
    }
}
</script>
    
<style>

</style>