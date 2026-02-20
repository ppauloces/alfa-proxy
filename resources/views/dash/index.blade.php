@extends('dash.layout')

@section('title', 'AlfaProxy - Gerenciar Proxies')

@section('styles')
    <style>
        .dash-section {
            display: none;
            animation: fadeIn 0.25s ease;
        }

        @keyframes slide-in-right {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in-right 0.3s ease-out;
        }

        .dash-section.active {
            display: block;
        }

        .hidden {
            display: none !important;
        }

        /* Notificação de Teste de Proxy */
        .proxy-test-notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 25px 70px rgba(15, 23, 42, 0.25);
            z-index: 10000;
            min-width: 420px;
            max-width: 90vw;
            animation: slideInScale 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .proxy-test-notification.success {
            border: 2px solid #10b981;
        }

        .proxy-test-notification.error {
            border: 2px solid #ef4444;
        }

        .proxy-test-notification.timeout {
            border: 2px solid #f59e0b;
        }

        .notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            animation: fadeIn 0.2s ease;
        }

        .notification-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }

        .notification-icon.success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
        }

        .notification-icon.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
        }

        .notification-icon.timeout {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
        }

        .notification-title {
            font-size: 1.25rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.75rem;
            color: #0f172a;
        }

        .notification-details {
            background: rgba(148, 163, 184, 0.1);
            border-radius: 12px;
            padding: 1rem;
            margin: 1rem 0;
            font-size: 0.875rem;
        }

        .notification-detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }

        .notification-detail-item:last-child {
            border-bottom: none;
        }

        .notification-detail-label {
            color: #64748b;
            font-weight: 600;
        }

        .notification-detail-value {
            color: #0f172a;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
        }

        .notification-message {
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
            margin-top: 1rem;
            padding: 0.75rem;
            background: rgba(148, 163, 184, 0.05);
            border-radius: 8px;
        }

        .notification-close-btn {
            width: 100%;
            padding: 0.75rem;
            border-radius: 12px;
            border: none;
            background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
            transition: all 0.2s ease;
        }

        .notification-close-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(32, 85, 221, 0.3);
        }

        @keyframes slideInScale {
            0% {
                transform: translate(-50%, -50%) scale(0.9);
                opacity: 0;
            }

            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
        }

        .section-label {
            text-transform: uppercase;
            letter-spacing: 0.35em;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .tab-bar {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 0.25rem;
        }

        .tab-btn {
            position: relative;
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: 0;
            background: transparent;
            color: #64748b;
            font-weight: 500;
            font-size: 0.8125rem;
            transition: color 0.2s ease;
            cursor: pointer;
            white-space: nowrap;
        }

        .tab-btn::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0.75rem;
            right: 0.75rem;
            height: 2px;
            background: transparent;
            border-radius: 1px;
            transition: background 0.2s ease;
        }

        .tab-btn:hover {
            color: #0f172a;
        }

        .tab-btn.active {
            color: var(--sf-blue);
            font-weight: 600;
        }

        .tab-btn.active::after {
            background: var(--sf-blue);
        }

        .tab-btn .tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.25rem;
            height: 1.25rem;
            padding: 0 0.35rem;
            margin-left: 0.4rem;
            border-radius: 6px;
            font-size: 0.6875rem;
            font-weight: 600;
            line-height: 1;
            background: #f1f5f9;
            color: #64748b;
        }

        .tab-btn.active .tab-count {
            background: rgba(35, 54, 111, 0.1);
            color: var(--sf-blue);
        }

        .proxy-card,
        .profile-card,
        .order-card,
        .transactions-card,
        .recharge-card,
        .support-card,
        .settings-card {
            background: #fff;
            border-radius: 28px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            padding: 1.5rem;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
        }

        .proxy-table,
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .proxy-table th,
        .transactions-table th {
            text-align: center;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #94a3b8;
            padding-bottom: 0.65rem;
            white-space: nowrap;
        }

        .proxy-table td,
        .transactions-table td {
            border-top: 1px solid rgba(226, 232, 240, 0.85);
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .address-chip {
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            background: rgba(148, 163, 184, 0.15);
            padding: 0.4rem 0.7rem;
            border-radius: 12px;
            display: inline-block;
            font-size: 0.75rem;
            line-height: 1.5;
            word-break: break-all;
            max-width: 100%;
        }

        .action-btn {
            border: 1px solid rgba(148, 163, 184, 0.4);
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
            font-size: 0.75rem;
            font-weight: 600;
            background: #fff;
            color: #0f172a;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            border-color: var(--sf-blue);
            color: var(--sf-blue);
        }

        .badge {
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-amber {
            background: rgba(251, 191, 36, 0.18);
            color: #b45309;
        }

        .badge-gray {
            background: rgba(148, 163, 184, 0.2);
            color: #475569;
        }

        .badge-success {
            background: rgba(34, 197, 94, 0.15);
            color: #15803d;
        }

        .badge-pending {
            background: rgba(251, 191, 36, 0.18);
            color: #b45309;
        }

        .badge-failed {
            background: rgba(239, 68, 68, 0.15);
            color: #b91c1c;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.25rem;
            font-size: 0.9375rem;
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #15803d;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.4rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 12px;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            background: #fff;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--sf-blue);
            box-shadow: 0 0 0 3px rgba(32, 85, 221, 0.1);
        }

        .form-input:disabled {
            background: rgba(148, 163, 184, 0.1);
            cursor: not-allowed;
        }

        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
            color: #fff;
            font-weight: 600;
            font-size: 0.9375rem;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(32, 85, 221, 0.3);
        }

        .btn-primary.btn-block {
            width: 100%;
            padding: 1rem 1.5rem;
        }

        .btn-secondary {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            background: rgba(148, 163, 184, 0.15);
            color: #475569;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-secondary:hover {
            background: rgba(148, 163, 184, 0.25);
        }

        .btn-danger {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            background: #ef4444;
            color: white;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .order-card {
            padding: 2rem;
        }



        .payment-method {
            padding: 1rem;
            border: 2px solid rgba(226, 232, 240, 0.9);
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .payment-method:hover {
            border-color: var(--sf-blue);
        }

        .payment-method.selected {
            border-color: var(--sf-blue);
            background: rgba(79, 139, 255, 0.05);
        }

        .summary-card {
            background: rgba(248, 250, 252, 1);
            padding: 1.5rem;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.9);
        }

        .stat-card {
            background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
            color: white;
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(32, 85, 221, 0.2);
        }

        .transactions-table td {
            padding: 1rem 0;
        }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            background: white;
            color: #475569;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-tab.active {
            background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
            color: white;
            border-color: transparent;
        }

        .balance-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 28px;
            box-shadow: 0 24px 60px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .amount-btn {
            padding: 1rem;
            border: 2px solid rgba(226, 232, 240, 0.9);
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .amount-btn.selected {
            border-color: var(--sf-blue);
            background: rgba(79, 139, 255, 0.05);
        }

        .switch-tabs {
            display: flex;
            background: rgba(148, 163, 184, 0.1);
            padding: 0.25rem;
            border-radius: 12px;
            gap: 0.25rem;
        }

        .switch-tab {
            flex: 1;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: #475569;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .switch-tab.active {
            background: white;
            color: var(--sf-blue);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.1);
        }

        .history-item {
            padding: 1rem;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            transition: background 0.2s ease;
        }

        .history-item:hover {
            background: rgba(248, 250, 252, 0.8);
        }

        .support-card {
            padding: 2rem;
        }

        .contact-method {
            padding: 1.5rem;
            border: 2px solid rgba(226, 232, 240, 0.9);
            border-radius: 20px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .contact-method:hover {
            border-color: var(--sf-blue);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(32, 85, 221, 0.15);
        }

        .faq-item {
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .faq-item:hover {
            border-color: var(--sf-blue);
        }

        .faq-answer {
            display: none;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(226, 232, 240, 0.5);
            color: #64748b;
        }

        .faq-item.open .faq-answer {
            display: block;
        }

        .faq-icon {
            transition: transform 0.2s ease;
        }

        .faq-item.open .faq-icon {
            transform: rotate(180deg);
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .setting-item:last-child {
            border-bottom: none;
        }

        .danger-zone {
            border: 2px solid rgba(239, 68, 68, 0.3);
            background: rgba(239, 68, 68, 0.02);
        }

        .switch {
            position: relative;
            width: 50px;
            height: 28px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: rgba(148, 163, 184, 0.4);
            transition: 0.2s;
            border-radius: 999px;
        }

        .slider:before {
            content: "";
            position: absolute;
            height: 22px;
            width: 22px;
            left: 3px;
            top: 3px;
            background-color: white;
            transition: 0.2s;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
        }

        .switch input:checked+.slider {
            background-color: var(--sf-blue);
        }

        .switch input:checked+.slider:before {
            transform: translateX(22px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .admin-grid {
            display: grid;
            gap: 1.5rem;
        }

        .admin-card,
        .vps-card,
        .finance-card {
            background: #fff;
            border-radius: 24px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            padding: 1.5rem;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
        }

        .admin-card h2 {
            margin-bottom: 0.85rem;
        }

        .admin-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.8rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(79, 139, 255, 0.1);
            color: var(--sf-blue);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #94a3b8;
            text-align: left;
            padding-bottom: 0.75rem;
        }

        .admin-table td {
            padding: 0.9rem 0;
            border-top: 1px solid rgba(226, 232, 240, 0.8);
            font-size: 0.92rem;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
            background: rgba(148, 163, 184, 0.15);
            color: #475569;
        }

        .badge-status[data-status="disponivel"] {
            background: rgba(16, 185, 129, 0.12);
            color: #047857;
        }

        .badge-status[data-status="vendida"] {
            background: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
        }

        .badge-status[data-status="bloqueada"] {
            background: rgba(248, 113, 113, 0.15);
            color: #b91c1c;
        }

        .badge-status[data-status="inativa"] {
            background: rgba(148, 163, 184, 0.2);
            color: #475569;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .status-dot[data-status="disponivel"] {
            background: #10b981;
        }

        .status-dot[data-status="vendida"] {
            background: #2563eb;
        }

        .status-dot[data-status="bloqueada"] {
            background: #ef4444;
        }

        .status-dot[data-status="caida"] {
            background: #f97316;
        }

        .vps-header {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
        }

        .vps-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: #64748b;
        }

        .vps-body {
            margin-top: 1.25rem;
            border-top: 1px dashed rgba(148, 163, 184, 0.4);
            padding-top: 1rem;
        }

        .proxy-pill {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.9);
        }

        .timeline {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
        }

        .timeline-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 1rem;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .trend-up {
            color: #059669;
        }

        .trend-down {
            color: #dc2626;
        }

        .finance-card .chart-bar {
            height: 6px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.3);
            overflow: hidden;
        }

        .finance-card .chart-bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, var(--sf-blue-light), var(--sf-blue));
        }

        .replace-panel {
            border-radius: 16px;
            border: 1px dashed rgba(148, 163, 184, 0.6);
            padding: 0.9rem;
            margin-top: 0.75rem;
            background: rgba(248, 250, 252, 0.8);
        }

        /* Bulk select bar */
        .bulk-bar {
            position: fixed;
            bottom: 0.5rem;
            left: 50%;
            transform: translateX(-50%) translateY(120%);
            background: #0f172a;
            color: #fff;
            border-radius: 20px;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            z-index: 9000;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.4);
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            white-space: nowrap;
        }

        .bulk-bar.visible {
            transform: translateX(-50%) translateY(0);
        }

        .bulk-bar .bulk-count {
            font-size: 0.8rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.15);
            padding: 0.35rem 0.75rem;
            border-radius: 10px;
        }

        .bulk-bar .bulk-separator {
            width: 1px;
            height: 24px;
            background: rgba(255, 255, 255, 0.2);
        }

        .bulk-bar .protocol-toggle {
            display: flex;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .bulk-bar .protocol-toggle button {
            padding: 0.35rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 700;
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .bulk-bar .protocol-toggle button.active {
            background: var(--sf-blue);
            color: #fff;
            border-radius: 8px;
        }

        .bulk-bar .bulk-copy-btn {
            padding: 0.5rem 1.25rem;
            border-radius: 12px;
            border: none;
            background: linear-gradient(120deg, var(--sf-blue-light), var(--sf-blue));
            color: #fff;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .bulk-bar .bulk-copy-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(32, 85, 221, 0.4);
        }

        .bulk-bar .bulk-close-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: rgba(255, 255, 255, 0.6);
            width: 28px;
            height: 28px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .bulk-bar .bulk-close-btn:hover {
            background: rgba(239, 68, 68, 0.3);
            color: #fff;
        }

        .proxy-checkbox {
            width: 18px;
            height: 18px;
            accent-color: var(--sf-blue);
            cursor: pointer;
        }

        tr.proxy-row-selected {
            background: rgba(79, 139, 255, 0.06) !important;
        }

        /* ===================================
                                                                           RESPONSIVIDADE MOBILE & TABLET
                                                                        =================================== */

        /* Mobile - Até 640px */
        @media (max-width: 640px) {

            /* Typography mobile */
            h1 {
                font-size: 1.75rem !important;
            }

            h2 {
                font-size: 1.25rem !important;
            }

            /* Cards mobile */
            .proxy-card,
            .profile-card,
            .order-card,
            .transactions-card,
            .recharge-card,
            .support-card,
            .settings-card,
            .admin-card,
            .vps-card,
            .finance-card {
                padding: 1rem !important;
                border-radius: 20px !important;
            }

            /* Table mobile - scroll horizontal */
            .proxy-table,
            .transactions-table,
            .admin-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .proxy-table th,
            .proxy-table td,
            .transactions-table th,
            .transactions-table td,
            .admin-table th,
            .admin-table td {
                padding: 0.5rem !important;
                font-size: 0.75rem !important;
            }

            /* Address chip mobile */
            .address-chip {
                font-size: 0.65rem !important;
                padding: 0.3rem 0.5rem !important;
            }

            /* Action buttons mobile */
            .action-btn {
                padding: 0.25rem 0.6rem !important;
                font-size: 0.65rem !important;
            }

            /* Stats grid mobile */
            .grid.md\:grid-cols-3 {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }

            /* Tabs mobile */
            .tab-bar {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .tab-btn {
                padding: 0.6rem 0.875rem !important;
                font-size: 0.75rem !important;
            }

            /* Balance card mobile */
            .balance-card {
                padding: 1.5rem !important;
            }

            .balance-card::before {
                width: 200px !important;
                height: 200px !important;
            }

            /* Modal notificação mobile */
            .proxy-test-notification {
                min-width: 90vw !important;
                padding: 1.5rem !important;
            }

            /* Filter tabs mobile */
            .filter-tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
            }

            .filter-tab {
                white-space: nowrap;
            }

            /* VPS header mobile */
            .vps-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            /* Payment method mobile */
            .payment-method {
                padding: 0.75rem !important;
            }

            /* Summary card mobile */
            .summary-card {
                padding: 1rem !important;
            }

            /* Amount buttons mobile */
            .amount-btn {
                padding: 0.75rem !important;
                font-size: 0.875rem !important;
            }

            /* Bulk bar mobile */
            .bulk-bar {
                bottom: 1rem;
                padding: 0.6rem 0.75rem;
                gap: 0.5rem;
                border-radius: 16px;
                max-width: 95vw;
                flex-wrap: wrap;
                justify-content: center;
            }

            .bulk-bar .protocol-toggle button {
                padding: 0.3rem 0.6rem;
                font-size: 0.65rem;
            }

            .bulk-bar .bulk-copy-btn {
                padding: 0.4rem 0.9rem;
                font-size: 0.7rem;
            }

            .bulk-bar .bulk-count {
                font-size: 0.7rem;
            }
        }

        /* Tablet - 641px até 1024px */
        @media (min-width: 641px) and (max-width: 1024px) {
            h1 {
                font-size: 2.5rem !important;
            }

            .proxy-card,
            .order-card {
                padding: 1.25rem !important;
            }

            .grid.lg\:grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
        }

        /* Ajustes para telas pequenas */
        @media (max-width: 768px) {

            /* Esconder colunas menos importantes em mobile */
            .proxy-table th:nth-child(5),
            .proxy-table td:nth-child(5) {
                display: none;
            }

            /* Switch tabs mobile */
            .switch-tab {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.75rem !important;
            }

            /* Timeline mobile */
            .timeline-item {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }

            /* Contact method mobile */
            .contact-method {
                padding: 1rem !important;
            }

            /* FAQ item mobile */
            .faq-item {
                padding: 1rem !important;
            }
        }

        /* Touch devices */
        @media (hover: none) and (pointer: coarse) {

            .tab-btn,
            .action-btn,
            .filter-tab,
            button {
                min-height: 44px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        use App\Models\User;
        use App\Models\Vps;

        // Função helper para obter URL da bandeira do país
        function getCountryFlag($countryCode)
        {
            if (empty($countryCode)) {
                return null;
            }
            $code = strtolower($countryCode);
            return "https://flagcdn.com/72x54/{$code}.webp";
        }

        $proxyGroups = $proxyGroups ?? [
            'SOCKS5' => [
            ]
        ];
        $pagamentos = $pagamentos ?? collect([
            (object) [
                'id' => 'TXN-001',
                'valor' => 100.00,
                'status' => 1,
                'metodo_pagamento' => 'PIX',
                'created_at' => now()->subDays(2),
            ],
            (object) [
                'id' => 'TXN-002',
                'valor' => 50.00,
                'status' => 0,
                'metodo_pagamento' => 'Cartão',
                'created_at' => now()->subHours(5),
            ],
        ]);
        $pagamentos_aprovados = $pagamentos_aprovados ?? $pagamentos->where('status', 1);
        $pagamentos_pendentes = $pagamentos_pendentes ?? $pagamentos->where('status', 0);
        $totalValor = $totalValor ?? $pagamentos_aprovados->sum('valor');
        $transacoes = $transacoes ?? $pagamentos;
        $currentSection = $activeSection ?? 'proxies';
        $couponCampaigns = $couponCampaigns ?? collect();
        $clientLeads = $clientLeads ?? collect();
        $statsCompraProxy = $statsCompraProxy ?? collect();
    @endphp

    <div class="space-y-10" data-sections-wrapper>
        <section class="dash-section {{ $currentSection === 'proxies' ? 'active' : 'hidden' }}" data-section="proxies">
            <div class="flex flex-col gap-6">
                {{-- Header da Seção --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-[#23366f] font-bold text-xs uppercase tracking-widest">
                            <span class="w-2 h-2 rounded-full bg-[#448ccb] animate-pulse"></span>
                            Monitoramento em Tempo Real
                        </div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Gerencie seus <span
                                class="text-[#23366f]">IPs</span></h1>
                        <p class="text-slate-500 font-medium max-w-xl">Veja o que falta para cada contratação expirar, teste
                            as rotas e controle a renovação automática.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" data-section-link="nova-compra"
                            class="group relative px-6 py-3 rounded-2xl bg-[#23366f] text-white text-sm font-bold overflow-hidden transition-all hover:pr-10">
                            <span class="relative z-10">Comprar novos proxies</span>
                            <i
                                class="fas fa-arrow-right absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all"></i>
                        </button>
                    </div>
                </div>

                @php
                    $dataLimitePromocao = \Carbon\Carbon::create(2026, 2, 2, 23, 59, 59);
                    $emPromocao = now()->lte($dataLimitePromocao) && !Auth::user()->isRevendedor();
                @endphp

                {{-- Banner de Promoção --}}
                @if($emPromocao)
                    <div
                        class="bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 rounded-[2rem] p-6 shadow-lg shadow-orange-500/20 border border-white/20">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3 flex-1">
                                <i class="fas fa-fire text-white text-3xl md:text-4xl animate-pulse"></i>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 bg-white/90 backdrop-blur-sm rounded-full">
                                            <span class="text-xs font-black text-red-600 uppercase tracking-wider">Promoção
                                                Ativa</span>
                                        </span>
                                    </div>
                                    <h3 class="text-xl md:text-2xl font-black text-white drop-shadow-lg">
                                        Proxies de 30 dias por R$ 15,00
                                    </h3>
                                    <p class="text-white/90 text-sm font-medium">
                                        Aproveite até <strong>02 de Fevereiro de 2026</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-white/90 backdrop-blur-sm px-5 py-3 rounded-xl shadow-lg">
                                    <div class="text-center">
                                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Economia</div>
                                        <div class="text-2xl md:text-3xl font-black text-red-600">25% OFF</div>
                                        <div class="text-[10px] font-bold text-green-600">R$ 5,00 menos</div>
                                    </div>
                                </div>
                                <button type="button" data-section-link="nova-compra"
                                    class="group relative px-5 py-2.5 rounded-xl bg-white text-red-600 text-sm font-bold hover:shadow-lg transition-all whitespace-nowrap">
                                    Aproveitar agora
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Aviso de Instabilidade --}}
                <div
                    class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-2 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex flex-col md:flex-row items-center gap-4 text-center md:text-left">
                        <div
                            class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mx-auto md:mx-0">
                            <i class="fab fa-facebook text-amber-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-amber-900 mb-1">Aviso de instabilidade</h3>
                            <p class="text-sm font-medium text-amber-700">Devido a algumas instabilidades na
                                <strong>Meta/Facebook</strong> algumas proxies apresentaram erros de carregamento. Retire
                                sua substituição agora mesmo com nosso atendimento.
                            </p>
                        </div>
                    </div>
                    <a href="https://wa.me/557798374278" target="_blank"
                        class="shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-green-500 text-white font-bold hover:bg-green-600 hover:scale-105 transition-all text-sm shadow-md">
                        <i class="fab fa-whatsapp text-lg"></i>
                        <span>Contatar via WhatsApp</span>
                    </a>
                </div>

                {{-- Status Geral --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total de Proxies</p>
                        <p class="text-3xl font-black text-slate-900">{{ collect($proxyGroups)->flatten(1)->count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Proxies SOCKS5</p>
                        <p class="text-3xl font-black text-slate-900">{{ count($proxyGroups['SOCKS5'] ?? []) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Próximo Vencimento
                        </p>
                        @php
                            $allProxies = collect($proxyGroups)->flatten(1);
                            $nextExp = $allProxies
                                ->where('bloqueada', '=', 0)
                                ->filter(fn($p) => \Carbon\Carbon::parse($p['expires_at'])->isFuture())
                                ->sortBy('expires_at')
                                ->first();
                        @endphp
                        <p class="text-3xl font-black text-slate-900">
                            {{ $nextExp ? intval(now()->diffInDays(\Carbon\Carbon::parse($nextExp['expires_at']))) . ' dias' : 'N/A' }}
                        </p>
                    </div>
                </div>

                @if(session('proxies_success'))
                    <div
                        class="alert alert-success bg-green-50 text-green-700 border-green-100 rounded-2xl p-4 font-semibold flex items-center gap-3">
                        <i class="fas fa-check-circle"></i> {{ session('proxies_success') }}
                    </div>
                @endif
                @if($errors->getBag('default')->has('error'))
                    <div
                        class="alert alert-error bg-red-50 text-red-700 border-red-100 rounded-2xl p-4 font-semibold flex items-center gap-3">
                        <i class="fas fa-exclamation-circle"></i> {{ $errors->getBag('default')->first('error') }}
                    </div>
                @endif

                {{-- Filtros e Tabs --}}
                <div class="tab-bar">
                    @foreach ($proxyGroups as $group => $proxies)
                        <button type="button" class="tab-btn {{ $loop->first ? 'active' : '' }}" data-tab="{{ $group }}">
                            {{ \Illuminate\Support\Str::headline($group) }}
                            <span class="tab-count">{{ count($proxies) }}</span>
                        </button>
                    @endforeach
                </div>

                @foreach ($proxyGroups as $group => $proxies)
                    <div class="proxy-card {{ $loop->first ? '' : 'hidden' }}" data-tab-panel="{{ $group }}">
                        @if(count($proxies))
                            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-x-auto">
                                <table class="proxy-table w-full" style="min-width: 1100px;">
                                    <thead>
                                        <tr class="bg-slate-50/50 border-b border-slate-100">
                                            <th class="px-4 py-5 text-center" style="width: 40px;">
                                                <input type="checkbox" class="proxy-checkbox proxy-select-all"
                                                    data-group="{{ $group }}" title="Selecionar todos">
                                            </th>
                                            <th
                                                class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-left">
                                                Endereço / Ações</th>
                                            <th
                                                class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                                País</th>
                                            <th
                                                class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                                Contratação</th>
                                            <th
                                                class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                                Expiração</th>
                                            <th
                                                class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                                Status</th>
                                            <th
                                                class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                                Renovar via Pix</th>
                                            <th
                                                class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                                                Renovação Automática</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @foreach ($proxies as $proxy)
                                                            <tr class="hover:bg-slate-50/50 transition-colors proxy-row" data-group="{{ $group }}"
                                                                data-proxy-string="{{ $proxy['ip'] }}:{{ $proxy['port'] }}:{{ $proxy['user'] }}:{{ $proxy['password'] }}">
                                                                <td class="px-4 py-6 text-center">
                                                                    <input type="checkbox" class="proxy-checkbox proxy-row-check"
                                                                        data-group="{{ $group }}">
                                                                </td>
                                                                <td class="px-8 py-6">
                                                                    <div class="flex flex-col gap-3">
                                                                        <div
                                                                            class="address-chip group relative bg-slate-100 hover:bg-[#23366f] hover:text-white transition-all cursor-pointer">
                                                                            {{ $proxy['ip'] }}:{{ $proxy['port'] }} | {{ $proxy['user'] }} |
                                                                            {{ $proxy['password'] }}
                                                                            <div
                                                                                class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-[#448ccb] text-white rounded-full scale-0 group-hover:scale-100 transition-transform">
                                                                                <i class="fas fa-lock text-[8px]"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex items-center gap-2">
                                                                            <button
                                                                                class="action-btn px-4 py-2 rounded-xl border border-slate-200 text-[11px] font-bold hover:bg-[#23366f] hover:!text-white hover:border-[#23366f] transition-all"
                                                                                onclick="testarProxy('{{ $proxy['ip'] }}', '{{ $proxy['port'] }}', '{{ $proxy['user'] }}', '{{ $proxy['password'] }}', this)">
                                                                                <i class="fas fa-bolt mr-1.5"></i> Testar proxy
                                                                            </button>
                                                                            <button
                                                                                class="action-btn px-4 py-2 rounded-xl border border-slate-200 text-[11px] font-bold hover:bg-[#23366f] hover:!text-white hover:border-[#23366f] transition-all"
                                                                                onclick="copyToClipboard('socks5://{{ $proxy['ip'] }}:{{ $proxy['port'] }}:{{ $proxy['user'] }}:{{ $proxy['password'] }}')">
                                                                                <i class="fas fa-copy mr-1.5"></i> Copiar
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-6">
                                                                    <div class="flex flex-col items-center gap-2">
                                                                        @php $flagUrl = getCountryFlag($proxy['country_code'] ?? null); @endphp
                                                                        @if($flagUrl)
                                                                            <img src="{{ $flagUrl }}"
                                                                                class="w-8 h-6 rounded-md shadow-sm object-cover border border-slate-100">
                                                                        @else
                                                                            <span class="text-xl">🌐</span>
                                                                        @endif
                                                                        <span
                                                                            class="text-[10px] font-bold text-slate-500 uppercase">{{ $proxy['country'] ?? 'BR' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-6 text-center">
                                                                    <p class="text-sm font-bold text-slate-700">
                                                                        {{ \Carbon\Carbon::parse($proxy['purchased_at'])->format('d/m/Y') }}
                                                                    </p>
                                                                    <p class="text-[10px] font-medium text-slate-400">
                                                                        {{ \Carbon\Carbon::parse($proxy['purchased_at'])->format('H:i') }}
                                                                    </p>
                                                                </td>
                                                                <td class="px-6 py-6 text-center">
                                                                    <p class="text-sm font-bold text-slate-700">
                                                                        {{ \Carbon\Carbon::parse($proxy['expires_at'])->format('d/m/Y') }}
                                                                    </p>
                                                                    @php
                                                                        $expiresAt = \Carbon\Carbon::parse($proxy['expires_at']);
                                                                        $now = now();

                                                                        if ($expiresAt->isPast()) {
                                                                            $days = 0;
                                                                            $colorClass = 'text-red-500 bg-red-50';
                                                                            $label = 'Expirado';
                                                                        } else {
                                                                            $days = (int) $now->diffInDays($expiresAt);
                                                                            $colorClass = $days < 3 ? 'text-red-500 bg-red-50' : ($days < 7 ? 'text-amber-500 bg-amber-50' : 'text-slate-500 bg-slate-50');
                                                                            $label = $days . ' dias restantes';
                                                                        }
                                                                    @endphp
                                                                    <span
                                                                        class="inline-block mt-1 px-3 py-1 rounded-lg text-[10px] font-bold {{ $colorClass }}">
                                                                        {{ $label }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-6 text-center">
                                                                    <span
                                                                        class="px-4 py-1.5 rounded-full bg-green-50 text-green-600 text-[10px] font-bold uppercase tracking-widest border border-green-100">
                                                                        Ativo
                                                                    </span>
                                                                </td>
                                                                {{-- Coluna: Renovar via Pix --}}
                                                                <td class="px-6 py-6 text-center">
                                                                    <div class="flex flex-col items-center gap-2">
                                                                        <button type="button" onclick="abrirModalRenovacao({{ json_encode([
                                                'id' => $proxy['id'],
                                                'endereco' => $proxy['ip'] . ':' . $proxy['port'],
                                                'pais' => $proxy['country'] ?? 'BR',
                                                'expiracao' => $proxy['expires_at'],
                                                'bloqueado' => $proxy['blocked'] ?? false,
                                                'expirado' => \Carbon\Carbon::parse($proxy['expires_at'])->isPast(),
                                            ]) }})"
                                                                            class="action-btn px-4 py-2.5 rounded-xl border-2 border-emerald-500 bg-emerald-50 text-emerald-700 hover:bg-emerald-500 hover:!text-white hover:border-emerald-500 font-bold text-[11px] transition-all shadow-sm hover:shadow-emerald-500/30">
                                                                            <i class="fas fa-qrcode mr-1.5"></i> Renovar via Pix
                                                                        </button>
                                                                        <p class="text-[8px] text-slate-400 text-center">Pagamento instantâneo</p>
                                                                    </div>
                                                                </td>
                                                                {{-- Coluna: Renovação Automática com Cartão --}}
                                                                <td class="px-6 py-6 text-center">
                                                                    <div class="flex flex-col items-center gap-2">
                                                                        <div class="flex items-center gap-2 mb-1">
                                                                            <i class="fas fa-credit-card text-sm text-slate-400"></i>
                                                                            <label class="switch scale-75">
                                                                                <input type="checkbox" class="auto-renew-toggle"
                                                                                    data-proxy-id="{{ $proxy['id'] }}" {{ $proxy['auto_renew'] ? 'checked' : '' }}>
                                                                                <span class="slider"></span>
                                                                            </label>
                                                                        </div>
                                                                        <p class="text-[8px] text-slate-400 text-center leading-tight">Cobrança
                                                                            automática<br>via cartão de crédito</p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-white rounded-[2rem] border-2 border-dashed border-slate-200 p-16 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-server text-3xl text-slate-300"></i>
                                </div>
                                <h3 class="text-xl font-black text-slate-900 mb-2">Nenhum proxy {{ $group }} ativo</h3>
                                <p class="text-slate-500 mb-8 max-w-sm mx-auto">Você ainda não possui proxies ativos neste grupo.
                                    Comece agora mesmo!</p>
                                <button type="button" data-section-link="nova-compra"
                                    class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-[#23366f] text-white font-bold hover:scale-105 transition-all">
                                    Comprar novos proxies
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Proxies Substituídos (histórico) --}}
            @if(!empty($proxiesSubstituidos))
                <div class="mt-8">
                    <button type="button" id="toggleSubstituidos"
                        class="flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors mb-4">
                        <i class="fas fa-history"></i>
                        Proxies substituídos ({{ count($proxiesSubstituidos) }})
                        <i class="fas fa-chevron-down text-xs transition-transform" id="chevronSubstituidos"></i>
                    </button>
                    <div id="substituidos-list" class="hidden">
                        <div class="bg-slate-50 border border-slate-200 rounded-3xl overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">
                                            Proxy</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">
                                            País</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-widest">
                                            Expiração</th>
                                        <th
                                            class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase tracking-widest">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($proxiesSubstituidos as $ps)
                                        <tr class="opacity-60">
                                            <td class="px-6 py-4">
                                                <span class="font-mono text-slate-600">{{ $ps['ip'] }}:{{ $ps['port'] }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($ps['country_code'])
                                                    <img src="https://flagcdn.com/20x15/{{ strtolower($ps['country_code']) }}.png"
                                                        class="inline mr-1" alt="">
                                                @endif
                                                <span class="text-slate-600">{{ $ps['country'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-slate-500">
                                                {{ $ps['expires_at'] ? \Carbon\Carbon::parse($ps['expires_at'])->format('d/m/Y') : '—' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-600">
                                                    <i class="fas fa-exchange-alt"></i> Substituído
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <script>
                    document.getElementById('toggleSubstituidos').addEventListener('click', function () {
                        const list = document.getElementById('substituidos-list');
                        const chevron = document.getElementById('chevronSubstituidos');
                        list.classList.toggle('hidden');
                        chevron.classList.toggle('rotate-180');
                    });
                </script>
            @endif

            {{-- Modal de Renovação --}}
            @include('dash.partials.modal-renovacao')

            {{-- Barra flutuante de cópia em massa --}}
            <div class="bulk-bar" id="bulkBar">
                <span class="bulk-count" id="bulkCount">0 selecionados</span>
                <div class="bulk-separator"></div>
                <div class="protocol-toggle">
                    <button type="button" class="active" data-protocol="com">socks5://</button>
                    <button type="button" data-protocol="sem">Sem protocolo</button>
                </div>
                <button type="button" class="bulk-copy-btn" id="bulkCopyBtn">
                    <i class="fas fa-copy"></i> Copiar
                </button>
                <button type="button" class="bulk-close-btn" id="bulkCloseBtn" title="Desmarcar todos">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </section>

        <section class="dash-section {{ $currentSection === 'perfil' ? 'active' : 'hidden' }}" data-section="perfil">
            @include('dash.partials.perfil')
        </section>

        <section class="dash-section {{ $currentSection === 'nova-compra' ? 'active' : 'hidden' }}"
            data-section="nova-compra">
            @include('dash.partials.nova-compra')
        </section>

        <section class="dash-section {{ $currentSection === 'transacoes' ? 'active' : 'hidden' }}"
            data-section="transacoes">
            @if(session('transacoes_success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('transacoes_success') }}
                </div>
            @endif
            @include('dash.partials.transacoes')
        </section>

        <section class="dash-section {{ $currentSection === 'saldo' ? 'active' : 'hidden' }}" data-section="saldo">
            @if(session('saldo_success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('saldo_success') }}
                </div>
            @endif
            @include('dash.partials.saldo')
        </section>

        <section class="dash-section {{ $currentSection === 'suporte' ? 'active' : 'hidden' }}" data-section="suporte">
            @include('dash.partials.suporte')
        </section>

        <section class="dash-section {{ $currentSection === 'configuracoes' ? 'active' : 'hidden' }}"
            data-section="configuracoes">
            @include('dash.partials.configuracoes')
        </section>

        <section class="dash-section {{ $currentSection === 'cartoes' ? 'active' : 'hidden' }}" data-section="cartoes">
            @include('dash.partials.cartoes')
        </section>

        @if(Auth::user()->isAdmin())
            <section class="dash-section {{ $currentSection === 'admin-proxies' ? 'active' : 'hidden' }}"
                data-section="admin-proxies">
                @include('dash.partials.admin.proxies')
            </section>

            <section class="dash-section {{ $currentSection === 'admin-historico-vps' ? 'active' : 'hidden' }}"
                data-section="admin-historico-vps">
                @include('dash.partials.admin.historico-vps')
            </section>

            <section class="dash-section {{ $currentSection === 'admin-usuarios' ? 'active' : 'hidden' }}"
                data-section="admin-usuarios">
                @include('dash.partials.admin.usuarios')
            </section>

            @if(Auth::user()->isSuperAdmin())
                <section class="dash-section {{ $currentSection === 'admin-transacoes' ? 'active' : 'hidden' }}"
                    data-section="admin-transacoes">
                    @include('dash.partials.admin.transacoes')
                </section>

                <section class="dash-section {{ $currentSection === 'admin-relatorios' ? 'active' : 'hidden' }}"
                    data-section="admin-relatorios">
                    @include('dash.partials.admin.relatorios')
                </section>

                <section class="dash-section {{ $currentSection === 'admin-cupons' ? 'active' : 'hidden' }}"
                    data-section="admin-cupons">
                    @include('dash.partials.admin.cupons')
                </section>

                <section class="dash-section {{ $currentSection === 'admin-colaboradores' ? 'active' : 'hidden' }}"
                    data-section="admin-colaboradores">
                    @include('dash.partials.admin.colaboradores')
                </section>

                <section class="dash-section {{ $currentSection === 'admin-financeiro' ? 'active' : 'hidden' }}"
                    data-section="admin-financeiro">
                    @include('dash.partials.admin.financeiro')
                </section>
            @endif

        @endif
    </div>

    {{-- Modal de Boas-vindas (coleta CPF e telefone) --}}
    @include('dash.partials.modal-welcome')
@endsection

@section('scripts')
    window.initialDashSection = @json($currentSection ?? 'proxies');

    (() => {
    const tabBtns = document.querySelectorAll('[data-tab]');
    const tabPanels = document.querySelectorAll('[data-tab-panel]');
    if (!tabBtns.length) return;

    tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
    const target = btn.dataset.tab;
    tabBtns.forEach(b => b.classList.remove('active'));
    tabPanels.forEach(panel => panel.classList.toggle('hidden', panel.dataset.tabPanel !== target));
    btn.classList.add('active');
    });
    });
    })();

    window.copyToClipboard = function (text, successMessage) {
    const toast = (msg, type = 'success') => {
    if (typeof window.showToast === 'function') {
    window.showToast(msg, type);
    } else {
    alert(msg);
    }
    };

    const copyModern = async () => {
    if (navigator.clipboard?.writeText && window.isSecureContext) {
    await navigator.clipboard.writeText(text);
    return true;
    }
    return false;
    };

    const copyFallback = () => {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
    const ok = document.execCommand('copy');
    textArea.remove();
    return ok;
    } catch (e) {
    textArea.remove();
    return false;
    }
    };

    (async () => {
    const okModern = await copyModern();
    const ok = okModern || copyFallback();

    if (ok) {
    toast(successMessage ?? `Proxy copiado: ${text}`, 'success');
    } else {
    toast('Não foi possível copiar. Use HTTPS/localhost ou copie manualmente.', 'error');
    }
    })();
    };


    (() => {
    const orderForm = document.getElementById('orderForm');
    if (!orderForm) return;

    const priceCards = orderForm.querySelectorAll('.price-card');
    const periodRadios = orderForm.querySelectorAll('input[name="periodo"]');
    const paymentMethods = orderForm.querySelectorAll('.payment-method');
    const paymentInput = document.getElementById('orderPaymentMethod');
    const quantityInput = document.getElementById('quantidade');
    let selectedPrice = 0;
    let selectedPeriod = null;

    const refreshSelectedPeriod = () => {
    const checked = orderForm.querySelector('input[name="periodo"]:checked');
    if (!checked) {
    selectedPeriod = null;
    selectedPrice = 0;
    return;
    }
    selectedPeriod = checked.value;
    selectedPrice = parseFloat(checked.dataset.price || '0');
    };

    const refreshFromDefaults = () => {
    refreshSelectedPeriod();
    paymentMethods.forEach(method => {
    if (method.classList.contains('selected')) {
    paymentInput.value = method.dataset.method;
    }
    });
    updateSummary();
    };

    const updateSummary = () => {
    const qty = parseInt(quantityInput.value || '1', 10);
    const total = selectedPrice * qty;
    document.getElementById('summary-qty').textContent = `${qty} ${qty > 1 ? 'proxies' : 'proxy'}`;
    document.getElementById('summary-period').textContent = selectedPeriod ? `${selectedPeriod} dias` : 'Selecione';
    document.getElementById('summary-unit').textContent = `R$ ${selectedPrice.toFixed(2).replace('.', ',')}`;
    document.getElementById('summary-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
    };

    periodRadios.forEach(radio => {
    radio.addEventListener('change', () => {
    refreshSelectedPeriod();
    updateSummary();
    });
    });

    priceCards.forEach(card => {
    card.addEventListener('click', () => {
    const label = card.closest('label');
    const radio = label?.querySelector('input[name="periodo"]');
    if (!radio) return;
    radio.checked = true;
    radio.dispatchEvent(new Event('change', { bubbles: true }));
    });
    });

    paymentMethods.forEach(method => {
    method.addEventListener('click', () => {
    if (!method.closest('#rechargeForm')) {
    paymentMethods.forEach(m => m.classList.remove('selected'));
    method.classList.add('selected');
    paymentInput.value = method.dataset.method;

    // Mostrar/ocultar campos do cartão de crédito
    const creditCardFields = document.getElementById('creditCardFields');
    if (creditCardFields) {
    creditCardFields.style.display = method.dataset.method === 'credit_card' ? 'block' : 'none';
    }
    }
    });
    });

    quantityInput.addEventListener('input', updateSummary);

    // Validação do formulário removida - agora está em nova-compra.blade.php

    refreshFromDefaults();
    })();

    (() => {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const rows = document.querySelectorAll('#transactionsBody tr');
    if (!filterTabs.length || !rows.length) return;

    filterTabs.forEach(tab => {
    tab.addEventListener('click', () => {
    filterTabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const filter = tab.dataset.filter;

    rows.forEach(row => {
    const status = row.dataset.status;
    let visible = false;
    if (filter === 'all') visible = true;
    else if (filter === 'aprovadas' && status === '1') visible = true;
    else if (filter === 'pendentes' && status === '0') visible = true;
    else if (filter === 'falhas' && status !== '0' && status !== '1') visible = true;
    row.style.display = visible ? '' : 'none';
    });
    });
    });
    })();

    (() => {
    const rechargeForm = document.getElementById('rechargeForm');
    if (!rechargeForm) return;

    const switchTabs = document.querySelectorAll('.switch-tab');
    const addBalance = document.getElementById('addBalance');
    const historyBalance = document.getElementById('historyBalance');
    const amountButtons = rechargeForm.querySelectorAll('.amount-btn');
    const paymentMethods = rechargeForm.querySelectorAll('.payment-method');
    const paymentInput = document.getElementById('walletPaymentMethod');
    const customAmount = document.getElementById('customAmount');

    // Máscara de dinheiro para o input customAmount
    if (customAmount) {
    customAmount.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value === '') {
    e.target.value = '';
    return;
    }

    // Converter para centavos e formatar
    value = (parseInt(value) / 100).toFixed(2);
    value = value.replace('.', ',');
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    e.target.value = value;
    });

    // Ao clicar nos botões de valor, aplicar a máscara também
    customAmount.addEventListener('blur', function(e) {
    if (e.target.value && !e.target.value.includes(',')) {
    let value = parseFloat(e.target.value).toFixed(2);
    value = value.replace('.', ',');
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    e.target.value = value;
    }
    });
    }

    switchTabs.forEach(tab => {
    tab.addEventListener('click', () => {
    switchTabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const view = tab.dataset.view;
    addBalance.style.display = view === 'add' ? 'block' : 'none';
    historyBalance.style.display = view === 'history' ? 'block' : 'none';
    });
    });

    amountButtons.forEach(btn => {
    btn.addEventListener('click', () => {
    amountButtons.forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    // Aplicar máscara ao valor do botão
    let value = parseFloat(btn.dataset.amount).toFixed(2);
    value = value.replace('.', ',');
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    customAmount.value = value;
    });
    });

    paymentMethods.forEach(method => {
    if (method.closest('#rechargeForm')) {
    method.addEventListener('click', () => {
    paymentMethods.forEach(m => m.classList.remove('selected'));
    method.classList.add('selected');
    paymentInput.value = method.dataset.method;
    });
    }
    });

    rechargeForm.addEventListener('submit', (event) => {
    // Converter o valor formatado (1.234,56) para número (1234.56)
    let amountStr = customAmount.value.replace(/\./g, '').replace(',', '.');
    const amount = parseFloat(amountStr || '0');

    if (!paymentInput.value) {
    event.preventDefault();
    alert('Selecione um metodo de pagamento.');
    return;
    }
    if (!amount || amount < 1) { event.preventDefault(); alert('Informe um valor valido (mínimo R$ 1,00).'); return; } //
        const hiddenInput=document.createElement('input'); hiddenInput.type='hidden' ; hiddenInput.name='valor' ;
        hiddenInput.value=amount; // Remover o atributo name do input visível para não enviar o valor formatado
        customAmount.removeAttribute('name'); // Adicionar o input hidden ao formulário
        rechargeForm.appendChild(hiddenInput); }); })(); (()=> {
        document.querySelectorAll('.faq-item').forEach(item => {
        item.addEventListener('click', () => {
        item.classList.toggle('open');
        });
        });
        })();

        (() => {
        const saveSettingsBtn = document.querySelector('[data-settings-save]');
        if (!saveSettingsBtn) return;
        saveSettingsBtn.addEventListener('click', () => {
        alert('Configuracoes salvas com sucesso!');
        });
        })();

        (() => {
        document.querySelectorAll('[data-admin-accordion]').forEach(trigger => {
        trigger.addEventListener('click', () => {
        const targetId = trigger.getAttribute('data-admin-accordion');
        const target = document.getElementById(targetId);
        target?.classList.toggle('hidden');
        trigger.querySelector('.fa-chevron-down')?.classList.toggle('rotate-180');
        });
        });
        })();

        (() => {
        const overlay = document.getElementById('buyerModalOverlay');
        const modal = document.getElementById('buyerModal');
        if (!overlay || !modal) return;

        const setModal = (btn) => {
        modal.querySelector('[data-buyer-name]').textContent = btn.dataset.buyerName ?? 'Cliente';
        modal.querySelector('[data-buyer-email]').textContent = btn.dataset.buyerEmail ?? '---';
        modal.querySelector('[data-buyer-orders]').textContent = btn.dataset.buyerOrders ?? '--';
        modal.querySelector('[data-buyer-spent]').textContent = btn.dataset.buyerSpent ?? '--';
        modal.querySelector('[data-buyer-note]').textContent = `Histórico consolidado: ${btn.dataset.buyerOrders ?? '--'}
        pedidos • ${btn.dataset.buyerSpent ?? '--'} em proxies.`;
        };

        const closeModal = () => {
        overlay.classList.remove('active');
        modal.classList.remove('active');
        };

        document.querySelectorAll('[data-open-buyer]').forEach(btn => {
        btn.addEventListener('click', () => {
        setModal(btn);
        overlay.classList.add('active');
        modal.classList.add('active');
        });
        });

        overlay.addEventListener('click', closeModal);
        modal.querySelectorAll('[data-close-buyer]').forEach(el => el.addEventListener('click', closeModal));
        })();



        (() => {
        document.querySelectorAll('[data-replace-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
        const target = document.querySelector(btn.dataset.replaceToggle);
        target?.classList.toggle('hidden');
        });
        });
        })();


        const renderPixModal = (pixData) => {
        if (!pixData) {
        return;
        }

        const existing = document.getElementById('pixModal');
        if (existing) {
        existing.remove();
        }

        const modalHTML = `
        <div id="pixModal" class="admin-modal-overlay active">
            <div class="admin-modal" style="max-width: 500px;">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-blue-50 text-[#23366f] flex items-center justify-center text-xl">
                            <i class="fa-brands fa-pix"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Pagamento PIX</h3>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Aguardando confirmacao</p>
                        </div>
                    </div>
                    <button onclick="fecharModalPix()" class="text-slate-400 hover:text-slate-900 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 mb-6">
                    <div class="text-center mb-4">
                        <p class="text-sm text-slate-600 mb-2">Valor a pagar</p>
                        <p class="text-3xl font-bold text-slate-900">R$ ${pixData.valor.toFixed(2).replace('.', ',')}</p>
                    </div>

                    <!-- QR Code -->
                    <div class="bg-white p-4 rounded-xl mb-4 flex items-center justify-center" style="min-height: 200px;">
                        ${pixData.qr_code_base64
                        ? `<img src="${pixData.qr_code_base64}" alt="QR Code PIX" class="max-w-full h-auto"
                            style="max-height: 250px;">`
                        : `<div class="text-center">
                            <svg class="w-32 h-32 mx-auto mb-2 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z" />
                            </svg>
                            <p class="text-xs text-slate-500">QR Code PIX</p>
                        </div>`
                        }
                    </div>

                    <!-- Temporizador -->
                    <div class="text-center text-sm font-black text-amber-600 uppercase tracking-widest mb-4">
                        <i class="fas fa-clock mr-1"></i> Expira em: <span id="pixTimer"></span>
                    </div>

                    <!-- Codigo Copia e Cola -->
                    <div class="bg-white rounded-xl p-4 mb-4">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">PIX Copia
                            e Cola</label>
                        <div class="flex gap-2">
                            <input type="text" id="pixCode" value="${pixData.copia_e_cola}" readonly
                                class="flex-1 px-3 py-2 bg-slate-50 border border-slate-100 rounded-lg text-xs font-mono font-bold text-slate-600 truncate px-2">
                            <button onclick="copiarPixCode()"
                                class="px-4 py-2 bg-[#23366f] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:scale-105 transition-all flex items-center gap-2 shadow-lg shadow-blue-900/20">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <button onclick="fecharModalPix()"
                        class="w-full py-4 bg-slate-200 text-slate-700 rounded-xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-300 transition-all">
                        Fechar Janela
                    </button>
                </div>

                <p class="text-[9px] font-bold text-center text-slate-300 uppercase tracking-widest">
                    ID da Transacao: ${pixData.transaction_code}
                </p>
            </div>
        </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);

        window.copiarPixCode = () => {
        const input = document.getElementById('pixCode');
        input.select();
        document.execCommand('copy');
        alert('Codigo PIX copiado!');
        };

        window.fecharModalPix = () => {
        document.getElementById('pixModal')?.remove();
        };

        const expiresAt = pixData.expira_timestamp * 1000;
        const updateTimer = () => {
        const now = Date.now();
        const diff = expiresAt - now;

        if (diff <= 0) { const timerEl=document.getElementById('pixTimer'); if (timerEl) timerEl.textContent='Expirado' ;
            return; } const minutes=Math.floor(diff / 60000); const seconds=Math.floor((diff % 60000) / 1000); const
            timerEl=document.getElementById('pixTimer'); if (timerEl)
            timerEl.textContent=`${minutes}:${seconds.toString().padStart(2, '0' )}`; }; updateTimer(); const
            timerInterval=setInterval(updateTimer, 1000); const checkPaymentStatus=async ()=> {
            try {
            const response = await fetch(`/api/transacao/${pixData.transaction_id}`);
            const data = await response.json();

            if (data.status === 1) {
            clearInterval(timerInterval);
            clearInterval(pollingInterval);

            const modalContent = document.querySelector('#pixModal > div');
            modalContent.innerHTML = `
            <div class="text-center py-8">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Pagamento Confirmado!</h2>
                <p class="text-slate-600 mb-6">Seus proxies foram alocados com sucesso</p>
                <div class="flex items-center justify-center gap-2 mb-4">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                    <p class="text-sm text-slate-600">Redirecionando...</p>
                </div>
            </div>
            `;

            setTimeout(() => {
            window.location.href = '/dash?section=proxies';
            }, 2000);
            }
            } catch (error) {
            console.error('Erro ao verificar status do pagamento:', error);
            }
            };

            const pollingInterval = setInterval(checkPaymentStatus, 3000);
            checkPaymentStatus();
            };
            // Modal PIX
            @if(session('pix_modal'))
                (() => {
                const pixData = @json(session('pix_modal'));
                renderPixModal(pixData);
                })();
            @endif

            window.showProxyTestNotification = function(data, type) {
            // Remover notificação existente se houver
            const existing = document.querySelector('.proxy-test-notification');
            if (existing) {
            existing.remove();
            }
            const existingOverlay = document.querySelector('.notification-overlay');
            if (existingOverlay) {
            existingOverlay.remove();
            }

            // Criar overlay
            const overlay = document.createElement('div');
            overlay.className = 'notification-overlay';
            document.body.appendChild(overlay);

            // Criar notificação
            const notification = document.createElement('div');
            notification.className = `proxy-test-notification ${type}`;

            let icon, title, detailsHTML;

            if (type === 'success') {
            icon = '<i class="fas fa-check-circle"></i>';
            title = 'Proxy Funcionando!';
            detailsHTML = `
            <div class="notification-detail-item">
                <span class="notification-detail-label">Configuração</span>
                <span class="notification-detail-value">${data.proxy_config}</span>
            </div>
            <div class="notification-detail-item">
                <span class="notification-detail-label">IP Detectado</span>
                <span class="notification-detail-value">${data.ip_visto_pelo_servidor}</span>
            </div>
            <div class="notification-detail-item">
                <span class="notification-detail-label">Latência</span>
                <span class="notification-detail-value">${data.latencia_ms}ms</span>
            </div>
            `;
            } else if (type === 'timeout') {
            icon = '<i class="fas fa-clock"></i>';
            title = 'Timeout';
            detailsHTML = `
            <div class="notification-message">
                ${data.mensagem || 'O proxy demorou muito para responder'}
            </div>
            `;
            } else {
            icon = '<i class="fas fa-times-circle"></i>';
            title = 'Proxy Offline';
            detailsHTML = `
            <div class="notification-message">
                ${data.mensagem || data.error || 'Não foi possível conectar ao proxy'}
            </div>
            `;
            }

            notification.innerHTML = `
            <div class="notification-icon ${type}">
                ${icon}
            </div>
            <div class="notification-title">${title}</div>
            <div class="notification-details">
                ${detailsHTML}
            </div>
            ${data.mensagem && type === 'success' ? `<div class="notification-message">${data.mensagem}</div>` : ''}
            <button class="notification-close-btn"
                onclick="this.closest('.proxy-test-notification').remove(); document.querySelector('.notification-overlay').remove();">
                Fechar
            </button>
            `;

            document.body.appendChild(notification);

            // Fechar ao clicar no overlay
            overlay.addEventListener('click', () => {
            notification.remove();
            overlay.remove();
            });
            };

            window.testarProxy = async function(ip, porta, usuario, senha, btnElement) {
            const originalText = btnElement.innerHTML;

            try {
            // Mostrar estado de carregamento
            btnElement.disabled = true;
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Testando...';

            // Chamar rota Laravel protegida
            const response = await fetch('{{ route("proxies.testar") }}', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
            ip: ip,
            porta: parseInt(porta),
            usuario: usuario,
            senha: senha
            })
            });

            const data = await response.json();

            // Verificar se o proxy está online
            if (response.ok && data.status === 'online') {
            // Mostrar notificação de sucesso
            showProxyTestNotification(data, 'success');

            // Restaurar botão com ícone de sucesso temporário
            btnElement.innerHTML = '<i class="fas fa-check text-xs"></i> Online';
            btnElement.classList.add('bg-green-500', 'text-white');

            setTimeout(() => {
            btnElement.innerHTML = originalText;
            btnElement.classList.remove('bg-green-500', 'text-white');
            btnElement.disabled = false;
            }, 3000);
            } else {
            // Determinar tipo de erro
            const notificationType = data.status === 'timeout' ? 'timeout' : 'error';

            // Mostrar notificação de erro
            showProxyTestNotification(data, notificationType);

            // Restaurar botão com ícone de erro temporário
            btnElement.innerHTML = '<i class="fas fa-times text-xs"></i> Offline';
            btnElement.classList.add('bg-red-500', 'text-white');

            setTimeout(() => {
            btnElement.innerHTML = originalText;
            btnElement.classList.remove('bg-red-500', 'text-white');
            btnElement.disabled = false;
            }, 3000);
            }
            } catch (error) {
            console.error('Erro ao testar proxy:', error);

            // Mostrar notificação de erro de conexão
            showProxyTestNotification({
            error: 'Erro ao conectar com o servidor de testes',
            mensagem: error.message
            }, 'error');

            // Restaurar botão
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;
            }
            };

            // ==========================================
            // MODAL DE RENOVAÇÃO DE PROXY
            // ==========================================

            let renovacaoProxyData = null;
            const autoRenewToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const setAutoRenew = async (proxyId, enabled, toggleEl) => {
            if (!proxyId || !autoRenewToken) {
            return;
            }

            if (toggleEl) {
            toggleEl.disabled = true;
            }

            try {
            const response = await fetch('{{ route("proxies.renovar") }}', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': autoRenewToken,
            },
            body: JSON.stringify({
            proxy_id: proxyId,
            auto_renew: enabled,
            }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.success) {
            throw new Error(data.error || data.message || 'Erro ao atualizar renovacao automatica');
            }
            } catch (error) {
            if (toggleEl) {
            toggleEl.checked = !enabled;
            }
            alert(error.message || 'Erro ao atualizar renovacao automatica');
            } finally {
            if (toggleEl) {
            toggleEl.disabled = false;
            }
            }
            };

            const hasCardSaved = {{ isset($savedCards) && count($savedCards) > 0 ? 'true' : 'false' }};

            const checkCardBeforeAutoRenew = (toggle) => {
            if (toggle.checked && !hasCardSaved) {
            toggle.checked = false;

            const toast = document.createElement('div');

            toast.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-credit-card text-amber-500 text-lg"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Nenhum cartão cadastrado</h4>
                    <p class="text-xs text-slate-500 mb-4">Para ativar a renovação automática, você precisa ter um cartão de
                        crédito cadastrado.</p>
                    <button type="button"
                        class="go-to-cards-btn px-4 py-2 rounded-xl bg-[#23366f] text-white text-xs font-bold hover:scale-[1.02] transition-all">
                        <i class="fas fa-plus mr-1.5"></i> Cadastrar cartão
                    </button>
                </div>
                <button type="button" class="close-toast text-slate-300 hover:text-slate-500 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            `;
            document.body.appendChild(toast);

            toast.querySelector('.go-to-cards-btn').addEventListener('click', () => {
            toast.remove();
            const cartoesLink = document.querySelector('[data-section-link="cartoes"]');
            if (cartoesLink) cartoesLink.click();
            });

            toast.querySelector('.close-toast').addEventListener('click', () => toast.remove());

            setTimeout(() => toast.remove(), 8000);

            return false;
            }
            return true;
            };

            document.querySelectorAll('.auto-renew-toggle').forEach(toggle => {
            toggle.addEventListener('change', () => {
            if (!checkCardBeforeAutoRenew(toggle)) return;
            const proxyId = toggle.dataset.proxyId;
            setAutoRenew(proxyId, toggle.checked, toggle);
            });
            });

            const modalAutoRenewToggle = document.getElementById('renovacao-auto-toggle');
            if (modalAutoRenewToggle) {
            modalAutoRenewToggle.addEventListener('change', () => {
            if (!checkCardBeforeAutoRenew(modalAutoRenewToggle)) return;
            const proxyId = modalAutoRenewToggle.dataset.proxyId;
            setAutoRenew(proxyId, modalAutoRenewToggle.checked, modalAutoRenewToggle);
            });
            }

            window.abrirModalRenovacao = function(proxyData) {
            renovacaoProxyData = proxyData;

            const modalAutoToggle = document.getElementById('renovacao-auto-toggle');
            if (modalAutoToggle) {
            modalAutoToggle.checked = !!proxyData.auto_renew;
            modalAutoToggle.dataset.proxyId = proxyData.id;
            }

            // Preencher informações do proxy
            document.getElementById('renovacao-proxy-endereco').textContent = proxyData.endereco;
            document.getElementById('renovacao-proxy-pais').textContent = proxyData.pais;
            document.getElementById('renovacao-expiracao-atual').textContent =
            new Date(proxyData.expiracao).toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
            });

            // Status do proxy
            const statusEl = document.getElementById('renovacao-proxy-status');
            if (proxyData.expirado) {
            statusEl.innerHTML = '<span class="text-red-600">Expirado</span>';
            } else if (proxyData.bloqueado) {
            statusEl.innerHTML = '<span class="text-amber-600">Bloqueado</span>';
            } else {
            statusEl.innerHTML = '<span class="text-green-600">Ativo</span>';
            }

            // Mostrar alerta se bloqueado
            const alertaBloqueado = document.getElementById('renovacao-alerta-bloqueado');
            if (proxyData.bloqueado) {
            alertaBloqueado.classList.remove('hidden');
            } else {
            alertaBloqueado.classList.add('hidden');
            }

            // Resetar seleção de período
            document.querySelectorAll('.renovacao-period-radio').forEach(radio => {
            radio.checked = false;
            });
            document.getElementById('btn-confirmar-renovacao').disabled = true;
            document.getElementById('renovacao-resumo-periodo').textContent = 'Selecione';
            document.getElementById('renovacao-resumo-valor').textContent = 'R$ 0,00';
            document.getElementById('renovacao-resumo-total').textContent = 'R$ 0,00';
            document.getElementById('renovacao-expiracao-nova').textContent = 'Selecione o período';
            document.getElementById('renovacao-resumo-nova-data').textContent = '--';

            // Event listeners para os períodos
            document.querySelectorAll('.renovacao-period-radio').forEach(radio => {
            radio.addEventListener('change', function() {
            const dias = parseInt(this.value);
            const preco = parseFloat(this.dataset.price);

            // Calcular nova data de expiração
            const expiracaoAtual = new Date(proxyData.expiracao);
            const hoje = new Date();
            let novaExpiracao;

            if (expiracaoAtual < hoje) { // Proxy expirado: adiciona dias a partir de hoje novaExpiracao=new Date(hoje);
                novaExpiracao.setDate(novaExpiracao.getDate() + dias); } else { novaExpiracao=new Date(expiracaoAtual);
                novaExpiracao.setDate(novaExpiracao.getDate() + dias); } // Atualizar resumo
                document.getElementById('renovacao-resumo-periodo').textContent=`${dias} dias`;
                document.getElementById('renovacao-resumo-valor').textContent=`R$ ${preco.toFixed(2).replace('.', ',' )}`;
                document.getElementById('renovacao-resumo-total').textContent=`R$ ${preco.toFixed(2).replace('.', ',' )}`;
                document.getElementById('renovacao-expiracao-nova').textContent=novaExpiracao.toLocaleDateString('pt-BR', {
                day: '2-digit' , month: '2-digit' , year: 'numeric' , hour: '2-digit' , minute: '2-digit' });
                document.getElementById('renovacao-resumo-nova-data').textContent=novaExpiracao.toLocaleDateString('pt-BR',
                { day: '2-digit' , month: '2-digit' , year: 'numeric' }); // Habilitar botão de confirmação
                document.getElementById('btn-confirmar-renovacao').disabled=false; }); }); // Exibir modal
                document.getElementById('modalRenovacao').style.display='flex' ; }; window.fecharModalRenovacao=function() {
                document.getElementById('modalRenovacao').style.display='none' ; renovacaoProxyData=null; };
                window.confirmarRenovacao=async function() { const
                periodoSelecionado=document.querySelector('.renovacao-period-radio:checked'); if (!periodoSelecionado ||
                !renovacaoProxyData) { alert('Selecione um período de renovação'); return; } const
                btnConfirmar=document.getElementById('btn-confirmar-renovacao'); const originalText=btnConfirmar.innerHTML;
                try { // Desabilitar botão e mostrar loading btnConfirmar.disabled=true;
                btnConfirmar.innerHTML='<i class="fas fa-spinner fa-spin"></i> Processando...' ; // Enviar requisição const
                response=await fetch('{{ route("proxies.renovar-pix") }}', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                proxy_id: renovacaoProxyData.id,
                periodo: parseInt(periodoSelecionado.value)
                })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                // Fechar modal de renovacao
                fecharModalRenovacao();

                if (data.pix_modal) {
                renderPixModal(data.pix_modal);
                return;
                }

                if (data.redirect) {
                window.location.href = data.redirect;
                }
                } else {
                alert(data.error || 'Erro ao processar renovação. Tente novamente.');
                btnConfirmar.innerHTML = originalText;
                btnConfirmar.disabled = false;
                }

                } catch (error) {
                console.error('Erro ao processar renovação:', error);
                alert('Erro ao conectar com o servidor. Tente novamente.');
                btnConfirmar.innerHTML = originalText;
                btnConfirmar.disabled = false;
                }
                };

                // Fechar modal ao clicar fora
                document.getElementById('modalRenovacao')?.addEventListener('click', function(e) {
                if (e.target === this) {
                fecharModalRenovacao();
                }
                });

                // ==========================================
                // SELEÇÃO EM MASSA E CÓPIA DE PROXIES
                // ==========================================
                (() => {
                const bulkBar = document.getElementById('bulkBar');
                if (!bulkBar) return;

                const bulkCount = document.getElementById('bulkCount');
                const bulkCopyBtn = document.getElementById('bulkCopyBtn');
                const bulkCloseBtn = document.getElementById('bulkCloseBtn');
                const protocolBtns = bulkBar.querySelectorAll('.protocol-toggle button');
                let withProtocol = true;

                // Toggle de protocolo
                protocolBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                protocolBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                withProtocol = btn.dataset.protocol === 'com';
                });
                });

                const getSelectedCount = () => {
                return document.querySelectorAll('.proxy-row-check:checked').length;
                };

                const updateBulkBar = () => {
                const count = getSelectedCount();
                bulkCount.textContent = `${count} selecionado${count !== 1 ? 's' : ''}`;

                if (count > 0) {
                bulkBar.classList.add('visible');
                } else {
                bulkBar.classList.remove('visible');
                }

                // Atualizar estado dos "select all" checkboxes
                document.querySelectorAll('.proxy-select-all').forEach(selectAll => {
                const group = selectAll.dataset.group;
                const panel = document.querySelector(`[data-tab-panel="${group}"]`);
                if (!panel || panel.classList.contains('hidden')) return;

                const rowChecks = panel.querySelectorAll('.proxy-row-check');
                const checkedCount = panel.querySelectorAll('.proxy-row-check:checked').length;

                selectAll.checked = rowChecks.length > 0 && checkedCount === rowChecks.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < rowChecks.length; });
                    document.querySelectorAll('.proxy-row').forEach(row=> {
                    const check = row.querySelector('.proxy-row-check');
                    row.classList.toggle('proxy-row-selected', check?.checked ?? false);
                    });
                    };

                    // Select all checkbox
                    document.querySelectorAll('.proxy-select-all').forEach(selectAll => {
                    selectAll.addEventListener('change', () => {
                    const group = selectAll.dataset.group;
                    const panel = document.querySelector(`[data-tab-panel="${group}"]`);
                    if (!panel) return;

                    panel.querySelectorAll('.proxy-row-check').forEach(check => {
                    check.checked = selectAll.checked;
                    });
                    updateBulkBar();
                    });
                    });

                    // Individual row checkboxes (event delegation)
                    document.addEventListener('change', (e) => {
                    if (e.target.classList.contains('proxy-row-check')) {
                    updateBulkBar();
                    }
                    });

                    // Copiar selecionados
                    bulkCopyBtn.addEventListener('click', () => {
                    const selected = document.querySelectorAll('.proxy-row-check:checked');
                    if (!selected.length) return;

                    const countProxies = selected.length;
                    const proxyRows = selected.length ? Array.from(selected).map(c =>
                    c.closest('.proxy-row')).filter(Boolean) : [];
                    const proxyStrings = proxyRows.map(row => {
                    const str = row.dataset.proxyString || '';
                    return withProtocol ? 'socks5://' + str : str;
                    }).join('\n');
                    copyToClipboard(proxyStrings || countProxies.toString(), countProxies + ' proxies copiadas');

                    // Feedback visual no botão
                    const originalHTML = bulkCopyBtn.innerHTML;
                    bulkCopyBtn.innerHTML = '<i class="fas fa-check"></i> ' + countProxies + ' proxies copiadas';
                    setTimeout(() => {
                    bulkCopyBtn.innerHTML = originalHTML;
                    }, 2000);
                    });

                    // Fechar / desmarcar todos
                    bulkCloseBtn.addEventListener('click', () => {
                    document.querySelectorAll('.proxy-row-check').forEach(c => c.checked = false);
                    document.querySelectorAll('.proxy-select-all').forEach(c => c.checked = false);
                    updateBulkBar();
                    });
})();@endsection