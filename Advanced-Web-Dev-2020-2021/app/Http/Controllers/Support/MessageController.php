<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\{Admin, File, Message, Notification, Problem, User};
use Illuminate\Http\Request;

class MessageController extends Controller
{

    public function index()
    {

        return View('Users.Admin.Support.index');
    }



    public function show()
    {

    }

    /**
     * stores a message for a problem
     */
    public function store($id,Request $request)
    {
        $user = User::where(['id' => session()->get('id') , 'user-token' => session()->get('_token')])->first() ??
            Admin::where(['id' => session()->get('id') , 'admin-token' => session()->get('_token')])->first();

        $message = new Message();
        $message->problem_id = $id;
        $message->message_text = $request->messagetext;

        if($user instanceof User)
        {
            $message->writer_user = session()->get('id');
        }
        else
        {
            $message->writer_admin = session()->get('id');
        }

        $message->save();

        $problem = Problem::where(['id' => $id])->get()->first();
        $problemauthor = User::select('name','lastname')
            ->where(['id' => $problem->author])
            ->first();

        $problem->authorName = $problemauthor->name;
        $problem->authorLastName = $problemauthor->lastname;

        /*$notification = new Notification();
        $notification->problem_id = $problem->id;
        $notification->type = "message";

        if($user instanceof User)
        {
            $notification->user_id = session()->get('id');
        }
        else
        {
            $notification->admin_id = session()->get('id');
        }
        $notification->save();*/

        $massMassages = Message::where(['problem_id'=> $problem->id])->get();


        $userID = [];
        $adminID = [];

        // Send to each user,who has sent something in this problem a notification
        foreach($massMassages as $msg)
        {
            //making sure notification is not sent twice to a user
            if(!in_array($msg->writer_user,$userID)  && $msg->writer_user !== null)
            {
                if(!($user instanceof User && $msg->writer_user === $user->id)){
                    $userID[] += ($msg->writer_user);

                    $notification = new Notification();
                    $notification->problem_id = $problem->id;
                    $notification->message_id = $message->id;
                    $notification->user_id = $msg->writer_user;
                    $notification->type = "message";
                    $notification->save();
                }

            }
            //making sure notification is not sent twice to a admin
            if(!in_array($msg->writer_admin,$adminID)  && $msg->writer_admin !== null)
            {
                if(!($user instanceof Admin && $msg->writer_admin === $user->id)){
                    $adminID[] += ($msg->writer_admin);

                    $notification = new Notification();
                    $notification->problem_id = $problem->id;
                    $notification->message_id = $message->id;
                    $notification->admin_id = $msg->writer_admin;
                    $notification->type = "message";
                    $notification->save();
                }
            }
        }


        $loaded_messages = Message::where(['problem_id' => $problem->id])->get();

        foreach($loaded_messages as $message){
            if($message->writer_user != null){
                $author = User::where('id',$message->writer_user)->first();
                $message->authorName = $author->name .' '. $author->lastname;
                $message->picture = $author->picture;
            }
            else{
                $author = Admin::where('id',$message->writer_admin)->first();
                $message->authorName = $author->name .' '. $author->lastname;
                $message->picture = $author->picture;
            }
        }

        return View('Users.Admin.Support.show',[
            'problem' => $problem->id,
            'messages' => $loaded_messages,
            'currentProblem' => $problem,
            ]);
    }

    /**
     * deletes a message of a problem
     */
    public function delete()
    {

    }
}
