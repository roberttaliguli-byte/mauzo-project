<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Orders - {{ $company->company_name }}</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #f59e0b;
            --primary-dark: #d97706;
            --secondary: #10b981;
            --dark: #1f2937;
            --gray: #6b7280;
            --light: #f3f4f6;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0,0,0,0.08);
            --radius: 16px;
            --radius-sm: 8px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        /* ===== HEADER ===== */
        .header {
            background: linear-gradient(135deg, var(--dark) 0%, #374151 100%);
            color: white;
            padding: 30px 16px 50px;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .header-top .back-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }
        
        .header-top .back-link:hover {
            color: white;
        }
        
        .header-top .customer-info {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.1);
            padding: 8px 16px;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }
        
        .header-top .customer-info .avatar {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        
        .header-top .customer-info .name {
            font-weight: 500;
            font-size: 14px;
        }
        
        .header-top .customer-info .code {
            font-size: 11px;
            font-family: monospace;
            color: rgba(255,255,255,0.6);
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 800;
        }
        
        .header h1 span { color: var(--primary); }
        
        .header p {
            color: rgba(255,255,255,0.7);
            font-size: 15px;
            margin-top: 4px;
        }
        
        /* ===== STATS ===== */
        .stats-bar {
            max-width: 1200px;
            margin: -30px auto 20px;
            padding: 0 16px;
            position: relative;
            z-index: 5;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
            gap: 6px;
            background: white;
            border-radius: var(--radius);
            padding: 12px 16px;
            box-shadow: var(--shadow);
            border: 1px solid #e5e7eb;
        }
        
        .stat-item {
            text-align: center;
            padding: 4px;
        }
        
        .stat-item .number {
            font-size: 16px;
            font-weight: 700;
            display: block;
        }
        
        .stat-item .label {
            font-size: 9px;
            color: var(--gray);
        }
        
        .stat-item .number.total { color: var(--dark); }
        .stat-item .number.pending { color: #f59e0b; }
        .stat-item .number.confirmed { color: #3b82f6; }
        .stat-item .number.processing { color: #8b5cf6; }
        .stat-item .number.ready { color: #6366f1; }
        .stat-item .number.shipped { color: #f97316; }
        .stat-item .number.delivered { color: #10b981; }
        .stat-item .number.cancelled { color: #ef4444; }
        
        /* ===== ORDERS LIST ===== */
        .orders-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px 40px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .section-header h2 {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-header h2 i { color: var(--primary); }
        
        .section-header .filter-buttons {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 4px 10px;
            border-radius: 50px;
            border: 1px solid #e5e7eb;
            background: white;
            font-size: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray);
        }
        
        .filter-btn:hover {
            border-color: var(--primary);
            color: var(--dark);
        }
        
        .filter-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        /* ===== ORDER CARD ===== */
        .order-card {
            background: white;
            border-radius: var(--radius);
            border: 1px solid #e5e7eb;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            cursor: pointer;
        }
        
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        
        .order-card .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .order-card .order-number {
            font-weight: 700;
            font-size: 15px;
            color: var(--dark);
        }
        
        .order-card .order-number small {
            font-weight: 400;
            color: var(--gray);
            font-size: 12px;
        }
        
        .order-card .order-date {
            font-size: 12px;
            color: var(--gray);
        }
        
        .order-card .order-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .order-card .order-status .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-saved { background: #fef3c7; color: #92400e; }
        .status-saved .status-dot { background: #f59e0b; }
        
        .status-confirmed { background: #dbeafe; color: #1e40af; }
        .status-confirmed .status-dot { background: #3b82f6; }
        
        .status-processing { background: #ede9fe; color: #5b21b6; }
        .status-processing .status-dot { background: #8b5cf6; }
        
        .status-ready { background: #e0e7ff; color: #3730a3; }
        .status-ready .status-dot { background: #6366f1; }
        
        .status-shipped { background: #ffedd5; color: #9a3412; }
        .status-shipped .status-dot { background: #f97316; }
        
        .status-delivered { background: #d1fae5; color: #065f46; }
        .status-delivered .status-dot { background: #10b981; }
        
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-cancelled .status-dot { background: #ef4444; }
        
        .order-card .order-body {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .order-card .order-items {
            flex: 1;
            min-width: 150px;
        }
        
        .order-card .order-items .item {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 13px;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .order-card .order-items .item:last-child {
            border-bottom: none;
        }
        
        .order-card .order-items .item .qty {
            color: var(--gray);
            font-size: 12px;
        }
        
        .order-card .order-total {
            text-align: right;
            min-width: 100px;
        }
        
        .order-card .order-total .label {
            font-size: 11px;
            color: var(--gray);
        }
        
        .order-card .order-total .amount {
            font-size: 17px;
            font-weight: 700;
            color: var(--primary);
        }
        
        /* ===== ORDER ACTIONS - MODERN BUTTONS ===== */
        .order-actions {
            display: flex;
            gap: 6px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f3f4f6;
            flex-wrap: wrap;
        }
        
        .order-actions .action-btn {
            padding: 6px 14px;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-family: inherit;
        }
        
        .order-actions .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .order-actions .action-btn:active {
            transform: scale(0.97);
        }
        
        .order-actions .btn-view {
            background: #f3f4f6;
            color: var(--dark);
        }
        
        .order-actions .btn-view:hover {
            background: #e5e7eb;
        }
        
        .order-actions .btn-whatsapp {
            background: #25D366;
            color: white;
        }
        
        .order-actions .btn-whatsapp:hover {
            background: #1DA851;
        }
        
        .order-actions .btn-track {
            background: var(--primary);
            color: white;
        }
        
        .order-actions .btn-track:hover {
            background: var(--primary-dark);
        }
        
        .order-actions .btn-print {
            background: #6b7280;
            color: white;
        }
        
        .order-actions .btn-print:hover {
            background: #4b5563;
        }
        
        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 56px;
            color: #d1d5db;
            margin-bottom: 16px;
        }
        
        .empty-state h3 {
            font-size: 20px;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .empty-state p {
            font-size: 14px;
        }
        
        .empty-state .btn-shop {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 30px;
            background: var(--primary);
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .empty-state .btn-shop:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        /* ===== ORDER DETAIL MODAL ===== */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 16px;
            backdrop-filter: blur(4px);
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: var(--radius);
            max-width: 520px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 24px;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f3f4f6;
        }
        
        .modal-content .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
        }
        
        .modal-content .modal-header .close-btn {
            background: none;
            border: none;
            font-size: 22px;
            color: var(--gray);
            cursor: pointer;
            transition: var(--transition);
            padding: 4px 8px;
            border-radius: 8px;
        }
        
        .modal-content .modal-header .close-btn:hover {
            background: #f3f4f6;
            color: var(--dark);
        }
        
        .modal-content .order-detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        
        .modal-content .order-detail-item:last-child {
            border-bottom: none;
        }
        
        .modal-content .order-detail-item .label {
            color: var(--gray);
        }
        
        .modal-content .order-detail-item .value {
            font-weight: 500;
        }
        
        .modal-content .tracking-timeline {
            margin: 16px 0;
            padding: 0;
            list-style: none;
            position: relative;
        }
        
        .modal-content .tracking-timeline::before {
            content: '';
            position: absolute;
            left: 14px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }
        
        .modal-content .tracking-timeline li {
            padding-left: 40px;
            padding-bottom: 16px;
            position: relative;
        }
        
        .modal-content .tracking-timeline li:last-child {
            padding-bottom: 0;
        }
        
        .modal-content .tracking-timeline li .dot {
            position: absolute;
            left: 8px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid #e5e7eb;
            background: white;
        }
        
        .modal-content .tracking-timeline li.active .dot {
            background: var(--secondary);
            border-color: var(--secondary);
        }
        
        .modal-content .tracking-timeline li .time {
            font-size: 11px;
            color: var(--gray);
        }
        
        .modal-content .tracking-timeline li .title {
            font-weight: 500;
            font-size: 14px;
        }
        
        .modal-content .modal-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #f3f4f6;
            flex-wrap: wrap;
        }
        
        .modal-content .modal-actions .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: inherit;
            flex: 1;
            justify-content: center;
            min-width: 80px;
        }
        
        .modal-content .modal-actions .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .modal-content .modal-actions .btn-whatsapp {
            background: #25D366;
            color: white;
        }
        
        .modal-content .modal-actions .btn-whatsapp:hover {
            background: #1DA851;
        }
        
        .modal-content .modal-actions .btn-print {
            background: #6b7280;
            color: white;
        }
        
        .modal-content .modal-actions .btn-print:hover {
            background: #4b5563;
        }
        
        .modal-content .modal-actions .btn-close {
            background: #f3f4f6;
            color: var(--dark);
        }
        
        .modal-content .modal-actions .btn-close:hover {
            background: #e5e7eb;
        }
        
        /* ===== LOADING ===== */
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--gray);
        }
        
        .loading .spinner {
            display: inline-block;
            width: 32px;
            height: 32px;
            border: 3px solid #e5e7eb;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s ease infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* ===== TOAST - CENTERED ===== */
        .toast-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            max-width: 90%;
            width: 400px;
            pointer-events: none;
        }
        
        .toast {
            padding: 12px 24px;
            border-radius: var(--radius-sm);
            color: white;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            animation: slideDown 0.4s ease forwards;
            width: 100%;
            text-align: center;
            pointer-events: auto;
        }
        
        .toast-success { background: #10b981; }
        .toast-error { background: #ef4444; }
        .toast-info { background: #3b82f6; }
        .toast-warning { background: #f59e0b; }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        .toast-out {
            animation: slideUp 0.3s ease forwards;
        }
        
        @keyframes slideUp {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-30px) scale(0.95); }
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .header h1 { font-size: 22px; }
            .header-top { flex-direction: column; align-items: stretch; }
            .header-top .customer-info { justify-content: center; }
            .stats-grid { grid-template-columns: repeat(4, 1fr); }
            .order-card .order-header { flex-direction: column; }
            .order-card .order-body { flex-direction: column; }
            .order-card .order-total { text-align: left; }
            .section-header .filter-buttons { width: 100%; overflow-x: auto; flex-wrap: nowrap; -webkit-overflow-scrolling: touch; }
            .filter-btn { font-size: 9px; padding: 3px 8px; white-space: nowrap; }
            .order-actions .action-btn { font-size: 10px; padding: 4px 10px; }
            .order-actions .action-btn span { display: none; }
            .modal-content { padding: 16px; margin: 8px; }
            .modal-content .modal-actions .action-btn { font-size: 11px; padding: 6px 12px; min-width: 60px; }
        }
        
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
            .stats-grid .stat-item { padding: 2px; }
            .stats-grid .stat-item .number { font-size: 14px; }
            .stats-grid .stat-item .label { font-size: 8px; }
            .order-card { padding: 12px; }
            .order-card .order-number { font-size: 13px; }
            .order-card .order-total .amount { font-size: 15px; }
        }
        
        @media (min-width: 769px) {
            .order-actions .action-btn span { display: inline; }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
</head>
<body>

    <!-- ===== HEADER ===== -->
    <header class="header">
        <div class="header-container">
            <div class="header-top">
                <a href="{{ route('public.showcase', ['identifier' => $company->id]) }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Shop
                </a>
                <div class="customer-info">
                    <div class="avatar">{{ substr($customer->jina, 0, 1) }}</div>
                    <div>
                        <div class="name">{{ $customer->jina }}</div>
                        <div class="code">{{ $customer->customer_code }}</div>
                    </div>
                    <button onclick="logoutCustomer()" style="background:none;border:none;color:rgba(255,255,255,0.6);cursor:pointer;font-size:14px;transition:var(--transition);padding:4px 8px;border-radius:8px;">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
            <h1>My <span>Orders</span></h1>
            <p>Track all your orders and their status in one place</p>
        </div>
    </header>

    <!-- ===== STATS BAR ===== -->
    <div class="stats-bar">
        <div class="stats-grid" id="statsGrid">
            <div class="stat-item">
                <span class="number total" id="statTotal">0</span>
                <span class="label">Jumla</span>
            </div>
            <div class="stat-item">
                <span class="number pending" id="statPending">0</span>
                <span class="label">Inasubiri</span>
            </div>
            <div class="stat-item">
                <span class="number confirmed" id="statConfirmed">0</span>
                <span class="label">Imethibitishwa</span>
            </div>
            <div class="stat-item">
                <span class="number processing" id="statProcessing">0</span>
                <span class="label">Inachakatwa</span>
            </div>
            <div class="stat-item">
                <span class="number ready" id="statReady">0</span>
                <span class="label">Tayari</span>
            </div>
            <div class="stat-item">
                <span class="number shipped" id="statShipped">0</span>
                <span class="label">Imesafirishwa</span>
            </div>
            <div class="stat-item">
                <span class="number delivered" id="statDelivered">0</span>
                <span class="label">Imewasilishwa</span>
            </div>
            <div class="stat-item">
                <span class="number cancelled" id="statCancelled">0</span>
                <span class="label">Imefutwa</span>
            </div>
        </div>
    </div>

    <!-- ===== ORDERS SECTION ===== -->
    <section class="orders-section">
        <div class="section-header">
            <h2><i class="fas fa-clipboard-list"></i> Orders</h2>
            <div class="filter-buttons" id="filterButtons">
                <button class="filter-btn active" data-filter="all">Zote</button>
                <button class="filter-btn" data-filter="saved">Inasubiri</button>
                <button class="filter-btn" data-filter="confirmed">Imethibitishwa</button>
                <button class="filter-btn" data-filter="processing">Inachakatwa</button>
                <button class="filter-btn" data-filter="ready">Tayari</button>
                <button class="filter-btn" data-filter="shipped">Imesafirishwa</button>
                <button class="filter-btn" data-filter="delivered">Imewasilishwa</button>
                <button class="filter-btn" data-filter="cancelled">Imefutwa</button>
            </div>
        </div>

        <!-- Orders List -->
        <div id="ordersList">
            @if($orders->count() > 0)
                @foreach($orders as $order)
                    <div class="order-card" data-status="{{ $order->status }}">
                        <div class="order-header">
                            <div>
                                <div class="order-number">
                                    {{ $order->order_number }}
                                    <small>{{ $order->order_type ?? 'delivery' }}</small>
                                </div>
                                <div class="order-date">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="order-status status-{{ $order->status }}">
                                <span class="status-dot"></span>
                                {{ $order->status_label ?? $order->status }}
                            </div>
                        </div>

                        <div class="order-body">
                            <div class="order-items">
                                @foreach(($order->items ?? []) as $item)
                                    <div class="item">
                                        <span>{{ $item['jina'] ?? $item['name'] ?? 'Product' }}</span>
                                        <span><span class="qty">{{ $item['idadi'] ?? $item['qty'] ?? 0 }}×</span> {{ number_format($item['bei'] ?? $item['price'] ?? 0, 0) }} TZS</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="order-total">
                                <div class="label">Jumla</div>
                                <div class="amount">{{ number_format($order->total, 0) }} TZS</div>
                            </div>
                        </div>

                        <!-- ===== MODERN ACTION BUTTONS ===== -->
                        <div class="order-actions">
                            <button class="action-btn btn-view" onclick="viewOrder('{{ $order->id }}')">
                                <i class="fas fa-eye"></i>
                                <span>Tazama</span>
                            </button>
                            <button class="action-btn btn-whatsapp" onclick="shareOrderWhatsApp('{{ $order->id }}')">
                                <i class="fab fa-whatsapp"></i>
                                <span>WhatsApp</span>
                            </button>
                            <button class="action-btn btn-track" onclick="trackOrder('{{ $order->id }}')">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Fuatilia</span>
                            </button>
                            <button class="action-btn btn-print" onclick="printOrder('{{ $order->id }}')">
                                <i class="fas fa-print"></i>
                                <span>Chapisha</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>Hakuna Oda Bado</h3>
                    <p>Anza kununua na uweke oda yako ya kwanza!</p>
                    <a href="{{ route('public.showcase', ['identifier' => $company->id]) }}" class="btn-shop">
                        <i class="fas fa-shopping-cart"></i> Anza Kununua
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- ===== ORDER DETAIL MODAL ===== -->
    <div class="modal" id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalOrderNumber">Maelezo ya Oda</h3>
                <button class="close-btn" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalBody">
                <!-- Dynamic content -->
            </div>
        </div>
    </div>

    <!-- ===== TOAST CONTAINER ===== -->
    <div id="toastContainer" class="toast-container"></div>

    <script>
        // ============================================
        // ORDERS PAGE - JavaScript
        // ============================================
        
        const COMPANY_ID = {{ $company->id }};
        const COMPANY_NAME = '{{ $company->company_name }}';
        let allOrders = [];
        let currentFilter = 'all';

        // ===== INIT =====
        document.addEventListener('DOMContentLoaded', function() {
            allOrders = document.querySelectorAll('.order-card');
            updateStats();
            
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.dataset.filter;
                    filterOrders(currentFilter);
                });
            });
        });

        // ===== FILTER ORDERS =====
        function filterOrders(filter) {
            const cards = document.querySelectorAll('.order-card');
            let visible = 0;
            
            cards.forEach(card => {
                const status = card.dataset.status;
                if (filter === 'all' || status === filter) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            const emptyState = document.querySelector('.empty-state');
            if (visible === 0 && cards.length > 0) {
                const container = document.getElementById('ordersList');
                let existingEmpty = container.querySelector('.empty-state-filter');
                if (!existingEmpty) {
                    const empty = document.createElement('div');
                    empty.className = 'empty-state empty-state-filter';
                    empty.innerHTML = `
                        <i class="fas fa-filter"></i>
                        <h3>Hakuna Oda</h3>
                        <p>Hakuna oda zenye hali ya "${filter}"</p>
                    `;
                    container.appendChild(empty);
                }
            } else {
                const empty = document.querySelector('.empty-state-filter');
                if (empty) empty.remove();
            }
        }

        // ===== UPDATE STATS =====
        function updateStats() {
            const cards = document.querySelectorAll('.order-card');
            const stats = {
                total: cards.length,
                saved: 0,
                confirmed: 0,
                processing: 0,
                ready: 0,
                shipped: 0,
                delivered: 0,
                cancelled: 0
            };
            
            cards.forEach(card => {
                const status = card.dataset.status;
                if (stats.hasOwnProperty(status)) {
                    stats[status]++;
                }
            });
            
            document.getElementById('statTotal').textContent = stats.total;
            document.getElementById('statPending').textContent = stats.saved;
            document.getElementById('statConfirmed').textContent = stats.confirmed;
            document.getElementById('statProcessing').textContent = stats.processing;
            document.getElementById('statReady').textContent = stats.ready;
            document.getElementById('statShipped').textContent = stats.shipped;
            document.getElementById('statDelivered').textContent = stats.delivered;
            document.getElementById('statCancelled').textContent = stats.cancelled;
        }

        // ===== VIEW ORDER =====
        function viewOrder(orderId) {
            fetch(`/shop/${COMPANY_ID}/orders-json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const order = data.orders.find(o => o.id == orderId);
                        if (order) {
                            displayOrderModal(order);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Hitilafu katika kupakia maelezo', 'error');
                });
        }

        function displayOrderModal(order) {
            const modal = document.getElementById('orderModal');
            const body = document.getElementById('modalBody');
            
            document.getElementById('modalOrderNumber').textContent = `Oda #${order.order_number}`;
            
            let itemsHtml = '';
            if (order.items && order.items.length > 0) {
                order.items.forEach(item => {
                    const itemName = item.jina || item.name || 'Bidhaa';
                    const itemQty = item.idadi || item.qty || 0;
                    const itemPrice = item.bei || item.price || 0;
                    const itemTotal = item.total || (itemQty * itemPrice);
                    itemsHtml += `
                        <div class="order-detail-item">
                            <span class="label">${itemName} × ${itemQty}</span>
                            <span class="value">${formatCurrency(itemTotal)}</span>
                        </div>
                    `;
                });
            }
            
            body.innerHTML = `
                <div class="order-detail-item">
                    <span class="label">Hali</span>
                    <span class="order-status status-${order.status}">
                        <span class="status-dot"></span>
                        ${order.status_label}
                    </span>
                </div>
                <div class="order-detail-item">
                    <span class="label">Tarehe</span>
                    <span class="value">${order.created_at_formatted}</span>
                </div>
                <div class="order-detail-item">
                    <span class="label">Aina</span>
                    <span class="value">${order.order_type || 'Delivery'}</span>
                </div>
                <div class="order-detail-item">
                    <span class="label">Mteja</span>
                    <span class="value">${order.customer_name}</span>
                </div>
                <div class="order-detail-item">
                    <span class="label">Simu</span>
                    <span class="value">${order.customer_phone || '-'}</span>
                </div>
                ${order.delivery_address ? `
                <div class="order-detail-item">
                    <span class="label">Anuani</span>
                    <span class="value">${order.delivery_address}</span>
                </div>
                ` : ''}
                ${order.special_instructions ? `
                <div class="order-detail-item">
                    <span class="label">Maelekezo</span>
                    <span class="value">${order.special_instructions}</span>
                </div>
                ` : ''}
                <div style="border-top:2px solid #e5e7eb;padding-top:12px;margin-top:12px;">
                    <strong>Bidhaa:</strong>
                    ${itemsHtml}
                </div>
                <div style="border-top:2px solid #e5e7eb;padding-top:12px;margin-top:12px;display:flex;justify-content:space-between;">
                    <strong>Jumla</strong>
                    <strong style="color:#f59e0b;font-size:18px;">${formatCurrency(order.total)}</strong>
                </div>
                <div class="modal-actions">
                    <button class="action-btn btn-whatsapp" onclick="shareOrderWhatsApp('${order.id}')">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </button>
                    <button class="action-btn btn-print" onclick="printOrder('${order.id}')">
                        <i class="fas fa-print"></i> Chapisha
                    </button>
                    <button class="action-btn btn-close" onclick="closeModal()">
                        <i class="fas fa-times"></i> Funga
                    </button>
                </div>
            `;
            
            modal.classList.add('active');
        }

        // ===== TRACK ORDER =====
        function trackOrder(orderId) {
            fetch(`/shop/${COMPANY_ID}/orders-json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const order = data.orders.find(o => o.id == orderId);
                        if (order) {
                            displayTrackingModal(order);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Hitilafu katika kupakia maelezo', 'error');
                });
        }

        function displayTrackingModal(order) {
            const modal = document.getElementById('orderModal');
            const body = document.getElementById('modalBody');
            
            document.getElementById('modalOrderNumber').textContent = `Fuatilia Oda #${order.order_number}`;
            
            const steps = [
                { status: 'saved', label: 'Oda Imewekwa', icon: 'fa-clipboard-check' },
                { status: 'confirmed', label: 'Imethibitishwa', icon: 'fa-check-circle' },
                { status: 'processing', label: 'Inachakatwa', icon: 'fa-cogs' },
                { status: 'ready', label: 'Tayari', icon: 'fa-box-open' },
                { status: 'shipped', label: 'Imesafirishwa', icon: 'fa-truck' },
                { status: 'delivered', label: 'Imewasilishwa', icon: 'fa-flag-checkered' }
            ];
            
            const currentStatus = order.status;
            let timelineHtml = '';
            let found = false;
            
            steps.forEach((step, index) => {
                const isActive = step.status === currentStatus || found;
                if (step.status === currentStatus) found = true;
                const isCompleted = steps.findIndex(s => s.status === currentStatus) >= index;
                
                timelineHtml += `
                    <li class="${isActive ? 'active' : ''}">
                        <span class="dot" style="${isCompleted ? 'background:#10b981;border-color:#10b981;' : ''}"></span>
                        <div>
                            <div class="title">${step.label}</div>
                            ${isCompleted ? `<div class="time">✓ Imekamilika</div>` : isActive ? `<div class="time">⏳ Inaendelea</div>` : `<div class="time">⏱ Inasubiri</div>`}
                        </div>
                    </li>
                `;
            });
            
            body.innerHTML = `
                <div style="margin-bottom:16px;">
                    <div class="order-detail-item">
                        <span class="label">Namba ya Oda</span>
                        <span class="value">${order.order_number}</span>
                    </div>
                    <div class="order-detail-item">
                        <span class="label">Hali</span>
                        <span class="order-status status-${order.status}">
                            <span class="status-dot"></span>
                            ${order.status_label}
                        </span>
                    </div>
                    <div class="order-detail-item">
                        <span class="label">Tarehe</span>
                        <span class="value">${order.created_at_formatted}</span>
                    </div>
                    <div class="order-detail-item">
                        <span class="label">Jumla</span>
                        <span class="value" style="color:#f59e0b;font-weight:700;">${formatCurrency(order.total)}</span>
                    </div>
                </div>
                <div style="border-top:1px solid #e5e7eb;padding-top:12px;">
                    <h4 style="margin-bottom:12px;">Mchakato wa Oda</h4>
                    <ul class="tracking-timeline">
                        ${timelineHtml}
                    </ul>
                </div>
                <div class="modal-actions">
                    <button class="action-btn btn-close" onclick="closeModal()">
                        <i class="fas fa-times"></i> Funga
                    </button>
                </div>
            `;
            
            modal.classList.add('active');
        }

        // ===== PRINT ORDER =====
        function printOrder(orderId) {
            fetch(`/shop/${COMPANY_ID}/orders-json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const order = data.orders.find(o => o.id == orderId);
                        if (order) {
                            printOrderReceipt(order);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Hitilafu katika kuchapisha', 'error');
                });
        }

        function printOrderReceipt(order) {
            const items = order.items || [];
            let itemsHtml = '';
            let subtotal = 0;
            
            items.forEach(item => {
                const itemName = item.jina || item.name || 'Bidhaa';
                const itemQty = item.idadi || item.qty || 0;
                const itemPrice = item.bei || item.price || 0;
                const itemTotal = item.total || (itemQty * itemPrice);
                subtotal += itemTotal;
                itemsHtml += `
                    <tr>
                        <td style="padding:4px 8px;border-bottom:1px solid #e5e7eb;font-size:12px;">${itemName}</td>
                        <td style="padding:4px 8px;border-bottom:1px solid #e5e7eb;text-align:center;font-size:12px;">${itemQty}</td>
                        <td style="padding:4px 8px;border-bottom:1px solid #e5e7eb;text-align:right;font-size:12px;">${formatCurrency(itemPrice)}</td>
                        <td style="padding:4px 8px;border-bottom:1px solid #e5e7eb;text-align:right;font-size:12px;font-weight:bold;">${formatCurrency(itemTotal)}</td>
                    </tr>
                `;
            });
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Oda #${order.order_number}</title>
                    <style>
                        body { font-family: 'Courier New', monospace; padding: 20px; max-width: 400px; margin: 0 auto; }
                        .header { text-align: center; margin-bottom: 20px; }
                        .header h1 { font-size: 18px; margin: 0; color: #1f2937; }
                        .header p { font-size: 12px; color: #6b7280; margin: 2px 0; }
                        .divider { border-top: 1px dashed #d1d5db; margin: 8px 0; }
                        .info { font-size: 12px; margin: 4px 0; }
                        .info strong { display: inline-block; width: 80px; }
                        table { width: 100%; font-size: 12px; border-collapse: collapse; margin: 8px 0; }
                        th { text-align: left; padding: 4px 8px; background: #f3f4f6; font-size: 11px; }
                        td { padding: 4px 8px; }
                        .total { font-size: 16px; font-weight: bold; text-align: right; padding-top: 8px; border-top: 1px solid #d1d5db; margin-top: 8px; }
                        .footer { text-align: center; font-size: 11px; color: #6b7280; margin-top: 16px; border-top: 1px dashed #d1d5db; padding-top: 8px; }
                        .status { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
                        .status-saved { background: #fef3c7; color: #92400e; }
                        .status-confirmed { background: #dbeafe; color: #1e40af; }
                        .status-processing { background: #ede9fe; color: #5b21b6; }
                        .status-ready { background: #e0e7ff; color: #3730a3; }
                        .status-shipped { background: #ffedd5; color: #9a3412; }
                        .status-delivered { background: #d1fae5; color: #065f46; }
                        .status-cancelled { background: #fee2e2; color: #991b1b; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>${COMPANY_NAME}</h1>
                        <p>Stakabadhi ya Oda</p>
                        <p>${order.order_number}</p>
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="info"><strong>Tarehe:</strong> ${order.created_at_formatted}</div>
                    <div class="info"><strong>Mteja:</strong> ${order.customer_name || 'Mteja wa Kutembea'}</div>
                    <div class="info"><strong>Simu:</strong> ${order.customer_phone || '-'}</div>
                    <div class="info"><strong>Hali:</strong> <span class="status status-${order.status}">${order.status_label}</span></div>
                    ${order.delivery_address ? `<div class="info"><strong>Anuani:</strong> ${order.delivery_address}</div>` : ''}
                    
                    <div class="divider"></div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Bidhaa</th>
                                <th style="text-align:center;">Qty</th>
                                <th style="text-align:right;">Bei</th>
                                <th style="text-align:right;">Jumla</th>
                            </tr>
                        </thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>
                    
                    <div class="total">
                        <div style="display:flex;justify-content:space-between;font-size:14px;">
                            <span>JUMLA:</span>
                            <span>${formatCurrency(order.total)}</span>
                        </div>
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="footer">
                        Asante kwa kununua! 🛍️<br>
                        Powered by MauzoSheetAI
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        // ===== SHARE ORDER VIA WHATSAPP =====
        function shareOrderWhatsApp(orderId) {
            fetch(`/shop/${COMPANY_ID}/orders-json`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const order = data.orders.find(o => o.id == orderId);
                        if (order) {
                            sendWhatsAppMessage(order);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Hitilafu katika kushare', 'error');
                });
        }

        function sendWhatsAppMessage(order) {
            let message = `🏪 *${COMPANY_NAME}*\n`;
            message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
            message += `*MAELEZO YA ODA*\n`;
            message += `Oda: ${order.order_number}\n`;
            message += `Tarehe: ${order.created_at_formatted}\n`;
            message += `Hali: ${order.status_label}\n`;
            message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
            message += `*BIDHAA*\n`;
            
            if (order.items && order.items.length > 0) {
                order.items.forEach(item => {
                    const itemName = item.jina || item.name || 'Bidhaa';
                    const itemQty = item.idadi || item.qty || 0;
                    const itemPrice = item.bei || item.price || 0;
                    const itemTotal = item.total || (itemQty * itemPrice);
                    message += `• ${itemName} × ${itemQty} = ${formatCurrency(itemTotal)}\n`;
                });
            }
            
            message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
            message += `*JUMLA: ${formatCurrency(order.total)}*\n`;
            message += `━━━━━━━━━━━━━━━━━━━━━━━━\n`;
            message += `Asante kwa kununua! 🛍️`;
            
            const encodedMessage = encodeURIComponent(message);
            const whatsappUrl = `https://wa.me/?text=${encodedMessage}`;
            window.open(whatsappUrl, '_blank');
        }

        // ===== LOGOUT CUSTOMER =====
        function logoutCustomer() {
            // Removed confirm dialog - direct action
            fetch(`/shop/customer/logout/${COMPANY_ID}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = `/shop/${COMPANY_ID}`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Hitilafu ya mtandao', 'error');
            });
        }

        // ===== MODAL =====
        function closeModal() {
            document.getElementById('orderModal').classList.remove('active');
        }

        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        // ===== UTILITY FUNCTIONS =====
        function formatCurrency(amount) {
            return amount.toLocaleString('en-TZ', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' TZS';
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const iconMap = {
                'success': 'fa-check-circle',
                'error': 'fa-exclamation-circle',
                'info': 'fa-info-circle',
                'warning': 'fa-exclamation-triangle'
            };
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `<i class="fas ${iconMap[type] || 'fa-info-circle'} mr-2"></i>${message}`;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('toast-out');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

</body>
</html>