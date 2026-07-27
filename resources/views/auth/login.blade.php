@extends('layouts.loginapp')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Sora:wght@600;700&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #dee0e4;
            min-height: 100vh;
            overflow: hidden;
        }

        /* ── FULL-SCREEN LAYOUT ── */
        .audit-login-wrap {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* ════════════════════════
       LEFT PANEL
    ════════════════════════ */
        .audit-left {
            flex: 1.15;
            background: #e4e5e6;

            flex-direction: column;
            justify-content: space-between;
            padding: 2.5rem 3rem;
            overflow: hidden;
        }

        /* grid overlay */
        .audit-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 52px 52px;
            pointer-events: none;
        }

        /* ambient glows */
        .glow-teal {
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 201, 167, 0.13) 0%, transparent 70%);
            bottom: -80px;
            left: -60px;
            pointer-events: none;
            animation: pulse-glow 5s ease-in-out infinite;
        }

        .glow-amber {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245, 166, 35, 0.09) 0%, transparent 70%);
            top: -60px;
            right: 60px;
            pointer-events: none;
            animation: pulse-glow 5s ease-in-out infinite 2.5s;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.08);
            }
        }

        /* brand */
        .brand-mark {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: #f5a623;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-icon svg {
            width: 22px;
            height: 22px;
        }

        .brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 1.45rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.02em;
        }

        .brand-name span {
            color: #f5a623;
        }

        /* floating metric cards */
        .cards-stage {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 0 0.5rem;
        }

        .mc {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 14px;
            padding: 1rem 1.3rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            backdrop-filter: blur(6px);
            animation: float-card 7s ease-in-out infinite;
        }

        .mc:nth-child(2) {
            margin-left: 2rem;
            animation-delay: -2.3s;
        }

        .mc:nth-child(3) {
            margin-left: 0.8rem;
            animation-delay: -4.6s;
        }

        @keyframes float-card {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-9px);
            }
        }

        .mc-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .mc-icon.amber {
            background: rgba(245, 166, 35, 0.15);
        }

        .mc-icon.teal {
            background: rgba(0, 201, 167, 0.15);
        }

        .mc-icon.blue {
            background: rgba(99, 141, 253, 0.15);
        }

        .mc-icon svg {
            width: 20px;
            height: 20px;
        }

        .mc-label {
            font-size: 10.5px;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 0.07em;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .mc-value {
            font-family: 'Sora', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: #fff;
        }

        .mc-pill {
            display: inline-block;
            margin-top: 4px;
            font-size: 10.5px;
            border-radius: 6px;
            padding: 2px 8px;
        }

        .mc-pill.teal {
            color: #00c9a7;
            background: rgba(0, 201, 167, 0.12);
        }

        .mc-pill.amber {
            color: #f5a623;
            background: rgba(245, 166, 35, 0.12);
        }

        /* tagline */
        .tagline {
            font-family: 'Sora', sans-serif;
            font-size: 1.85rem;
            font-weight: 700;
            color: #000000;
            line-height: 1.3;
            margin-bottom: 0.8rem;
            letter-spacing: -0.02em;
        }

        .tagline em {
            color: #f5a623;
            font-style: normal;
        }

        .tagline-sub {
            font-size: 14PX;
            color: rgba(0, 0, 0);
            line-height: 1.65;
            max-width: 300px;
        }

        .trust-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1.2rem;
        }

        .trust-chip {
            font-size: 13px;
            color: rgba(0, 0, 0);
            border: 1px solid rgba(72, 65, 65, 0.1);
            border-radius: 20px;
            padding: 4px 12px;
        }

        .trust-chip::before {
            content: '✓ ';
            color: #00c9a7;
        }

        /* ════════════════════════
       RIGHT PANEL
    ════════════════════════ */
        .audit-right {
            flex: 0.85;
            background: #00376a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .audit-right::after {
            content: '';
            position: absolute;
            bottom: -100px;
            right: -100px;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245, 166, 35, 0.07) 0%, transparent 65%);
            pointer-events: none;
        }

        .form-shell {
            width: 100%;
            max-width: 370px;
            position: relative;
            z-index: 2;
            animation: slide-up 0.55s cubic-bezier(.22, .68, 0, 1.2) both;
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.4rem;
        }

        .form-eyebrow-line {
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
        }

        .form-eyebrow-text {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.75);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .form-shell h1 {
            font-family: 'Sora', sans-serif;
            font-size: 1.7rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
        }

        .form-shell .form-sub {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.45);
            margin-bottom: 2rem;
        }

        /* form fields */
        .fg {
            margin-bottom: 1.2rem;
        }

        .fg>label {
            display: block;
            font-size: 11.5px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.45);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .fi {
            position: relative;
        }

        .fi-icon {
            position: absolute;
            left: 14px;
            top: 16px;
            color: #000;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .fi-icon svg {
            width: 15px;
            height: 15px;
        }

        .fi input {
            width: 100%;
            background: #fff !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.78rem 1rem 0.78rem 2.6rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #000;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .fi input::placeholder {
            color: rgba(255, 255, 255, 0.22);
        }

    
        /* Laravel validation states */
        .fi input.is-invalid {
            border-color: rgba(235, 87, 87, 0.7);
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: #eb5757;
            margin-top: 5px;
        }

        .pw-toggle {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            /* color: rgba(255, 255, 255, 0.28); */
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

      

        .pw-toggle svg {
            width: 15px;
            height: 15px;
        }

        /* options row */
        .form-opts {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.6rem;
        }

        .cb-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.45);
            cursor: pointer;
            user-select: none;
        }

        .cb-label input[type=checkbox] {
            width: 15px;
            height: 15px;
            accent-color: #f5a623;
            cursor: pointer;
        }

        .fp-link {
            font-size: 13px;
            color: #f5a623 !important;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .fp-link:hover {
            opacity: 1;
        }

        /* submit button */
        .btn-audit {
            width: 100%;
            background: #f5a623;
            border: none;
            border-radius: 10px;
            padding: 0.88rem 1rem;
            font-family: 'Sora', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: #0d1b36;
            cursor: pointer;
            letter-spacing: 0.01em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .btn-audit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 30%, rgba(255, 255, 255, 0.18) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.5s;
        }

        .btn-audit:hover::after {
            transform: translateX(100%);
        }

        .btn-audit:hover {
            background: #f7b84a;
            box-shadow: 0 6px 24px rgba(245, 166, 35, 0.3);
        }

        .btn-audit:active {
            transform: scale(0.985);
        }

        .btn-audit svg {
            width: 16px;
            height: 16px;
        }

        /* footer */
        .form-foot {
            margin-top: 1.8rem;
            padding-top: 1.4rem;
            border-top: 1px solid rgba(229, 224, 224, 0.07);
            text-align: center;
        }

        .form-foot p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.6;
        }

        .ssl-note {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 0.7rem;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.22);
        }

        .ssl-note svg {
            width: 12px;
            height: 12px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 800px) {
            .audit-login-wrap {
                flex-direction: column;
                overflow: auto;
            }

            .audit-left {
                flex: none;
                min-height: 220px;
                padding: 2rem;
            }

            .cards-stage {
                display: none;
            }

            .audit-right {
                flex: none;
                padding: 2rem 1.4rem 3rem;
                overflow: auto;
            }

            body {
                overflow: auto;
            }
        }

        .brand-bottom {
            margin-top: 40px
        }
    </style>

    <div class="audit-login-wrap">

        {{-- ═══ LEFT PANEL ═══ --}}
        <div class="audit-left">
            <div class="glow-teal"></div>
            <div class="glow-amber"></div>

            {{-- Logo --}}
            <div class="brand-mark">
                <div class="brand-icon">
                    <svg viewBox="0 0 22 22" fill="none">
                        <path d="M3 5h16M3 10h9M3 15h6" stroke="#0d1b36" stroke-width="2.2" stroke-linecap="round" />
                        <circle cx="16" cy="14" r="4.5" stroke="#0d1b36" stroke-width="2" />
                        <path d="M19.5 17.5L21 19" stroke="#0d1b36" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <img class="align-content" src="{{ URL::asset('/public/images/app_logo2.png') }}" width="35%" alt="">

            </div>

            {{-- Metric cards --}}


            {{-- Tagline --}}
            <div class="brand-bottom ">
                <div class="tagline">Audit smarter,<br>close <em>faster.</em></div>
                <p class="tagline-sub">Centralize collection, legal, and agency audits — track every cycle, score, and
                    action in one secure workspace.</p>
                <div class="trust-row">
                    <div class="trust-chip">Role-based access</div>
                    <div class="trust-chip">Real-time reports</div>
                    <div class="trust-chip">Multi-cycle tracking</div>
                </div>
            </div>
        </div>

        {{-- ═══ RIGHT PANEL ═══ --}}
        <div class="audit-right">
            <div class="form-shell">

                <div class="form-eyebrow">
                    <div class="form-eyebrow-line"></div>
                    <div class="form-eyebrow-text">Secure Access</div>
                    <div class="form-eyebrow-line"></div>
                </div>

                <h1>Welcome back</h1>
                <p class="form-sub">Sign in to your AuditMitr workspace</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="fg">
                        <label for="email">Email address</label>
                        <div class="fi">
                            <span class="fi-icon" aria-hidden="true">
                                <svg viewBox="0 0 16 16" fill="none">
                                    <rect x="1" y="3" width="14" height="10" rx="2" stroke="currentColor"
                                        stroke-width="1.4" />
                                    <path d="M1 5.5l7 4.5 7-4.5" stroke="currentColor" stroke-width="1.4"
                                        stroke-linecap="round" />
                                </svg>
                            </span>
                            <input type="email" id="email" name="email" placeholder="you@company.com"
                                value="{{ old('email') }}" autocomplete="email" autofocus required
                                class="@error('email') is-invalid @enderror">
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="fg">
                        <label for="my-password">Password</label>
                        <div class="fi">
                            <span class="fi-icon" aria-hidden="true">
                                <svg viewBox="0 0 16 16" fill="none">
                                    <rect x="3" y="7" width="10" height="7" rx="1.5" stroke="currentColor"
                                        stroke-width="1.4" />
                                    <path d="M5 7V5a3 3 0 016 0v2" stroke="currentColor" stroke-width="1.4"
                                        stroke-linecap="round" />
                                </svg>
                            </span>
                            <input type="password" id="my-password" name="password" placeholder="Enter your password"
                                autocomplete="current-password" required class="@error('password') is-invalid @enderror">
                            <button type="button" class="pw-toggle" onclick="auditTogglePw()"
                                aria-label="Toggle password visibility">
                                <svg id="eye-show" viewBox="0 0 16 16" fill="none">
                                    <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z" stroke="currentColor"
                                        stroke-width="1.4" />
                                    <circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.4" />
                                </svg>
                                <svg id="eye-hide" viewBox="0 0 16 16" fill="none" style="display:none">
                                    <path
                                        d="M2 2l12 12M6.5 6.6A2 2 0 0010 10M4 4.3C2.5 5.5 1.5 7 1.5 7S4 12 8 12c1.1 0 2.1-.3 3-.8M7 4.1A5.7 5.7 0 018 4c4 0 6.5 3 6.5 3s-.5 1-1.5 2.2"
                                        stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                                </svg>
                            </button>
                            @error('password')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    {{-- Remember / Forgot --}}
                    <div class="form-opts">
                        <label class="cb-label">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="fp-link">Forgot password?</a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-audit">
                        Sign in
                        <svg viewBox="0 0 16 16" fill="none">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>

                </form>

                <div class="form-foot">
                    <p>Access is restricted to authorized personnel only.<br>Contact on Info@qdegrees.com to request access or demo.
                    </p>
                    {{-- <div class="ssl-note">
                        <svg viewBox="0 0 12 12" fill="none">
                            <path d="M6 1L1.5 3v3.5c0 2.5 2 4.3 4.5 5 2.5-.7 4.5-2.5 4.5-5V3L6 1z" stroke="#00c9a7"
                                stroke-width="1.2" />
                            <path d="M4 6l1.5 1.5L8 4" stroke="#00c9a7" stroke-width="1.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        256-bit SSL encrypted connection
                    </div> --}}
                </div>

            </div>
        </div>

    </div>

    <script>
        function auditTogglePw() {
            var inp = document.getElementById('my-password');
            var show = document.getElementById('eye-show');
            var hide = document.getElementById('eye-hide');
            if (inp.type === 'password') {
                inp.type = 'text';
                show.style.display = 'none';
                hide.style.display = 'block';
            } else {
                inp.type = 'password';
                show.style.display = 'block';
                hide.style.display = 'none';
            }
        }
    </script>
@endsection
