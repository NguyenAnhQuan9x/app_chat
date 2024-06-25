<script setup>
import { RouterLink, RouterView } from 'vue-router'
import HelloWorld from './components/HelloWorld.vue'
import Login from "./views/auth/Login.vue";
import SideBarLeft from "./components/SideBarLeft.vue";
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';
const router = useRouter()
const today = new Date()
onMounted(()=>{
    if(localStorage.getItem('token')){
        const tokenExpiry = JSON.parse(localStorage.getItem('token'));
    // if(today.getTime() > tokenExpiry.expiry){
    //     localStorage.removeItem('token')
    // }
    }else{
        router.push({name:'auth.login'})
    }
})
console.log('quan')
</script>

<template>
    <div id="home" class="row">
        <RouterView />
    </div>
</template>

<style scoped>

</style>
