@extends('Layout.layoutraw')

@section('content')
    <script>localStorage.removeItem('user');</script>
<div class="formWrap">
    @if ($errors->any())
        <div class="errorContainer">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form class="authContainer" method="POST" action="{{ route('login.logging') }}">
        @csrf
        <h1>{{__('auth.register.login')}}</h1>
        <label>E-Mail</label>
        <input name="email" type="email" class="{{($errors->first('email') ? "formError" : "")}}" required title="Valid email needed">
        <label>{{__('users.user.password')}}</label>
        <input type="password" name="password" class="{{($errors->first('password') ? "formError" : "")}}" title="Valid password needed" required maxlength="50">
        <button class="containerBtn" type="submit">{{__('auth.register.login_btn')}}</button>
    </form>
    <div class="formLinks">
        <a href="{{ route('registration') }}">{{__('auth.register.register_new')}}</a>
    </div>
</div>

@endsection
