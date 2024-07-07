<script setup>
import SideBarLeft from "./components/SideBarLeft.vue";
import { onMounted, reactive, ref } from "vue";
import { httpService } from "./services/app-service";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
const router = useRouter()
const contacts = reactive({})
const contact = reactive({})
const contact_default = ref(0)
const activePage = ref(0)
const today = new Date()
const conversationForm = reactive({
    'user_id': ''
})
const userContact = reactive({
    'user_id':'',
    'name':'',
    'creator_id':''
})
var ampm = today.getHours() >= 12 ? "PM" : "AM"
const time = today.getHours() + ":" + today.getMinutes() + ampm
const name = ref(null)
const user_search = reactive({})
onMounted(() => {
    httpService.getContact()
        .then(res => {
            contacts.value = res.data
            console.log(contacts.value)
        }).catch(err => {

        })

    httpService.getUser()
    .then(res=>{
        const data = res.data
        if(data){
            userContact.creator_id = data.id
        }
    }).catch(err=>{

    })
})
if (router.currentRoute.value.params.user_id) {
    contact_default.value = router.currentRoute.value.params.user_id
    httpService.getContactDetail(contact_default.value)
        .then(res => {
            contact.value = res.data
            console.log(contact.value)
            activePage.value = contact_default.value
        }).catch(err => {

        })
}
function contactDetail(user_id) {
    router.push({ name: 'contact-detail', params: { user_id: user_id } })
    contact_default.value = user_id
    httpService.getContactDetail(user_id)
        .then(res => {
            contact.value = res.data
            console.log(contact.value)
            activePage.value = user_id
        }).catch(err => {

        })

}
//Tạo cuộc trò chuyện với user trong danh bạ
function createConversation($user_id) {
    conversationForm.user_id = $user_id
    httpService.addConversation(conversationForm, $user_id)
        .then(res => {
            console.log(res.data)
            router.push({ name: 'conversation', params: { conversation_id: res.data.data.id } })
        }).catch(err => {

        })
}
//Tìm kiếm user theo tên
function userSearch() {
    console.log(name.value)
    httpService.searchUser(name.value)
        .then(res => {
            console.log(res.data)
            user_search.value = res.data
        }).catch(err => {

        })
}

//Thêm user vào danh bạ
function addContact(user_id,name) {
    console.log(name)
    userContact.user_id = user_id
    userContact.name = name
    console.log(userContact)
    httpService.addContact(userContact)
        .then(res => {
            window.location.reload()
        }).catch(err => {

        })
}
//Xóa user khỏi danh bạ
function removeContact(contact_id){
httpService.removeContact(contact_id)
    .then(res=>{
        router.push({name:'contact'})
        Swal.fire('Bạn đã xóa thành công',
            'You clicked the button!',
            'success').then(result=>{
            window.location.reload()
        })
    }).catch(err=>{

})
}

