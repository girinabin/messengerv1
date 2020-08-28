<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Events\MessageSentEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class MessageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        return view('messages');
    }

    public function allMessages(){
        return Chat::with('user')->get();
    }

    public function sendMessage(Request $request){
       $message =  Auth::user()->messages()->create([
            'message'=>$request->message
        ]);
        broadcast(new MessageSentEvent($message->load('user')))->toOthers();
        
        return ['status'=>'success'];

    }
}
