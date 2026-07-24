<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MauzoSheetAI | Sajili Kampuni</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: radial-gradient(ellipse at 50% 0%, #fef3c7 0%, #fde68a 20%, #d97706 50%, #78350f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -200px;
            right: -200px;
            pointer-events: none;
            animation: floatBlob 20s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -150px;
            left: -150px;
            pointer-events: none;
            animation: floatBlob 25s ease-in-out infinite reverse;
        }

        @keyframes floatBlob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(1.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 
                0 20px 60px -20px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 
                0 30px 80px -20px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }

        .register-card-wrapper {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
            animation: cardFadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardFadeIn {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .glass-card > *:not(:last-child) {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .glass-card > *:last-child {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        @media (max-width: 640px) {
            .glass-card > *:not(:last-child) {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }
            .glass-card > *:last-child {
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }
            .register-card-wrapper {
                max-width: 100%;
            }
            body {
                padding: 0.75rem;
            }
        }

        .logo-circle {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid rgba(251, 191, 36, 0.3);
            box-shadow: 0 8px 24px rgba(217, 119, 6, 0.15);
            transition: all 0.3s ease;
        }

        .logo-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 32px rgba(217, 119, 6, 0.25);
        }

        .logo-img-small {
            height: 32px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
        }

        h1 {
            font-size: 1.75rem !important;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #78350f, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-size: 0.9rem !important;
            color: #6b7280;
            font-weight: 400;
        }

        .stepper-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            padding: 0.5rem 0 0.25rem;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .step-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            border: 2px solid #e5e7eb;
            background: #f9fafb;
            color: #9ca3af;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            flex-shrink: 0;
        }

        .step-dot.active {
            border-color: #d97706;
            background: #d97706;
            color: white;
            box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.15), 0 4px 12px rgba(217, 119, 6, 0.3);
            transform: scale(1.05);
        }

        .step-dot.completed {
            border-color: #22c55e;
            background: #22c55e;
            color: white;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        }

        .step-dot .check-mark {
            display: none;
        }

        .step-dot.completed .step-number {
            display: none;
        }

        .step-dot.completed .check-mark {
            display: block;
        }

        .step-line {
            width: 48px;
            height: 2px;
            background: #e5e7eb;
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .step-line .line-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #d97706, #f59e0b);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            border-radius: 2px;
        }

        .step-line.completed .line-fill {
            width: 100%;
        }

        .step-line.partial .line-fill {
            width: 50%;
        }

        .step-label {
            text-align: center;
            font-size: 0.65rem;
            font-weight: 500;
            color: #9ca3af;
            margin-top: 0.4rem;
            transition: color 0.3s ease;
            letter-spacing: 0.02em;
        }

        .step-label.active {
            color: #d97706;
        }

        .step-label.completed {
            color: #22c55e;
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-group:last-of-type {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
            letter-spacing: 0.01em;
            transition: color 0.2s ease;
        }

        .form-label .label-icon {
            margin-right: 0.4rem;
            color: #d97706;
            font-size: 0.85rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            background: #fafbfc;
            color: #111827;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
            font-family: inherit;
            appearance: none;
        }

        .form-input:hover {
            border-color: #d1d5db;
            background: #ffffff;
        }

        .form-input:focus {
            border-color: #d97706;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.1), 0 4px 12px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }

        .form-input.valid {
            border-color: #22c55e;
            background: #f0fdf4;
        }

        .form-input.valid:focus {
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        }

        .form-input.invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .form-input.invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .form-input:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        select.form-input {
            padding-right: 2.5rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 12px;
            cursor: pointer;
        }

        select.form-input:focus {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23d97706' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        }

        .validation-message {
            font-size: 0.75rem;
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            opacity: 0;
            transform: translateY(-4px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            height: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .validation-message.visible {
            opacity: 1;
            transform: translateY(0);
            height: auto;
            overflow: visible;
            pointer-events: auto;
        }

        .validation-message.valid {
            color: #22c55e;
        }

        .validation-message.invalid {
            color: #ef4444;
        }

        .validation-message .msg-icon {
            font-size: 0.7rem;
            flex-shrink: 0;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0.25rem;
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .password-toggle:hover {
            color: #6b7280;
        }

        .password-toggle:focus {
            outline: none;
            color: #d97706;
        }

        .password-strength-container {
            margin-top: 0.5rem;
        }

        .password-strength-bar {
            height: 4px;
            border-radius: 4px;
            background: #e5e7eb;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .password-strength-fill {
            height: 100%;
            border-radius: 4px;
            width: 0%;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .password-strength-fill.weak {
            width: 25%;
            background: #ef4444;
        }

        .password-strength-fill.medium {
            width: 50%;
            background: #f59e0b;
        }

        .password-strength-fill.strong {
            width: 75%;
            background: #22c55e;
        }

        .password-strength-fill.excellent {
            width: 100%;
            background: #059669;
        }

        .password-strength-label {
            font-size: 0.7rem;
            font-weight: 500;
            margin-top: 0.25rem;
            opacity: 0;
            transition: all 0.3s ease;
            height: 0;
            overflow: hidden;
        }

        .password-strength-label.visible {
            opacity: 1;
            height: auto;
            margin-top: 0.25rem;
        }

        .password-strength-label.weak { color: #ef4444; }
        .password-strength-label.medium { color: #f59e0b; }
        .password-strength-label.strong { color: #22c55e; }
        .password-strength-label.excellent { color: #059669; }

        .password-checklist {
            margin-top: 0.5rem;
            display: none;
            animation: slideDown 0.3s ease forwards;
        }

        .password-checklist.visible {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .checklist-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            padding: 0.15rem 0;
            color: #9ca3af;
            transition: all 0.3s ease;
        }

        .checklist-item.met {
            color: #22c55e;
        }

        .checklist-item .check-icon {
            font-size: 0.6rem;
            width: 14px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .password-sample {
            font-size: 0.7rem;
            color: #6b7280;
            background: #f3f4f6;
            padding: 0.2rem 0.7rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-top: 0.25rem;
            font-family: monospace;
            border: 1px solid #e5e7eb;
        }

        .btn-primary {
            background: linear-gradient(135deg, #d97706, #b45309);
            border: none;
            padding: 0.65rem 1.75rem;
            border-radius: 14px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.3);
            position: relative;
            overflow: hidden;
            font-family: inherit;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(217, 119, 6, 0.35);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0px);
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.2);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-primary .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.7s linear infinite;
        }

        .btn-primary.loading .btn-text {
            visibility: hidden;
        }

        .btn-primary.loading .spinner {
            display: inline-block;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid #d97706;
            padding: 0.65rem 1.5rem;
            border-radius: 14px;
            color: #d97706;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: inherit;
        }

        .btn-secondary:hover:not(:disabled) {
            background: #d97706;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.2);
        }

        .btn-secondary:active:not(:disabled) {
            transform: translateY(0px);
        }

        .btn-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .step-badge.complete {
            background: #dcfce7;
            color: #166534;
        }

        .step-badge.incomplete {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-card {
            border-radius: 16px;
            padding: 0.9rem 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: slideInAlert 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideInAlert {
            from {
                opacity: 0;
                transform: translateY(-12px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #86efac;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
        }

        .alert-icon {
            font-size: 1rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .alert-success .alert-icon { color: #22c55e; }
        .alert-error .alert-icon { color: #ef4444; }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 0.1rem;
        }

        .alert-success .alert-title { color: #166534; }
        .alert-error .alert-title { color: #991b1b; }

        .alert-message {
            font-size: 0.8rem;
            color: #4b5563;
        }

        .alert-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0.25rem;
            transition: color 0.2s ease;
            font-size: 0.85rem;
        }

        .alert-close:hover {
            color: #6b7280;
        }

        #website {
            display: none !important;
            position: absolute !important;
            left: -9999px !important;
            top: -9999px !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            pointer-events: none !important;
        }

        .step-transition {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .step-hidden {
            display: none;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        @media (max-width: 640px) {
            .grid-2 {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
        }

        .footer-link a {
            color: #d97706;
            font-weight: 600;
            transition: color 0.2s ease;
            text-decoration: none;
        }

        .footer-link a:hover {
            color: #b45309;
        }

        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 640px) {
            .top-bar {
                padding: 0.5rem 1rem;
            }
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .brand-logo {
            height: 30px;
            width: auto;
            border-radius: 8px;
        }

        .brand-text {
            font-weight: 700;
            font-size: 0.9rem;
            color: #78350f;
            letter-spacing: -0.01em;
        }

        .home-link {
            background: rgba(217, 119, 6, 0.08);
            border-radius: 40px;
            padding: 0.35rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #78350f;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .home-link:hover {
            background: rgba(217, 119, 6, 0.15);
            transform: translateY(-1px);
        }

        .home-link i {
            font-size: 0.7rem;
            color: #d97706;
        }

        .card-content {
            padding: 2rem 2rem 1.5rem;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }

        .card-content::-webkit-scrollbar {
            width: 4px;
        }

        .card-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .card-content::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        @media (max-width: 640px) {
            .card-content {
                padding: 1.25rem 1.25rem 1rem;
                max-height: calc(100vh - 100px);
            }
        }

        .form-footer {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f3f4f6;
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="brand-container">
            <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI" class="brand-logo" onerror="this.src='https://placehold.co/60x60/d97706/white?text=M'">
            <span class="brand-text">MauzoSheetAI</span>
        </div>
        <a href="{{ route('landing') }}" class="home-link">
            <i class="fas fa-home"></i>
            <span>Nyumbani</span>
        </a>
    </div>

    <!-- REGISTRATION CARD -->
    <div class="register-card-wrapper">
        <div class="glass-card">

            <!-- HEADER -->
            <div class="text-center pt-6 pb-4 border-b border-gray-100/50">
                <div class="logo-circle rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <img src="{{ asset('logo11.jpg') }}" alt="MauzoSheetAI" class="logo-img-small" onerror="this.src='https://placehold.co/50x50/d97706/white?text=M'">
                </div>
                <h1 class="font-extrabold tracking-tight">Sajili Kampuni</h1>
                <p class="subtitle mt-0.5">Anza kutumia MauzoSheetAI leo</p>
            </div>

            <!-- ALERTS -->
            <div style="padding-left:2rem;padding-right:2rem;padding-top:0.75rem;">
                @if(session('success'))
                    <div class="alert-card alert-success" id="successAlert">
                        <i class="fas fa-check-circle alert-icon"></i>
                        <div class="alert-content">
                            <div class="alert-title">Imefanikiwa!</div>
                            <div class="alert-message">{{ session('success') }}</div>
                        </div>
                        <button class="alert-close" onclick="this.closest('.alert-card').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <script>setTimeout(() => { let el = document.getElementById('successAlert'); if(el) el.remove(); }, 5000);</script>
                @endif

                @if(session('error'))
                    <div class="alert-card alert-error" id="errorAlert">
                        <i class="fas fa-exclamation-circle alert-icon"></i>
                        <div class="alert-content">
                            <div class="alert-title">Hitilafu!</div>
                            <div class="alert-message">{{ session('error') }}</div>
                        </div>
                        <button class="alert-close" onclick="this.closest('.alert-card').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-card alert-error" id="validationAlert">
                        <i class="fas fa-exclamation-triangle alert-icon"></i>
                        <div class="alert-content">
                            <div class="alert-title">Tafadhali sahihisha makosa</div>
                            <div class="alert-message">
                                @foreach ($errors->all() as $error)
                                    <p class="text-xs">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                        <button class="alert-close" onclick="this.closest('.alert-card').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <script>setTimeout(() => { let el = document.getElementById('validationAlert'); if(el) el.remove(); }, 8000);</script>
                @endif
            </div>

            <!-- CARD CONTENT -->
            <div class="card-content">

                <!-- PREMIUM STEPPER -->
                <div class="stepper-container mb-5">
                    @php
                        $currentStep = $currentStep ?? 1;
                    @endphp
                    @foreach([1, 2, 3] as $step)
                        <div class="step-item">
                            <div class="step-dot 
                                @if($step == $currentStep) active 
                                @elseif($step < $currentStep) completed 
                                @endif" 
                                data-step="{{ $step }}">
                                <span class="step-number">{{ $step }}</span>
                                <span class="check-mark"><i class="fas fa-check text-white"></i></span>
                            </div>
                            @if($step < 3)
                                <div class="step-line 
                                    @if($step < $currentStep) completed 
                                    @elseif($step == $currentStep) partial 
                                    @endif">
                                    <div class="line-fill"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- STEP LABELS -->
                <div class="flex justify-between text-center px-1 mb-5" style="font-size:0.65rem;font-weight:500;color:#9ca3af;">
                    <span class="step-label @if($currentStep >= 1) active @endif">Kampuni</span>
                    <span class="step-label @if($currentStep >= 2) active @endif">Mawasiliano</span>
                    <span class="step-label @if($currentStep >= 3) active @endif">Akaunti</span>
                </div>

                <!-- FORM -->
                <form id="multiStepForm" method="POST" action="{{ route('register.post') }}">
                    @csrf

                    <!-- Honeypot -->
                    <div id="website-wrapper">
                        <input type="text" id="website" name="website" value="" tabindex="-1" autocomplete="off">
                    </div>
                    <input type="hidden" name="form_start_time" id="form_start_time" value="">

                    <!-- STEP 1 -->
                    <div class="step-transition @if(($currentStep ?? 1) != 1) step-hidden @endif" data-step="1">
                        <div class="form-group">
                            <label class="form-label" for="company_name">
                                <i class="fas fa-building label-icon"></i>Jina la Kampuni
                            </label>
                            <div class="input-wrapper">
                                <input name="company_name" id="company_name" value="{{ old('company_name') }}" required
                                    placeholder="Mfano: MauzoShop Ltd"
                                    class="form-input"
                                    minlength="2" maxlength="255"
                                    data-validate="company_name"
                                    autocomplete="organization">
                            </div>
                            <div class="validation-message" id="company_name_message"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="owner_name">
                                <i class="fas fa-user-tie label-icon"></i>Jina la Mmiliki
                            </label>
                            <div class="input-wrapper">
                                <input name="owner_name" id="owner_name" value="{{ old('owner_name') }}" required
                                    placeholder="Jina kamili la mmiliki"
                                    class="form-input"
                                    pattern="[a-zA-Z\s\.\-]+" minlength="2" maxlength="255"
                                    data-validate="owner_name"
                                    autocomplete="name">
                            </div>
                            <div class="validation-message" id="owner_name_message"></div>
                        </div>

                        <input type="hidden" name="owner_gender" value="male">
                        <input type="hidden" name="owner_dob" value="2000-01-01">

                        <div class="flex items-center justify-between mt-5">
                            <span class="step-badge incomplete" id="step1_badge">
                                <i class="fas fa-times"></i> Haijakamilika
                            </span>
                            <button type="button" data-action="next" id="step1_next"
                                class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="btn-text">Endelea <i class="fas fa-arrow-right ml-1"></i></span>
                                <span class="spinner"></span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div class="step-transition @if(($currentStep ?? 1) != 2) step-hidden @endif" data-step="2">
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label" for="location">
                                    <i class="fas fa-map-marker-alt label-icon"></i>Mahali
                                </label>
                                <div class="input-wrapper">
                                    <input name="location" id="location" value="{{ old('location') }}" required
                                        placeholder="Eneo"
                                        class="form-input"
                                        minlength="2" maxlength="255"
                                        data-validate="location"
                                        autocomplete="address-level2">
                                </div>
                                <div class="validation-message" id="location_message"></div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="region">
                                    <i class="fas fa-map-pin label-icon"></i>Mkoa
                                </label>
                                <div class="input-wrapper">
                                    <select name="region" id="region" required class="form-input" data-validate="region">
                                        <option value="">Chagua Mkoa</option>
                                        @php $regions = ["Arusha","Dar es Salaam","Dodoma","Geita","Iringa","Kagera","Katavi","Kigoma","Kilimanjaro","Lindi","Manyara","Mara","Mwanza","Mbeya","Morogoro","Mtwara","Njombe","Pwani","Ruvuma","Rukwa","Shinyanga","Simiyu","Singida","Tabora","Tanga","Zanzibar North","Zanzibar South","Zanzibar Urban/West"]; @endphp
                                        @foreach($regions as $region)
                                            <option value="{{ $region }}" {{ old('region')==$region ? 'selected' : '' }}>{{ $region }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="validation-message" id="region_message"></div>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label" for="phone">
                                    <i class="fas fa-phone-alt label-icon"></i>Simu
                                </label>
                                <div class="input-wrapper">
                                    <input name="phone" id="phone" value="{{ old('phone') }}" required
                                        placeholder="07XXXXXXXX"
                                        class="form-input"
                                        pattern="^0[0-9]{9}$" maxlength="10" minlength="10"
                                        data-validate="phone"
                                        autocomplete="tel">
                                </div>
                                <div class="validation-message" id="phone_message"></div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="company_email">
                                    <i class="fas fa-envelope label-icon"></i>Barua Pepe
                                </label>
                                <div class="input-wrapper">
                                    <input name="company_email" id="company_email" type="email" value="{{ old('company_email') }}" required
                                        placeholder="info@kampuni.com"
                                        class="form-input"
                                        maxlength="255"
                                        data-validate="email"
                                        autocomplete="email">
                                </div>
                                <div class="validation-message" id="company_email_message"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="business_type">
                                <i class="fas fa-store label-icon"></i>Aina ya Biashara
                            </label>
                            <div class="input-wrapper">
                                <select name="business_type" id="business_type" required class="form-input" data-validate="business_type">
                                    <option value="">Chagua aina</option>
                                    <option value="retail_shop" {{ old('business_type')=='retail_shop' ? 'selected' : '' }}>Retail Shop / Duka</option>
                                    <option value="mini_market" {{ old('business_type')=='mini_market' ? 'selected' : '' }}>Mini Market</option>
                                    <option value="supermarket" {{ old('business_type')=='supermarket' ? 'selected' : '' }}>Supermarket</option>
                                    <option value="pharmacy" {{ old('business_type')=='pharmacy' ? 'selected' : '' }}>Pharmacy / Dawa</option>
                                    <option value="hardware" {{ old('business_type')=='hardware' ? 'selected' : '' }}>Hardware</option>
                                    <option value="stationery" {{ old('business_type')=='stationery' ? 'selected' : '' }}>Stationery</option>
                                    <option value="restaurant" {{ old('business_type')=='restaurant' ? 'selected' : '' }}>Restaurant</option>
                                    <option value="hotel" {{ old('business_type')=='hotel' ? 'selected' : '' }}>Hotel</option>
                                    <option value="salon" {{ old('business_type')=='salon' ? 'selected' : '' }}>Salon / Kinyozi</option>
                                    <option value="electronics" {{ old('business_type')=='electronics' ? 'selected' : '' }}>Electronics</option>
                                    <option value="wholesale" {{ old('business_type')=='wholesale' ? 'selected' : '' }}>Jumla / Wholesale</option>
                                    <option value="other" {{ old('business_type')=='other' ? 'selected' : '' }}>Nyingine</option>
                                </select>
                            </div>
                            <div class="validation-message" id="business_type_message"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="hear_about_us">
                                <i class="fas fa-bullhorn label-icon"></i>Umetusikia Wapi?
                            </label>
                            <div class="input-wrapper">
                                <select name="hear_about_us" id="hear_about_us" required class="form-input" data-validate="hear_about_us">
                                    <option value="">Chagua</option>
                                    <option value="friend" {{ old('hear_about_us')=='friend' ? 'selected' : '' }}>Rafiki</option>
                                    <option value="facebook" {{ old('hear_about_us')=='facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="instagram" {{ old('hear_about_us')=='instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="tiktok" {{ old('hear_about_us')=='tiktok' ? 'selected' : '' }}>TikTok</option>
                                    <option value="whatsapp" {{ old('hear_about_us')=='whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="google" {{ old('hear_about_us')=='google' ? 'selected' : '' }}>Google Search</option>
                                    <option value="invited" {{ old('hear_about_us')=='invited' ? 'selected' : '' }}>Nimealikwa</option>
                                    <option value="advertisement" {{ old('hear_about_us')=='advertisement' ? 'selected' : '' }}>Tangazo</option>
                                    <option value="other" {{ old('hear_about_us')=='other' ? 'selected' : '' }}>Nyingine</option>
                                </select>
                            </div>
                            <div class="validation-message" id="hear_about_us_message"></div>
                        </div>

                        <div class="flex items-center justify-between mt-5">
                            <button type="button" data-action="prev" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Rudi
                            </button>
                            <div class="flex items-center gap-3">
                                <span class="step-badge incomplete" id="step2_badge">
                                    <i class="fas fa-times"></i> Haijakamilika
                                </span>
                                <button type="button" data-action="next" id="step2_next"
                                    class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="btn-text">Endelea <i class="fas fa-arrow-right ml-1"></i></span>
                                    <span class="spinner"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div class="step-transition @if(($currentStep ?? 1) != 3) step-hidden @endif" data-step="3">
                        <div class="form-group">
                            <label class="form-label" for="username">
                                <i class="fas fa-user-circle label-icon"></i>Jina la Mtumiaji
                            </label>
                            <div class="input-wrapper">
                                <input name="username" id="username" value="{{ old('username') }}" required
                                    placeholder="Jina la kuingia mfumo"
                                    class="form-input"
                                    pattern="^[a-zA-Z0-9_]+$" minlength="3" maxlength="50"
                                    data-validate="username"
                                    autocomplete="username">
                            </div>
                            <div class="validation-message" id="username_message"></div>
                        </div>

                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label" for="password">
                                    <i class="fas fa-lock label-icon"></i>Neno la Siri
                                </label>
                                <div class="input-wrapper">
                                    <input type="password" name="password" id="password" required
                                        placeholder="Mfano: Mauzo@123"
                                        class="form-input"
                                        minlength="8"
                                        data-validate="password"
                                        autocomplete="new-password">
                                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="validation-message" id="password_message"></div>

                                <div class="password-strength-container">
                                    <div class="password-strength-bar">
                                        <div class="password-strength-fill" id="strengthFill"></div>
                                    </div>
                                    <div class="password-strength-label" id="strengthLabel"></div>
                                </div>

                                <div class="password-checklist" id="passwordChecklist">
                                    <div class="checklist-item" id="cl-length">
                                        <span class="check-icon"><i class="fas fa-times"></i></span>
                                        Angalau herufi 8
                                    </div>
                                    <div class="checklist-item" id="cl-uppercase">
                                        <span class="check-icon"><i class="fas fa-times"></i></span>
                                        Angalau herufi kubwa
                                    </div>
                                    <div class="checklist-item" id="cl-lowercase">
                                        <span class="check-icon"><i class="fas fa-times"></i></span>
                                        Angalau herufi ndogo
                                    </div>
                                    <div class="checklist-item" id="cl-number">
                                        <span class="check-icon"><i class="fas fa-times"></i></span>
                                        Angalau namba
                                    </div>
                                    <div class="checklist-item" id="cl-special">
                                        <span class="check-icon"><i class="fas fa-times"></i></span>
                                        Angalau alama maalum (@,#,$,etc)
                                    </div>
                                </div>

                                <div class="password-sample">
                                    <i class="fas fa-info-circle"></i> Mfano: <strong>Mauzo@123</strong>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="password_confirmation">
                                    <i class="fas fa-check-circle label-icon"></i>Thibitisha
                                </label>
                                <div class="input-wrapper">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                        placeholder="Andika tena"
                                        class="form-input"
                                        data-validate="password_confirm"
                                        autocomplete="new-password">
                                    <button type="button" class="password-toggle" id="toggleConfirm" aria-label="Show password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="validation-message" id="password_confirmation_message"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-5">
                            <button type="button" data-action="prev" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Rudi
                            </button>
                            <div class="flex items-center gap-3">
                                <span class="step-badge incomplete" id="step3_badge">
                                    <i class="fas fa-times"></i> Haijakamilika
                                </span>
                                <button type="submit" id="submitBtn"
                                    class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="btn-text"><i class="fas fa-check-circle mr-1"></i> Sajili</span>
                                    <span class="spinner"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>

                <!-- FOOTER -->
                <div class="form-footer text-center">
                    <p class="text-sm text-gray-500">
                        Una akaunti tayari?
                        <a href="{{ route('login') }}" class="link-primary">Ingia hapa</a>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- JAVASCRIPT - AUTO-VALIDATION & NAVIGATION -->
    <!-- ========================================= -->
    <script>
        (function() {
            'use strict';

            const form = document.getElementById('multiStepForm');
            const steps = Array.from(document.querySelectorAll('.step-transition'));
            let currentStep = {{ $currentStep ?? 1 }} - 1;
            const stepDots = document.querySelectorAll('.step-dot');
            const stepLines = document.querySelectorAll('.step-line');
            const stepLabels = document.querySelectorAll('.step-label');

            // Validation rules - only show errors when user makes a mistake
            const validations = {
                company_name: {
                    validate: (v) => v.trim().length >= 2,
                    msg: 'Jina la kampuni lazima liwe na angalau herufi 2'
                },
                owner_name: {
                    validate: (v) => /^[a-zA-Z\s\.\-]+$/.test(v) && v.trim().length >= 2,
                    msg: 'Jina lazima liwe na herufi tu na angalau herufi 2'
                },
                location: {
                    validate: (v) => v.trim().length >= 2,
                    msg: 'Tafadhali weka eneo lako'
                },
                region: {
                    validate: (v) => v !== '',
                    msg: 'Tafadhali chagua mkoa wako'
                },
                phone: {
                    validate: (v) => /^0[0-9]{9}$/.test(v),
                    msg: 'Nambari ya simu lazima iwe tarakimu 10 kuanzia 0'
                },
                email: {
                    validate: (v) => {
                        if (!v) return false;
                        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!re.test(v)) return false;
                        const domain = v.split('@')[1];
                        const disposable = ['mailinator.com', 'guerrillamail.com', '10minutemail.com',
                            'temp-mail.org', 'yopmail.com', 'throwawaymail.com', 'fakeinbox.com'
                        ];
                        return !disposable.includes(domain);
                    },
                    msg: 'Tafadhali tumia barua pepe halisi'
                },
                business_type: {
                    validate: (v) => v !== '',
                    msg: 'Tafadhali chagua aina ya biashara'
                },
                hear_about_us: {
                    validate: (v) => v !== '',
                    msg: 'Tafadhali chagua umetusikia wapi'
                },
                username: {
                    validate: (v) => /^[a-zA-Z0-9_]{3,50}$/.test(v),
                    msg: 'Jina la mtumiaji lazima liwe na herufi, namba au underscore, angalau 3'
                },
                password: {
                    validate: (v) => {
                        return v.length >= 8 &&
                            /[A-Z]/.test(v) &&
                            /[a-z]/.test(v) &&
                            /\d/.test(v) &&
                            /[@#$%^&+=!]/.test(v);
                    },
                    msg: 'Nenosiri lazima liwe na angalau herufi 8, herufi kubwa, herufi ndogo, namba na alama maalum'
                },
                password_confirm: {
                    validate: (v) => {
                        const pwd = document.getElementById('password');
                        return pwd && v === pwd.value && v.length > 0;
                    },
                    msg: 'Nenosiri halilingani'
                }
            };

            // Validate single field - only show error when user has typed something
            function validateField(field) {
                const input = document.getElementById(field);
                if (!input) return true;

                const rule = validations[field];
                if (!rule) return true;

                const value = input.value;
                const hasValue = value !== '' && value !== null && value !== undefined;
                const isValid = rule.validate(value);
                const msgEl = document.getElementById(field + '_message');

                // Password fields handled separately
                if (field === 'password' || field === 'password_confirm') {
                    if (field === 'password') {
                        updatePasswordStrength(value);
                    }
                    if (field === 'password_confirm') {
                        updatePasswordConfirm(value);
                    }
                    // Only return false if there's a value and it's invalid
                    if (hasValue && !isValid) return false;
                    return true;
                }

                // Only show validation if user has typed something
                if (!hasValue) {
                    input.classList.remove('valid', 'invalid');
                    if (msgEl) {
                        msgEl.className = 'validation-message';
                        msgEl.innerHTML = '';
                        msgEl.classList.remove('visible');
                    }
                    return false;
                }

                if (isValid) {
                    input.classList.remove('invalid');
                    input.classList.add('valid');
                    if (msgEl) {
                        msgEl.className = 'validation-message valid visible';
                        msgEl.innerHTML = '<span class="msg-icon"><i class="fas fa-check-circle"></i></span> Inaonekana vizuri';
                    }
                } else {
                    input.classList.remove('valid');
                    input.classList.add('invalid');
                    if (msgEl) {
                        msgEl.className = 'validation-message invalid visible';
                        msgEl.innerHTML = '<span class="msg-icon"><i class="fas fa-exclamation-circle"></i></span> ' + rule.msg;
                    }
                }

                return isValid;
            }

            // Password strength
            function updatePasswordStrength(password) {
                const fill = document.getElementById('strengthFill');
                const label = document.getElementById('strengthLabel');
                const checklist = document.getElementById('passwordChecklist');

                const checks = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[@#$%^&+=!]/.test(password)
                };

                // Update checklist
                const clMap = {
                    length: 'cl-length',
                    uppercase: 'cl-uppercase',
                    lowercase: 'cl-lowercase',
                    number: 'cl-number',
                    special: 'cl-special'
                };

                let metCount = 0;
                Object.keys(checks).forEach(key => {
                    const el = document.getElementById(clMap[key]);
                    if (el) {
                        const icon = el.querySelector('.check-icon i');
                        if (checks[key]) {
                            el.classList.add('met');
                            el.classList.remove('unmet');
                            if (icon) { icon.className = 'fas fa-check'; }
                            metCount++;
                        } else {
                            el.classList.remove('met');
                            el.classList.add('unmet');
                            if (icon) { icon.className = 'fas fa-times'; }
                        }
                    }
                });

                // Show checklist only if password has length > 0
                if (password.length > 0) {
                    checklist.classList.add('visible');
                } else {
                    checklist.classList.remove('visible');
                }

                // Update strength bar
                let strength = 'weak';
                let labelText = 'Dhaifu';
                if (password.length === 0) {
                    fill.className = 'password-strength-fill';
                    fill.style.width = '0%';
                    label.className = 'password-strength-label';
                    label.textContent = '';
                    return;
                }

                if (metCount <= 2) {
                    strength = 'weak';
                    labelText = 'Dhaifu';
                } else if (metCount === 3) {
                    strength = 'medium';
                    labelText = 'Wastani';
                } else if (metCount === 4) {
                    strength = 'strong';
                    labelText = 'Nzuri';
                } else {
                    strength = 'excellent';
                    labelText = 'Bora!';
                }

                fill.className = 'password-strength-fill ' + strength;
                label.className = 'password-strength-label ' + strength + ' visible';
                label.textContent = labelText;

                // Validate password field - only show error if user has typed
                const pwdInput = document.getElementById('password');
                const msgEl = document.getElementById('password_message');
                const isValid = checks.length && checks.uppercase && checks.lowercase && checks.number && checks.special;

                if (password.length > 0) {
                    if (isValid) {
                        pwdInput.classList.remove('invalid');
                        pwdInput.classList.add('valid');
                        if (msgEl) {
                            msgEl.className = 'validation-message valid visible';
                            msgEl.innerHTML = '<span class="msg-icon"><i class="fas fa-check-circle"></i></span> Inaonekana vizuri';
                        }
                    } else {
                        pwdInput.classList.remove('valid');
                        pwdInput.classList.add('invalid');
                        if (msgEl) {
                            msgEl.className = 'validation-message invalid visible';
                            msgEl.innerHTML = '<span class="msg-icon"><i class="fas fa-exclamation-circle"></i></span> Nenosiri halikidhi mahitaji';
                        }
                    }
                } else {
                    pwdInput.classList.remove('valid', 'invalid');
                    if (msgEl) {
                        msgEl.className = 'validation-message';
                        msgEl.innerHTML = '';
                        msgEl.classList.remove('visible');
                    }
                }

                return isValid;
            }

            // Password confirm
            function updatePasswordConfirm(value) {
                const pwd = document.getElementById('password');
                const input = document.getElementById('password_confirmation');
                const msgEl = document.getElementById('password_confirmation_message');

                if (value.length === 0) {
                    input.classList.remove('valid', 'invalid');
                    if (msgEl) {
                        msgEl.className = 'validation-message';
                        msgEl.innerHTML = '';
                        msgEl.classList.remove('visible');
                    }
                    return false;
                }

                const isValid = value === pwd.value;

                if (isValid) {
                    input.classList.remove('invalid');
                    input.classList.add('valid');
                    if (msgEl) {
                        msgEl.className = 'validation-message valid visible';
                        msgEl.innerHTML = '<span class="msg-icon"><i class="fas fa-check-circle"></i></span> Nenosiri linalingana';
                    }
                } else {
                    input.classList.remove('valid');
                    input.classList.add('invalid');
                    if (msgEl) {
                        msgEl.className = 'validation-message invalid visible';
                        msgEl.innerHTML = '<span class="msg-icon"><i class="fas fa-exclamation-circle"></i></span> Nenosiri halilingani';
                    }
                }

                return isValid;
            }

            // Get fields in a step
            function getStepFields(stepIndex) {
                const step = steps[stepIndex];
                if (!step) return [];
                return Array.from(step.querySelectorAll('[data-validate]')).map(el => el.id);
            }

            // Validate entire step
            function validateStep(stepIndex) {
                const fields = getStepFields(stepIndex);
                let allValid = true;

                fields.forEach(field => {
                    const isValid = validateField(field);
                    if (!isValid) allValid = false;
                });

                // Special: check password confirm
                if (stepIndex === 2) {
                    const confirmValid = validateField('password_confirm');
                    if (!confirmValid) allValid = false;
                }

                return allValid;
            }

            // Update step badge
            function updateStepBadge(stepIndex) {
                const badge = document.getElementById('step' + (stepIndex + 1) + '_badge');
                if (!badge) return;

                const isValid = validateStep(stepIndex);
                if (isValid) {
                    badge.className = 'step-badge complete';
                    badge.innerHTML = '<i class="fas fa-check"></i> Imekamilika';
                } else {
                    badge.className = 'step-badge incomplete';
                    badge.innerHTML = '<i class="fas fa-times"></i> Haijakamilika';
                }

                const nextBtn = document.getElementById('step' + (stepIndex + 1) + '_next');
                if (nextBtn) {
                    nextBtn.disabled = !isValid;
                }
            }

            // Update stepper UI
            function updateStepper(activeIndex) {
                stepDots.forEach((dot, idx) => {
                    dot.classList.remove('active', 'completed');
                    dot.querySelector('.step-number').style.display = '';
                    dot.querySelector('.check-mark').style.display = 'none';

                    if (idx === activeIndex) {
                        dot.classList.add('active');
                    } else if (idx < activeIndex) {
                        dot.classList.add('completed');
                        dot.querySelector('.step-number').style.display = 'none';
                        dot.querySelector('.check-mark').style.display = 'block';
                    }
                });

                stepLines.forEach((line, idx) => {
                    line.classList.remove('completed', 'partial');
                    if (idx < activeIndex) {
                        line.classList.add('completed');
                    } else if (idx === activeIndex) {
                        line.classList.add('partial');
                    }
                });

                stepLabels.forEach((label, idx) => {
                    label.classList.remove('active', 'completed');
                    if (idx === activeIndex) {
                        label.classList.add('active');
                    } else if (idx < activeIndex) {
                        label.classList.add('completed');
                    }
                });
            }

            // Update steps visibility
            function updateSteps() {
                steps.forEach((step, idx) => {
                    if (idx === currentStep) {
                        step.classList.remove('step-hidden');
                        step.style.display = 'block';
                    } else {
                        step.classList.add('step-hidden');
                        step.style.display = 'none';
                    }
                });

                updateStepper(currentStep);
                updateAllBadges();
            }

            function updateAllBadges() {
                for (let i = 0; i < steps.length; i++) {
                    updateStepBadge(i);
                }
            }

            // Event listeners for all validate fields
            document.querySelectorAll('[data-validate]').forEach(input => {
                input.addEventListener('input', function() {
                    const field = this.id;
                    validateField(field);
                    updateStepBadge(currentStep);

                    const isStepValid = validateStep(currentStep);
                    const nextBtn = document.getElementById('step' + (currentStep + 1) + '_next');
                    if (nextBtn) {
                        nextBtn.disabled = !isStepValid;
                    }
                });

                input.addEventListener('blur', function() {
                    validateField(this.id);
                });

                input.addEventListener('change', function() {
                    validateField(this.id);
                });
            });

            // Password toggle
            document.getElementById('togglePassword').addEventListener('click', function() {
                const input = document.getElementById('password');
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });

            document.getElementById('toggleConfirm').addEventListener('click', function() {
                const input = document.getElementById('password_confirmation');
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });

            // Password input listeners
            const pwdInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');

            pwdInput.addEventListener('input', function() {
                validateField('password');
                validateField('password_confirm');
                updateStepBadge(currentStep);
            });

            confirmInput.addEventListener('input', function() {
                validateField('password_confirm');
                updateStepBadge(currentStep);
            });

            // Navigation
            document.querySelectorAll('[data-action]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const action = this.dataset.action;

                    if (action === 'next') {
                        if (!validateStep(currentStep)) {
                            const step = steps[currentStep];
                            const firstInvalid = step.querySelector('.form-input.invalid, [data-validate].invalid');
                            if (firstInvalid) {
                                firstInvalid.focus();
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return;
                        }

                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            updateSteps();
                            document.querySelector('.register-card-wrapper').scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    } else if (action === 'prev' && currentStep > 0) {
                        currentStep--;
                        updateSteps();
                    }
                });
            });

            // Form submit
            form.addEventListener('submit', function(e) {
                for (let i = 0; i < steps.length; i++) {
                    if (!validateStep(i)) {
                        e.preventDefault();
                        currentStep = i;
                        updateSteps();
                        const firstInvalid = steps[i].querySelector('.form-input.invalid, [data-validate].invalid');
                        if (firstInvalid) {
                            setTimeout(() => {
                                firstInvalid.focus();
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }, 300);
                        }
                        return;
                    }
                }

                const submitBtn = document.getElementById('submitBtn');
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });

            // Init
            updateSteps();

            // Initial validation for old values
            setTimeout(() => {
                document.querySelectorAll('[data-validate]').forEach(input => {
                    if (input.value) {
                        validateField(input.id);
                        updateStepBadge(currentStep);
                    }
                });
                updateAllBadges();
            }, 200);

            console.log('✅ Premium registration UI loaded - validation only shows errors');

        })();
    </script>
</body>
</html>