</script>
<template>
    <SideBarLeft></SideBarLeft>
    <div class="col-3 border border-1" id="conversation">
        <div class="sticky-top bg-light">
            <div class="position-relative">
                <h3>Friends</h3>
                <ul class="nav flex-nowrap position-absolute top-0 end-0">
                    <li class="nav-item list-inline-item ">
                        <a class="nav-link" href=""><i class="fa-regular fa-bell"></i></a>
                    </li>
                    <li class="nav-item list-inline-item ">
                        <div class="btn-group">
                            <a class=" nav-link" href="#" data-bs-toggle="dropdown" data-bs-display="static"
                                aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg-end">
                                <li>
                                    <button class="dropdown-item" type="button">Action</button>
                                </li>
                                <li>
                                    <button class="dropdown-item" type="button">Another action</button>
                                </li>
                                <li>
                                    <button class="dropdown-item" type="button">Something else here</button>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="position-relative">
                <div class="btn-group mt-2">
                    <button type="button" class="btn border border-1 dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        All chats
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">All chats</a></li>
                        <li><a class="dropdown-item" href="#">Friends</a></li>
                        <li><a class="dropdown-item" href="#">Groups</a></li>
                    </ul>
                </div>
                <div class="search iq-search-bar device-search mx-3 mt-2 col-8 position-absolute top-0 end-0">
                    <form action="" class="searchbox ml-4">
                        <div class="form-group has-search">
                            <a href="#" class="form-control-feedback"></a>
                            <input type="text" name="keyword" class="form-control w-80 align-content-center"
                                placeholder="Search">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <ul class="list-group contact-list">
                <li class="contacts-item friends list-group-item mt-3" :class="{ active: activePage == contact.user_id }"
                    v-for="contact in contacts.value" :key="contact.id" @click="contactDetail(contact.user_id)">
                    <div class="row">
                        <div class="col-md-2" id="conversation-avatar">
                            <img src="https://www.w3schools.com/w3images/avatar2.png" class="img-fluid rounded-circle"
                                alt="...">
                        </div>
                        <div class="card-body col-md-10">
                            <h6 class="fw-semibold">{{ contact.users.name }}</h6>
                            <p class="opacity-50">{{ contact.users.address }}</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="friends px-0 py-2 p-xl-3 col-8" v-if="contact_default">
        <div class="container-xl" v-if="contact.value">
            <div class="row">
                <div class="col">
                    <div class="card card-body card-bg-1 mb-3 position-relative">
                        <div class="d-flex flex-column align-items-center">
                            <div class="avatar avatar-lg mb-3">
                                <img src="https://www.w3schools.com/w3images/avatar2.png" class="img-fluid rounded-circle"
                                    alt="..." width="100px">
                            </div>
                            <div class="d-flex flex-column align-items-center">
                                <h5 class="mb-1">{{ contact.value[0].name }}</h5>
                                <div class="d-flex mt-2">
                                    <div class="btn btn-primary btn-icon rounded-circle text-light mx-2"
                                        @click="createConversation(contact.value[0].user_id)">
                                        <i class="fa-regular fa-comment-dots"></i>
                                    </div>
                                    <div class="btn btn-success btn-icon rounded-circle text-light mx-2">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-options position-absolute top-0 end-0 me-3">
                            <div class="btn-group"><a class="nav-link" href="#" data-bs-toggle="dropdown"
                                    data-bs-display="static" aria-expanded="false"><i
                                        class="fa-solid fa-ellipsis-vertical"></i></a>
                                <ul class="dropdown-menu dropdown-menu-lg-end">
                                    <li>
                                        <button class="dropdown-item" type="button" @click="removeContact(contact.value[0].id)">Remove</button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" type="button">Block</button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row friends-info">
                <div class="col">
                    <div class="card">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="media align-items-center">
                                    <div class="media-body">
                                        <p class="small text-muted mb-0">Local Time</p>
                                        <p class="mb-0">{{ time }}</p>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="media align-items-center">
                                    <div class="media-body">
                                        <p class="small text-muted mb-0">Birthdate</p>
                                        <p class="mb-0">{{ contact.value[0].users.birthday }}</p>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="media align-items-center">
                                    <div class="media-body">
                                        <p class="small text-muted mb-0">Phone</p>
                                        <p class="mb-0">{{ contact.value[0].users.phone_number }}</p>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="media align-items-center">
                                    <div class="media-body">
                                        <p class="small text-muted mb-0">Email</p>
                                        <p class="mb-0">{{ contact.value[0].users.email }}</p>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="media align-items-center">
                                    <div class="media-body">
                                        <p class="small text-muted mb-0">Address</p>
                                        <p class="mb-0">{{ contact.value[0].users.address }}</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="chats col-8" v-else>
        <div class="d-flex flex-column justify-content-center text-center h-100 w-100">
            <div class="container">
                <div class="avatar-default avatar-lg mb-2">
                    <img class="avatar-img rounded-circle"
                        src="https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?cs=srgb&dl=pexels-pixabay-220453.jpg&fm=jpg"
                        alt="">
                </div>

                <h5>Welcome to contact</h5>
                <p class="text-muted">Please select a user to add your contact.</p>
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addContact">
                    Add Contact
                </button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addContact" tabindex="-1" aria-labelledby="staticBackdropLabel"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenteredScrollableTitle">Tìm kiếm user</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 hide-scrollbar">
                    <div class="row">
                        <div class="col-12">
                            <!-- Search Start -->
                            <div class="form-inline w-100 p-2 border-bottom">
                                <div class="input-group w-100 bg-light">
                                    <input type="text"
                                        class="form-control form-control-md search border-right-0 transparent-bg pr-0"
                                        v-model="name" placeholder="Search">
                                    <button class="border border-0" style="width: 40px;"><i
                                            class="fa-solid fa-magnifying-glass" @click="userSearch"></i></button>
                                </div>
                            </div>
                            <!-- Search End -->
                        </div>

                        <div class="col-12" v-if="user_search.value">
                            <!-- List Group Start -->
                            <ul class="list-group list-group-flush">

                                <!-- List Group Item Start -->
                                <li class="list-group-item" v-for="user_search in user_search.value">
                                    <div class="media position-relative">
                                        <!-- <div class="avatar avatar-online mr-2">
                                            <img src="./../../public/media/tu-anh-3241-1681750589.jpg" alt=""
                                                class="rounded-circle">
                                        </div> -->

                                        <div class="media-body">
                                            <h6 class="text-truncate">
                                                <a href="#" class="text-reset">{{ user_search.name }}</a>
                                            </h6>

                                            <p class="text-muted mb-0">Online</p>
                                        </div>
                                        <div class="dropdown position-absolute top-0 end-0">
                                            <i class="fa-solid fa-ellipsis-vertical" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                <li><a class="dropdown-item" href="#" @click="addContact(user_search.id,user_search.name)">Add contact</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <!-- List Group Item End -->


                            </ul>
                            <!-- List Group End -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
        </div>
    </div>
</div></template>

<style scoped></style>
