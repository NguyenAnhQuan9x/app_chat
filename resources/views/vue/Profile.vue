<script setup>
import Swal from "sweetalert2";
import moment from "moment";
import SideBarLeft from "./components/SideBarLeft.vue";
import { httpService } from "./services/app-service";
import { useRouter } from "vue-router";
import {onMounted, reactive, ref} from "vue";
import data from "bootstrap/js/src/dom/data";
const router = useRouter()
const user = reactive({
})
const today = new Date()

function logoutClick(){
    httpService.logOut()
    .then(res=>{
        localStorage.removeItem('token')
        location.reload()
    }).catch(err=>{

    })
}
function editProfile(){
    httpService.editProfile(user.value)
        .then(res=>{
            const status = res.data.status
            if(status){
                Swal.fire(
                    'Cập nhật thành công',
                    'You clicked the button!',
                    'success'
                )
            }
        })
        .catch(err=>{

        })
}
onMounted(()=>{
    httpService.getUser()
    .then(res=>{
        const data = res.data.data
        if(data){
            user.value = data
            console.log(user.value.name)
        }
    }).catch(err=>{

    })
})

</script>
<template>
    <SideBarLeft></SideBarLeft>
    <div class="tab-pane active col-3 border border-1 p-0" id="profile-content">
        <div class="d-flex flex-column h-100">
            <div class="hide-scrollbar">
                <!-- Sidebar Header Start -->
                <div class="sidebar-header bg-light sticky-top p-2 mb-3 border border-1">
                    <h5 class="font-weight-semibold">Profile</h5>
                    <p class="text-muted opacity-75 mb-0">Personal Information &amp; Settings</p>
                </div>
                <!-- Sidebar Header end -->
                <!-- Sidebar Content Start -->
                <div class="container-xl">
                    <div class="row" v-if="user.value">
                        <div class="col">

                            <!-- Card Start -->
                            <div class="card card-body card-bg-5">

                                <!-- Card Details Start -->
                                <div class="d-flex flex-column align-items-center">
                                    <div class="avatar avatar-lg mb-3">
                                        <!-- <img src="../../public/media/tu-anh-3241-1681750589.jpg"
                                             class="img-fluid rounded-circle" alt="..."> -->
                                    </div>

                                    <div class="d-flex flex-column align-items-center">
                                        <h5>{{ user.value.name }}</h5>
                                    </div>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-outline-dark" @click="logoutClick"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Logout</button>
                                    </div>

                                </div>
                                <!-- Card Details End -->


                            </div>
                            <!-- Card End -->
                            <!-- Card Start -->
                            <div class="card mt-3">

                                <!-- List Group Start -->
                                <ul class="list-group list-group-flush">

                                    <!-- List Group Item Start -->
                                    <li class="list-group-item py-2">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <p class="small text-muted mb-0">Local Time</p>
                                                <p class="mb-0">{{moment(today.getTime()).format('hh:mm A')}}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- List Group Item End -->

                                    <!-- List Group Item Start -->
                                    <li class="list-group-item py-2">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <p class="small text-muted mb-0">Birthdate</p>
                                                <p class="mb-0">{{user.value.birthday}}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- List Group Item End -->

                                    <!-- List Group Item Start -->
                                    <li class="list-group-item py-2">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <p class="small text-muted mb-0">Phone</p>
                                                <p class="mb-0">{{user.value.phone_number}}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- List Group Item End -->

                                    <!-- List Group Item Start -->
                                    <li class="list-group-item py-2">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <p class="small text-muted mb-0">Email</p>
                                                <p class="mb-0">{{ user.value.email }}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- List Group Item End -->

                                    <!-- List Group Item Start -->
                                    <li class="list-group-item pt-2">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <p class="small text-muted mb-0">Address</p>
                                                <p class="mb-0">{{
                                                        user.value.address
                                                    }}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- List Group Item End -->

                                </ul>
                                <!-- List Group End -->

                            </div>
                            <!-- Card End -->

                        </div>
                    </div>
                </div>
                <!-- Sidebar Content End -->
            </div>
        </div>
    </div>
    <div class="profile col-8 p-0 m-0">
        <div class="page-main-heading sticky-top bg-light py-2 px-3 mb-3 border border-1">
            <div class="pl-2 pl-xl-0">
                <h5 class="font-weight-semibold">Settings</h5>
                <p class="text-muted mb-0">Update Personal Information &amp; Settings</p>
            </div>
        </div>

        <div class="container-xl px-2 px-sm-3">
            <div class="row m-4">
                <div class="col">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-1">Account</h6>
                            <p class="mb-0 text-muted small">Update personal &amp; contact information</p>
                        </div>

                        <div class="card-body">
                            <div class="row" v-if="user.value">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="firstName">User Name</label>
                                        <input type="text" class="form-control form-control-md" id="firstName" v-model="user.value.name" placeholder="Type your first name" >
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mobileNumber">Mobile number</label>
                                        <input type="text" class="form-control form-control-md" id="mobileNumber" v-model="user.value.phone_number" placeholder="Type your mobile number">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="birthDate">Birth date</label>
                                        <input type="date" class="form-control form-control-md" id="birthDate"  v-model="user.value.birthday">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="emailAddress">Email address</label>
                                        <input type="email" class="form-control form-control-md" id="emailAddress" placeholder="Type your email address" v-model="user.value.email" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="Address">Address</label>
                                        <input type="text" class="form-control form-control-md" id="Address" placeholder="Type your address" v-model="user.value.address">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" @click="editProfile">Save Changes</button>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-custom-sidebar border  border-1 appbar m-0 p-0">
        <div class="appbar-header border border-1 m-0 sticky-top bg-light">
            <svg width="25px" class="hw-20" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            <h6 class="mb-0 mt-0">App</h6>
        </div>
    </div>
</template>


<style scoped>

</style>
