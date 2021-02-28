@extends('Layout.layout')

@section('content')
    <form class="editUserContainer" action="{{ route('userrights.edit',['id' => $user->id]) }} " method="POST" enctype="multipart/form-data">
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
                           value="{{ $user->name }}" name="name" type="text" placeholder="Name"
                           required pattern="[A-Za-z]+" title="Only letter allowed" maxlength="200">
                    <label>{{__('users.user.surname')}}:</label>
                    <input class="inputTextForm {{($errors->first('lastname') ? "formError" : "")}}"
                           value="{{ $user->lastname }}" name="lastname" type="text" placeholder="Surname"
                           required pattern="[A-Za-z]+" title="Only letter allowed" maxlength="200">
                    <label>{{__('users.user.email')}}:</label>
                    <input class="inputTextForm {{($errors->first('email') ? "formError" : "")}}"
                           value="{{ $user->email }}" name="email" type="email" placeholder="E-Mail" title="Valid email needed" required>
                </div>
            </div>
        </div>
        <div class="departmentSelectContainer">
            <div class="departmentSelectRow">
                <div class="departmentSelectColumn">
                    <h1>{{__('users.rights.departments')}}</h1>
                    <div class="selectContainer" ondrop="dropEnabled(event)" ondragover="event.preventDefault()" id="container2">
                        @foreach($departmentusers as $departmentuser)
                            <div draggable="true" ondragstart="drag(event)"
                                 class="selectableDepartment circleFinance"
                                 id='{{ "department" . ($departmentuser->department_id-1) }}'>
                                <p>{{$possibleDepartments[$departmentuser->department_id-1]}}</p>
                                <div class="closeSelectable crossIcon"></div>
                                <input type="hidden"
                                       value="{{$possibleDepartments[$departmentuser->department_id-1]}}"
                                       name="{{ "department" . ($departmentuser->department_id -1) }}">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="departmentSelectColumn">
                    <h1>{{__('users.rights.departments')}}</h1>
                    <div class="selectContainer" ondrop="dropDisabled(event)" ondragover="event.preventDefault()" id="container1">
                        @foreach($notSelectedDepartments as $department)
                            <div draggable="true" ondragstart="drag(event)"
                                 class="selectableDepartment circleMarketing"
                                 id="{{ "department" . ($department-1) }}">
                                <p>{{ $possibleDepartments[$department-1] }} </p>
                                <div class="addSelectable plusIcon"></div>
                                <input type="hidden" value="{{$possibleDepartments[$department-1]}}"
                                       name="{{ "department" . ($department-1) }}" disabled>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="saveSelectableDepartmentsBtn buttonSuccess">{{__('users.rights.save')}}</button>
    </form>

    <script>
        function drag(event) {
            event.dataTransfer.setData("text", event.target.id);
        }

        function dropEnabled(event) {
            let data = event.dataTransfer.getData("text");
            document.getElementById(data).childNodes[5].disabled = false;
            event.target.appendChild(document.getElementById(data));
            event.preventDefault();
        }

        function dropDisabled(event) {
            let data = event.dataTransfer.getData("text");
            document.getElementById(data).childNodes[5].disabled = true;
            event.target.appendChild(document.getElementById(data));
            event.preventDefault();
        }

    </script>
@endsection


