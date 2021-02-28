@extends('Layout.layout')

@section('content')
    <div class="profileHeader">
        <div class="profileIMG userIcon"></div>
        <h1>{{__('users.user.create')}}</h1>
    </div>
    <div class="profileInformation">
        <div class="informationDetailsWrap">
            <div class="userInformationDetails">
                <p>{{__('users.user.username')}}:</p>
                <p>{{__('users.user.name')}}:</p>
                <p>{{__('users.user.surname')}}:</p>
                <p>{{__('users.user.password')}}:</p>
                <p>{{__('users.user.confirm_password')}}:</p>
                <p>{{__('users.user.email')}}:</p>
                <input type="submit" value={{__('users.user.create')}}>
            </div>
            <div class="userInformationOutput">
                <input class="inputTextForm" name="username" type="text" placeholder="Günther123z">
                <input class="inputTextForm" name="firstname" type="text" placeholder="Günther">
                <input class="inputTextForm" name="lastname" type="text" placeholder="Jauch">
                <input class="inputTextForm" name="password" type="text" placeholder="Passwort">
                <input class="inputTextForm" name="passwordverify" type="text" placeholder="Passwort bestätigen">
                <input class="inputTextForm" name="firstname" type="text" placeholder="g.Jauch@gmail.com">
            </div>
        </div>
    </div>
@endsection
