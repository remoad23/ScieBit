@extends('Layout.layout')

@section('content')
    <form class="editUserContainer" action="{{ route('adminrights.edit',['id' => $user->id]) }} " method="POST" enctype="multipart/form-data">
        @method('PUT') @csrf
        <div class="profileHeader">
            @if($user->picture === null)
                <div class="profileIMG userIcon" >
                    <input type="file" name="profileImage" class="imageInput">
                </div>
            @else
                <div class="profileIMG userIcon" style="background-image: url({{asset('storage/'.$user->picture)}}) !important; background-size: cover !important;">
                    <div class="editIconWhite editIconImage" onclick="document.getElementById('uploadIMG').click();"></div>
                    <input type="file" name="profileImage" id="uploadIMG"  class="imageInput">
                </div>
            @endif
            <h1>Edit Admin</h1>
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
                           title="Only letter allowed" required pattern="[a-zA-Z]+" maxlength="200">
                    <label>{{__('users.user.surname')}}:</label>
                    <input class="inputTextForm {{($errors->first('lastname') ? "formError" : "")}}"
                           value="{{ $user->lastname }}" name="lastname" type="text" placeholder="Surname"
                           title="Only letter allowed" required pattern="[a-zA-Z]+" maxlength="200">
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
                                <div class="closeSelectable crossIcon" onclick="disableDepartment(event)"></div>
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
                                <div class="addSelectable plusIcon" onclick="enableDepartment(event)"></div>
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
            let changeIcon = document.getElementById(data).childNodes[3];
            changeIcon.setAttribute("onclick","disableDepartment(event)");
            changeIcon.setAttribute("class","closeSelectable crossIcon");
            document.getElementById(data).childNodes[5].disabled = false;
            event.target.appendChild(document.getElementById(data));
            event.preventDefault();
        }

        function dropDisabled(event) {
            let data = event.dataTransfer.getData("text");
            let changeIcon = document.getElementById(data).childNodes[3];
            changeIcon.setAttribute("onclick","enableDepartment(event)");
            changeIcon.setAttribute("class","addSelectable plusIcon");
            document.getElementById(data).childNodes[5].disabled = true;
            event.target.appendChild(document.getElementById(data));
            event.preventDefault();
        }

        function enableDepartment(event){
            let target = event.target;
            event.target.setAttribute("onclick","disableDepartment(event)");
            event.target.setAttribute("class","closeSelectable crossIcon");
            let departmentEnabledContainer = document.getElementById("container2");
            let parentElement = target.parentElement;
            parentElement.childNodes[5].disabled = false;
            departmentEnabledContainer.appendChild(parentElement);
        }

        function disableDepartment(event){
            let target = event.target;
            event.target.setAttribute("onclick","enableDepartment(event)");
            event.target.setAttribute("class","addSelectable plusIcon");
            let departmentEnabledContainer = document.getElementById("container1");
            let parentElement = target.parentElement;
            parentElement.childNodes[5].disabled = true;
            departmentEnabledContainer.appendChild(parentElement);
        }

    </script>
@endsection


