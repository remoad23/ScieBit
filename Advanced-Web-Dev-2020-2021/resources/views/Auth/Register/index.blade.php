@extends('Layout.layoutraw')

@section('content')
<div class="formWrap registerWrap">
    @if ($errors->any())
        <div class="errorContainer">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{route('registration.store')}}" class="registerContainer authContainer" >
        @csrf
        <h1>{{__('auth.register.registration')}}</h1>
        <label>Firstname</label>
        <input name="firstname" type="text" value="{{ old('firstname') }}" class="{{($errors->first('firstname') ? "formError" : "")}}" title="Only letter allowed" required pattern="[A-Za-z]+" maxlength="200">
        <label>Lastname</label>
        <input name="lastname" type="text" value="{{ old('lastname') }}" class="{{($errors->first('lastname') ? "formError" : "")}}" title="Only letter allowed" required pattern="[A-Za-z]+" maxlength="200">
        <label>{{__('users.user.email')}}</label>
        <input type="email" name="email" value="{{ old('email') }}" class="{{($errors->first('email') ? "formError" : "")}}" required title="Valid email needed">
        <label>{{__('users.user.password')}}</label>
        <input type="password" name="password" class="{{($errors->first('password') ? "formError" : "")}}" required maxlength="50" >
        <label>{{__('users.user.confirm_password')}}</label>
        <input type="password" name="verifypassword" class="{{($errors->first('password') ? "formError" : "")}}" required maxlength="50">
        <button class="containerBtn" type="submit">{{__('auth.register.register')}}</button>
    </form>

    <div class="formLinks">
        <a href="{{ route('login') }}">{{__('auth.register.login_new')}}</a>
    </div>
</div>
@endsection
