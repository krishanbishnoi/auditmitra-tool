@php
    // Rotating accent palette used to color-code client avatars/banners by index
    $palette = [
        ['#4338ca', '#818cf8'],
        ['#0891b2', '#67e8f9'],
        ['#c026d3', '#f0abfc'],
        ['#ea580c', '#fdba74'],
        ['#059669', '#6ee7b7'],
        ['#dc2626', '#fca5a5'],
    ];
@endphp

<style>
    :root{
        --bg:#f2f4fb;
        --ink:#1a1830;
        --muted:#6b7086;
        --card:#ffffff;
        --line:#edeef6;
        --danger:#dc2626;
        --danger-soft:#fef2f2;
        --ring:0 1px 2px rgba(30,27,58,.04);
    }

    @media (prefers-reduced-motion: reduce){
        *{ animation-duration:.01ms !important; animation-iteration-count:1 !important; transition-duration:.01ms !important; }
    }

    body{
        background:var(--bg);
        font-family:'Inter',-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    }

    .qa-page{
        position:relative;
    }

    .qa-bg-decor{
        position:fixed;
        inset:0;
        z-index:-1;
        overflow:hidden;
        pointer-events:none;
    }

    .qa-bg-decor span{
        position:absolute;
        border-radius:50%;
        filter:blur(70px);
        opacity:.35;
    }

    .qa-bg-decor span:nth-child(1){
        width:420px; height:420px;
        top:-140px; left:-100px;
        background:#818cf8;
    }

    .qa-bg-decor span:nth-child(2){
        width:380px; height:380px;
        top:120px; right:-140px;
        background:#67e8f9;
    }

    .qa-bg-decor span:nth-child(3){
        width:320px; height:320px;
        bottom:-160px; left:38%;
        background:#f0abfc;
        opacity:.25;
    }

    .qa-header{
        background:rgba(255,255,255,.72);
        backdrop-filter:blur(14px);
        border:1px solid rgba(255,255,255,.6);
        border-radius:14px;
        padding:12px 18px;
        margin-bottom:20px;
        box-shadow:var(--ring), 0 12px 34px -14px rgba(30,27,58,.14);
        position:relative;
        overflow:hidden;
        animation:dropIn .5s ease both;
    }

    .qa-header-row{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        flex-wrap:wrap;
    }

    .qa-brand{
        display:flex;
        align-items:center;
        gap:10px;
    }

    .qa-brand-mark{
        height:60px;
        display:flex;
        align-items:center;
        flex-shrink:0;
    }

    .qa-brand-mark img{
        height:100%;
        width:auto;
        object-fit:contain;
        display:block;
    }

    .qa-brand-name{
        font-family:'Sora','Inter',sans-serif;
        font-size:15px;
        font-weight:500;
        letter-spacing:-.01em;
        color:var(--ink);
        line-height:1.2;
    }

    .qa-brand-name span{
        color:#4338ca;
    }

    .qa-header-greeting{
        margin:1px 0 0;
        font-size:12.5px;
        color:var(--muted);
    }

    .logout-btn{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border:1px solid var(--line);
        background:#fff;
        color:var(--muted);
        font-size:12.5px;
        font-weight:600;
        padding:7px 13px;
        border-radius:9px;
        cursor:pointer;
        white-space:nowrap;
        transition:border-color .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
    }

    .logout-btn:hover{
        border-color:var(--danger);
        color:var(--danger);
        transform:translateY(-1px);
        box-shadow:0 8px 18px -10px rgba(220,38,38,.4);
    }

    .logout-btn:focus-visible{
        outline:2px solid #4338ca;
        outline-offset:2px;
    }

    .logout-btn svg{
        width:13px;
        height:13px;
        flex-shrink:0;
    }

    .client-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(275px,1fr));
        gap:24px;
    }

    .client-btn{
        border:none;
        background:none;
        padding:0;
        width:100%;
        text-align:left;
        cursor:pointer;
        border-radius:18px;
    }

    .client-btn:focus-visible .client-card{
        outline:2px solid #4338ca;
        outline-offset:3px;
    }

    .client-card{
        background:var(--card);
        border-radius:18px;
        overflow:hidden;
        box-shadow:var(--ring), 0 10px 24px -12px rgba(30,27,58,.14);
        transition:transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s ease;
        position:relative;
        height:100%;
        display:flex;
        flex-direction:column;
        animation:cardIn .5s cubic-bezier(.2,.8,.2,1) both;
        animation-delay:calc(var(--i) * 50ms);
    }

    .client-btn:hover .client-card,
    .client-btn:focus-visible .client-card{
        transform:translateY(-7px);
        box-shadow:0 24px 44px -18px rgba(30,27,58,.26);
    }

    .client-banner{
        height:64px;
        background:linear-gradient(115deg, var(--accent,#4338ca), var(--accent-2,#818cf8));
        position:relative;
    }

    .client-banner::after{
        content:"";
        position:absolute;
        inset:0;
        background:radial-gradient(circle at 85% 20%, rgba(255,255,255,.35), transparent 55%);
    }

    .client-avatar-wrap{
        margin:-34px auto 0;
        width:68px;
        height:68px;
        position:relative;
        z-index:2;
    }

    .client-avatar{
        width:100%;
        height:100%;
        border-radius:50%;
        background:var(--accent,#4338ca);
        color:#fff;
        font-family:'Sora','Inter',sans-serif;
        font-size:24px;
        font-weight:700;
        display:flex;
        align-items:center;
        justify-content:center;
        border:4px solid var(--card);
        box-shadow:0 6px 16px -4px rgba(30,27,58,.3);
        transition:transform .3s cubic-bezier(.2,.8,.2,1);
    }

    .client-btn:hover .client-avatar{
        transform:scale(1.08) rotate(-4deg);
    }

    .status-dot{
        position:absolute;
        bottom:0;
        right:0;
        width:16px;
        height:16px;
        border-radius:50%;
        border:3px solid var(--card);
        background:#16a34a;
    }

    .status-dot.is-empty{
        background:#d1d5db;
    }

    .status-dot.is-ok::after{
        content:"";
        position:absolute;
        inset:0;
        border-radius:50%;
        background:#16a34a;
        animation:pulseRing 2.2s ease-out infinite;
    }

    .client-body{
        padding:14px 26px 22px;
        text-align:center;
        flex:1;
    }

    .client-name{
        font-family:'Sora','Inter',sans-serif;
        font-size:18px;
        font-weight:600;
        color:var(--ink);
        margin-bottom:2px;
    }

    .qa-block{
        text-align:left;
        border-top:1px solid var(--line);
        margin-top:16px;
        padding-top:14px;
    }

    .qa-block small.eyebrow{
        text-transform:uppercase;
        letter-spacing:.06em;
        font-size:11px;
        font-weight:700;
        color:var(--muted);
    }

    .qa-entry{
        margin-top:10px;
        display:flex;
        justify-content:space-between;
        align-items:baseline;
        gap:10px;
    }

    .qa-entry strong{
        font-size:14.5px;
        color:var(--ink);
    }

    .qa-entry small{
        font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
        font-size:11.5px;
        color:var(--muted);
        white-space:nowrap;
    }

    .qa-missing{
        margin-top:16px;
        padding:10px 12px;
        border-radius:10px;
        background:var(--danger-soft);
        color:var(--danger);
        font-size:13px;
        font-weight:600;
        text-align:left;
    }

    .client-footer{
        padding:14px;
        border-top:1px solid var(--line);
        text-align:center;
        color:var(--accent,#4338ca);
        font-weight:600;
        font-size:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        gap:6px;
        background:linear-gradient(180deg, transparent, rgba(0,0,0,.015));
    }

    .client-footer .arrow{
        display:inline-block;
        transition:transform .25s ease;
    }

    .client-btn:hover .client-footer .arrow{
        transform:translateX(5px);
    }

    .empty-box{
        background:var(--card);
        padding:64px 40px;
        border-radius:20px;
        text-align:center;
        box-shadow:var(--ring), 0 10px 30px -12px rgba(30,27,58,.10);
        animation:dropIn .5s ease both;
    }

    .empty-box svg{
        width:52px;
        height:52px;
        color:#c7cbe0;
        margin-bottom:14px;
    }

    .empty-box h4{
        font-family:'Sora','Inter',sans-serif;
        color:var(--ink);
        margin-bottom:6px;
    }

    .empty-box p{
        color:var(--muted);
        margin:0;
    }

    @keyframes dropIn{
        from{ opacity:0; transform:translateY(-10px); }
        to{ opacity:1; transform:translateY(0); }
    }

    @keyframes cardIn{
        from{ opacity:0; transform:translateY(18px) scale(.97); }
        to{ opacity:1; transform:translateY(0) scale(1); }
    }

    @keyframes pulseRing{
        0%{ opacity:.55; transform:scale(1); }
        100%{ opacity:0; transform:scale(2.4); }
    }
</style>

<div class="qa-page">

    <div class="qa-bg-decor">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="container-fluid">

        <div class="qa-header">
            <div class="qa-header-row">
                <div class="qa-brand">
                    <div class="qa-brand-mark">
                        <img src="{{ url('public/images/app_logo.png') }}" alt="AuditMitr logo">
                    </div>
                    <div>
                        <div class="qa-brand-name">Hi {{ $user->name }}</div>
                        <p class="qa-header-greeting">select a client to continue</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        @if($clients->count())

            <div class="client-grid">

                @foreach($clients as $client)

                    @php
                        [$accent, $accent2] = $palette[$loop->index % count($palette)];
                        $hasQa = $client->quality_auditors->count() > 0;
                    @endphp

                    <form action="{{ route('masterqa.login.client') }}" method="POST" class="client-btn-form">
                        @csrf

                        {{-- Login as Quality Auditor --}}
                        <input type="hidden"
                               name="quality_auditor_id"
                               value="{{ $client->quality_auditor_ids->first() }}">

                        {{-- Optional --}}
                        <input type="hidden"
                               name="client_id"
                               value="{{ $client->id }}">

                        <button type="submit" class="client-btn">

                            <div class="client-card" style="--accent: {{ $accent }}; --accent-2: {{ $accent2 }}; --i: {{ $loop->index }};">

                                <div class="client-banner"></div>

                                <div class="client-avatar-wrap">
                                    <div class="client-avatar">
                                        {{ strtoupper(substr($client->name, 0, 1)) }}
                                    </div>
                                    <span class="status-dot {{ $hasQa ? 'is-ok' : 'is-empty' }}"></span>
                                </div>

                                <div class="client-body">

                                    <div class="client-name">
                                        {{ $client->name }}
                                    </div>

                                    @if($hasQa)

                                        <div class="qa-block">
                                            <small class="eyebrow">Login as</small>

                                            @foreach($client->quality_auditors as $qa)
                                                <div class="qa-entry">
                                                    <strong>{{ $qa->name }}</strong>
                                                    <small>ID {{ $qa->id }}</small>
                                                </div>
                                            @endforeach
                                        </div>

                                    @else

                                        <div class="qa-missing">
                                            No quality auditor assigned
                                        </div>

                                    @endif

                                </div>

                                <div class="client-footer">
                                    Continue <span class="arrow">→</span>
                                </div>

                            </div>

                        </button>

                    </form>

                @endforeach

            </div>

        @else

            <div class="empty-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 7l1.5-3h15L21 7"/>
                    <path d="M3 7v11a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7"/>
                    <path d="M3 7h18"/>
                    <path d="M9 12h6"/>
                </svg>
                <h4>No clients assigned</h4>
                <p>Once a client is assigned to you, it will show up here.</p>
            </div>

        @endif

    </div>

</div>