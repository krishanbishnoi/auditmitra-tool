@extends('layouts.master')

@section('css')
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .coming-soon-card {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            background: #fafafa;
        }

        .coming-soon-card i {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('content')
    @php
        $user = Auth::user();
        $isLegal = request()->is('legal/*');
    @endphp

    <div class="content">
        <div class="container-fluid">

            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="welcome-card">
                        <h3 class="mb-1">
                            Welcome, <span class="text-danger">{{ $user->name }}</span>
                        </h3>
                        <p class="text-muted mb-0">
                            You are currently viewing the
                            <strong>{{ $isLegal ? 'Legal Audit' : 'Compliance Audit' }}</strong> dashboard.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Coming Soon Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="coming-soon-card">
                        <i class="fa fa-cogs"></i>
                        <h4 class="mb-2">Dashboard Coming Soon</h4>
                        <p class="text-muted mb-0">
                            We are working on powerful insights, analytics, and reports.<br>
                            Stay tuned for upcoming updates.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
