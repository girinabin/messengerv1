<template>
<div class="row">
    <div class="col-md-8">
        <div class="card ">
            <div class="card-header">
                Messages
            </div>
            <div class="card-body">
                <ul style="list-style:none;height:300px;overflow-y:scroll" v-chat-scroll>
                   
                    <li class="p-2 m-1"  v-for="(message,index) in messages" :key="index">
                        <strong> {{  message.user.name }}</strong>
                      
                       {{ message.message }}
                    </li>
                </ul>
            <span class="text-muted" v-if="onlineUser" >{{onlineUser.name}} typing...</span>

                <input 
                @keydown="sendTyping"
                @keyup.enter="sendMessage"
                v-model="message"
                type="text"
                class="form-control"
                name="message"
                placeholder="Type your Message.."
                 >
            </div>

        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Online Users
            </div>
            <div class="card-body">
                <ul>
                    <li class="pt-2 mt-1" style="list-style-type:circle; color:green;" v-for="(user,index) in users" :key="index">{{user.name}}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</template>
<script>
export default {
    props:['user'],
    data(){
        return{
            users:[],
            messages:[],
            message:'',
            onlineUser:false,
            timer:false
        }
    },
    created(){
        this.getMessages();
        Echo.join('chat')
        .here(user =>{
            this.users = user;
        })
        .joining(user =>{
            this.users.push(user);
        })
        .leaving(user => {
            
            this.users = this.users.filter(usr => usr.id != user.id)
        })
        .listen('MessageSentEvent',(event)=>{
            this.messages.push(event.chatmessage);
        })
        .listenForWhisper('typing', response=> {
            this.onlineUser = response
            if(this.timer){
                clearTimeout(this.timer)
            }
            this.timer = setTimeout(()=>{
                this.onlineUser = false
            },4000)
        })
    },
    methods:{
        getMessages(){

            axios.get('messages')
            .then(res=>{
                this.messages = res.data
            })
        },
        sendMessage(){
            this.messages.push({
                user:this.user,
                message:this.message
            })
            axios.post('messages',{message:this.message});
            this.message = ''
        },

        sendTyping(){
            Echo.join('chat')
            .whisper('typing',this.user);
            
        }
        
    }
    
}
</script>