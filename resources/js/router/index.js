import { createRouter, createWebHistory } from 'vue-router'

import Login from '../components/auth/Login.vue';
import Logout from '../components/auth/Logout.vue';

import Dashboard from '../components/pages/Dashboard.vue';

import Home from '../components/pages/ui/Home.vue';
import Err403 from '../components/pages/ui/Err403.vue';
// produits et familles
import ProduitIndex from '../components/pages/ui/produits/ProduitIndex.vue';
import Familles from '../components/pages/ui/produits/Familles.vue';
// tresorerie
import CaisseIndex from '../components/pages/ui/caisses/CaisseIndex.vue';
import Operations from '../components/pages/ui/caisses/Operations.vue';
// sources
import SourceIndex from '../components/pages/ui/sources/SourceIndex.vue';
// charges
import ChargeIndex from '../components/pages/ui/charges/ChargeIndex.vue';
// clients
import ClientIndex from '../components/pages/ui/clients/ClientIndex.vue';
// achats
import Reception from '../components/pages/ui/achats/Reception.vue';
import RetourAchat from '../components/pages/ui/achats/RetourAchat.vue';
import FaireAchat from '../components/pages/ui/achats/FaireAchat.vue';
// ventes
import Livraisons from '../components/pages/ui/ventes/Livraisons.vue';
import Journal from '../components/pages/ui/ventes/Journal.vue';
const routes = [
    // routes auth
    { path: '/login', component: Login, name:'login'},
    { path: '/logout', component: Logout, name:'logout'},

    {
        path: '/dashboard',
        name:'dashboard',
        component: Dashboard,
        children:[
            {path: '/home', component: Home, name: 'home'},
            {path: '/403', component: Err403, name: '403'},
            // produit routes
            {path: '/produits', component: ProduitIndex, name: 'produits'},
            {path: '/familles', component: Familles, name: 'familles'},
            // tresorerie routes
            {path: '/caisses', component: CaisseIndex, name: 'caisses'},
            {path: '/operations', component: Operations, name: 'operations'},
            // sources routes
            {path: '/sources', component: SourceIndex, name: 'sources'},
            // charges routes 
            {path: '/charges', component: ChargeIndex, name: 'charges'},
            // clients routes 
            {path: '/clients', component: ClientIndex, name: 'clients'},
            // achats routes 
            {path: '/receptions', component: Reception, name: 'receptions'},
            {path: '/retour-achat', component: RetourAchat, name: 'achat.retour'},
            {path: '/ajouter-achat', component: FaireAchat, name: 'achat.ajouter'},
            //ventes route
            {path: '/ventes', component: Livraisons, name: 'ventes'},
            {path: '/journal', component: Journal, name: 'journal'},
        ]
    },
];
export default createRouter({
    history: createWebHistory(),
    routes
})