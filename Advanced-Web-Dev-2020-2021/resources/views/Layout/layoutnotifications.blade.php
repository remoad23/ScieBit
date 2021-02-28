@if(isset($new_notifications))
    @foreach($new_notifications as $notification)
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
