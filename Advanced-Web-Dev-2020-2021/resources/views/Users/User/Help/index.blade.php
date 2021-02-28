@extends('Layout.layout')

@section('content')
    <form action="{{route('contact.store')}}" method="post" class="formContainer contactContainer">
        @csrf
            <h1>{{__('users.help.contact_us')}}</h1>
            @if ($errors->any())
                <div class="errorContainer">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <label>{{__('users.help.subject')}}</label>
            <input name="title" type="text" required maxlength="200">
            <label>{{__('users.help.problem')}}</label>
            <textarea cols="10" rows="8" class="formTextarea" name="problemtext"></textarea>
            <button class="containerBtn" type="submit">{{__('users.help.submit')}}</button>
    </form>
@endsection
