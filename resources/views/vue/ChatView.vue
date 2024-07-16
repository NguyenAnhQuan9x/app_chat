<script setup>
    import SideBarLeft from "./components/SideBarLeft.vue";
    import Swal from "sweetalert2";
    import moment from 'moment';
    import './assets/scss/chat.css'
    import { ref, onMounted, reactive } from "vue"
    import { httpService } from "./services/app-service";
    import { useRouter } from "vue-router";
    import $ from "jquery"
    const router = useRouter()
    const conversation = reactive({})
    const message_search_result = reactive({})
    const activePage = ref(0)
    const conversations = reactive({})
    const chat_id = ref(0)
    const messageRecover = ref([])
    const messageDelete = ref([])
    const messages = reactive({})
    const message = reactive({})
    const messageReply = reactive({})
    const messageForm = reactive({
        'user': '',
        'msg_content': '',
        'conversation_id': ''
    })
    const user = reactive({
    })
    const user_search = reactive({})
    const newMessages = ref([])
    const conversationForm = reactive({
        'user_id': ''
    })
    const name = ref(null)
    const search_result_check = ref(1)
    const conversation_name = ref(null)
    const keyword_message = ref(null)
    const addUserGroup = ref([])
    const groupForm = reactive({
        'title': '',
        'user_id': ''
    })
    const notiCreateGroup = ref(null)

    onMounted(() => {
        httpService.getConversation()
            .then(res => {
                conversations.value = res.data.data
                //console.log(res.data)
            }).catch(err => {

            })
        httpService.getUser()
            .then(res => {
                const data = res.data.data
                if (data) {
                    user.value = data
                }
            }).catch(err => {

            })
        //Ở lại cuộc trò chuyện sau khi tải lại trang
        if (router.currentRoute.value.params.conversation_id) {
            chat_id.value = router.currentRoute.value.params.conversation_id
            httpService.getConversationDetail(chat_id.value)
                .then(res => {
                    conversation.value = res.data.data
                    newMessageChannel()
                    //console.log(conversation.value)
                    activePage.value = chat_id.value
                    scrollToBottom()
                }).catch(err => {

                })
            getMessage()
            readMessage()
        }
        httpService.channelMessage(chat_id.value)
        .then(res=>{
            httpService.getMessage(chat_id.value)
                .then(res => {
                    messages.value = res.data.data
                    //console.log(res.data)
                }).catch(err => {

                })
        })
    })

    
    //Lấy thông tin chi tiết cuộc trò chuyêện
    function chatConversation(conversation_id) {
        router.push({ name: 'conversation', params: { conversation_id: conversation_id } })
        chat_id.value = conversation_id
        httpService.getConversationDetail(conversation_id)
            .then(res => {
                conversation.value = res.data.data
                //console.log(conversation.value)
                newMessageChannel()
                activePage.value = conversation_id
                scrollToBottom()
            }).catch(err => {

            })
        getMessage()
        readMessage()
    }
    function newMessageChannel(){
        window.Echo.private('conversation-'+chat_id.value)
                    .listen('NewChatMessage',(e)=>{
                    console.log('listen event')
                    newMessages.value.push(e.chatMessage) ;
                    console.log(e);
                    console.log(newMessages.value);
                    })
    }
    //show/hide search result in conversation
    function showSearchResult(){
        $('#conversation').toggleClass('show-search-result')
        //console.log('search')
    }
    //Lấy tin nhắn
    function getMessage()
    {
        httpService.getMessage(chat_id.value)
                .then(res => {
                    messages.value = res.data.data
                    //console.log(res.data)
                }).catch(err => {

                })
    }
    //Xác nhận tin nhắn đã đọc
    function readMessage()
    {
        httpService.readMessage(chat_id.value)
                .then(res => {
                    //console.log(res.data.message)
                }).catch(err => {

                })
    }
    //Gưi tin nhắn
    function sendMessage() {
        messageForm.user = user.value.id
        messageForm.conversation_id = chat_id.value
        if (messageReply.value) {
            httpService.sendMessageReplay(messageForm, chat_id.value, messageReply.value.id)
                .then(res => {
                    scrollToBottom()
                    closeReply()
                    //console.log('messsage sent')
                    //window.location.reload()
                }).catch(err => {

                })
                
        } else {
            httpService.sendMessage(messageForm, chat_id.value)
                .then(res => {
                    message.value = res.data.data
                    scrollToBottom()
                    //window.location.reload()
                }).catch(err => {

                })
        }

    }
    function scrollToBottom()
    {
        $('.chat-body').animate({scrollTop:($('.message-day').height()+150)},100)
       
    }
    function scrollToEle(message_id)
    {

        $('.chat-body').animate({
            scrollTop:($('#message-pos-'+message_id).offset().top-($(window).height()/2)+$('.chat-body').scrollTop()+10)
        },100)
        search_result_check.value = message_id
        //$('#message-pos-'+message_id).css({"border":"2px solid var(--extra-color)","box-shadow":"0px 1px 8px 0px gray"})
    }

    //trả lời tin nhắn
    function replyMessage(message_id) {
        httpService.getMessageDetail(message_id)
            .then(res => {
                messageReply.value = res.data.data
                //console.log(messageReply.value)
            }).catch(err => {

            })
    }
    function closeReply() {
        messageReply.value = ''
    }
    //Xóa tin nhắn
    function removeMyMessage(message_id) {
        httpService.deleteMessage(message_id)
            .then(res => {
                messageDelete.value.push(parseInt(res.data.data))
            }).catch(err => {

            })
    }
    function recoverMyMessage(message_id) {
        httpService.recoverMessage(message_id)
            .then(res => {
                if (res.data.status) {
                    messageRecover.value.push(parseInt(res.data.data))
                    //console.log(messageRecover.value)
                }
            }).catch(err => {

            })
    }
    //Xóa cuộc trò chuyện
    function removeConversation(conversation_id) {
        Swal.fire({
            icon: 'question',
            html: `
            <div>
                <p>Bạn có chắc chắn muốn xoá không?</p>
            </div>
            `,
            title: 'Xóa cuộc trò chuyện',
            showConfirmButton: true,
            showCancelButton: true
        }).then(res => {
            if (res.isConfirmed) {
                httpService.removeConversation(conversation_id)
                    .then(res => {
                        if (res.data.status) {
                            router.push({ name: 'home' })
                            Swal.fire('Bạn đã xóa thành công',
                                'You clicked the button!',
                                'success').then(result => {
                                    window.location.reload()
                                })
                        }
                    }).catch(err => {

                    })
            }
        })

    }

    //Xóa nội dung cuộc trò chuyện
    function removeMessageConversation(conversation_id) {
        Swal.fire({
            icon: 'question',
            html: `
            <div>
                <p>Bạn có chắc chắn muốn xoá không?</p>
            </div>
            `,
            title: 'Xóa nội dung cuộc trò chuyện',
            showConfirmButton: true,
            showCancelButton: true
        }).then(res => {
            if (res.isConfirmed) {
                httpService.removeMessageConversation(conversation_id)
                    .then(res => {
                        if (res.data.status) {
                            Swal.fire('Bạn đã xóa thành công',
                                'You clicked the button!',
                                'success').then(result => {
                                    window.location.reload()
                                })
                        }
                    }).catch(err => {

                    })
            }
        })

    }
    //Thêm cuộc trò chuyện cá nhân mới
    function Chat(user_id) {
        conversationForm.user_id = user_id
        httpService.addConversation(conversationForm, user_id)
            .then(res => {
                //console.log(res.data)
                router.push({ name: 'conversation', params: { conversation_id: res.data.data.id } })
                Swal.fire('Bạn đã tạo cuộc trò chuyện thành công',
                    'Bắt đầu chat ngay bây giờ!',
                    'success').then(result => {
                        window.location.reload()
                    })
            }).catch(err => {

            })
    }
    //Tìm kiếm cuộc trò chuyện
    function searchConversation()
    {
        httpService.searchConversation(conversation_name.value)
        .then(res=>{
            conversations.value = res.data.data
        }).catch(err=>{

        })
    }
    //Tìm kiếm tin nhắn
    function searchMessage()
    {
        httpService.searchMessage(conversation.value.id,keyword_message.value)
        .then(res=>{
            message_search_result.value = res.data.data
        }).catch(err=>{

        })
    }
    //Tìm kiếm user theo tên
    function userSearch() {
        httpService.searchUser(name.value)
            .then(res => {
                //console.log(res.data.data)
                user_search.value = res.data.data
            }).catch(err => {

            })
    }
    //Tạo group chat
    function createGroup() {
        groupForm.user_id = addUserGroup.value
        //console.log(groupForm)
        httpService.createGroup(groupForm)
            .then(res => {
                if (res => data.status) {
                    notiCreateGroup.value = res.data.status
                }
            }).catch(err => {

            })
    }
    function inviteUserGroup(conversation_id) {
        groupForm.user_id = addUserGroup.value
        //console.log(conversation_id)
        httpService.inviteUserGroup(groupForm,conversation_id)
            .then(res => {
                Swal.fire('Bạn đã thêm thành viên thành công',
                    'Bắt đầu chat ngay bây giờ!',
                    'success').then(result => {
                        window.location.reload()
                    })
            }).catch(err => {
                Swal.fire('Lỗi thêm thành viên mới',
                    'Có vẻ như bạn thêm thành viên đã có trong nhóm',
                    'error').then(result => {
                       
                    })
            })
    }
    function removeUserGroup(conversation_id, user_id) {
            Swal.fire({
            icon: 'question',
            html: `
            <div>
                <p>Bạn có chắc chắn muốn xoá thành viên này?</p>
            </div>
            `,
            title: 'Xoá thành viên khỏi nhóm',
            showConfirmButton: true,
            showCancelButton: true
        }).then(res => {
            if (res.isConfirmed) {
                httpService.removeUserGroup(conversation_id, user_id)
                    .then(res => {
                        if (res.data.status) {
                            Swal.fire('Bạn đã xóa thành công',
                                '',
                                'success').then(result => {
                                    window.location.reload()
                                })
                        }
                    }).catch(err => {

                    })
            }
        })
    }
