<script setup>
import {httpService} from "../services/app-service";
import {useRouter} from "vue-router";
import {ref,reactive} from "vue";
const router = useRouter()
const errors = reactive({})
const token = reactive({})
const inputName = ref(null)
const LoginForm =reactive({
    'name':'',
    'email':'',
    'password':''
})
const today = new Date()
function registerClick(){
    httpService.register(LoginForm)
        .then(res=>{
            token.value = {
                'value':res.data.token,
                'expiry': today.getTime()+1000000
            }
            localStorage.setItem('token',JSON.stringify(token.value))
            router.push({name:'home'})
        }).catch(err=>{
        const data = err.response.data
        if(data.errors){
            errors.value = data.errors
            inputName.value.focus()
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
                    <p class="form-login-title">Sign up</p>
                    <p class="text-secondary">Sign up to continue to Qnichat</p>
                </div>

                    <div class="form">

                        <div class="mb-3">
                            <input ref="inputName" type="text" class="form-control" :class="{'is-invalid':errors&&errors.name}" v-model="LoginForm.name" id="name" aria-describedby="emailHelp"
                                   placeholder="Name">
                            <p class="message-error" v-if="errors.value"><span v-if="errors.value.name">{{ errors.value.name[0] }}</span></p>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" :class="{'is-invalid':errors&&errors.email}" id="email" v-model="LoginForm.email" aria-describedby="emailHelp"
                                   placeholder="Email">
                                   <p class="message-error" v-if="errors.value"><span v-if="errors.value.email">{{ errors.value.email[0] }}</span></p>

                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" :class="{'is-invalid':errors&&errors.password}" v-model="LoginForm.password" id="password" placeholder="Password">
                            <p class="message-error" v-if="errors.value"><span v-if="errors.value.password">{{ errors.value.password[0] }}</span></p>

                        </div>
                        <div class="text-center"><button type="submit" class="btn btn-color px-5 mb-3 w-100" @click="registerClick">Register</button></div>
                        <div id="emailHelp" class="form-text text-center">Already have an account? <router-link :to="{name:'auth.login'}" class="mx-1 fw-bold"> Login</router-link>
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