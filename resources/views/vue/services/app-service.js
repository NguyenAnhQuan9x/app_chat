import axios from "axios";
export const appService = axios.create({
    baseURL : import.meta.env.VITE_API_URL
})
class AppService {
    channelMessage(conversaton_id)
    {
        return appService.get('channel/conversations/'+conversaton_id,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    login(data){
        return appService.post('api/auth/login',data)
    }
    register(data)
    {
        return appService.post('api/auth/register',data)
    }
    logOut()
    {
        return appService.get('api/auth/logout',{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    getUser(){
        return appService.get('api/user',{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    editProfile(data){
        return appService.put('api/user/update',data,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    addConversation(data,user_id){
        return appService.post('api/conversations/store/'+user_id,data,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    getConversation(){
        return appService.get('api/conversations',{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    getConversationDetail(conversaton_id){
        return appService.get('api/conversations/'+conversaton_id,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    removeConversation(conversation_id){
        return appService.delete('api/conversations/delete/'+conversation_id,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    //Xóa toàn bộ tin nhắn trong cuộc trò chuyện
    removeMessageConversation(conversation_id){
        return appService.delete('api/conversations/messages/delete/'+conversation_id,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    getMessage(conversation_id){
        return appService.get('api/conversations/messages/'+conversation_id,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    getMessageDetail(message_id){
        return appService.get('api/conversations/message/'+message_id,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    sendMessage(data,conversation_id){
        return appService.post('api/conversations/message/store/'+conversation_id,data,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    sendMessageReplay(data,conversation_id,message_rl){
        return appService.post('api/conversations/message/store/'+conversation_id+'/'+message_rl,data,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    //Xác nhận đã đọc tin nhắn
    readMessage(conversation_id){
        return appService.put('api/conversations/message/read/'+conversation_id,'',{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
     //Thu hồi tin nhắn của chính mình
     recoverMessage(message_id){
        return appService.put('api/conversations/message/recover/'+message_id,'',{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    //xóa tin nhắn của chính mình
    deleteMessage(message_id){
        return appService.put('api/conversations/message/delete/'+message_id,'',{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    getContact(){
        return appService.get('api/contacts',{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    getContactDetail(user_id){
        return appService.get('api/contacts/user/'+user_id,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    addContact(data){
        return appService.post('api/contacts/create',data,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    removeContact(contact_id){
        return appService.delete('api/contacts/delete/'+contact_id,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    searchUser(data){
        return appService.get('api/user/search?keyword='+data,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    createGroup(data){
        return appService.post('api/conversations/group/store',data,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    inviteUserGroup(data,conversations_id){
        return appService.post('api/conversations/group/member/add/'+conversations_id,data,{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }
    removeUserGroup(conversation_id,user_id){
        return appService.post('api/conversations/group/member/delete/'+conversation_id+'/'+user_id,'',{
            headers:{
                Authorization:'Bearer '+ JSON.parse(localStorage.getItem('token')).value
            }
        })
    }

}
export const httpService = new AppService()
