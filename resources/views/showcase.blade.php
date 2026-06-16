{{-- resources/views/showcase.blade.php --}}
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Order from {{ $company->company_name }} - Browse products and place your order online">
    <title>{{ $company->company_name }} - Order Online</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* ============================================
           SINGLE TEMPLATE - WORKS FOR ALL COMPANIES
           ============================================ */
        
        :root {
            --primary: #f59e0b;
            --primary-dark: #d97706;
            --primary-light: #fbbf24;
            --secondary: #10b981;
            --secondary-dark: #059669;
            --dark: #1f2937;
            --gray: #6b7280;
            --light: #f3f4f6;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0,0,0,0.08);
            --shadow-hover: 0 8px 30px rgba(0,0,0,0.15);
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
            padding-bottom: 100px;
        }
        
        /* ===== LOADING ===== */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: opacity 0.5s ease;
        }
        
        .page-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .loader-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid var(--light);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s ease infinite;
        }
        
        @keyframes spin { to { transform: rotate(360deg); } }
        
        /* ===== HERO ===== */
        .hero-section {
            position: relative;
            background: linear-gradient(135deg, var(--dark) 0%, #374151 100%);
            color: white;
            padding: 50px 16px 70px;
            overflow: hidden;
            min-height: 320px;
            display: flex;
            align-items: center;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.10) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .hero-container {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(245, 158, 11, 0.3);
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 12px;
        }
        
        .hero-badge i { color: var(--primary); }
        
        .hero-title {
            font-size: 32px;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 8px;
        }
        
        .hero-title span { color: var(--primary); }
        
        .hero-subtitle {
            font-size: 16px;
            color: rgba(255,255,255,0.8);
            max-width: 500px;
            margin-bottom: 16px;
        }
        
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }
        
        .btn-hero {
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none;
        }
        
        .btn-hero-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-hero-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }
        
        .btn-hero-secondary {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .btn-hero-secondary:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .hero-search {
            max-width: 450px;
            position: relative;
        }
        
        .hero-search input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: none;
            border-radius: 50px;
            font-size: 14px;
            background: white;
            color: var(--dark);
            outline: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .hero-search input::placeholder { color: #9ca3af; }
        .hero-search i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .hero-stats {
            display: flex;
            gap: 24px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-wrap: wrap;
        }
        
        .hero-stats-item .number {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            display: block;
        }
        
        .hero-stats-item .label {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
        }
        
        /* ===== COMPANY PROFILE ===== */
        .company-profile {
            position: relative;
            z-index: 2;
            margin-top: -30px;
            padding: 0 16px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .company-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px 24px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
        }
        
        .company-logo {
            width: 64px;
            height: 64px;
            border-radius: var(--radius-sm);
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .company-info { flex: 1; min-width: 160px; }
        
        .company-info h1 {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .company-info h1 .verified {
            color: var(--secondary);
            font-size: 16px;
        }
        
        .company-info .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
            color: var(--gray);
            margin-top: 2px;
        }
        
        .company-info .meta i {
            width: 14px;
            color: var(--primary);
        }
        
        .company-contact {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-left: auto;
        }
        
        .contact-btn {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        
        .contact-btn-whatsapp { background: #25D366; color: white; }
        .contact-btn-whatsapp:hover { background: #1DA851; transform: translateY(-2px); }
        .contact-btn-call { background: var(--dark); color: white; }
        .contact-btn-call:hover { background: #374151; transform: translateY(-2px); }
        
        /* ===== PRODUCTS ===== */
        .products-section {
            max-width: 1200px;
            margin: 24px auto 0;
            padding: 0 16px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .section-header h2 {
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-header h2 i { color: var(--primary); }
        
        .section-header .count {
            font-size: 13px;
            color: var(--gray);
        }
        
        /* ===== CATEGORY FILTERS ===== */
        .category-filters {
            display: flex;
            flex-wrap: nowrap;
            gap: 6px;
            margin-bottom: 16px;
            overflow-x: auto;
            padding-bottom: 4px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        
        .category-filters::-webkit-scrollbar { display: none; }
        
        .category-filter {
            padding: 6px 16px;
            border-radius: 50px;
            border: 2px solid #e5e7eb;
            background: white;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
            color: var(--gray);
            flex-shrink: 0;
        }
        
        .category-filter:hover {
            border-color: var(--primary);
            color: var(--dark);
        }
        
        .category-filter.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        /* ===== PRODUCT GRID ===== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }
        
        .product-card {
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            cursor: pointer;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }
        
        .product-card .image-wrapper {
            position: relative;
            padding-top: 75%;
            background: var(--light);
            overflow: hidden;
        }
        
        .product-card .image-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .product-card:hover .image-wrapper img { transform: scale(1.05); }
        
        .product-card .stock-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 2px 10px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .stock-in-stock { background: rgba(16, 185, 129, 0.9); color: white; }
        .stock-low { background: rgba(245, 158, 11, 0.9); color: white; }
        .stock-out { background: rgba(239, 68, 68, 0.9); color: white; }
        
        .product-card .info { padding: 12px; }
        
        .product-card .info .name {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-card .info .category {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 4px;
        }
        
        .product-card .info .price {
            font-size: 17px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .product-card .add-btn {
            width: 100%;
            padding: 8px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .product-card .add-btn:hover { background: var(--primary-dark); }
        .product-card .add-btn:disabled { background: #d1d5db; cursor: not-allowed; }
        
        /* ===== FLOATING CART ===== */
        .floating-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }
        
        .cart-toggle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(245, 158, 11, 0.4);
            transition: var(--transition);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .cart-toggle:hover { transform: scale(1.05); }
        
        .cart-toggle .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* ===== CART PANEL ===== */
        .cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: none;
            backdrop-filter: blur(4px);
        }
        
        .cart-overlay.active { display: block; }
        
        .cart-panel {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 400px;
            height: 100vh;
            background: var(--white);
            box-shadow: -4px 0 40px rgba(0,0,0,0.1);
            z-index: 1001;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }
        
        .cart-panel.open { right: 0; }
        
        .cart-panel .header {
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        
        .cart-panel .header h3 {
            font-size: 18px;
            font-weight: 700;
        }
        
        .cart-panel .header .close-cart {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--gray);
            cursor: pointer;
        }
        
        .cart-panel .items {
            flex: 1;
            overflow-y: auto;
            padding: 12px 16px;
        }
        
        .cart-item {
            display: flex;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .cart-item:last-child { border-bottom: none; }
        
        .cart-item .item-image {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            background: var(--light);
            flex-shrink: 0;
            overflow: hidden;
        }
        
        .cart-item .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-item .item-info { flex: 1; }
        .cart-item .item-info .name {
            font-size: 13px;
            font-weight: 600;
        }
        .cart-item .item-info .price {
            font-weight: 600;
            color: var(--primary);
            font-size: 13px;
        }
        
        .cart-item .qty-controls {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
        }
        
        .cart-item .qty-controls button {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid #e5e7eb;
            background: white;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        
        .cart-item .qty-controls button:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .cart-item .qty-controls .qty {
            font-weight: 600;
            width: 20px;
            text-align: center;
            font-size: 13px;
        }
        
        .cart-item .remove-btn {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            padding: 4px;
        }
        
        .cart-panel .footer {
            padding: 12px 16px;
            border-top: 1px solid #e5e7eb;
            flex-shrink: 0;
            background: #fafafa;
        }
        
        .cart-panel .footer .summary {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .cart-panel .footer .summary .total { color: var(--primary); }
        
        .cart-panel .footer .checkout-btn {
            width: 100%;
            padding: 12px;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .cart-panel .footer .checkout-btn:hover { background: var(--secondary-dark); }
        
        /* ===== CHECKOUT MODAL ===== */
        .checkout-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }
        
        .checkout-modal.active { display: flex; }
        
        .checkout-modal .modal-content {
            background: var(--white);
            border-radius: var(--radius);
            max-width: 480px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .checkout-modal .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .checkout-modal .modal-content .modal-header h3 {
            font-size: 20px;
            font-weight: 700;
        }
        
        .checkout-modal .modal-content .modal-header .close-modal {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--gray);
            cursor: pointer;
        }
        
        .checkout-modal .modal-content .form-group { margin-bottom: 12px; }
        
        .checkout-modal .modal-content .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 3px;
            color: var(--dark);
        }
        
        .checkout-modal .modal-content .form-group input,
        .checkout-modal .modal-content .form-group select,
        .checkout-modal .modal-content .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: var(--radius-sm);
            font-size: 14px;
            transition: var(--transition);
            font-family: inherit;
        }
        
        .checkout-modal .modal-content .form-group input:focus,
        .checkout-modal .modal-content .form-group select:focus,
        .checkout-modal .modal-content .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }
        
        .checkout-modal .modal-content .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }
        
        .checkout-modal .modal-content .order-summary {
            background: var(--light);
            border-radius: var(--radius-sm);
            padding: 12px;
            margin: 12px 0;
        }
        
        .checkout-modal .modal-content .order-summary .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 13px;
        }
        
        .checkout-modal .modal-content .order-summary .summary-row.total {
            font-weight: 700;
            font-size: 16px;
            border-top: 2px solid #e5e7eb;
            padding-top: 6px;
            margin-top: 6px;
        }
        
        .checkout-modal .modal-content .order-summary .summary-row.total .amount {
            color: var(--primary);
        }
        
        .checkout-modal .modal-content .submit-btn {
            width: 100%;
            padding: 12px;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .checkout-modal .modal-content .submit-btn:hover { background: var(--secondary-dark); }
        .checkout-modal .modal-content .submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
        
        /* ===== ORDER SUCCESS ===== */
        .order-success { text-align: center; padding: 20px 0; }
        .order-success .icon {
            font-size: 56px;
            color: var(--secondary);
            margin-bottom: 12px;
        }
        .order-success h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .order-success .order-number {
            font-size: 16px;
            color: var(--gray);
            background: var(--light);
            padding: 6px 16px;
            border-radius: var(--radius-sm);
            display: inline-block;
            margin: 6px 0;
        }
        .order-success .message { color: var(--gray); margin: 8px 0 16px; }
        
        /* ===== TOAST ===== */
        .toast-container {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-width: 90%;
        }
        
        .toast {
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            color: white;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease;
        }
        
        .toast-success { background: var(--secondary); }
        .toast-error { background: #ef4444; }
        .toast-info { background: #3b82f6; }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .toast-out {
            animation: slideOutRight 0.3s ease forwards;
        }
        
        @keyframes slideOutRight {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(50px); }
        }
        
        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s ease infinite;
        }
        
        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 40px;
            color: #d1d5db;
            margin-bottom: 12px;
        }
        
        .empty-state p { font-size: 16px; font-weight: 500; }
        .empty-state .sub { font-size: 14px; }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-title { font-size: 26px; }
            .hero-subtitle { font-size: 15px; }
            .hero-actions .btn-hero { width: 100%; justify-content: center; }
            .hero-stats { gap: 16px; }
            .hero-stats-item .number { font-size: 18px; }
            
            .company-card { flex-direction: column; text-align: center; padding: 16px; }
            .company-info .meta { justify-content: center; }
            .company-contact { margin-left: 0; justify-content: center; width: 100%; }
            .company-contact .contact-btn { flex: 1; justify-content: center; }
            
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .product-card .info { padding: 10px; }
            .product-card .info .name { font-size: 13px; }
            .product-card .info .price { font-size: 15px; }
            .product-card .add-btn { font-size: 12px; padding: 6px; }
            
            .cart-panel { max-width: 100%; }
            .section-header h2 { font-size: 18px; }
            .category-filter { font-size: 12px; padding: 5px 12px; }
            
            .checkout-modal .modal-content { padding: 16px; margin: 8px; }
        }
        
        @media (max-width: 480px) {
            .hero-section { padding: 30px 12px 50px; min-height: 260px; }
            .hero-title { font-size: 22px; }
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            
            .floating-cart { bottom: 12px; right: 12px; }
            .cart-toggle { width: 48px; height: 48px; font-size: 18px; }
        }
        
        /* ===== UTILITY ===== */
        .hidden { display: none !important; }
        .text-center { text-align: center; }
        .mt-8 { margin-top: 8px; }
        .mb-8 { margin-bottom: 8px; }
    </style>
</head>
<body>

    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
        <p style="margin-top:12px;color:var(--gray);font-weight:500;font-size:14px;">Loading {{ $company->company_name }}...</p>
    </div>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-badge">
                <i class="fas fa-store"></i>
                <span>Open for Orders</span>
            </div>
            
            <h1 class="hero-title">
                Welcome to <span>{{ $company->company_name }}</span>
            </h1>
            
            <p class="hero-subtitle">
                Browse our products and place your order online. Fast delivery and quality service guaranteed.
            </p>
            
            <div class="hero-actions">
                <a href="#products" class="btn-hero btn-hero-primary">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
                <a href="#contact" class="btn-hero btn-hero-secondary">
                    <i class="fas fa-phone"></i> Contact Us
                </a>
            </div>
            
            <div class="hero-search">
                <i class="fas fa-search"></i>
                <input type="text" id="searchProducts" placeholder="Search products, categories, or barcode...">
            </div>
            
            <div class="hero-stats">
                <div class="hero-stats-item">
                    <span class="number">{{ $stats['total_products'] }}</span>
                    <span class="label">Products</span>
                </div>
                <div class="hero-stats-item">
                    <span class="number">{{ $stats['total_categories'] }}</span>
                    <span class="label">Categories</span>
                </div>
                <div class="hero-stats-item">
                    <span class="number">{{ $stats['in_stock'] }}</span>
                    <span class="label">In Stock</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== COMPANY PROFILE ===== -->
    <section class="company-profile">
        <div class="company-card">
            <div class="company-logo">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->company_name }}">
                @else
                    {{ strtoupper(substr($company->company_name, 0, 2)) }}
                @endif
            </div>
            
            <div class="company-info">
                <h1>
                    {{ $company->company_name }}
                    @if($company->is_verified)
                        <span class="verified" title="Verified Business">
                            <i class="fas fa-check-circle"></i>
                        </span>
                    @endif
                </h1>
                <div class="meta">
                    @if($company->business_type)
                        <span><i class="fas fa-tag"></i> {{ $company->business_type }}</span>
                    @endif
                    @if($company->location)
                        <span><i class="fas fa-map-marker-alt"></i> {{ $company->location }}</span>
                    @endif
                    @if($company->phone)
                        <span><i class="fas fa-phone"></i> {{ $company->phone }}</span>
                    @endif
                    @if($company->email)
                        <span><i class="fas fa-envelope"></i> {{ $company->email }}</span>
                    @endif
                </div>
            </div>
            
            <div class="company-contact">
                @if($company->phone)
                    <a href="tel:{{ $company->phone }}" class="contact-btn contact-btn-call">
                        <i class="fas fa-phone"></i> Call
                    </a>
                @endif
                @php
                    $whatsapp = $socialLinks['whatsapp'] ?? $company->phone ?? null;
                    $whatsappNum = $whatsapp ? preg_replace('/[^0-9]/', '', $whatsapp) : null;
                @endphp
                @if($whatsappNum)
                    <a href="https://wa.me/{{ $whatsappNum }}" target="_blank" class="contact-btn contact-btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- ===== PRODUCTS SECTION ===== -->
    <section class="products-section" id="products">
        <div class="section-header">
            <h2>
                <i class="fas fa-box"></i> Our Products
            </h2>
            <span class="count" id="productCount">{{ $products->count() }} items</span>
        </div>
        
        <!-- Category Filters -->
        <div class="category-filters" id="categoryFilters">
            <button class="category-filter active" data-category="all">All Products</button>
            @foreach($categories as $category)
                <button class="category-filter" data-category="{{ $category }}">{{ $category }}</button>
            @endforeach
        </div>
        
        <!-- Product Grid -->
        <div class="product-grid" id="productGrid">
            @forelse($products as $product)
                <div class="product-card" 
                     data-id="{{ $product->id }}"
                     data-name="{{ $product->jina }}"
                     data-price="{{ $product->bei_kuuza }}"
                     data-stock="{{ $product->idadi }}"
                     data-category="{{ $product->aina }}">
                    
                    <div class="image-wrapper">
                        @if($product->image_data_url)
                            <img src="{{ $product->image_data_url }}" alt="{{ $product->jina }}" loading="lazy">
                        @else
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='225'%3E%3Crect width='300' height='225' fill='%23f3f4f6'/%3E%3Ctext x='150' y='112' text-anchor='middle' font-family='Arial' font-size='14' fill='%239ca3af'%3ENo Image%3C/text%3E%3C/svg%3E" alt="{{ $product->jina }}" loading="lazy">
                        @endif
                        
                        <span class="stock-badge 
                            @if($product->idadi > 10) stock-in-stock
                            @elseif($product->idadi > 0) stock-low
                            @else stock-out @endif">
                            @if($product->idadi > 10)
                                <i class="fas fa-check"></i> In Stock
                            @elseif($product->idadi > 0)
                                <i class="fas fa-exclamation"></i> {{ number_format($product->idadi, 0) }} left
                            @else
                                <i class="fas fa-times"></i> Out
                            @endif
                        </span>
                    </div>
                    
                    <div class="info">
                        <div class="name">{{ $product->jina }}</div>
                        <div class="category">
                            {{ $product->aina }}
                            @if($product->kipimo)
                                <span style="color:#9ca3af;">• {{ $product->kipimo }}</span>
                            @endif
                        </div>
                        <div class="price">
                            {{ number_format($product->bei_kuuza, 0) }} TZS
                        </div>
                        <button class="add-btn" 
                                onclick="addToCart({{ $product->id }}, '{{ addslashes($product->jina) }}', {{ $product->bei_kuuza }}, {{ $product->idadi }})"
                                @if($product->idadi <= 0) disabled @endif>
                            <i class="fas fa-cart-plus"></i> 
                            @if($product->idadi > 0)
                                Add
                            @else
                                Out
                            @endif
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="grid-column:1/-1;">
                    <i class="fas fa-box-open"></i>
                    <p>No products available</p>
                    <p class="sub">Please check back later</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- ===== FLOATING CART ===== -->
    <div class="floating-cart" id="floatingCart">
        <button class="cart-toggle" onclick="toggleCart()">
            <i class="fas fa-shopping-cart"></i>
            <span class="badge" id="cartCount">0</span>
        </button>
    </div>

    <!-- ===== CART OVERLAY ===== -->
    <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>

    <!-- ===== CART PANEL ===== -->
    <div class="cart-panel" id="cartPanel">
        <div class="header">
            <h3><i class="fas fa-shopping-cart" style="color:var(--primary);"></i> Your Cart</h3>
            <button class="close-cart" onclick="toggleCart()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="items" id="cartItems">
            <div style="text-align:center;padding:40px 0;color:var(--gray);">
                <i class="fas fa-shopping-basket" style="font-size:40px;color:#e5e7eb;margin-bottom:8px;"></i>
                <p style="font-size:15px;font-weight:500;">Your cart is empty</p>
                <p style="font-size:13px;">Start shopping to add items</p>
            </div>
        </div>
        
        <div class="footer" id="cartFooter" style="display:none;">
            <div class="summary">
                <span>Subtotal</span>
                <span class="total" id="cartTotal">0 TZS</span>
            </div>
            <button class="checkout-btn" onclick="openCheckout()">
                <i class="fas fa-arrow-right"></i> Proceed to Checkout
            </button>
        </div>
    </div>

    <!-- ===== CHECKOUT MODAL ===== -->
    <div class="checkout-modal" id="checkoutModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-clipboard-check" style="color:var(--primary);"></i> Checkout</h3>
                <button class="close-modal" onclick="closeCheckout()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="order-success" id="orderSuccess" style="display:none;">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <h3>Order Placed!</h3>
                <div class="order-number" id="orderNumberDisplay">#ORD-20250101-0001</div>
                <p class="message">Thank you for your order. We'll confirm it shortly.</p>
                <button class="btn-hero btn-hero-primary" onclick="closeCheckout()" style="margin-top:8px;width:100%;justify-content:center;">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </button>
            </div>
            
            <div id="checkoutForm">
                <p style="color:var(--gray);margin-bottom:16px;font-size:13px;">
                    <i class="fas fa-info-circle" style="color:var(--primary);"></i> 
                    Fill in your details to complete the order
                </p>
                
                <div class="form-group">
                    <label for="customerName">Full Name *</label>
                    <input type="text" id="customerName" placeholder="Enter your full name" required>
                </div>
                
                <div class="form-group">
                    <label for="customerPhone">Phone Number *</label>
                    <input type="tel" id="customerPhone" placeholder="Enter phone number" required>
                </div>
                
                <div class="form-group">
                    <label for="customerEmail">Email Address</label>
                    <input type="email" id="customerEmail" placeholder="Enter email (optional)">
                </div>
                
                <div class="form-group">
                    <label for="deliveryAddress">Delivery Address</label>
                    <textarea id="deliveryAddress" placeholder="Enter delivery address (optional)"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="orderType">Order Type *</label>
                    <select id="orderType" required>
                        <option value="delivery">Delivery</option>
                        <option value="pickup">Pickup</option>
                        <option value="dine_in">Dine In</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="specialInstructions">Special Instructions</label>
                    <textarea id="specialInstructions" placeholder="Any special instructions?"></textarea>
                </div>
                
                <div class="order-summary">
                    <div class="summary-row">
                        <span>Items</span>
                        <span id="checkoutItemCount">0 items</span>
                    </div>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="checkoutSubtotal">0 TZS</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span class="amount" id="checkoutTotal">0 TZS</span>
                    </div>
                </div>
                
                <button class="submit-btn" onclick="placeOrder()">
                    <i class="fas fa-check-circle"></i> Place Order
                </button>
            </div>
        </div>
    </div>

    <!-- ===== TOAST CONTAINER ===== -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // ============================================
        // SINGLE TEMPLATE - WORKS FOR ALL COMPANIES
        // ============================================
        
        // ===== CONFIGURATION =====
        const COMPANY_ID = {{ $company->id }};
        const COMPANY_NAME = '{{ $company->company_name }}';
        const CUSTOMER_TOKEN = '{{ $customerToken }}';
        const STORAGE_KEY = 'showcase_cart_' + COMPANY_ID;
        
        // ===== STATE =====
        let cart = [];
        let isCartOpen = false;
        
        // ===== DOM REFS =====
        const cartItemsEl = document.getElementById('cartItems');
        const cartFooterEl = document.getElementById('cartFooter');
        const cartTotalEl = document.getElementById('cartTotal');
        const cartCountEl = document.getElementById('cartCount');
        const cartPanelEl = document.getElementById('cartPanel');
        const cartOverlayEl = document.getElementById('cartOverlay');
        const productGridEl = document.getElementById('productGrid');
        const searchInputEl = document.getElementById('searchProducts');
        const categoryFiltersEl = document.getElementById('categoryFilters');
        const productCountEl = document.getElementById('productCount');
        
        // ===== INIT =====
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loader
            setTimeout(() => {
                document.getElementById('pageLoader').classList.add('hidden');
            }, 500);
            
            // Load cart from localStorage
            loadCart();
            
            // Category filters
            document.querySelectorAll('.category-filter').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.category-filter').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterProducts(this.dataset.category);
                });
            });
            
            // Search with debounce
            searchInputEl.addEventListener('input', debounce(function() {
                searchProducts(this.value);
            }, 300));
            
            // Close cart on Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (cartPanelEl.classList.contains('open')) toggleCart();
                    if (document.getElementById('checkoutModal').classList.contains('active')) closeCheckout();
                }
            });
        });
        
        // ===== CART FUNCTIONS =====
        function addToCart(id, name, price, stock) {
            if (stock <= 0) {
                showToast('Product is out of stock', 'error');
                return;
            }
            
            const existing = cart.find(item => item.id === id);
            
            if (existing) {
                if (existing.qty >= stock) {
                    showToast('Not enough stock available', 'error');
                    return;
                }
                existing.qty += 1;
            } else {
                cart.push({ id, name, price, qty: 1, stock });
            }
            
            updateCart();
            saveCart();
            showToast(`${name} added to cart!`, 'success');
            
            // Animate the cart button
            const btn = document.querySelector(`.product-card[data-id="${id}"] .add-btn`);
            if (btn) {
                btn.style.transform = 'scale(0.9)';
                setTimeout(() => { btn.style.transform = ''; }, 200);
            }
            
            // Open cart on mobile
            if (window.innerWidth < 768) {
                toggleCart();
            }
        }
        
        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCart();
            saveCart();
        }
        
        function updateQty(index, change) {
            const item = cart[index];
            const newQty = item.qty + change;
            
            if (newQty <= 0) {
                cart.splice(index, 1);
            } else if (newQty > item.stock) {
                showToast('Not enough stock available', 'error');
                return;
            } else {
                item.qty = newQty;
            }
            
            updateCart();
            saveCart();
        }
        
        function updateCart() {
            const count = cart.reduce((sum, item) => sum + item.qty, 0);
            const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            
            cartCountEl.textContent = count;
            cartTotalEl.textContent = formatCurrency(total);
            
            if (cart.length === 0) {
                cartItemsEl.innerHTML = `
                    <div style="text-align:center;padding:40px 0;color:var(--gray);">
                        <i class="fas fa-shopping-basket" style="font-size:40px;color:#e5e7eb;margin-bottom:8px;"></i>
                        <p style="font-size:15px;font-weight:500;">Your cart is empty</p>
                        <p style="font-size:13px;">Start shopping to add items</p>
                    </div>
                `;
                cartFooterEl.style.display = 'none';
            } else {
                let html = '';
                cart.forEach((item, index) => {
                    html += `
                        <div class="cart-item">
                            <div class="item-image">
                                <img src="https://via.placeholder.com/48x48/${item.name}" alt="${item.name}">
                            </div>
                            <div class="item-info">
                                <div class="name">${item.name}</div>
                                <div class="price">${formatCurrency(item.price)}</div>
                                <div class="qty-controls">
                                    <button onclick="updateQty(${index}, -1)"><i class="fas fa-minus"></i></button>
                                    <span class="qty">${item.qty}</span>
                                    <button onclick="updateQty(${index}, 1)"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:600;color:var(--primary);font-size:14px;">${formatCurrency(item.price * item.qty)}</div>
                                <button class="remove-btn" onclick="removeFromCart(${index})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                cartItemsEl.innerHTML = html;
                cartFooterEl.style.display = 'block';
            }
        }
        
        function toggleCart() {
            cartPanelEl.classList.toggle('open');
            cartOverlayEl.classList.toggle('active');
            document.body.style.overflow = cartPanelEl.classList.contains('open') ? 'hidden' : '';
        }
        
        // ===== CHECKOUT =====
        function openCheckout() {
            if (cart.length === 0) {
                showToast('Your cart is empty', 'error');
                return;
            }
            
            document.getElementById('checkoutForm').style.display = 'block';
            document.getElementById('orderSuccess').style.display = 'none';
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const count = cart.reduce((sum, item) => sum + item.qty, 0);
            
            document.getElementById('checkoutItemCount').textContent = count + ' items';
            document.getElementById('checkoutSubtotal').textContent = formatCurrency(total);
            document.getElementById('checkoutTotal').textContent = formatCurrency(total);
            
            document.getElementById('checkoutModal').classList.add('active');
        }
        
        function closeCheckout() {
            document.getElementById('checkoutModal').classList.remove('active');
            if (cart.length === 0) updateCart();
        }
        
        function placeOrder() {
            const name = document.getElementById('customerName').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();
            const email = document.getElementById('customerEmail').value.trim();
            const address = document.getElementById('deliveryAddress').value.trim();
            const orderType = document.getElementById('orderType').value;
            const instructions = document.getElementById('specialInstructions').value.trim();
            
            // Validate
            if (!name) {
                showToast('Please enter your full name', 'error');
                document.getElementById('customerName').focus();
                return;
            }
            
            if (!phone) {
                showToast('Please enter your phone number', 'error');
                document.getElementById('customerPhone').focus();
                return;
            }
            
            if (!/^[0-9]{7,15}$/.test(phone.replace(/[^0-9]/g, ''))) {
                showToast('Please enter a valid phone number', 'error');
                return;
            }
            
            // Build order data
            const items = cart.map(item => ({
                product_id: item.id,
                quantity: item.qty
            }));
            
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            
            const orderData = {
                items: items,
                customer_name: name,
                customer_phone: phone,
                customer_email: email,
                delivery_address: address,
                special_instructions: instructions,
                order_type: orderType,
                subtotal: subtotal,
                delivery_fee: 0,
                total: subtotal
            };
            
            // Disable button
            const btn = document.querySelector('.submit-btn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Processing...';
            
            fetch(`/shop/${COMPANY_ID}/order`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                
                if (data.success) {
                    document.getElementById('checkoutForm').style.display = 'none';
                    document.getElementById('orderSuccess').style.display = 'block';
                    document.getElementById('orderNumberDisplay').textContent = data.order_number;
                    
                    cart = [];
                    saveCart();
                    updateCart();
                    
                    showToast('Order placed successfully!', 'success');
                    
                    // Play notification sound if supported
                    try {
                        const audio = new Audio('/sounds/order-placed.mp3');
                        audio.play().catch(() => {});
                    } catch (e) {}
                } else {
                    showToast(data.message || 'Failed to place order', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = originalText;
                showToast('Network error. Please try again.', 'error');
            });
        }
        
        // ===== PRODUCT SEARCH & FILTER =====
        function filterProducts(category) {
            const cards = productGridEl.querySelectorAll('.product-card');
            let visible = 0;
            
            cards.forEach(card => {
                const cat = card.dataset.category;
                if (category === 'all' || cat === category) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            productCountEl.textContent = visible + ' items';
        }
        
        function searchProducts(query) {
            if (query.length < 2) {
                const activeCategory = document.querySelector('.category-filter.active');
                filterProducts(activeCategory ? activeCategory.dataset.category : 'all');
                return;
            }
            
            const cards = productGridEl.querySelectorAll('.product-card');
            let visible = 0;
            const search = query.toLowerCase().trim();
            
            cards.forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const category = card.dataset.category.toLowerCase();
                if (name.includes(search) || category.includes(search)) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            productCountEl.textContent = visible + ' items';
        }
        
        // ===== STORAGE =====
        function saveCart() {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
            } catch (e) {
                console.warn('Could not save cart', e);
            }
        }
        
        function loadCart() {
            try {
                const data = localStorage.getItem(STORAGE_KEY);
                if (data) {
                    cart = JSON.parse(data);
                    // Verify stock with current product cards
                    cart = cart.filter(item => {
                        const card = document.querySelector(`.product-card[data-id="${item.id}"]`);
                        if (card) {
                            const stock = parseInt(card.dataset.stock);
                            item.stock = stock;
                            if (item.qty > stock) item.qty = stock;
                            return true;
                        }
                        return false;
                    });
                    updateCart();
                }
            } catch (e) {
                console.warn('Could not load cart', e);
            }
        }
        
        // ===== UTILITY FUNCTIONS =====
        function formatCurrency(amount) {
            return amount.toLocaleString('en-TZ', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' TZS';
        }
        
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }
        
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = message;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('toast-out');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Close modal on overlay click
        document.getElementById('checkoutModal').addEventListener('click', function(e) {
            if (e.target === this) closeCheckout();
        });
    </script>

</body>
</html>