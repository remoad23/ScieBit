@extends('Layout.layout')

@section('content')
<table class="generalTable">
    <thead>
    <tr>
        <th></th>
        <th>{{__('users.help.subject')}}</th>
        <th>{{__('users.user.name')}}</th>
        <th>{{__('users.support.created_at')}}</th>
        <th>{{__('users.rights.edit')}}</th>
    </tr>
    </thead>
    <tbody>
        @foreach($problems as $problem)
            <tr>
                <td style="margin-top: 1vh;">
                </td>
                <td>{{$problem->title}}</td>
                <td>{{$problem->name . " " . $problem->lastname}}</td>
                <td>{{$problem->created_at->format('d/m/Y H:i:s') ?? ""}}</td>
                <td class="editColumn">
                    <div class="circleMenuIcon supportCircleMenuIcon" onclick="toggleCircleMenu(event)">
                        <div class="circleMenu">
                            <a class="circleMenuOption" href="{{ route('support.show',['id' => $problem->id]) }}"><i class="circleMenuRowIcon editIconWhite"></i>{{__('users.rights.edit')}}</a>
                            <a class="circleMenuOption"><i class="circleMenuRowIcon trashIconWhite"></i>{{__('users.rights.delete')}}</a>
                        </div>
                    </div>
                    <a class="editIcon editIconEdiTable" href="{{ route('support.show',['id' => $problem->id]) }}"></a>
                    <form action="{{ route('support.delete',['id' => $problem->id]) }}" method="POST">
                        @csrf @method('DELETE')
                        <input type="submit" class="trashIcon trashEditTable" value="">
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
                <a href="{{route('support.index',$x)}}">{{$x}}</a>
            @endfor
        @else
            @for($x = $pagination-1; $x < $pagination+7;$x++)
                <a href="{{route('support.index',$x)}}">{{$x}}</a>
            @endfor
        @endif
        <a id="arrowRight" class="arrowIcon" href="{{ route('support.index',++$pagination)}}"></a>
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

