<template>

<div class="d-flex justify-content-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'receptions' }">Bon de reception</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Ajouter</li>
            </ol>
        </nav>
    </div>

    <div class="card p-4 w100">
        <div class="row">
            <div class="col-2">
                <button class="btn btn-success"
                style="width: 100%; --bs-btn-padding-y: 1.2rem; --bs-btn-padding-x: 2.5rem; --bs-btn-font-size: 1.5rem;"
                ><i class="fa-solid fa-check"></i> Valide</button>
            </div>
            <div class="col-2">
                <button class="btn btn-dark"
                style="width: 100%; --bs-btn-padding-y: 1.2rem; --bs-btn-padding-x: 2.5rem; --bs-btn-font-size: 1.5rem;"
                ><i class="fa-solid fa-print"></i> Imp</button>
            </div>
            <div class="col-2">
                <button class="btn btn-dark"
                style="width: 100%; --bs-btn-padding-y: 1.2rem; --bs-btn-padding-x: 2.5rem; --bs-btn-font-size: 1.5rem;"
                ><i class="fa-regular fa-file-excel"></i> Export</button>
            </div>
            <div class="col-2">
                <button class="btn btn-dark"
                style="width: 100%; --bs-btn-padding-y: 1.2rem; --bs-btn-padding-x: 2.5rem; --bs-btn-font-size: 1.5rem;"
                ><i class="fa-solid fa-trash-can-arrow-up me-2"></i> Vider</button>
            </div>
            <div class="col-2">
                <button class="btn btn-dark"
                style="width: 100%; --bs-btn-padding-y: 1.2rem; --bs-btn-padding-x: 2.5rem; --bs-btn-font-size: 1.5rem;"
                ><i class="fas fa-dolly"></i> Fourn </button>
            </div>
            <div class="col-2">
                <button class="btn btn-dark"
                style="width: 100%; --bs-btn-padding-y: 1.2rem; --bs-btn-padding-x: 2.5rem; --bs-btn-font-size: 1.5rem;"
                ><i class="fa-solid fa-power-off "></i> Quitter</button>
            </div>
        </div>
    </div>

</template>

<script>
</script>

<style>
</style>