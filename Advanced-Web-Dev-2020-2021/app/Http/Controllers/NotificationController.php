<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Problem;
use App\Models\User;
use Illuminate\Http\Request;
use Lang;

class NotificationController extends Controller
{

    public function index($pagination)
    {

        $user = User::where(['id' => session()->get('id') , 'user-token' => session()->get('_token')])->first() ??
            Admin::where(['id' => session()->get('id') , 'admin-token' => session()->get('_token')])->first();

        $new_notifications = null;

        if($user instanceof User)
        {
            $new_notifications = Notification::where(['user_id' => $user->id])
                ->take(5)
                ->skip(5*$pagination)
                ->get();
        }
        else{
            $new_notifications = Notification::where(['admin_id' => $user->id])
                ->take(5)
                ->skip(5*$pagination)
                ->get();
        }

        $notificationsHTML = null;

        if(sizeof($new_notifications) < 1)
        {
            return;
        }
        return response()->view('Layout.layoutnotifications',['new_notifications' => $new_notifications,'pagination' => $pagination]);
    }

    public function driveIndex($id,$token,$pagination)
    {

        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $notifications = null;

        if($user instanceof User)
        {
            $notifications = Notification::where(['user_id' => $user->id])
                ->skip(5*$pagination)
                ->take(5)
                ->get();
        }
        else{
            $notifications = Notification::where(['admin_id' => $user->id])
                ->skip(5*$pagination)
                ->take(5)
                ->get();
        }

        $notificationsHTML = null;

        if(sizeof($notifications) < 1)
        {
            return;
        }

        $html = (object) ['stringHtml' => [],'idArray' => []];
        $notificationMessage = Lang::get('layout.new_message');
        $notificationShared = Lang::get('layout.new_sharedfile');
        $sharedFileRoute = route('docs.shared');
        foreach($notifications as $notification)
        {
            $notificationId = $notification->id;
            if($notification->type == 'message')
            {
                $supportShowRoute = route('support.show',['id' => $notification->problem_id]);
                $html->stringHtml[] = "<a class='notificationLink' href='{$supportShowRoute}'>
                                            <i class='addFileIcon'></i>{$notificationMessage}
                                        </a>
                                        <a class='crossIcon crossNotification'></a>";
            }
            else{
                $html->stringHtml[] = "<a class='notificationLink' href='{$sharedFileRoute}'>
                                            <i class='addFileIcon'></i>{$notificationShared}
                                        </a>
                                        <a class='crossIcon crossNotification'></a>";
            }

            $html->idArray[] = $notificationId;
        }

        return response()->json([$html]);
    }


    public function delete($id)
    {
        $notification = Notification::where(['id' => $id]);
        $notification->delete();


        return redirect()->back();
    }

    public function driveDelete($id,$token,$messageId)
    {

        $user = User::where(['id' => $id , 'user-token' => $token])->first() ??
            Admin::where(['id' => $id , 'admin-token' => $token])->first();

        $notification = Notification::where(['id' => $messageId])->first();
        if($user instanceof User){
            if($notification->user_id == $user->id){
                $notification->delete();
            }
        }
        else{
            if($notification->admin_id == $user->id){
                $notification->delete();
            }
        }

        return;
    }


    public function deleteByClick($id,$problem_id)
    {
        $notification = Notification::where(['id' => $id]);
        $notification->delete();

        $problem = Problem::find($id);

        $messages = Message::where(['problem_id' => $problem_id])->get();

        return redirect()->route('support.show', ['currentProblem' => $problem,'messages' => $messages]);
    }
}