</script>
<template>
    <SideBarLeft></SideBarLeft>
    <div class="col-3" id="conversation">
        <div class="sticky-top bg-light p-2">
            <div class="position-relative">
                <h3>Chat</h3>
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
                                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                        data-bs-target="#addContact">New chat</button>
                                </li>
                                <li>
                                    <button class="dropdown-item" type="button" role="button" data-bs-toggle="modal"
                                        data-bs-target="#createGroup1">Create Group</button>
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
                <div class="search iq-search-bar device-search mx-3 mt-2 col-7 position-absolute top-0 end-0">
                    <form action="" class="searchbox ml-4">
                        <div class="form-group has-search">
                            <a href="#" class="form-control-feedback"></a>
                            <input type="text" name="keyword" class="form-control w-80 align-content-center"
                                placeholder="Search" v-model="conversation_name" @input="event=>searchConversation()">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="mt-4 m-2 conversation-list">
            <ul class="list-group contact-list">
                <li class="contacts-item friends list-group-item mt-3" :class="{active:activePage==conversation.id}"
                    v-for="conversation in conversations.value" @click="chatConversation(conversation.id)"
                    :key="conversation.id">
                    <div class="row">
                        <div class="col-md-3 pt-2" id="conversation-avatar">
                            <img src="https://www.w3schools.com/w3images/avatar2.png" class="img-fluid rounded-circle"
                                alt="...">
                        </div>
                        <i class="fa-solid fa-circle" id="status-online"></i>
                        <div class="card-body col-md-9" v-if="conversation.type == 'private'">
                            <h6 class="fw-semibold" v-for="value in conversation.users" v-if="user.value"><span
                                    v-if="value.id != user.value.id">{{ value.name }}</span></h6>
                            <p :class="{'opacity-50':conversation.messages_count <= 0}"
                                v-if="conversation.messages.length > 0 && user.value"><span
                                    v-if="conversation.messages[0].user_id==user.value.id">Bạn:
                                </span>{{conversation.messages[0].content.slice(0,20)+'...'}}<span
                                    v-if="conversation.messages_count > 0"
                                    class="message-count mx-2">{{conversation.messages_count}}</span></p>
                        </div>
                        <div class="card-body col-md-9" v-else>
                            <h6 class="fw-semibold"><span>{{ conversation.title}}</span></h6>
                            <p :class="{'opacity-50':conversation.messages_count <= 0}"
                                v-if="conversation.messages.length > 0">
                                {{conversation.messages[0].content.slice(0,35)+'...'}}<span
                                    v-if="conversation.messages_count > 0"
                                    class="message-count mx-2">{{conversation.messages_count}}</span></p>

                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="search-result mx-2">
            <h4>Kết quả tìm kiếm</h4>
            <div class="search">
                <p>Tin nhắn</p>
            </div>
            <div class="search-result-list">
                <ul class="list-group contact-list">
                <li class="contacts-item friends list-group-item mt-3"
                    v-for="message_search in message_search_result.value" 
                    >
                    <div class="row">
                        <div class="col-md-3 pt-2" id="conversation-avatar">
                            <img src="https://www.w3schools.com/w3images/avatar2.png" class="img-fluid rounded-circle"
                                alt="...">
                        </div>
                        <i class="fa-solid fa-circle" id="status-online"></i>
                        <div href="" class="card-body col-md-9" @click="scrollToEle(message_search.id)">
                            <h6 class="fw-semibold" ><span>{{ message_search.users.name }}</span></h6>
                            <p v-html="message_search.content"></p>
                        </div>
                        
                    </div>
                </li>
            </ul>
            </div>
        </div>
    </div>
    <div class="modal modal-lg-fullscreen fade" id="createGroup1" tabindex="-1" role="dialog"
        aria-labelledby="createGroupLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-dialog-zoom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title js-title-step" id="createGroupLabel">Tạo nhóm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-0 hide-scrollbar">
                    <div class="row pt-2 hide" data-step="1" data-title="Create a New Group">

                        <div class="col-12">
                            <div class="form-group">
                                <label for="groupName">Tên nhóm</label>
                                <input type="text" class="form-control form-control-md" id="groupName"
                                    placeholder="Type group name here" v-model="groupForm.title">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Ảnh đại diện</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="profilePictureInput"
                                        accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="modal-header">
                            <h5 class="modal-title js-title-step" id="createGroupLabel">Thêm user vào group</h5>
                        </div>
                        <div class="modal-body py-0 hide-scrollbar">
                            <div class="row pt-2 hide" data-step="2" data-title="Add Group Members">
                                <div class="col-12 px-0">
                                    <!-- Search Start -->
                                    <div class="form-inline w-100 p-2 border-bottom">
                                        <div class="input-group w-100 bg-light">
                                            <input type="text"
                                                class="form-control form-control-md search border-right-0 transparent-bg pr-0"
                                                v-model="name" placeholder="Search" @input="event => userSearch()"> 
                                            <button class="border border-0" style="width: 40px;"><i
                                                    class="fa-solid fa-magnifying-glass"
                                                    @click="userSearch"></i></button>
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
                                                <div class="avatar avatar-online mr-2">
                                                    <!-- <img src="./../../public/media/tu-anh-3241-1681750589.jpg" alt=""
                                                        class="rounded-circle"> -->
                                                </div>

                                                <div class="media-body">
                                                    <h6 class="text-truncate">
                                                        <a href="#" class="text-reset">{{ user_search.name }}</a>
                                                    </h6>

                                                    <p class="text-muted mb-0">Online</p>
                                                </div>
                                                <div class="dropdown position-absolute top-0 end-0">
                                                    <input type="checkbox" :id="user_search.id" :value="user_search.id"
                                                        v-model="addUserGroup">
                                                </div>
                                            </div>
                                        </li>
                                        <!-- List Group Item End -->


                                    </ul>
                                    <!-- List Group End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted js-btn-step mr-auto" data-orientation="cancel"
                        data-bs-dismiss="modal" aria-label="Close">Huỷ</button>
                    <button class="btn btn-primary" type="button" role="button" data-bs-toggle="modal"
                        data-bs-target="#createGroup3" @click="createGroup">Tạo nhóm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-lg-fullscreen fade" id="createGroup3" tabindex="-1" role="dialog"
        aria-labelledby="createGroupLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-dialog-zoom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title js-title-step" id="createGroupLabel">Hoàn thành</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-0 hide-scrollbar">

                    <div class="row pt-2 h-100 hide" data-step="3" data-title="Finished">
                        <div class="col-12">
                            <div class="d-flex justify-content-center align-items-center flex-column h-100">
                                <div class="bg-success btn-icon rounded-circle text-light mb-3" v-if="notiCreateGroup">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div class="bg-danger btn-icon rounded-circle text-light mb-3" v-else>
                                    <i class="fa-solid fa-exclamation"></i>
                                </div>
                                <h6 v-if="notiCreateGroup">Group Created Successfully</h6>
                                <h6 v-else>Group Created Error. Please try again!</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted js-btn-step mr-auto" data-orientation="cancel"
                        data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" class="btn btn-primary js-btn-step" data-orientation="cancel"
                        data-bs-dismiss="modal" aria-label="Close">Hoàn thành</button>
                </div>
            </div>
        </div>
    </div>
    <div class="chat col p-0" v-if="chat_id != 0">
        <div class="chat-body">
            <div class="chat-header sticky-top border border-1 bg-light">
                <div class="row m-2">
                    <div class="col-md-1" id="conversation-avatar">
                        <img src="https://www.w3schools.com/w3images/avatar2.png" class="img-fluid rounded-circle"
                            alt="...">
                    </div>
                    <div class="card-body col-md-10" v-if="conversation.value">
                        <div v-if="conversation.value.type == 'private'">
                            <h6 class="fw-semibold" v-for="value in conversation.value.users" v-if="user.value"><span
                                v-if="value.id != user.value.id">{{ value.name }}</span></h6>
                            <p class="opacity-50">Online</p>
                        </div>
                        <div v-else>
                            <h6 class="fw-semibold"><span>{{ conversation.value.title }}</span></h6>
                            <p>{{conversation.value.users_count}} thành viên </p>
                        </div>
                    </div>
                </div>
                <ul class="nav flex-nowrap position-absolute top-0 end-0 m-3">
                    <li class="nav-item list-inline-item mt-2" @click="showSearchResult()">
                        <p data-bs-toggle="collapse" data-bs-target="#searchCollapse" aria-expanded="false"
                            aria-controls="collapseExample" role="button">
                            <i class="fa fa-magnifying-glass"></i>
                        </p>
                    </li>
                    <li class="nav-item list-inline-item mt-2" v-if="conversation.value">
                        <button v-if="conversation.value.type == 'group'" class="dropdown-item" type="button" role="button" data-bs-toggle="modal"
                            data-bs-target="#addUserGroup"><i class="fa-solid fa-users"><span
                                    class="add-user-group">+</span></i></button>
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
                                    <button class="dropdown-item" type="button">Thông tin</button>
                                </li>
                                <li>
                                    <button class="dropdown-item" type="button"
                                        @click="removeMessageConversation(conversation.value.id)">Xóa nội dung cuộc trò
                                        chuyện</button>
                                </li>
                                <li>
                                    <button class="dropdown-item" type="button"
                                        @click="removeConversation(conversation.value.id)">Xóa cuộc trò chuyện</button>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
                
                <div id="searchCollapse" class="collapse border border-1">
                    <div class="container-xl py-2 px-0 px-md-3">
                        <div class="search iq-search-bar device-search mx-3 mt-2 row">
                            <form action="" class="searchbox ml-4 col-10">
                                <div class="form-group has-search">
                                    <a href="#" class="form-control-feedback"></a>
                                    <input type="text" name="keyword_message" v-model="keyword_message" class="form-control w-80 align-content-center"
                                        placeholder="Search" @input="event=>searchMessage()">
                                </div>
                            </form>
                            <div class="col-1" @click="showSearchResult()">
                                <button class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#searchCollapse" aria-expanded="false"
                            aria-controls="collapseExample" >Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
            <div class="modal modal-lg-fullscreen fade" id="addUserGroup" tabindex="-1" role="dialog"
                            aria-labelledby="createGroupLabel" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-dialog-zoom">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title js-title-step" id="createGroupLabel">Thêm thành viên</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-0 hide-scrollbar">
                                        <div class="row pt-2 hide" data-step="1" data-title="Create a New Group">
                                            <div class="modal-header">
                                                <h5 class="modal-title js-title-step" id="createGroupLabel">Nhập tên thanh viên</h5>
                                            </div>
                                            <div class="modal-body py-0 hide-scrollbar">
                                                <div class="row pt-2 hide" data-step="2" data-title="Add Group Members">
                                                    <div class="col-12 px-0">
                                                        <!-- Search Start -->
                                                        <div class="form-inline w-100 p-2 border-bottom">
                                                            <div class="input-group w-100 bg-light">
                                                                <input type="text"
                                                                    class="form-control form-control-md search border-right-0 transparent-bg pr-0"
                                                                    v-model="name" placeholder="Search" @input="event => userSearch()">
                                                                <button class="border border-0" style="width: 40px;"><i
                                                                        class="fa-solid fa-magnifying-glass"
                                                                        @click="userSearch"></i></button>
                                                            </div>
                                                        </div>
                                                        <!-- Search End -->
                                                    </div>

                                                    <div class="col-12" v-if="user_search.value">
                                                        <!-- List Group Start -->
                                                        <ul class="list-group list-group-flush">

                                                            <!-- List Group Item Start -->
                                                            <li class="list-group-item"
                                                                v-for="user_search in user_search.value">
                                                                <div class="media position-relative">
                                                                    <div class="avatar avatar-online mr-2">
                                                                        <!-- <img src="./../../public/media/tu-anh-3241-1681750589.jpg"
                                                                            alt="" class="rounded-circle"> -->
                                                                    </div>

                                                                    <div class="media-body">
                                                                        <h6 class="text-truncate">
                                                                            <a href="#" class="text-reset">{{
                                                                                user_search.name }}</a>
                                                                        </h6>

                                                                        <p class="text-muted mb-0">Online</p>
                                                                    </div>
                                                                    <div class="dropdown position-absolute top-0 end-0">
                                                                        <input type="checkbox" :id="user_search.id"
                                                                            :value="user_search.id"
                                                                            v-model="addUserGroup">
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <!-- List Group Item End -->


                                                        </ul>
                                                        <!-- List Group End -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                                        <button class="btn btn-primary" type="button" role="button"
                                            data-bs-toggle="modal" 
                                            @click="inviteUserGroup(conversation.value.id)">Thêm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
            <div class="chat-start" v-if="conversation.value">
                <div v-if="conversation.value.type == 'private'" class="" v-for="value in conversation.value.users">
                    <div v-if="user.value">
                        <div v-if="value.id != user.value.id">
                            <img class="avatar-img rounded-circle"
                                src="https://i.pinimg.com/originals/51/a3/7c/51a37c78042b19cee727c4e210d03108.jpg"
                                alt="">
                        </div>
                        <p class="fw-semibold">{{ value.name }}</p>
                    </div>
                </div>
                <div v-else class="">
                    <div>
                        <div>
                            <img class="avatar-img rounded-circle"
                                src="https://i.pinimg.com/originals/51/a3/7c/51a37c78042b19cee727c4e210d03108.jpg"
                                alt="">
                        </div>
                        <p class="fw-semibold">{{ conversation.value.title }}</p>
                    </div>
                </div>
                <p class="">
                    Các bạn đã được kết nối<br>
                    Hãy bắt đầu cuộc trò chuyện ngay bây giờ
                </p>
            </div>
            <div class="chat-content p-2" :class="{'chat-content-rl':messageReply.value}" id="messageBody">
                <div class="container">

                    <!-- Message Day Start -->
                    <div class="message-day" v-if="user.value">

                        <div class="message-divider sticky-top pb-2" data-label="Yesterday">&nbsp;</div>

                        <!-- Self Message Start -->
                        <div :class="{'message-self':user.value.id==message.user_id,'message':user.value.id!=message.user_id,'message-deleted':message.status==0&&message.user_id==user.value.id}"
                            v-for="(message,index) in messages.value">
                            <div
                                v-if="message.status==0&&message.user_id==user.value.id ||messageDelete.includes(message.id)">
                            </div>
                            <div v-else>
                                <div class="msg-date text-center" v-if="index==0">
                                    <span
                                        v-if="( new Date(moment().format('MM/DD/YYYY'))-new Date(moment(messages.value[index].created_at).format('MM/DD/YYYY')))/86400000 ==0">Hôm
                                        nay</span>
                                    <span
                                        v-else-if="( new Date(moment().format('MM/DD/YYYY'))-new Date(moment(messages.value[index].created_at).format('MM/DD/YYYY')))/86400000==1">Hôm
                                        qua</span>
                                    <span v-else>{{ moment(messages.value[index].created_at).format('MM/DD/YYYY')
                                        }}</span>
                                </div>
                                <div v-else>
                                    <div class="msg-date text-center"
                                        v-if="moment(messages.value[index].created_at).format('MM/DD/YYYY') != moment(messages.value[index-1].created_at).format('MM/DD/YYYY')">
                                        <span
                                            v-if="( new Date(moment().format('MM/DD/YYYY'))-new Date(moment(messages.value[index].created_at).format('MM/DD/YYYY')))/86400000==0">Hôm
                                            nay</span>
                                        <span
                                            v-else-if="( new Date(moment().format('MM/DD/YYYY'))-new Date(moment(messages.value[index].created_at).format('MM/DD/YYYY')))/86400000==1">Hôm
                                            qua</span>
                                        <span v-else>{{ moment(messages.value[index].created_at).format('MM/DD/YYYY')
                                            }}</span>
                                    </div>
                                </div>
                                <div class="message-wrapper" :class="{'message-user':message.user_id!=user.value.id&&messages.value[index].user_id==messages.value[index-1].user_id}">
                                    <div class="message-user-img" v-if="message.user_id!=user.value.id&&messages.value[index].user_id==messages.value[index-1].user_id">
                                        <img alt="" class="rounded-circle"
                                            src="https://i.pinimg.com/originals/51/a3/7c/51a37c78042b19cee727c4e210d03108.jpg"
                                            width="26px" height="26px">
                                    </div>
                                    <div class="message-content recover-message"
                                        v-if="message.is_published==0||messageRecover.includes(message.id)">
                                        Tin nhắn được được thu hồi
                                    </div>
                                    <div class="message-content" :class="{'message-search-marker':message.id==search_result_check}" :id="['message-pos-'+message.id]">
                                        <div class="msg-rl-content" v-if="message.reply_message">
                                            <div class="msg-rl-user">
                                                {{message.reply_message.users.name}}
                                            </div>
                                            <div class="content">
                                                {{message.reply_message.content}}
                                            </div>
                                        </div>
                                        <div class="msg-user"
                                            v-if="message.user_id != user.value.id && conversation.value.type=='group'">
                                            <span>{{ message.users.name }} </span>
                                        </div>
                                        <span>{{
                                            message.content
                                            }}</span>
                                    </div>
                                </div>
                                <div v-if="user.value.id==message.user_id &&index == messages.value.length-1"
                                    style="font-size: 12px">
                                    <span v-if="message.is_read == 1">Đã xem</span>
                                    <span v-else>Đã gửi</span>
                                </div>
                                <div class="message-options">
                                    <div class="avatar avatar-sm "
                                        v-if="user.value.id==message.user_id&&message.is_read == 1&&index == messages.value.length-1">
                                        <img alt="" class="rounded-circle"
                                            src="https://i.pinimg.com/originals/51/a3/7c/51a37c78042b19cee727c4e210d03108.jpg"
                                            width="36px" height="36px">
                                    </div>

                                    <span class="message-date">{{moment(message.created_at).format('hh:mm A')}}</span>

                                    <div class="dropdown">
                                        <a class="nav-link" href="#" data-bs-toggle="dropdown" data-bs-display="static"
                                            aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="messageOptions">
                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                <span>Copy tin nhắn</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center" href="#"
                                                @click="replyMessage(message.id)">
                                                <span>Trả lời</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center text-danger" href="#"
                                                @click="recoverMyMessage(message.id)">
                                                <span>Thu hồi</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center text-danger" href="#"
                                                @click="removeMyMessage(message.id)">
                                                <span>Xoá chỉ ở phía tôi</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="newMessages.length">
                            <div :class="{'message-self':user.value.id==newMessage.user_id,'message':user.value.id!=newMessage.user_id,'message-deleted':newMessage.status==0&&newMessage.user_id==user.value.id}"
                            v-for="newMessage in newMessages">
                            <div
                                v-if="newMessage.status==0&&newMessage.user_id==user.value.id ||messageDelete.includes(newMessage.id)">
                            </div>
                            <div v-else>
        
                                <div class="message-wrapper">
                                    <div class="message-content recover-message"
                                        v-if="newMessage.is_published==0||messageRecover.includes(newMessage.id)">
                                        Tin nhắn được được thu hồi
                                    </div>
                                    <div class="message-content" v-else>
                                        <div class="msg-rl-content" v-if="newMessage.reply_message">
                                            <div class="msg-rl-user">
                                                {{newMessage.reply_message.users.name}}
                                            </div>
                                            <div class="content">
                                                {{newMessage.reply_message.content}}
                                            </div>
                                        </div>
                                        <div class="msg-user"
                                            v-if="newMessage.user_id != user.value.id && conversation.value.type=='group'">
                                            <span>{{ newMessage.users.name }} </span>
                                        </div>
                                        <span>{{
                                            newMessage.content
                                            }}</span>
                                    </div>
                                </div>
                                <div v-if="user.value.id==newMessage.user_id"
                                    style="font-size: 12px">
                                    <span v-if="newMessage.is_read == 1">Đã xem</span>
                                    <span v-else>Đã gửi</span>
                                </div>
                                <div class="message-options">
                                    <div class="avatar avatar-sm "
                                        v-if="user.value.id==newMessage.user_id&&newMessage.is_read == 1">
                                        <img alt="" class="rounded-circle"
                                            src="https://i.pinimg.com/originals/51/a3/7c/51a37c78042b19cee727c4e210d03108.jpg"
                                            width="36px" height="36px">
                                    </div>

                                    <span class="message-date">{{moment(newMessage.created_at).format('hh:mm A')}}</span>

                                    <div class="dropdown">
                                        <a class="nav-link" href="#" data-bs-toggle="dropdown" data-bs-display="static"
                                            aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="messageOptions">
                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                <span>Copy tin nhắn</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center" href="#"
                                                @click="replyMessage(newMessage.id)">
                                                <span>Trả lời</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center text-danger" href="#"
                                                @click="recoverMyMessage(newMessage.id)">
                                                <span>Thu hồi</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center text-danger" href="#"
                                                @click="removeMyMessage(newMessage.id)">
                                                <span>Xoá chỉ ở phía tôi</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                </div>

                <!-- Scroll to finish -->
                <div class="chat-finished" id="chat-finished"></div>
            </div>
            <div class="chat-footer">
                <div class="msg-rl" v-if="messageReply.value">
                    <div class="msg-rl-content">
                        <div class="title">
                            Đang trả lời <span>{{ messageReply.value.users.name }}</span>
                        </div>
                        <div class="content">
                            {{ messageReply.value.content }}
                        </div>
                    </div>
                    <div class="btn-close-rl">
                        <button @click="closeReply()">
                            x
                        </button>
                    </div>
                </div>
                <div class="attachment">
                    <div class="dropup">
                        <a href="#" class="nav-link" data-bs-display="static" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-regular fa-square-plus"></i>
                        </a>
                        <ul class="dropdown-menu" style="">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <span>Gallery</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <span>Document</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <textarea class="form-control" id="messageInput" rows="1" placeholder="Type your message here..."
                    v-model="messageForm.msg_content"></textarea>
                <button class="btn btn-primary rounded-circle p-3 send-icon" @click="sendMessage">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="chats col" v-else>
        <div class="d-flex flex-column justify-content-center text-center h-100 w-100">
            <div class="container">
                <div class="avatar-default avatar-lg mb-2">
                    <img class="avatar-img rounded-circle"
                        src="https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?cs=srgb&dl=pexels-pixabay-220453.jpg&fm=jpg"
                        alt="">
                </div>

                <h5>Welcome, Christina!</h5>
                <p class="text-muted">Please select a chat to Start messaging.</p>
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                    data-bs-target="#addContact">
                    Start a conversation
                </button>
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
                    <div class="col-12">
                        <!-- Search Start -->
                        <div class="form-inline w-100 p-2 border-bottom">
                            <div class="input-group w-100 bg-light">
                                <input type="text"
                                    class="form-control form-control-md search border-right-0 transparent-bg pr-0"
                                    v-model="name" placeholder="Search" @input="event => userSearch()">
                                <button class="border border-0" style="width: 40px;"><i
                                        class="fa-solid fa-magnifying-glass" @click="userSearch"></i></button>
                            </div>
                        </div>
                        <!-- Search End -->
                    </div>

                    <div class="modal-body p-0 hide-scrollbar">
                        <div class="row">

                            <div class="col-12" v-if="user_search.value">
                                <!-- List Group Start -->
                                <ul class="list-group list-group-flush">

                                    <!-- List Group Item Start -->
                                    <li class="list-group-item" v-for="user_search in user_search.value">
                                        <div class="media position-relative">
                                            <div class="avatar avatar-online mr-2">
                                                <!-- <img src="./../../public/media/tu-anh-3241-1681750589.jpg" alt=""
                                                    class="rounded-circle"> -->
                                            </div>

                                            <div class="media-body">
                                                <h6 class="text-truncate">
                                                    <a href="#" class="text-reset">{{ user_search.name }}</a>
                                                </h6>

                                                <p class="text-muted mb-0">Online</p>
                                            </div>
                                            <div class="dropdown position-absolute top-0 end-0">
                                                <i class="fa-solid fa-ellipsis-vertical" id="dropdownMenuButton1"
                                                    data-bs-toggle="dropdown" aria-expanded="false"
                                                    style="cursor: pointer;"></i>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                    <li><a class="dropdown-item" href="#"
                                                            @click="Chat(user_search.id,user_search.name)">Chat</a></li>
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
        </div>

    </div>
    <div class="col-2 conversation-info border border-1 appbar m-0 p-0">
        <div class="appbar-header border border-1 m-0" v-if="chat_id  == 0">
            <svg width="35px" class="hw-20" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                viewBox="0 0 24 24" stroke="currentColor">
                <path
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            <h6 class="mb-0 mt-0">App</h6>
        </div>
        <div class="chat-infor" v-else>
            <div class="appbar-header border border-1 m-0">
                <svg width="35px" class="hw-20" fill="none" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                <h6 class="mb-0 mt-0">App</h6>
            </div>
            <div class="infor-avatar mt-2" v-if="conversation.value">
                <div v-if="conversation.value.type == 'private'">
                    <div class="" v-for="value in conversation.value.users" v-if="user.value">
                        <div v-if="value.id != user.value.id">
                            <div>
                                <img class="avatar-img rounded-circle"
                                    src="https://i.pinimg.com/originals/51/a3/7c/51a37c78042b19cee727c4e210d03108.jpg"
                                    alt="">
                            </div>
                            <p class="fw-semibold">{{ value.name }}</p>
                            <hr>
                            <div class="user-chat-infor mx-2" style="text-align: left">
                                <h6>Thông tin user</h6>
                                <span>Address:</span>
                                <p>{{value.address}}</p>
                                <span>Phone:</span>
                                <p>{{value.phone_number}}</p>
                                <span>Email:</span>
                                <p>{{value.email}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div v v-else>
                    <div class="">
                        <div>
                            <div>
                                <img class="avatar-img rounded-circle"
                                    src="https://i.pinimg.com/originals/51/a3/7c/51a37c78042b19cee727c4e210d03108.jpg"
                                    alt="">
                            </div>
                            <p><span class="fw-semibold">{{ conversation.value.title }}</span><br>
                                {{ conversation.value.users_count }} thành viên
                            </p>
                            <hr>

                            <div class="user-chat-infor mx-2" style="text-align: left">
                                <h6>Thông tin nhóm chat</h6>
                                <span>Thành viên:</span>
                                <a class="btn btn-primary" data-bs-toggle="collapse" href="#groupUser" role="button"
                                    aria-expanded="false" aria-controls="groupUser"><i
                                        class="fa-solid fa-users"></i></a>
                                <div class="position-relative collapse" id="groupUser">
                                    <ul>
                                        <li class="contacts-item friends list-group-item mt-3"
                                            v-for="user_group in conversation.value.users">
                                            <div class="row">
                                                <div class="col-md-2" id="conversation-user-avatar">
                                                    <img src="https://www.w3schools.com/w3images/avatar2.png"
                                                        class="img-fluid rounded-circle" alt="...">
                                                </div>

                                                <div class="card-body col-md-10">
                                                    <h6 class=""><span>{{ user_group.name }}</span></h6>
                                                    <p v-if="user_group.id == conversation.value.user_id">Admin</p>
                                                    <p v-else>user</p>
                                                </div>
                                                <a href="" style="width: 10px; margin-right: 35px"
                                                    class="nav-link position-absolute top-0 end-0"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </a>
                                                <div class="dropdown">
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" v-if="user.value">
                                                        <li><a class="dropdown-item" href="#"
                                                                @click="Chat(user_group.id)">Chat</a></li>
                                                        <li
                                                            v-if="user.value.id == conversation.value.user_id && conversation.value.user_id !=user_group.id">
                                                            <a class="dropdown-item" href="#"
                                                                @click="removeUserGroup(conversation.value.id,user_group.id)">Xóa
                                                                khỏi nhóm</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <hr>
                                        </li>

                                    </ul>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>