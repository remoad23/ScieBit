@extends('Layout.layout')

@section('content')
    <table class="generalTable">
        <thead>
        <tr>
            <th></th>
            <th>{{__('users.user.name')}}</th>
            <th>{{__('users.user.email')}}</th>
            <th>{{__('users.rights.departments')}}</th>
            <th>{{__('users.rights.rights')}}</th>
            <th>{{__('users.rights.edit')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
        <tr>
            <td>
                @if($user->picture === null)
                <img class="tableProfileIMG">
                @else
                <img class="tableProfileIMG" src="{{asset('storage/'.$user->picture)}}">
                @endif
            </td>
            <td><a class="nameTableProfile" href="{{route('rights.edit',['id' => $user->id])}}">{{$user->name}} {{$user->lastname}}</a></td>
            <td>{{$user->email}}</td>
            <td>
            <!--    <button>{{__('users.rights.departments')}}</button> -->
                <div class="departmentWrapper">
                    @foreach($user->departments as $departmentName)
                        <div class="departmentCircle circle{{$departmentName}}">{{$departmentName[0]}}</div>
                    @endforeach
                </div>
            </td>
            <td>
                <form action="{{route('userrights.switchToAdmin',['id' => $user->id])}}" method="Post">
                    @method('PUT') @csrf
                    <select onchange="document.getElementById('{{'subSelect'.$user->id}}').click()" >
                        <option>User</option>
                        <option>Admin</option>
                    </select>
                    <input style="display: none" id="{{'subSelect'.$user->id}}" type="submit">
                </form>
            </td>
            <td class="editColumn">
<!--                <i class="editIcon">{{__('users.rights.edit')}}</i>
                <i class="trashIcon">{{__('users.rights.delete')}}</i> -->
                <div class="circleMenuIcon" onclick="toggleCircleMenu(event)">
                    <div class="circleMenu">
                        <a class="circleMenuOption" href="{{ route('rights.edit',['id' => $user->id] ) }}"><i class="circleMenuRowIcon editIconWhite"></i>{{__('users.rights.edit')}}</a>
                        <form class="circleMenuOption" action="{{route('user.delete',['id' => $user->id])}}" method="Post" onclick="submitDelete(event)">
                            @method('DELETE') @csrf
                            <i class="circleMenuRowIcon trashIconWhite"></i>
                            <input value="{{__('users.rights.delete')}}" type="submit">
                        </form>
                    </div>
                </div>
                <a class="editIcon editIconEdiTable" href="{{ route('rights.edit',['id' => $user->id] ) }}"></a>
                <form action="{{route('user.delete',['id' => $user->id])}}" method="Post">
                    @method('DELETE') @csrf
                    <input class="trashIcon trashEditTable" value="" type="submit">
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div class="pagination">
        <div class="paginationCenter">
            <a id="arrowLeft" class="arrowIcon" style="cursor:pointer;" onclick="decreasePagination({{$pagination}})"></a>
            @if($pagination <= 0)
                @for($x = $pagination; $x < $pagination+8;$x++)
                    <a href="{{route('rights.index',$x)}}">{{$x}}</a>
                @endfor
            @else
                @for($x = $pagination-1; $x < $pagination+7;$x++)
                    <a href="{{route('rights.index',$x)}}">{{$x}}</a>
                @endfor
            @endif
            <a id="arrowRight" class="arrowIcon" href="{{ route('rights.index',++$pagination ) }}"></a>
        </div>
    </div>
@endsection


<script>
    function toggleCircleMenu(event)
    {
        if(event.target.classList.contains("circleMenuIcon")){
            let openedMenus = document.getElementsByClassName("showCircleMenu");
            let menu = event.target.childNodes[1];
            for(let i = 0; i < openedMenus.length; i++){
                if(openedMenus[i] != menu){
                    openedMenus[i].classList.toggle("showCircleMenu");
                }
            }
            menu.classList.toggle("showCircleMenu");
        }
    }

    function submitDelete(event)
    {
        let submitButton = event.target.childNodes[7];
        submitButton.click();
    }

    function decreasePagination(pagination)
    {
        if(pagination <= 0){
            pagination = 0;
        }
        else{
            pagination = pagination - 1;
        }
        window.location.href = pagination;
    }
</script>
