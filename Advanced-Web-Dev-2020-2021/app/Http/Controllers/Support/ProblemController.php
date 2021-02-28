<?php

namespace App\Http\Controllers\Support;

use App\Models\Admin;
use App\Models\Message;
use App\Models\Problem;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProblemController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * shows all Problems listed
     */
    public function index($pagination = 0)
    {
        $problems = Problem::skip( 8 * $pagination)
            ->take(8)
            ->join('user', 'problem.author', '=', 'user.id')
            ->select('problem.*', 'user.name', 'user.lastname')
            ->get();


        return View('Users.Admin.Support.index',['problems' => $problems,'pagination' => $pagination]);
    }


    /**
     * Shows a Problem intified by the Problem's id
     */
    public function show($id)
    {

        $problem = Problem::find($id);

        $user = User::where(['id' => session()->get('id') , 'user-token' => session()->get('_token')])->first() ??
            Admin::where(['id' => session()->get('id') , 'admin-token' => session()->get('_token')])->first();

        $messages = Message::where(['problem_id' => $id])->get();

        /*foreach($messages as $message){
            if($message->writer_user != null){
                $message = Message::where(['message.id' => $message->id])
                    ->join('user', 'message.writer_user', '=', 'user.id')
                    ->select('message.message_text', 'user.name', 'user.lastname','user.picture')
                    ->get();
            }
            else{
                $message = Message::where(['message.id' => $message->id])
                    ->join('admin', 'message.writer_admin', '=', 'admin.id')
                    ->select('message.message_text', 'admin.name', 'admin.lastname','admin.picture')
                    ->get();
            }
        }*/

        foreach($messages as $message){
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

        /*if($user instanceof User)
        {
            $messages = Message::where(['problem_id' => $id])
                ->join('user', 'message.writer_user', '=', 'user.id')
                ->select('message.message_text', 'user.name', 'user.lastname','user.picture')
                ->get();
        }
        else
        {
            $messages = Message::where(['problem_id' => $id])
                ->join('admin', 'message.writer_admin', '=', 'admin.id')
                ->select('message.message_text', 'admin.name', 'admin.lastname','admin.picture')
                ->get();

        }*/

        $problemauthor = User::select('name','lastname')
            ->where(['id' => $problem->author])
            ->first();

        $problem->authorName = $problemauthor->name;
        $problem->authorLastName = $problemauthor->lastname;

        return View('Users.Admin.Support.show',['currentProblem' => $problem,'messages' => $messages]);
    }

    /**
     * stores a Problem
     */
    public function store(Request $request)
    {

        $request->validate([
                'problemtext' => 'required|min:1|max:5000',
            ]);

        $problem = new Problem();
        $problem->title = $request->title;
        $problem->problemtext = $request->problemtext;
        $problem->author = session()->get('id');

        $problem->save();

        $user = User::where(['id' => session()->get('id') , 'user-token' => session()->get('_token')])->first() ??
            Admin::where(['id' => session()->get('id') , 'admin-token' => session()->get('_token')])->first();

        $message = new Message();
        $message->problem_id = $problem->id;
        $message->message_text = $request->problemtext;

        if($user instanceof User)
        {
            $message->writer_user = session()->get('id');
        }
        else
        {
            $message->writer_admin = session()->get('id');
        }

        $message->save();

        return view('Users.User.Help.index');
    }

    /**
     * deletes a Problem once its solved
     */
    public function delete($id)
    {
        $problem = Problem::where('id',$id)->first();
        $problem->delete();
        return redirect()->back();
    }
}
