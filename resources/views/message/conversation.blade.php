@extends('layouts.app')

<style>
    .select2-container {
        width: 100% !important;
    }
</style>
@section('content')
    <div class="row chat-row">
        <div class="col-md-3">
            <div class="users">
                <h5>Users</h5>

                <ul class="list-group list-chat-item">
                    @if($users->count())
                        @foreach($users as $user)
                            <li class="chat-user-list
                                @if($user->id == $friendInfo->id) active @endif">
                                <a href="{{ route('message.conversation', $user->id) }}">

                                    <div class="chat-name font-weight-bold">
                                        {{ $user->name }}
                                        <div class='status-circle user-status-icon user-icon-{{ $user->id }}'></div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

        </div>

        <div class="col-md-9 chat-section">
            <div class="chat-header">
                <div class="chat-image">
                </div>

                <div class="chat-name font-weight-bold">
                    {{ $user->name }}
                    <div class='status-circle user-status-icon user-icon-{{ $user->id }}'></div>
                </div>
            </div>

            <div class="chat-body" id="chatBody">
                <div class="message-listing" id="messageWrapper">

                </div>
            </div>

            <div class="chat-box">
                <div class="chat-input bg-white" id="chatInput" contenteditable="">

                </div>


            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
<script src="https://cdn.socket.io/4.4.1/socket.io.min.js" integrity="sha384-fKnu0iswBIqkjxrhQCTZ7qlLHOFEgNkRmK2vaO/LbTZSXdJfAu6ewRBdwHPhBo/H" crossorigin="anonymous"></script>
<script>
    $(function (){
        let $chatInput = $(".chat-input");
        let $chatInputToolbar = $(".chat-input-toolbar");
        let $chatBody = $(".chat-body");
        let $messageWrapper = $("#messageWrapper");


        let user_id = "{{ auth()->user()->id }}";
        let ip_address = '127.0.0.1';
        let socket_port = '8005';
        let socket = io(ip_address + ':' + socket_port);
        let friendId = "{{ $friendInfo->id }}";

        socket.on('connect', function() {
            socket.emit('user_connected', user_id);
        });

        socket.on('updateUserStatus', (data) => {
            let $userStatusIcon = $('.user-status-icon');
            $userStatusIcon.css('background-color', 'grey');

            $.each(data, function (key, val) {
                if (val !== null && val !== 0) {
                    let $userIcon = $(".user-icon-"+key);
                    $userIcon.css('background-color', '#198754');
                }
            });
        });

        socket.on('sendChatToClient', (user_id, message) => {
            appendMessageToReceiver(user_id, message);
        });

        $chatInput.keypress(function (e) {
           let message = $(this).html();
           if (e.which === 13 && !e.shiftKey) {
               $chatInput.html("");
               appendMessageToSender(message);
               socket.emit('sendChatToServer', user_id, message);
               return false;
            }
        });

        function appendMessageToSender(message) {
            let name = '{{ $myInfo->name }}';

            let userInfo = '<div class="col-md-12 user-info">\n' +
                '<div class="chat-name font-weight-bold">\n' +
                name +
                '</div>\n' +
                '</div>\n';

            let messageContent = '<div class="col-md-12 message-content">\n' +
                '<div class="message-text">\n' + message + '</div>\n' + '</div>';

            let newMessage = '<div class="row message align-items-center mb-2">'
                +userInfo + messageContent +
                '</div>';
            $messageWrapper.append(newMessage);
        }

        function appendMessageToReceiver(user_id, message) {
            if ({{$friendInfo->id}} == user_id) {
                let name = '{{ $friendInfo->name }}';
                let userInfo = '<div class="col-md-12 user-info">\n' +
                    '<div class="chat-name font-weight-bold">\n' +
                    name +
                    '</div>\n' +
                    '</div>\n';
                let messageContent = '<div class="col-md-12 message-content">\n' +
                    '                            <div class="message-text">\n' + message +
                    '                            </div>\n' +
                    '                        </div>';
                let newMessage = '<div class="row message align-items-center mb-2">'
                    +userInfo + messageContent +
                    '</div>';
                $messageWrapper.append(newMessage);
            }
        }
    });
</script>
@endpush
