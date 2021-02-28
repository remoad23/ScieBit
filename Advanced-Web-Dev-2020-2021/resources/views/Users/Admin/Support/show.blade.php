@extends('Layout.layout')

@section('content')
    <div class="messageContainer">
        <div class="message">
            <div class="messageTitle">{{$currentProblem->title ?? ""}}</div>
            <div class="showMsgs">
                @foreach($messages as $message)
                    <div class="messageBox">
                        <div class="messageHeader">
                            @if($message->picture === null)
                                <div class="userIcon answerImg"></div>
                            @else
                                <img class="answerImg" src="{{asset('storage/'.$message->picture)}}">
                            @endif
                            <div class="messageAuthorContainer">
                                <div class="messageAuthor">{{$message->authorName . " " . $message->lastname}}</div>
                                <div class="messageDate">{{$message->created_at->format('d/m/Y H:i:s')}}</div>
                            </div>
                        </div>
                        <br>
                        <p class="messageText">{{$message->message_text}}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @if ($errors->any())
            <div class="errorContainer">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="post" action="{{ route('message.store',['id' => $currentProblem->id]) }}" class="answerMessage">
            @csrf
            <p>{{__('users.support.reply')}}</p>
            <textarea maxlength="5000" required class="unchanged {{($errors->first('messagetext') ? "formError" : "")}}"
                      id="msgInput" name="messagetext" type="text" placeholder="{{__('users.support.your_text')}}" onchange="checkValid()"></textarea>
            <input type="submit" value={{__('users.support.reply_button')}}>
        </form>
    </div>
    <script>
    function checkValid(){
        let msgInput = document.getElementById('msgInput');
        if(msgInput.classList.contains('unchanged')){

            msgInput.classList.remove('unchanged');
        }
    }

    </script>
@endsection
