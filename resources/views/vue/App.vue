<script setup>
import { RouterLink, RouterView } from 'vue-router'
import HelloWorld from './components/HelloWorld.vue'
import Login from "./auth/Login.vue";
import SideBarLeft from "./components/SideBarLeft.vue";
import { useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
const router = useRouter()
const today = new Date()
const token = ref();
onMounted(()=>{
    if(localStorage.getItem('token')){
        const tokenExpiry = JSON.parse(localStorage.getItem('token'));
        token.value = tokenExpiry;
    if(today.getTime() > tokenExpiry.expiry){
        localStorage.removeItem('token')
    }
    }else{
        router.push({name:'auth.login'})
    }
})
</script>

<template>
    <div class="row" :class ="{'homepage':token,'login-page':!token}">
        <RouterView />
    </div>
</template>

<style scoped>

</style>
