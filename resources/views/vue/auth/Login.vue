<script setup>
import {httpService} from "../services/app-service";
import {useRouter} from "vue-router"
import {onMounted,reactive,ref} from "vue"
import axios from "axios";
// import {c} from "../../../dist/assets/index-a08f2061";
const router = useRouter()
const token = reactive({})
const LoginForm = reactive({
    'email':'',
    'password':''
})
const errors = reactive({

})
const errorInvaild = ref(null)
const inputEmail = ref(null)
const today = new Date()
function loginClick(){
    httpService.login(LoginForm)
        .then(res=>{
            token.value = {
                'value':res.data.token,
                'expiry': today.getTime()+7200000
            }
            localStorage.setItem('token',JSON.stringify(token.value))
            //console.log(res.data)
            router.push({name:'home'})
        }).catch(err=>{
            const data = err.response.data
        if(data.errors){
            errors.value=data.errors
            console.log(errors.value.email)
            inputEmail.value.focus()
        }
        if (data.error){
            errors.value = null
            errorInvaild.value = data.error
        }
    })
}

</script>
<template>
    <div class="container">
        <div class="row">
            <div class="form-login">
                <h1 class="text-center m-5"><img src="logo_qni_chat.png" alt="qunichat" width="25px" height="25px">Quichat</h1>
                <div class="title text-center">
                    <p class="form-login-title">Sign in</p>
                    <p class="text-secondary">Sign in to continue to Qnichat</p>
                </div>
                    <div class="form">

                        <div class="mb-3">
                            <label for="email" class=" mb-2">Email:</label><br>
                            <input ref="inputEmail" type="text" class="form-control" :class="{'is-invalid':errors&&errors.email}" v-model="LoginForm.email" id="email" aria-describedby="emailHelp"
                                   placeholder="Email">
                            <p class="message-error" v-if="errors.value"><span v-if="errors.value.email">{{ errors.value.email[0] }}</span></p>
                        </div>
                        <div class="mb-3">
                            <label for="password" class=" mb-2">Password:</label><br>
                            <input type="password" class="form-control" :class="{'is-invalid':errors&&errors.password}" v-model="LoginForm.password" id="password" placeholder="Password">
                            <p class="message-error" v-if="errors.value"><span v-if="errors.value.password">{{ errors.value.password[0] }}</span></p>
                        </div>
                            <p class="message-error" v-if="errorInvaild">Tên email hoặc mật khẩu không đúng, vui  kiểm tra lại</p>
                        <div class="text-center"><button class="btn btn-color px-5 mb-3 w-100" @click="loginClick">Login</button></div>
                        <div id="emailHelp" class="form-text text-center mb-3">Not
                            Registered? <router-link :to="{name:'auth.register'}" class="mx-1 fw-bold"> Create an
                                Account</router-link>
                        </div>
                    </div>

            </div>
        </div>
    </div>
</template>


<style scoped>
.message-error{
    font-size: 15px;
    color: red;
}
</style>
