<style>
    #helpChatWrapper {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    #helpChatToggle {
        background-color: #4a90e2;
        color: white;
        padding: 10px 15px;
        border-radius: 50px;
        cursor: pointer;
        user-select: none;
        transition: opacity 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    #helpChatBox {
        margin-top: 10px;
        width: 340px;
        max-height: 420px;
        background-color: white;
        border: 1px solid #ccc;
        box-shadow: 0 0 12px rgba(0,0,0,0.2);
        display: none;
        border-radius: 10px;
        overflow: hidden;
        flex-direction: column;
    }

    #helpChatWrapper:hover #helpChatToggle {
        opacity: 0;
        pointer-events: none;
    }

    #helpChatWrapper:hover #helpChatBox {
        display: flex;
    }

    #helpChatBoxHeader {
        background-color: #4a90e2;
        color: white;
        padding: 10px;
        font-weight: bold;
        text-align: center;
        font-size: 16px;
    }

    #helpChatMessages {
        padding: 10px;
        overflow-y: auto;
        height: 250px;
        background-color: #f9f9f9;
    }

    .message {
        margin: 5px 0;
        padding: 8px 12px;
        border-radius: 8px;
        max-width: 80%;
        font-size: 13px;
        line-height: 1.4;
        clear: both;
    }

    .message.user {
        background-color: #e0f3ff;
        align-self: flex-end;
        text-align: right;
        margin-left: auto;
    }

    .message.bot {
        background-color: #f1f1f1;
        align-self: flex-start;
        text-align: left;
        margin-right: auto;
    }

    .message strong {
        display: block;
        font-weight: bold;
        margin-bottom: 3px;
        color: #444;
    }

    .question-btn {
        margin: 4px 5px;
        padding: 6px 12px;
        font-size: 13px;
        background-color: #ededed;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .question-btn:hover {
        background-color: #dcdcdc;
    }
</style>

<div id="helpChatWrapper">
    <div id="helpChatToggle">💬 Help</div>

    <div id="helpChatBox">
        <div id="helpChatBoxHeader">Help Chat</div>
        <div id="helpChatMessages">
            @php
    $messages = session('chat_messages', []);
@endphp

@foreach($messages as $msg)
    <div class="message {{ $msg['sender'] === 'user' ? 'user' : 'bot' }}">
        <strong>{{ ucfirst($msg['sender']) }}:</strong> {{ $msg['message'] }}
    </div>
@endforeach

            <div class="message bot">
                <strong>Bot:</strong> Hi, {{ auth()->user()->name ?? 'Guest' }}! Need help? Choose a question below.
            </div>
        </div>
        <div style="padding: 8px; border-top: 1px solid #eee; background-color: #fdfdfd;">
            @php
                use App\PredefinedQuestion;
                $user = auth()->user();
                $userRole = $user?->getRoleNames()?->first();
                $questions = \App\PredefinedQuestion::where('role', $userRole)->get();
            @endphp

            @foreach($questions as $q)
                <button class="question-btn" data-id="{{ $q->id }}">{{ $q->question }}</button>
            @endforeach
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const chat = document.getElementById('helpChatMessages');

    function appendMessage(sender, text) {
        const div = document.createElement('div');
        div.className = 'message ' + (sender === 'You' ? 'user' : 'bot');
        div.innerHTML = `<strong>${sender}:</strong> ${text}`;
        chat.appendChild(div);
        chat.scrollTop = chat.scrollHeight;
    }

    document.querySelectorAll('.question-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const question = this.innerText;
            const id = this.dataset.id;

            appendMessage('You', question);

            axios.post('{{ route('help.chat.answer') }}', {
                id: id,
                _token: '{{ csrf_token() }}'
            }).then(res => {
                appendMessage('Bot', res.data.answer);
            }).catch(() => {
                appendMessage('Bot', '❌ Sorry, there was a problem getting an answer.');
            });
        });
    });
</script>
