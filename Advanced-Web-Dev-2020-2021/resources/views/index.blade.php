@extends('Layout.layout')

@section('content')
    <div class="dashboard">
        <div class="dashboardWrap">
            <div class="dashboardUser">
            </div>
            <div class="dashboardContent">
                <div class="dashboardContentWrapper">
                    @if($user->picture === null)
                        <div class="userIcon userIconDashboard"></div>
                    @else
                        <img class="userIconDashboard" src="{{asset('storage/'.$user->picture)}}">
                    @endif
                    <h1  class="usernameDashboard">{{session()->get('username')}} {{session()->get('lastname')}}</h1>
                </div>
            </div>
            <div class="dashboardContent">
                <h1>Dashboard</h1>
                <div class="dashboardContentWrapper">
                    <a href="{{ route('user.index',['id' => request()->session()->get('id') ]) }}" class="dashboardElement userIcon"></a>
                    <a href="{{ route('docs.redirect') }}" class="dashboardElement fileIcon"></a>
                    @if($user->admin === true)
                        <a href="{{ route('rights.index',['pagination' => 0]) }}" class="dashboardElement editIconUser"></a>
                        <a href="{{ route('rights.admin.index',['pagination' => 0]) }}" class="dashboardElement editIconAdmin"></a>
                        <a href="{{ route('support.index',['pagination' => 0]) }}" class="dashboardElement searchIcon"></a>
                    @else
                        <a href="{{ route('contact.index') }}" class="dashboardElement letterIconBlue"></a>
                    @endif
                </div>
            </div>
        </div>
        <div class="dashboardNews">
            <h1>{{__('users.dashboard.news')}}</h1>
            <div class="newsContainer">
                @foreach($notifications as $notification)
                    @if($notification->type === "message")
                        <a href="{{ route('support.show',['id' => $notification->problem_id]) }}" class="newsContent">
                            <div class="newsIMG addFileIcon"></div>
                            <div class="newsText">{{__('users.dashboard.received_message')}}</div>
                        </a>
                    @endif
                    @if($notification->type === "sharedfile")
                        <div class="newsContent">
                            <div class="newsIMG addFileIcon"></div>
                            <div class="newsText">{{__('layout.new_sharedfile')}}</div>
                        </div>
                    @endif
                    @if($notification->type === "newdepartment")
                        <div class="newsContent">
                            <div class="newsIMG addFileIcon"></div>
                            <div class="newsText">{{__('users.dashboard.assigned_department')}}</div>
                        </div>
                    @endif
                @endforeach
                @if(!isset($notifications))
                    <div class="newsContent">
                        <div class="newsIMG addFileIcon"></div>
                        <div class="newsText">{{__('users.dashboard.no_news')}}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
