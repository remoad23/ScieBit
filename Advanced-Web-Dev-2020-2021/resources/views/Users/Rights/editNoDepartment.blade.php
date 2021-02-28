@extends('Layout.layout')

@section('content')
    <form class="editUserContainer" action="{{ route('userrights.edit.nodepartment',['id' => $user->id]) }} " method="POST" enctype="multipart/form-data">
        @method('PUT') @csrf
        <div class="profileHeader">
            @if($user->picture === null)
                <div class="profileIMG userIcon" >
                    <input type="file" name="profileImage" class="imageInput">
                </div>
            @else
                <div class="profileIMG userIcon" style="background-image: url({{asset('storage/'.$user->picture)}}) !important; background-size: cover !important;">
                    <div class="editIconWhite editIconImage" onclick="document.getElementById('uploadIMG').click();"></div>
                    <input type="file" name="profileImage" id="uploadIMG" class="imageInput">
                </div>
            @endif
            <h1>Edit User</h1>
        </div>
        <div class="profileInformation">
            @if ($errors->any())
                <div class="errorContainer editError">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="informationDetailsWrap">
                <div class="userInformationOutput">
                    <label>{{__('users.user.name')}}:</label>
                    <input class="inputTextForm {{($errors->first('name') ? "formError" : "")}}"
                           value="{{ $user->name }}" name="name" type="text" placeholder="Name" title="Only letter allowed" required pattern="[A-Za-z]+" maxlength="50">
                    <label>{{__('users.user.surname')}}:</label>
                    <input class="inputTextForm {{($errors->first('lastname') ? "formError" : "")}}"
                           value="{{ $user->lastname }}" name="lastname" type="text" placeholder="Surname" title="Only letter allowed" required pattern="[A-Za-z]+" maxlength="50">
                    <label>{{__('users.user.email')}}:</label>
                    <input class="inputTextForm {{($errors->first('email') ? "formError" : "")}}"
                           value="{{ $user->email }}" name="email" type="email" placeholder="E-Mail" title="valid ssE-Mail needed" required>
                </div>
            </div>
        </div>
        <button type="submit" class="saveSelectableDepartmentsBtn buttonSuccess">{{__('users.rights.save')}}</button>
    </form>
@endsection


