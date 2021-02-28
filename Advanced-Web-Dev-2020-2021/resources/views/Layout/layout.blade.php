<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="{{ url('/css/app.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ url('/css/assets.css') }}" />
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <title>ScieBit</title>
    </head>

    <body>
        <header>
            <div id="burgerMenu" class="burgerMenuIcon" onclick="toggleSideMenu()"></div>
            <p>{{session()->get('username')}} {{session()->get('lastname')}}</p>
            <div id="loggedUserImg">
                <div class="headerNav">
                    @if(sizeOf($notifications) === 0)
                    <div class="notificationIcon headerNotification" onclick="showNotifications()"></div>
                    @else
                    <div class="newNotificationIcon headerNotification" onclick="showNotifications()"></div>
                    @endif
                        <div id="userImg" class="userIconWhite" onclick="showOptions()" style="background-image: url('{{asset('storage/'.$profileimage)}}')"></div>
                </div>
                <ul id="headerOptions">
                    <li><a href="{{ route('changeLanguage') }}"><i class="worldIcon"></i>{{__('layout.language')}}</a></li>
                    <li><a href="{{ route('user.index',['id' => request()->session()->get('id') ]) }}"><i class="userIconWhite"></i>{{__('layout.user')}}</a></li>
                    <li><a href="{{ route('login.logout') }}"><i class="powerIcon"></i>{{__('layout.logout')}}</a></li>
                </ul>
                <ul id="headerNotificationList">
                        @if(isset($notifications))
                            @foreach($notifications as $notification)
                                @if( $loop->index === 5)
                                    @break
                                @endif
                                @if($notification->type === "message")
                                    <li>
                                        <a style="overflow: hidden; text-overflow: clip;" href="{{ route('support.show',['id' => $notification->problem_id]) }}">
                                            <i class="addFileIcon"></i>{{__('layout.new_message')}}
                                        </a>
                                        <form action="{{ route('notification.delete',['id' => $notification->id]) }}" METHOD="post">
                                            @csrf @method('delete')
                                            <input class="crossIcon crossNotification" value="" type="submit">
                                        </form>

                                    </li>
                                @endif
                                @if($notification->type === "sharedfile")
                                    <li>
                                        <a href="{{ route('docs.shared') }}">
                                            <i class="addFileIcon"></i>{{__('layout.new_sharedfile')}}
                                        </a>
                                        <form action="{{ route('notification.delete',['id' => $notification->id]) }}" METHOD="post">
                                            @csrf @method('delete')
                                            <input class="crossIcon crossNotification" value="" type="submit">
                                        </form>

                                    </li>
                                @endif
                            @endforeach
                        @endif
                    <li  onclick="getMoreNotifications()" id="getMoreNotifcations">
                        <i class="plusIcon" id="notificationPlusIcon">
                        </i>{{__('layout.further_messages')}}
                    </li>
                </ul>
            </div>
        </header>
        <div class="asideMainTable">
            <aside id="sideMenu">
                <div id="logoContainer">
                    <div class="logoIcon" ></div>
                    <h1>ScieBit</h1>
                </div>
                <ul>
                    <li>
                        <a href="{{ route('dashboard.index') }}"><i class="homeIcon"></i>{{__('layout.dashboard')}}</a></li>
                    <li>
                        <a href="{{ route('user.index',['id' => request()->session()->get('id') ]) }} "><i class="userIconWhite"></i>{{__('layout.user')}}</a>
                    </li>
                    <li>
                        <a href="{{ route('docs.redirect') }}"><i class="folderIconWhite"></i>{{__('layout.documents')}}</a>
                    </li>
                </ul>

                <ul>
                    @if(auth()->guard('user')->check())
                    <li>
                        <a href="{{ route('contact.index') }}"><i class="letterIcon"></i>{{__('layout.contact')}}</a>
                    </li>
                    @endif
                    @if(auth()->guard('admin')->check())
                        <li>
                            <a href="{{ route('rights.index',['pagination' => 0]) }}"><i class="editIconWhite"></i>{{__('layout.manage_user')}}</a>
                        </li>
                        <li>
                            <a href="{{ route('rights.admin.index',['pagination' => 0]) }}"><i class="editIconWhite"></i>{{__('layout.manage_admin')}}</a>
                        </li>
                        <li>
                            <a href="{{ route('support.index',['pagination' => 0]) }}"><i class="searchIconWhite"></i>{{__('layout.support')}}</a>
                        </li>
                    @endif
                </ul>
            </aside>
            <main>
                <div class="main-wrap">
                    @yield('content')
                </div>
            </main>
        </div>
        <footer>
            <div class="list">
                <ul class="footerList">
                    <h1>{{__('layout.information')}}</h1>
                    <li><a href="{{ route('impressum') }}">{{__('layout.imprint')}}</a></li>
                    <li><a href="{{ route('policy') }}">{{__('layout.policy')}}</a></li>
                    <li><a href="{{ route('about') }}">{{__('layout.about')}}</a></li>
                </ul>
                <ul class="footerList">
                    <h1>{{__('layout.information')}}</h1>
                    <li><a href="/"> Placeholder</a></li>
                    <li><a href="/"> Placeholder</a></li>
                    <li><a href="/"> Placeholder</a></li>
                </ul>
            </div>
            <p id="copyrightText" >Copyright by ScieBit 2020</p>
        </footer>
    </body>
</html>

<script>
    var notificationShown = false;
    var optionShown = false;
    function showNotifications()
    {
        let notification = document.getElementById("headerNotificationList");
        if(optionShown){
            showOptions();
        }
        if(notification.style.display === "flex")
        {
            notification.style.display = "none";
            notification.style.opacity = "0";
            notificationShown = false;
        }
        else{
            notification.style.display = "flex";
            notification.style.opacity = "1";
            notificationShown = true;
        }
    }

    function showOptions()
    {
        let options = document.getElementById("headerOptions");
        if(notificationShown){
            showNotifications();
        }
        if(options.style.display === "flex")
        {
            options.style.display = "none";
            options.style.opacity = "0";
            optionShown = false;
        }
        else{
            options.style.display = "flex";
            options.style.opacity = "1";
            optionShown = true;
        }
    }

    let pagination = {index: 1};

    async function getMoreNotifications() {
        let response = await fetch( "http://localhost/Advanced-Web-Dev-2020-2021/public/notification/" + pagination.index );
        let body = await response.text();
        pagination.index++;
        if(body === "NoNotificationsFound")
        {
            return;
        }
        else{
            let headerList = document.getElementById('headerNotificationList')
            headerList.innerHTML += (body);
            let moreNotifications = document.getElementById('getMoreNotifcations');
            headerList.removeChild(moreNotifications);
            headerList.append(moreNotifications);
        }
    }


    function toggleSideMenu(){
        let burgerMenu = document.getElementById("burgerMenu");
        let sideMenu = document.getElementById("sideMenu");
        sideMenu.classList.toggle("visible");
        burgerMenu.classList.toggle("crossIcon");
        burgerMenu.classList.toggle("burgerMenuIcon");
        burgerMenu.classList.toggle("fixed");
    }



</script>
