@extends('Layout.layout')

@section('content')
    <div class="profileHeader">
        @if($user->picture === null)
        <img class="profileIMG userIcon">
        @else
        <img class="profileIMG userIcon" src="{{asset('storage/'.$user->picture)}}">
        @endif
        <h1>{{$user->name}} {{$user->lastname}}</h1>
    </div>
    <div class="profileInformation">
        <div class="informationDetailsWrap">
            <div class="userInformationOutput">
                <label>{{__('users.user.firstname')}}:</label>
                <input class="inputTextForm" style="border: none;" value="{{ $user->name }}" type="text" placeholder="Name" disabled>
                <label>{{__('users.user.surname')}}:</label>
                <input class="inputTextForm" style="border: none;" value="{{ $user->lastname }}" type="text" placeholder="Surname" disabled>
                <label>{{__('users.user.email')}}:</label>
                <input class="inputTextForm" style="border: none;" value="{{ $user->email }}" type="email" placeholder="E-Mail" disabled>
            </div>
        </div>
    </div>
    <button class="saveSelectableDepartmentsBtn buttonSuccess">
        <a style="text-decoration: none; color: white;"
           href="{{ $usertype === "Admin" ? route('rights.admin.edit',session()->get('id')) : route('rights.edit.nodepartment',session()->get('id')) }}">
            {{__('users.rights.edit')}}
        </a>
    </button>
@endsection
