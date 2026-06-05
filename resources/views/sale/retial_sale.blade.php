<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail Pharmacy POS</title>
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <script src="{{asset('assets/js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
    <script src="{{asset('assets/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/js/ckeditor.js')}}"></script>

    <style>
        :root {
            --pos-primary: #48af5a;
            --pos-primary-dark: #3a9449;
            --pos-primary-light: #e8f5eb;
            --pos-accent: #9f1c20;
            --pos-accent-dark: #7d1619;
            --pos-bg: #eef4f0;
            --pos-surface: #ffffff;
            --pos-border: #d4e4d8;
            --pos-border-strong: #b8cfc0;
            --pos-text: #1e2b22;
            --pos-text-muted: #5a6b60;
            --pos-radius: 10px;
            --pos-radius-sm: 6px;
            --pos-shadow: 0 1px 3px rgba(30, 43, 34, 0.08);
            --pos-shadow-md: 0 4px 16px rgba(30, 43, 34, 0.1);
            --pos-focus: 0 0 0 3px rgba(72, 175, 90, 0.35);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--pos-bg);
            color: var(--pos-text);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 0.9375rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .pos-shell {
            max-width: 1600px;
            margin: 0 auto;
            padding: 12px 12px 88px;
        }

        #popu-message {
            position: fixed;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            min-width: 280px;
            max-width: 90vw;
            padding: 12px 20px;
            text-align: center;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: var(--pos-radius);
            box-shadow: var(--pos-shadow-md);
            display: none;
            color: #fff;
            z-index: 100000;
        }

        .pos-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            background: var(--pos-surface);
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius);
            padding: 12px 16px;
            margin-bottom: 12px;
            box-shadow: var(--pos-shadow);
        }

        .pos-topbar-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--pos-text);
            letter-spacing: -0.02em;
        }

        .pos-topbar-store {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--pos-primary-light);
            color: var(--pos-primary-dark);
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            border: 1px solid var(--pos-border);
        }

        .pos-topbar-store::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--pos-primary);
        }

        .pos-card {
            background: var(--pos-surface);
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius);
            box-shadow: var(--pos-shadow);
            padding: 14px 16px;
            margin-bottom: 12px;
        }

        .pos-card-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pos-text-muted);
            margin: 0 0 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--pos-border);
        }

        .pos-card-title .pos-hint {
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
            color: var(--pos-text-muted);
            font-size: 0.6875rem;
        }

        .header-section .pos-field-label {
            color: rgba(255, 255, 255, 0.95);
        }

        .pos-field-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--pos-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .pos-product-fields .pos-field-label {
            color: #000;
        }

        .pos-card-line-items {
            padding-top: 14px;
        }

        .ckeditor-full-height .cke_inner {
            height: 100% !important;
        }

        .ckeditor-full-height .cke_contents {
            height: calc(100% - 70px) !important;
        }

        .header-section input,
        .header-section select,
        .header-section label,
        .header-section .pos-field-label {
            color: var(--pos-text);
        }

        .header-section {
            background: linear-gradient(135deg, var(--pos-primary) 0%, var(--pos-primary-dark) 100%);
            border: none;
            border-radius: var(--pos-radius);
            padding: 14px 16px;
            margin-bottom: 0;
            box-shadow: var(--pos-shadow);
        }

        .header-section label,
        .header-section .pos-field-label {
            font-weight: 600;
            font-size: 0.75rem !important;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
            opacity: 0.9;
        }

        .header-section .form-control,
        .header-section .form-select {
            background: var(--pos-surface);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .table-header {
            background: linear-gradient(180deg, var(--pos-accent) 0%, var(--pos-accent-dark) 100%);
            color: #fff;
            font-weight: 600;
            text-align: center;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .table {
            background-color: var(--pos-surface);
            border: 1px solid var(--pos-border-strong);
        }

        .footer-section {
            background: var(--pos-surface);
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius);
            padding: 16px;
            margin-top: 12px;
            box-shadow: var(--pos-shadow);
        }

        .footer-section label,
        .footer-section .pos-field-label {
            color: var(--pos-text-muted);
            font-weight: 600;
            font-size: 0.6875rem !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .footer-section .form-control,
        .footer-section .form-select {
            background: #f8fbf9;
            border-color: var(--pos-border) !important;
        }

        .btn-custom {
            background: var(--pos-primary);
            color: #fff;
            border: none;
            border-radius: var(--pos-radius-sm);
            font-weight: 600;
            font-size: 0.875rem;
            padding: 10px 20px;
            transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
            box-shadow: var(--pos-shadow);
        }

        .btn-custom:hover,
        .btn-custom:focus {
            background: var(--pos-primary-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: var(--pos-shadow-md);
        }

        .btn-custom:active {
            transform: translateY(0);
        }

        .sidebar {
            background: var(--pos-surface);
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius);
            padding: 14px 16px;
            box-shadow: var(--pos-shadow);
            height: 100%;
        }

        .sidebar h5 {
            text-align: center;
            color: var(--pos-primary-dark) !important;
            font-size: 0.9375rem;
            font-weight: 700;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--pos-primary-light);
        }

        .sidebar button {
            width: 100%;
            margin-bottom: 8px;
        }

        .no-photo {
            width: 100%;
            height: 90px;
            border-radius: var(--pos-radius-sm);
            margin-bottom: 10px;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: var(--pos-shadow);
            background: var(--pos-primary-light);
        }

        .no-photo h5 {
            font-size: 1.2rem;
            text-align: center;
        }

        .table_scroll {
            height: 385px;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .table_scroll::-webkit-scrollbar {
            width: 6px;
        }

        .table_scroll::-webkit-scrollbar-thumb {
            background: var(--pos-border-strong);
            border-radius: 3px;
        }

        .table_scroll table {
            border-top: 0 !important;
        }

        .table_scroll table td {
            padding: 8px 6px !important;
            font-size: 0.8125rem;
            text-align: center;
            vertical-align: middle;
            border-color: var(--pos-border) !important;
        }

        .table_scroll table tbody tr:hover td {
            background-color: var(--pos-primary-light) !important;
        }

        .table_scroll table td:nth-child(2) {
            text-align: left !important;
            font-weight: 500;
        }

        .table_scroll table td.editable {
            cursor: pointer;
        }

        .table_scroll table td.editable:hover {
            background-color: #fff9e6 !important;
            outline: 1px dashed var(--pos-primary);
            outline-offset: -1px;
        }

        .table_scroll table .remove_item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            padding: 0;
            border-radius: var(--pos-radius-sm);
            font-size: 1rem;
            line-height: 1;
            font-weight: 700;
        }

        .form-control,
        .form-select {
            padding: 8px 12px !important;
            border-radius: var(--pos-radius-sm) !important;
            font-size: 0.875rem;
            border: 1px solid var(--pos-border-strong) !important;
            box-shadow: none !important;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--pos-primary) !important;
            box-shadow: var(--pos-focus) !important;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background: #f4f8f5;
            opacity: 1;
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid var(--pos-border-strong) !important;
            border-radius: var(--pos-radius-sm) !important;
            padding: 4px 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            font-size: 0.875rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--pos-primary) !important;
            box-shadow: var(--pos-focus);
        }

        .select2-dropdown {
            border: 1px solid var(--pos-border-strong);
            border-radius: var(--pos-radius-sm);
            box-shadow: var(--pos-shadow-md);
        }

        .pos-refresh-products-link {
            margin-left: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--pos-primary);
            text-decoration: none;
            cursor: pointer;
        }

        .pos-refresh-products-link:hover {
            color: var(--pos-primary-dark);
            text-decoration: underline;
        }

        .pos-refresh-products-link.is-loading {
            opacity: 0.6;
            pointer-events: none;
            cursor: wait;
            text-decoration: none;
        }

        .pos-page-loader {
            position: fixed;
            inset: 0;
            z-index: 10050;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(238, 244, 240, 0.88);
            backdrop-filter: blur(2px);
        }

        .pos-page-loader.is-active {
            display: flex;
        }

        .pos-page-loader__panel {
            text-align: center;
            padding: 1.5rem 2rem;
            background: var(--pos-surface);
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius);
            box-shadow: var(--pos-shadow-md);
            min-width: 220px;
        }

        .pos-page-loader__spinner {
            width: 42px;
            height: 42px;
            margin: 0 auto 0.75rem;
            border: 3px solid var(--pos-border);
            border-top-color: var(--pos-primary);
            border-radius: 50%;
            animation: pos-loader-spin 0.8s linear infinite;
        }

        .pos-page-loader__text {
            margin: 0;
            font-size: 0.9rem;
            color: var(--pos-text-muted);
            font-weight: 600;
        }

        @keyframes pos-loader-spin {
            to { transform: rotate(360deg); }
        }

        body.pos-loading .pos-shell {
            pointer-events: none;
        }

        .pos-qty-mode-toggle {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px 14px;
            margin-bottom: 6px;
        }

        .pos-qty-mode-toggle label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin: 0;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--pos-text, #1e2b22);
            cursor: pointer;
        }

        .pos-qty-mode-toggle input[type="radio"] {
            width: 15px;
            height: 15px;
            margin: 0;
            cursor: pointer;
            accent-color: var(--pos-primary);
        }

        .tableHead {
            margin-bottom: 0 !important;
        }

        .tableHead tr th {
            padding: 10px 6px !important;
            font-size: 0.75rem;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        .ward_request_table_class {
            background: var(--pos-surface);
            height: 250px;
            overflow: auto;
            border-radius: var(--pos-radius-sm);
        }

        .previous-bills {
            background: transparent;
            overflow: auto;
        }

        .previous-bills-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius-sm);
        }

        .previous-bills table tr th,
        .previous-bills table tr td {
            padding: 8px 10px;
            font-size: 0.8125rem;
            vertical-align: middle;
        }

        .previous-bills table thead th {
            background: var(--pos-primary-light);
            color: var(--pos-primary-dark);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 2px solid var(--pos-border) !important;
        }

        .previous-bills table tr td:first-child {
            width: 90px;
            font-weight: 600;
            color: var(--pos-primary-dark);
        }

        .previous-bills table tr td:last-child,
        .previous-bills table tr th:last-child {
            width: auto;
            min-width: 180px;
            white-space: nowrap;
        }

        .previous-bills table tr td a {
            font-size: 0.6875rem;
            padding: 4px 8px;
            margin: 2px;
            border-radius: 4px;
        }

        .horizontal-menu {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .horizontal-menu li {
            background: #fff3f3;
            color: var(--pos-accent);
            border: 1px solid #f5c6c6;
            padding: 6px 12px;
            border-radius: var(--pos-radius-sm);
            font-size: 0.75rem;
            font-weight: 500;
            max-width: 100%;
            word-break: break-word;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .horizontal-menu li .remove_low_qty_item {
            padding: 0 6px !important;
            font-size: 0.6875rem !important;
            border-radius: 4px;
        }

        .pos-line-items-scroll {
            width: 100%;
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            border: 1px solid var(--pos-border-strong);
            border-radius: var(--pos-radius-sm);
            background: var(--pos-surface);
        }

        .pos-line-items-inner {
            min-width: 1080px;
        }

        .pos-product-grid-table thead th.col-disc,
        .pos-product-grid-table tbody td.col-disc {
            width: 56px;
            max-width: 64px;
            padding-left: 2px !important;
            padding-right: 2px !important;
        }

        .pos-product-grid-table .pos-grid-cell-input[data-field="row_discount"] {
            min-width: 44px;
            max-width: 56px;
            padding-left: 4px !important;
            padding-right: 4px !important;
            font-size: 0.875rem;
        }

        .pos-product-grid-scroll {
            overflow-x: auto;
            overflow-y: scroll;
        }

        .pos-product-grid-table {
            width: 100%;
            table-layout: fixed;
            margin-bottom: 0;
        }

        .pos-product-grid-table thead th {
            position: sticky;
            top: 0;
            z-index: 3;
            padding: 10px 4px !important;
            font-size: 0.7rem;
            line-height: 1.2;
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        .pos-product-grid-table tbody td {
            padding: 8px 4px !important;
            font-size: 0.8125rem;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pos-product-grid-table tbody td.col-product {
            text-align: left !important;
            font-weight: 500;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
        }

        .pos-product-grid-table tbody td.pos-cell-editable {
            padding: 4px 3px !important;
            vertical-align: middle;
        }

        .pos-product-grid-table tbody td.editable {
            cursor: default;
        }

        .pos-product-grid-table tbody td.editable:hover {
            background-color: inherit !important;
            outline: none;
        }

        .pos-product-grid-table .pos-grid-cell-input,
        .pos-product-grid-table .editable-input {
            width: 100%;
            min-width: 56px;
            height: 38px;
            padding: 8px 6px !important;
            font-size: 0.9375rem;
            font-weight: 600;
            text-align: center;
            border: 1px solid var(--pos-border-strong) !important;
            border-radius: var(--pos-radius-sm);
            background: #fff;
            box-shadow: none !important;
            -moz-appearance: textfield;
        }

        .pos-product-grid-table .pos-grid-cell-input:focus,
        .pos-product-grid-table .editable-input:focus {
            border-color: var(--pos-primary) !important;
            box-shadow: var(--pos-focus) !important;
            outline: none;
        }

        .pos-product-grid-table .pos-grid-cell-input::-webkit-outer-spin-button,
        .pos-product-grid-table .pos-grid-cell-input::-webkit-inner-spin-button,
        .pos-product-grid-table .editable-input::-webkit-outer-spin-button,
        .pos-product-grid-table .editable-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Keep Actions column always visible */
        .pos-product-grid-table thead th.col-actions,
        .pos-product-grid-table tbody td.col-actions {
            position: sticky;
            right: 0;
            z-index: 2;
            min-width: 48px;
            width: 48px;
            background: var(--pos-surface);
            box-shadow: -3px 0 8px rgba(0, 0, 0, 0.06);
        }

        .pos-product-grid-table thead th.col-actions {
            background: var(--pos-accent-dark);
            color: #fff;
            z-index: 4;
        }

        .pos-product-grid-table tbody tr:nth-child(odd) td.col-actions {
            background: #f8faf9;
        }

        .pos-product-grid-table tbody tr:hover td.col-actions {
            background: var(--pos-primary-light) !important;
        }

        #previous-bill-table {
            min-width: 640px;
            margin-bottom: 0 !important;
        }

        #previous-bill-table td:last-child,
        #previous-bill-table th:last-child {
            min-width: 180px;
            white-space: nowrap;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.8125rem;
            padding: 8px 4px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--pos-border-strong);
            border-radius: var(--pos-radius-sm);
            padding: 4px 10px;
        }

        .dataTables_wrapper .paginate_button.current {
            background: var(--pos-primary) !important;
            border-color: var(--pos-primary) !important;
            color: #fff !important;
            border-radius: 4px;
        }

        .pos-amount-field .form-control {
            font-weight: 700;
            text-align: right;
        }

        .pos-amount-total .form-control {
            font-size: 1.375rem !important;
            color: var(--pos-text);
            background: #f0f7f2;
        }

        .pos-amount-net .form-control {
            font-size: 1.5rem !important;
            color: var(--pos-accent) !important;
            background: #fff5f5;
            border-color: #f5c6c6 !important;
        }

        .pos-amount-received .form-control {
            font-size: 1.375rem !important;
            color: var(--pos-primary-dark);
            background: var(--pos-primary-light);
            border-color: var(--pos-primary) !important;
        }

        .pos-amount-return .form-control {
            font-size: 1.375rem !important;
            color: #856404;
            background: #fffbea;
            border-color: #ffeaa7 !important;
        }

        .pos-amount-discount .form-control {
            color: var(--pos-primary-dark);
            font-weight: 600;
        }

        .pos-payment-summary-grid {
            align-items: stretch;
        }

        .pos-payment-calc-col,
        .pos-payment-inputs-col {
            display: flex;
            flex-direction: column;
        }

        .pos-payment-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #555;
            margin-bottom: 8px;
        }

        .pos-bill-calc-panel {
            flex: 1 1 auto;
            background: #f8faf9;
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius);
            padding: 12px 14px;
            min-height: 94%;
        }

        .pos-calc-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 5px 0;
            font-size: 0.875rem;
            color: #222;
            border-bottom: 1px solid #ececec;
        }

        .pos-calc-row:last-child {
            border-bottom: none;
        }

        .pos-calc-label {
            color: #555;
            font-weight: 600;
        }

        .pos-calc-value {
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        .pos-calc-row-total {
            margin-top: 6px;
            padding-top: 10px;
            border-top: 2px solid var(--pos-primary);
            font-size: 1rem;
        }

        .pos-calc-row-total .pos-calc-label,
        .pos-calc-row-total .pos-calc-value {
            color: var(--pos-primary-dark);
            font-weight: 800;
        }

        .pos-calc-subsection {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #ddd;
        }

        .pos-calc-subsection-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #666;
            margin-bottom: 6px;
        }

        .pos-payment-inputs-panel {
            flex: 1 1 auto;
            background: var(--pos-surface);
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius);
            padding: 12px 14px;
        }

        .pos-payment-inputs-panel .row > .pos-amount-field {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .pos-payment-hidden-fields {
            display: none !important;
        }

        .pos-tax-breakdown {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 6px;
            padding: 6px 8px;
            min-height: 2rem;
        }

        .pos-tax-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 0.8125rem;
            line-height: 1.4;
            padding: 2px 0;
            color: #222;
        }

        .pos-tax-line + .pos-tax-line {
            border-top: 1px dashed #e5e5e5;
            margin-top: 2px;
            padding-top: 4px;
        }

        .pos-tax-name {
            flex: 1 1 auto;
            font-weight: 600;
        }

        .pos-tax-pct {
            flex: 0 0 auto;
            color: #666;
            min-width: 2.5rem;
            text-align: right;
        }

        .pos-tax-amt {
            flex: 0 0 auto;
            font-weight: 700;
            min-width: 3.5rem;
            text-align: right;
        }

        .pos-tax-line-total {
            font-weight: 700;
            color: var(--pos-primary-dark);
        }

        .pos-action-buttons {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1050;
            background: var(--pos-surface);
            border-top: 1px solid var(--pos-border-strong);
            box-shadow: 0 -4px 18px rgba(30, 43, 34, 0.14);
            margin-top: 0;
            padding: 0;
        }

        .pos-action-buttons-inner {
            max-width: 1600px;
            margin: 0 auto;
            padding: 10px 12px;
        }

        #save_bill {
            min-width: 140px;
        }

        #saveBillConfirmModal {
            z-index: 10600;
        }

        #saveBillConfirmModal .modal-content {
            border-radius: var(--pos-radius);
            border: 1px solid var(--pos-border-strong);
            box-shadow: 0 12px 40px rgba(43, 30, 30, 0.18);
        }

        #saveBillConfirmModal .modal-header {
            background: var(--pos-primary-light);
            border-bottom: 1px solid var(--pos-border);
        }

        #saveBillConfirmModal .save-bill-confirm-amount {
            color: var(--pos-primary-dark);
            font-size: 1.35rem;
        }

        .modal-backdrop.show {
            z-index: 10590;
        }

        .go_to_home {
            background: #2d6a3e !important;
        }

        .go_to_home:hover {
            background: #245530 !important;
        }

        .previous-bills-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .pos-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin: 0 0 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--pos-border);
        }

        .pos-card-header .pos-card-title {
            margin: 0;
            padding-bottom: 0;
            border-bottom: none;
            flex: 1;
        }

        .pos-card-header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        #toggle-previous-bills,
        .pos-card-header-actions .go_to_home {
            padding: 3px 8px;
            font-size: 0.625rem;
            font-weight: 600;
            line-height: 1.3;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .pos-print-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            padding: 4px 8px 4px 10px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 999px;
            box-shadow: 0 2px 10px rgba(20, 40, 28, 0.14);
            backdrop-filter: blur(6px);
        }

        .pos-print-toggle__heading {
            font-size: 0.625rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: rgba(30, 43, 34, 0.72);
            margin: 0;
            white-space: nowrap;
            line-height: 1;
        }

        .pos-print-toggle__control {
            position: relative;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: stretch;
            min-width: 168px;
            padding: 3px;
            border-radius: 999px;
            background: rgba(30, 43, 34, 0.08);
            border: 1px solid rgba(30, 43, 34, 0.1);
            isolation: isolate;
        }

        .pos-print-toggle__control::before {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;
            width: calc(50% - 3px);
            height: calc(100% - 6px);
            border-radius: 999px;
            background: linear-gradient(145deg, #ffffff 0%, #f0faf2 55%, #e3f3e8 100%);
            box-shadow:
                0 1px 2px rgba(30, 43, 34, 0.08),
                0 4px 12px rgba(72, 175, 90, 0.22);
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 0;
            pointer-events: none;
        }

        .pos-print-toggle__control[data-active="a4"]::before,
        .pos-print-toggle__control.is-printer::before {
            transform: translateX(calc(100% + 3px));
        }

        .pos-print-toggle__option {
            position: relative;
            z-index: 1;
            margin: 0;
            cursor: pointer;
            user-select: none;
        }

        .pos-print-toggle__option input.retail-print-mode {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .pos-print-toggle__pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            width: 100%;
            min-height: 28px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.6875rem;
            font-weight: 600;
            line-height: 1.2;
            color: rgba(30, 43, 34, 0.55);
            transition: color 0.22s ease, transform 0.22s ease;
        }

        .pos-print-toggle__icon {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.75;
            stroke-linecap: round;
            stroke-linejoin: round;
            opacity: 0.75;
            transition: opacity 0.22s ease, stroke 0.22s ease;
        }

        .pos-print-toggle__option input.retail-print-mode:checked + .pos-print-toggle__pill {
            color: var(--pos-primary-dark);
            transform: scale(1.02);
        }

        .pos-print-toggle__option input.retail-print-mode:checked + .pos-print-toggle__pill .pos-print-toggle__icon {
            opacity: 1;
            stroke: var(--pos-primary);
        }

        .pos-print-toggle__option:hover .pos-print-toggle__pill {
            color: var(--pos-text);
        }

        .pos-print-toggle__option:focus-within .pos-print-toggle__pill {
            outline: 2px solid rgba(72, 175, 90, 0.45);
            outline-offset: 1px;
        }

        .pos-print-toggle__text {
            white-space: nowrap;
        }

        #pos-previous-bills-sidebar {
            display: none;
        }

        #pos-workspace-row.previous-bills-open #pos-previous-bills-sidebar {
            display: block;
        }

        .previous-bills-section-title {
            margin-bottom: 8px;
        }

        @media (max-width: 991.98px) {
            #pos-workspace-row.previous-bills-open .pos-sale-main {
                order: 2;
            }

            #pos-workspace-row.previous-bills-open #pos-previous-bills-sidebar {
                order: 1;
                margin-bottom: 12px;
            }

            .pos-card-header {
                flex-wrap: wrap;
                align-items: stretch;
                gap: 10px;
            }

            .pos-card-header .pos-card-title {
                flex: 1 1 100%;
                min-width: 0;
                text-align: center !important;
            }

            .pos-card-header-actions {
                flex: 1 1 100%;
                flex-shrink: 1;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                gap: 8px;
                max-width: 100%;
                min-width: 0;
            }

            .pos-print-toggle {
                flex-shrink: 1;
                min-width: 0;
                max-width: 100%;
            }

            .pos-print-toggle__control {
                min-width: 0;
                width: auto;
                max-width: 100%;
            }
        }

        label{
            color:red !important;
        }

        @media (max-width: 767.98px) {
            .pos-shell {
                padding: 8px 8px 120px;
            }

            .pos-action-buttons-inner {
                padding: 10px 8px;
            }

            .header-section {
                padding: 12px;
            }

            .header-section label,
            .header-section .pos-field-label {
                font-size: 0.6875rem !important;
            }

            .table_scroll {
                height: 280px;
            }

            .table_scroll table td,
            .tableHead tr th {
                font-size: 0.75rem;
                padding: 6px 4px !important;
            }

            .footer-section .row>div {
                margin-bottom: 8px;
            }

            .footer-section input,
            .footer-section select {
                font-size: 16px !important;
            }

            .pos-action-buttons .btn {
                width: 100%;
                margin-bottom: 0 !important;
                margin-right: 0 !important;
            }

            .pos-topbar-title {
                font-size: 1rem;
            }

            .pos-print-toggle {
                padding: 3px 6px;
                gap: 4px;
                max-width: calc(100% - 4px);
            }

            .pos-print-toggle__heading {
                display: none;
            }

            .pos-print-toggle__control {
                min-width: 7.5rem;
                max-width: min(100%, 10.5rem);
            }

            .pos-print-toggle__pill {
                padding: 4px 6px;
                gap: 0;
                min-height: 26px;
            }

            .pos-print-toggle__text {
                display: none;
            }

            .pos-print-toggle__icon {
                width: 16px;
                height: 16px;
                opacity: 1;
            }

            .pos-print-toggle__option input.retail-print-mode:checked + .pos-print-toggle__pill {
                transform: none;
            }

            #toggle-previous-bills,
            .pos-card-header-actions .go_to_home {
                font-size: 0.5625rem;
                padding: 4px 6px;
            }
        }

        @media (max-width: 399.98px) {
            .pos-card-header-actions {
                gap: 6px;
            }

            .pos-print-toggle__control {
                min-width: 6.75rem;
                max-width: 9rem;
            }

            #toggle-previous-bills {
                max-width: 4.5rem;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
    </style>
</head>

<body>
    <div id="popu-message">Error Occur</div>
    <div id="pos-page-loader" class="pos-page-loader" aria-hidden="true" role="status">
        <div class="pos-page-loader__panel">
            <div class="pos-page-loader__spinner" aria-hidden="true"></div>
            <p class="pos-page-loader__text">Loading...</p>
        </div>
    </div>
    <div class="container-fluid pos-shell">

        <!-- <div class="pos-topbar">
            <span class="pos-topbar-title">Retail Pharmacy POS</span>
            <span class="pos-topbar-store">{{ session('store_name') }}</span>
        </div> -->

        <!-- Bill Information -->
        <div class="pos-card" style="background: #69bf69 !important;">
            <div class="pos-card-header">
                <p class="pos-card-title" style="text-align: center;">Walk-In Customer Bill</p>
                <div class="pos-card-header-actions">
                    @php
                        $initialRetailPrintMode = ($retail_print_mode ?? 'a4') === 'thermal' ? 'thermal' : 'a4';
                    @endphp
                    <div class="pos-print-toggle" title="Default print format for new bills and Recent Bills">
                        <span class="pos-print-toggle__heading">Print</span>
                        <div class="pos-print-toggle__control {{ $initialRetailPrintMode === 'thermal' ? 'is-thermal' : 'is-printer' }}"
                             data-active="{{ $initialRetailPrintMode }}"
                             role="group"
                             aria-label="Print format">
                            <label class="pos-print-toggle__option" for="retail_print_thermal" title="Thermal receipt">
                                <input class="retail-print-mode" type="radio" name="retail_print_mode" id="retail_print_thermal" value="thermal"
                                    @if($initialRetailPrintMode === 'thermal') checked @endif>
                                <span class="pos-print-toggle__pill">
                                    <svg class="pos-print-toggle__icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M6 3h12v4H6z"></path>
                                        <path d="M6 9v12h12V9"></path>
                                        <path d="M9 13h6"></path>
                                        <path d="M9 16h4"></path>
                                    </svg>
                                    <span class="pos-print-toggle__text">Thermal</span>
                                </span>
                            </label>
                            <label class="pos-print-toggle__option" for="retail_print_a4" title="A4 printer">
                                <input class="retail-print-mode" type="radio" name="retail_print_mode" id="retail_print_a4" value="a4"
                                    @if($initialRetailPrintMode !== 'thermal') checked @endif>
                                <span class="pos-print-toggle__pill">
                                    <svg class="pos-print-toggle__icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M7 4h10v14H7z"></path>
                                        <path d="M9 8h6"></path>
                                        <path d="M9 11h6"></path>
                                        <path d="M9 14h4"></path>
                                        <path d="M6 20h12"></path>
                                    </svg>
                                    <span class="pos-print-toggle__text">Printer</span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-custom btn-sm" id="toggle-previous-bills" aria-expanded="false" aria-controls="pos-previous-bills-sidebar">Recent Bills</button>
                    <a class="btn btn-custom btn-sm go_to_home" href="javascript:void(0)">Home</a>
                </div>
            </div>
            <div class="row header-section g-2 mx-0" style="display: none;">

            <div class="col-md-2 d-none">
                <label for="invoice_number">Barcode</label>
                <input type="text" id="barcode" class="form-control" placeholder="Barcode">
            </div>
            <div class="col-6 col-md-1">
                <label class="pos-field-label" for="invoice_number">Invoice #</label>
                <input type="text" id="invoice_number" style="pointer-events: none;" required="required" value="{{$invoiceNo ?? ''}}" class="form-control" readonly>
            </div>

            <div class="col-md-2 d-none">
                <label for="date">Medicine Type</label>
                <select class="form-select" id="medicine_type">
                    <option value="">Select Medicine Type...</option>
                    <option value="Ward" {{($type == "Ward") ? "selected" : ""}}>Ward Medicine</option>
                    <option value="OT" {{($type == "OT") ? "selected" : ""}}>OT Medicine</option>
                    <option value="Home" selected="selected">Home Medicine</option>
                </select>
            </div>

            <div class="col-12 col-md-3" style="display: none;">
                <label class="pos-field-label" for="appointment_id">Appointment</label>
                <select id="appointment_id" name="appointment_id" class="form-control">
                    <option value="">Please Select Appointment...</option>
                    {{-- <option data-admission_id="0" value="2" selected="selected">Walking Customer </option>--}}
                    <?php foreach ($appointments as $key => $value) { ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->patient->name . " | Appointment# " . $value->appointment_number . " | MR#: " . $value->patient->mr_no; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-12 col-md-3" style="display: none;">
                <label class="pos-field-label" for="SID">Patient</label>
                <select id="SID" name="SID" class="form-control">
                    <option value="">Please Select Patient...</option>
                    {{-- <option data-admission_id="0" value="2" selected="selected">Walking Customer </option>--}}
                    <?php foreach ($admitted_patients as $key => $value) { ?>
                        <option data-admission_id="0" value="<?php echo $value->id; ?>" {{($value->patient_type == "walking_customer") ? "selected" : "selected"}}><?php echo $value->name; ?></option>
                    <?php } ?>
                </select>
            </div>



            <div class="col-6 col-md-2" style="display: none;">
                <label class="pos-field-label" for="bill_date">Date</label>
                <input type="date" id="bill_date" class="form-control" value="<?php echo date("Y-m-d") ?>">
            </div>


            <div class="col-12 col-md-2">
                <label class="pos-field-label" for="previous_balance">Description</label>
                <input type="text" id="previous_balance" class="form-control" value="">
            </div>

            </div>
        </div>

        <!-- Main workspace -->
        <div class="row g-2 pos-workspace-row" id="pos-workspace-row">
            <div class="col-12 pos-sale-main" id="pos-sale-main">

                <div class="row g-2 mb-2 pos-product-fields pos-card">
                    <!--<div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Item/Bar Code">
                </div>-->
                    @if($type !='' || $type == '')
                    <div class="col-12 col-md-6">
                        <label class="pos-field-label d-block mb-1" for="product_id" style="color: black !important;">
                            Product
                            <a href="javascript:void(0)" id="refresh_products" class="pos-refresh-products-link" title="Reload products with latest stock">Refresh</a>
                        </label>
                        <select class="form-control" id="product_id">
                            <option value="">Select Product...</option>
                        </select>
                        {{--<input type="text" name="product_name" id="product_name" class="form-control" placeholder="Product Name">--}}
                    </div>


                    @if($type == "Home" || $type == "Ward")
                    <div class="col-12 col-md-2 mt-1 d-none">
                        <p style="font-size: 12px;line-height: 0px;font-weight: bold;">Dose Type</p>
                        <select class="form-select" id="dose_type">
                            
                            <option value="-" selected>-</option>
                            <option value="TDS">TDS (صبح ,دوپہر ,شام )</option>
                            <option value="BD">BD (صبح ,شام )</option>
                            <option value="OD">OD (صبح )</option>

                            <option value="HS">HS (رات کو)</option>
                            <option value="QID">QID (ہر 6 گھنٹے بعد)</option>
                        </select>
                    </div>
                    @endif




                     <div class="col-6 col-md-3">
                        <div class="pos-qty-mode-toggle">
                            <label for="qty_mode_pack">
                                <input type="radio" name="qty_entry_mode" id="qty_mode_pack" value="pack" checked>
                                Pack Qty
                            </label>
                            <label for="qty_mode_unit">
                                <input type="radio" name="qty_entry_mode" id="qty_mode_unit" value="unit">
                                Unit Qty
                            </label>
                        </div>
                        <div id="pos-pack-qty-field-wrap">
                            <!-- <label class="pos-field-label d-block mb-1" for="pack_quantity" style="color: black !important;">Pack Qty</label> -->
                            <input type="number" class="form-control" id="pack_quantity" placeholder="1" value="1" min="0" step="1">
                        </div>
                        <div id="pos-unit-qty-field-wrap" class="d-none">
                            <!-- <label class="pos-field-label d-block mb-1" for="sale_quantity" style="color: black !important;">Unit Qty</label> -->
                            <input type="number" class="form-control" id="sale_quantity" placeholder="1" value="1" min="0" step="1">
                        </div>
                    </div>

                     <div class="col-12 col-md-2">
                        <label class="pos-field-label" for="SalePrice" style="color: black !important;">Unit Price</label>
                        <input type="number" id="SalePrice" class="form-control" placeholder="0.00" min="0" step="0.01">
                    </div>








                    <div class="col-12 col-md-2 mt-1 d-none">
                        <p style="font-size: 12px;line-height: 0px;font-weight: bold;">Avaliable Qty</p>
                        <input type="text" disabled class="form-control" id="avaliable_qty" placeholder="Avaliable Quantity" readonly>
                    </div>
                    @endif
                </div>

                <ul class="horizontal-menu"></ul>

                <div class="pos-card mb-0 pos-card-line-items">
                    <div class="pos-line-items-scroll">
                    <div class="pos-line-items-inner">
                        <div class="table_scroll pos-product-grid-scroll">
                            <table class="table table-bordered table-striped mb-0 pos-product-grid-table">
                                <colgroup>
                                    <col style="width: 3%">
                                    <col style="width: 20%">
                                    <col style="width: 7%">
                                    <col style="width: 7%">
                                    <col style="width: 8%">
                                    <col class="col-disc" style="width: 6%">
                                    <col style="width: 8%">
                                    <col style="width: 7%">
                                    <col style="width: 7%">
                                    <col style="width: 8%">
                                    <col style="width: 5%">
                                </colgroup>
                                <thead class="table-header">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Pack Qty</th>
                                        <th>Unit Qty</th>
                                        <th>Rate</th>
                                        <th class="col-disc">Disc %</th>
                                        <th>Amount</th>
                                        <th>Sale Tax %</th>
                                        <th>Income Tax %</th>
                                        <th>Avail. Qty</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="product_table">









                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar Section -->
            <div class="col-12 col-lg-4 pos-previous-bills-sidebar" id="pos-previous-bills-sidebar">
                <div class="sidebar mt-0 mt-lg-0 h-100">
                <!--<div class="no-photo">
                <h5><?php /*echo Company_Name; */ ?></h5>
            </div>-->
                {{--<table class="table table-bordered">
                <tr>
                    <td width="50%">Previous Balance</td>
                    <td><span id="prv_balance" style="font-weight: bold; font-size: 14px"></span></td>
                </tr>
                <tr>
                    <td>Current Bill</td>
                    <td><span id="current_bill" style="font-weight: bold; font-size: 14px"></span></td>
                </tr>

                <tr>
                    <td>Total</td>
                    <td><span id="total_bill" style="font-weight: bold; font-size: 14px"></span></td>
                </tr>
            </table>--}}




                <h5 class="previous-bills-section-title">Recent Bills</h5>
                <div class="previous-bills previous-bills-wrap" id="previous-bills-panel">

                    <table class="table table-bordered mb-0" style="width: 100%" id="previous-bill-table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Patient</th>
                                <th>Amount</th>
                                <th>Net Amount</th>
                                <th>Created By</th>
                                <th style="width: 30%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>


                    </table>

                </div>

                </div>
            </div>
        </div>

        <div class="footer-section">
            <p class="pos-card-title mb-3">Payment Summary</p>
            <div class="row g-3 pos-payment-summary-grid">
                <div class="col-lg-6 pos-payment-calc-col">
                    <div class="pos-payment-section-title">Bill Calculation</div>
                    <div class="pos-bill-calc-panel" id="bill_calculation_details" aria-live="polite">
                        <div class="pos-calc-row">
                            <span class="pos-calc-label">Total Bill</span>
                            <span class="pos-calc-value" id="display_bill_amount">0</span>
                        </div>
                        <div class="pos-calc-row">
                            <span class="pos-calc-label">Bill Discount</span>
                            <span class="pos-calc-value" id="display_item_discount">0</span>
                        </div>
                        <div class="pos-calc-row">
                            <span class="pos-calc-label">Invoice Discount</span>
                            <span class="pos-calc-value" id="display_invoice_discount">0</span>
                        </div>
                        <div class="pos-calc-row">
                            <span class="pos-calc-label">Amount After Discount</span>
                            <span class="pos-calc-value" id="display_after_discount">0</span>
                        </div>
                        <div class="pos-calc-subsection">
                            <div class="pos-calc-subsection-title">Tax Details</div>
                            <div id="bill_tax_breakdown" class="pos-tax-breakdown">
                                <div class="pos-tax-line text-muted">No tax applied</div>
                            </div>
                        </div>
                        <div class="pos-calc-row">
                            <span class="pos-calc-label">Total Tax</span>
                            <span class="pos-calc-value" id="display_total_tax">0</span>
                        </div>
                        <div class="pos-calc-row pos-calc-row-total">
                            <span class="pos-calc-label">Net Amount (incl. tax)</span>
                            <span class="pos-calc-value" id="display_net_amount">0</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 pos-payment-inputs-col">
                    <div class="pos-payment-section-title">Payment Inputs</div>
                    <div class="pos-payment-inputs-panel">
                        <div class="row g-2">
                            <div class="pos-payment-hidden-fields">
                                <input type="number" readonly id="BillAmount" value="0">
                                <input type="number" readonly id="discount_amount" value="0">
                                <input type="number" readonly id="amount_after_discount" value="0">
                                <input type="hidden" id="bill_tax_amount" value="0">
                                <input type="number" id="ReceivedAmount" disabled value="0">
                                <input type="text" name="BillDiscription" id="BillDiscription" value="-">
                            </div>

                            <div class="col-6 pos-amount-field">
                                <label class="pos-field-label" for="discount_id">Discount %</label>
                                <select class="form-control" id="discount_id">
                                    <option value="0">Select Discount...</option>
                                    <option value="2">2%</option>
                                    <option value="3">3%</option>
                                    <option value="4">4%</option>
                                    <option value="5">5%</option>
                                    <option value="6">6%</option>
                                    <option value="7">7%</option>
                                    <option value="8">8%</option>
                                    <option value="9">9%</option>
                                    <option value="10">10%</option>
                                </select>
                            </div>

                            <div class="col-6 pos-amount-field">
                                <label class="pos-field-label" for="invoice_discount">Invoice Discount</label>
                                <input type="number" id="invoice_discount" value="0" class="form-control" min="0">
                            </div>

                            <div class="col-6 pos-amount-field pos-amount-received">
                                <label class="pos-field-label" for="ReceivedAmountFromCustomer">Received</label>
                                <input type="number" name="ReceivedAmountFromCustomer" id="ReceivedAmountFromCustomer" value="0" class="form-control" min="0">
                            </div>

                            <div class="col-6 pos-amount-field pos-amount-return">
                                <label class="pos-field-label" for="ReturnToCustomer">Return</label>
                                <input type="number" name="ReturnToCustomer" id="ReturnToCustomer" value="0" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="pos-action-buttons">
        <div class="pos-action-buttons-inner">
            <div class="d-flex flex-column flex-md-row flex-wrap align-items-stretch align-items-md-center gap-2">
                <button class="btn btn-custom btn-lg" id="save_bill" type="button">Save Bill</button>
                <a class="btn btn-custom" href="{{route('pos.retail_pharmacy_sale')}}" target="_blank">New Bill</a>
            </div>
        </div>

        <!-- Action Buttons Section -->

    </div>

    <div class="modal fade" id="saveBillConfirmModal" tabindex="-1" aria-labelledby="saveBillConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveBillConfirmModalLabel">Confirm Save Bill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-2">Are you sure you want to save this bill?</p>
                    <p class="mb-0 save-bill-confirm-amount fw-bold">Total: <span id="save-bill-confirm-amount">0.00</span></p>
                    <p class="text-muted small mt-3 mb-0">Click <strong>Yes</strong> to save the bill, or <strong>No</strong> to continue adding items or make changes.</p>
                </div>
                <div class="modal-footer justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" id="save-bill-confirm-no">No</button>
                    <button type="button" class="btn btn-custom px-4" id="save-bill-confirm-yes">Yes, Save Bill</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>

    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>

    <script>
        $("body").on("click", ".view_ward_request", function() {
            var id = $(this).attr('data_ward_request_id');
            var url = "{{route('pos.add_new_sale')}}?type=Ward&ward_request=" + id;
            window.location = url;
        });

        $("body").on("keyup", "#ReceivedAmountFromCustomer", function() {

            calculateReturnAmount();
        });

        $("body").on("keyup", "#invoice_discount", function() {


            reload_table();
        });

        function calculateReturnAmount() {
            var bill_amount = $("#ReceivedAmount").val();
            var invoice_discount = $("#invoice_discount").val();
            if (invoice_discount == '') {
                invoice_discount = 0;
            }
            var ReceivedAmountFromCustomer = $("#ReceivedAmountFromCustomer").val();
            if (bill_amount == '') {
                bill_amount = 0;
            }
            if (ReceivedAmountFromCustomer == '') {
                ReceivedAmountFromCustomer = 0;
            }

            var return_amount = (ReceivedAmountFromCustomer) - (bill_amount);
            $("#ReturnToCustomer").val(return_amount);
        }

        var previous_bill_table = null;

        function adjustPreviousBillsTable() {
            if ($.fn.DataTable.isDataTable('#previous-bill-table')) {
                $('#previous-bill-table').DataTable().columns.adjust();
            }
        }

        $('#toggle-previous-bills').on('click', function () {
            var $row = $('#pos-workspace-row');
            var $main = $('#pos-sale-main');
            var isOpen = $row.toggleClass('previous-bills-open').hasClass('previous-bills-open');

            if (isOpen) {
                $main.removeClass('col-12').addClass('col-lg-8');
            } else {
                $main.removeClass('col-lg-8').addClass('col-12');
            }

            $(this).text(isOpen ? 'Hide Bills' : 'Recent Bills');
            $(this).attr('aria-expanded', isOpen ? 'true' : 'false');

            if (isOpen) {
                setTimeout(adjustPreviousBillsTable, 100);
            }
        });
    </script>
    <script type="text/javascript">
        var preValue = '';
        var selectedRow = "";
        var ProductList = [];
        var RETAIL_PRINT_MODE_KEY = 'retail_print_mode';
        var RETAIL_PRINT_URL_THERMAL = "{{ route('pos.print_retail_thermal_bill', ['id' => 0]) }}".replace(/\/0$/, '');
        var RETAIL_PRINT_URL_A4 = "{{ route('pos.print_customer_bill', ['id' => 0]) }}".replace(/\/0$/, '');
        var serverRetailPrintMode = @json($retail_print_mode ?? 'a4');
        var retailPrintWindow = null;
        var posGridInputPrevious = {};
        var saveBillConfirmModalInstance = null;

        function formatBillCurrency(amount) {
            return (parseFloat(amount) || 0).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function getSaveBillConfirmModal() {
            var modalEl = document.getElementById('saveBillConfirmModal');
            if (!modalEl) {
                return null;
            }
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                if (!saveBillConfirmModalInstance) {
                    saveBillConfirmModalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                }
                return saveBillConfirmModalInstance;
            }
            return null;
        }

        function showSaveBillConfirmModal(netAmount, onConfirm) {
            $('#save-bill-confirm-amount').text(formatBillCurrency(netAmount));
            var modal = getSaveBillConfirmModal();
            if (!modal) {
                if (window.confirm('Are you sure you want to save this bill? Total: ' + formatBillCurrency(netAmount))) {
                    onConfirm();
                }
                return;
            }
            $('#save-bill-confirm-yes').off('click.saveBillConfirm').on('click.saveBillConfirm', function() {
                modal.hide();
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            });
            modal.show();
        }

        function buildPosGridCellInput(field, value, productId) {
            var isTax = field === 'sale_tax' || field === 'income_tax';
            var isRate = field === 'rate';
            var isRowDiscount = field === 'row_discount';
            var isDecimal = isTax || isRate || isRowDiscount;
            var displayValue = (value === undefined || value === null || value === '') ? (isDecimal ? 0 : (field === 'pack_qty' ? 1 : 0)) : value;
            var maxAttr = isRowDiscount ? ' max="100"' : '';
            return '<input type="number" class="form-control pos-grid-cell-input" ' +
                'data-field="' + field + '" data-product-id="' + productId + '" ' +
                'min="0" step="' + (isDecimal ? '0.01' : '1') + '" inputmode="' + (isDecimal ? 'decimal' : 'numeric') + '" ' +
                maxAttr +
                ' value="' + displayValue + '" autocomplete="off">';
        }

        function findProductInList(productId) {
            var id = String(productId);
            return ProductList.find(function(p) {
                return String(p.ProductID) === id;
            });
        }

        function getPosGridFieldName($input) {
            return $input.attr('data-field') || $input.data('field') || '';
        }

        function normalizeRowDiscountPercentage(requestedPct) {
            var pct = parseFloat(requestedPct) || 0;
            if (pct < 0) {
                return 0;
            }
            if (pct > 100) {
                return 100;
            }
            return pct;
        }

        /** Reset bill Discount % when line discounts are not uniform from the dropdown. */
        function syncBillDiscountDropdownFromRows() {
            if (!ProductList.length) {
                return;
            }
            var billPct = parseFloat($('#discount_id').val()) || 0;
            var linePcts = ProductList.map(function(item) {
                return parseFloat(item.discount_percentage) || 0;
            });
            var hasAnyDiscount = linePcts.some(function(p) { return p > 0; });
            if (!hasAnyDiscount) {
                return;
            }
            var first = linePcts[0];
            var allLinesUniform = linePcts.every(function(p) { return p === first; });
            var matchesBillDropdown = billPct > 0 && allLinesUniform && first === billPct;

            if (!matchesBillDropdown) {
                $('#discount_id').val('0');
            }
        }

        function updateProductDiscountByID(product_id, discountPct, skipReload) {
            var product = findProductInList(product_id);
            if (!product) {
                return;
            }
            product.discount_percentage = normalizeRowDiscountPercentage(discountPct);
            recalculateRetailLine(product);
            if (skipReload) {
                refreshProductRowInGrid(product_id);
                syncBillTotalsFromProductList();
            } else {
                reload_table();
            }
        }

        function getPosGridInputKey($input) {
            return String($input.attr('data-product-id') || $input.data('productId') || '') + '-' + getPosGridFieldName($input);
        }

        function toIntQty(val) {
            var n = parseInt(String(val).trim(), 10);
            if (isNaN(n) || n < 0) {
                return 0;
            }
            return n;
        }

        function packQtyDisplayFromUnitQty(unitQty, packSize) {
            unitQty = toIntQty(unitQty);
            packSize = parseInt(packSize, 10) || 0;
            if (packSize <= 0) {
                return 0;
            }
            return Math.floor(unitQty / packSize);
        }

        function unitQtyFromPackQty(packQty, packSize) {
            packQty = toIntQty(packQty);
            packSize = parseInt(packSize, 10) || 0;
            if (packSize <= 0) {
                return packQty;
            }
            return packQty * packSize;
        }

        function recalculateRetailLine(product) {
            var actualQuantity = toIntQty(parseFloat(product.Quantity) - parseFloat(product.ReturnQuantity || 0));
            var itemTotal = actualQuantity * parseFloat(product.UnitePrice);
            product.Total = itemTotal;

            if (actualQuantity <= 0) {
                product.discount_percentage_amount = 0;
            } else if (parseFloat(product.discount_percentage) > 0) {
                product.discount_percentage_amount = (itemTotal * parseFloat(product.discount_percentage)) / 100;
            } else {
                product.discount_percentage_amount = 0;
            }

            calculateRowTaxAmounts(product);

            return {
                actualQuantity: actualQuantity,
                finalItemAmount: itemTotal - (parseFloat(product.discount_percentage_amount) || 0)
            };
        }

        function applyPosGridCellChange($input) {
            var $tr = $input.closest('tr');
            var product_id = $tr.find('td:eq(0)').attr('data-id');
            if (!product_id) {
                return;
            }

            var editedField = getPosGridFieldName($input);
            var newValue = $.trim($input.val());
            var inputKey = getPosGridInputKey($input);
            var previousValue = posGridInputPrevious[inputKey];
            var product = findProductInList(product_id);

            if (newValue === '' || newValue === null) {
                $input.val(previousValue !== undefined ? previousValue : '');
                return;
            }

            if (editedField === 'sale_tax' || editedField === 'income_tax') {
                updateProductTaxByID(product_id, editedField, newValue, true);
                posGridInputPrevious[inputKey] = $input.val();
                return;
            }

            if (editedField === 'row_discount') {
                var rowDisc = parseFloat(newValue) || 0;
                if (rowDisc < 0) {
                    rowDisc = 0;
                }
                if (rowDisc > 100) {
                    rowDisc = 100;
                }
                updateProductDiscountByID(product_id, rowDisc, true);
                product = findProductInList(product_id);
                if (product) {
                    $input.val(product.discount_percentage);
                }
                posGridInputPrevious[inputKey] = $input.val();
                return;
            }

            if (editedField !== 'pack_qty' && editedField !== 'quantity' && editedField !== 'rate') {
                return;
            }

            var pack_qty = toIntQty($tr.find('[data-field="pack_qty"]').val());
            var quantity = toIntQty($tr.find('[data-field="quantity"]').val());
            var rate = parseFloat($tr.find('[data-field="rate"]').val()) || 0;
            var avaliable_qty = parseFloat($tr.find('td:eq(9)').text()) || 0;
            var packSize = product ? (parseInt(product.pack_size, 10) || 0) : 0;

            if (editedField === 'pack_qty') {
                pack_qty = toIntQty($tr.find('[data-field="pack_qty"]').val());
                quantity = unitQtyFromPackQty(pack_qty, packSize);
                $tr.find('[data-field="pack_qty"]').val(pack_qty);
                $tr.find('[data-field="quantity"]').val(quantity);
            } else if (editedField === 'quantity') {
                quantity = toIntQty(newValue);
                pack_qty = packQtyDisplayFromUnitQty(quantity, packSize);
                $tr.find('[data-field="quantity"]').val(quantity);
                $tr.find('[data-field="pack_qty"]').val(pack_qty);
            } else if (editedField === 'rate') {
                rate = parseFloat(newValue) || 0;
                $tr.find('[data-field="rate"]').val(rate.toFixed(2));
            }

            if (quantity > avaliable_qty) {
                if (product) {
                    var lineReset = recalculateRetailLine(product);
                    var packDisplay = packQtyDisplayFromUnitQty(lineReset.actualQuantity, packSize);
                    $tr.find('[data-field="pack_qty"]').val(packDisplay);
                    $tr.find('[data-field="quantity"]').val(lineReset.actualQuantity);
                } else {
                    $input.val(previousValue !== undefined ? previousValue : '');
                }
                popupMsg("Exceeding Available Quantity. You can't change the value.", "error");
                return;
            }

            if (!quantity || !rate) {
                return;
            }

            if (product) {
                product.Quantity = quantity;
                product.UnitePrice = rate;
                product.pack_qty = pack_qty;
                var line = recalculateRetailLine(product);
                $tr.find('td:eq(6)').text(line.finalItemAmount.toFixed(2));
                updateProductByID(product_id, quantity, rate, product.Total, pack_qty, true);
            }

            posGridInputPrevious[inputKey] = $input.val();
        }

        function getRetailPrintMode() {
            var mode = localStorage.getItem(RETAIL_PRINT_MODE_KEY);
            if (mode === 'thermal' || mode === 'a4') {
                return mode;
            }

            if (serverRetailPrintMode === 'thermal' || serverRetailPrintMode === 'a4') {
                return serverRetailPrintMode;
            }

            return 'a4';
        }

        function setRetailPrintModeUi(mode) {
            $('.retail-print-mode[value="' + mode + '"]').prop('checked', true);
            $('.pos-print-toggle__control')
                .attr('data-active', mode)
                .toggleClass('is-thermal', mode === 'thermal')
                .toggleClass('is-printer', mode === 'a4');
        }

        function syncRetailPrintModeToSession(mode) {
            $.ajax({
                type: 'POST',
                url: "{{ route('pos.set_retail_print_mode') }}",
                data: {
                    mode: mode,
                    _token: '{{ csrf_token() }}'
                }
            });
        }

        function applyRetailPrintMode(mode) {
            if (mode !== 'thermal' && mode !== 'a4') {
                mode = 'a4';
            }
            localStorage.setItem(RETAIL_PRINT_MODE_KEY, mode);
            setRetailPrintModeUi(mode);
            syncRetailPrintModeToSession(mode);
        }

        function getRetailBillPrintUrl(saleId) {
            saleId = parseInt(saleId, 10) || 0;
            if (getRetailPrintMode() === 'thermal') {
                return RETAIL_PRINT_URL_THERMAL + '/' + saleId;
            }
            return RETAIL_PRINT_URL_A4 + '/' + saleId;
        }

        function openRetailBillPrint(saleId) {
            var url = getRetailBillPrintUrl(saleId);
            if (retailPrintWindow && !retailPrintWindow.closed) {
                retailPrintWindow.location.href = url;
                retailPrintWindow.focus();
                return;
            }
            window.open(url, '_blank');
        }

        applyRetailPrintMode(getRetailPrintMode());

        function initPreviousBillsTable() {
            if ($.fn.DataTable.isDataTable('#previous-bill-table')) {
                return;
            }

            previous_bill_table = $('#previous-bill-table').DataTable({
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 8,
                ajax: {
                    url: `{{ route('pos.retail_previous_bills') }}`,
                    data: function (d) {
                        d.retail_print_mode = getRetailPrintMode();
                    }
                },
                columns: [
                    {
                        data: 'InvoiceNo',
                        name: 'InvoiceNo',
                        searchable: true
                    },
                    {
                        data: 'patient.name',
                        name: 'patient.name',
                        searchable: true
                    },
                    {
                        data: 'received_amount',
                        name: 'received_amount',
                        searchable: true
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount',
                        searchable: true,
                        orderable: true,
                        className: 'text-end'
                    },
                    {
                        data: 'created_by.name',
                        name: 'created_by.name',
                        searchable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false,
                        className: 'text-nowrap'
                    }
                ],
                responsive: false,
                searching: true,
                sorting: true,
                paging: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        }

        initPreviousBillsTable();

        $(document).on('change', '.retail-print-mode', function () {
            applyRetailPrintMode($(this).val());
            if (previous_bill_table) {
                previous_bill_table.ajax.reload(null, false);
            }
        });
        var PreviousBalance = 0;
        var taxPercentage = 0;
        var defaultRowTaxes = @json($default_row_taxes ?? ['sale_tax' => 0, 'income_tax' => 0]);
        var currentAvailableQuantity = 0;
        var item_packSize = 0;
        var patient_admission_id = 0;
        var currentAllowPercentage = 100;
        var currentProductAllowPercentages = {};
        ward_request_id = 0;

        @if(count($list_products) > 0)
        ProductList = @json($list_products);
        @endif
        reload_table();

        setTimeout(function() {
            @if($patient_id != '')
            ward_request_id = "{{$ward_request}}";
            $("#SID").val("{{$patient_id}}").trigger("change");
            @endif

        }, 500);

        $(document).on('click', '.go_to_home', function(e) {
            e.preventDefault();

            window.open("{{ route('settings.home') }}", '_blank', 'noopener,noreferrer');
        });



        function getSelectedProductPackSize() {
            return parseInt($('#product_id option:selected').attr('data-packsize'), 10) || parseInt(item_packSize, 10) || 0;
        }

        function normalizeProductSelectItem(item) {
            item = item || {};
            var packsize = item.packsize != null && item.packsize !== '' ? item.packsize : item.packSize;
            var purchasePrice = item.purchasePrice != null && item.purchasePrice !== ''
                ? item.purchasePrice
                : (item.unit_sale_price != null ? item.unit_sale_price : item.purchase_price);

            return {
                id: item.id != null ? String(item.id) : '',
                text: item.text || '',
                packsize: packsize,
                purchasePrice: purchasePrice,
                taxPercentage: item.taxPercentage != null ? item.taxPercentage : 0,
                allowPercentage: item.allowPercentage != null ? item.allowPercentage : 0
            };
        }

        function getSelect2SelectedProductData() {
            if (!$('#product_id').data('select2')) {
                return null;
            }
            var data = $('#product_id').select2('data');
            if (Array.isArray(data)) {
                data = data.length ? data[0] : null;
            }
            return data ? normalizeProductSelectItem(data) : null;
        }

        function resolveProductItemData(item) {
            item = normalizeProductSelectItem(item || getSelect2SelectedProductData() || {});
            var id = item.id || $('#product_id').val();
            if (!id) {
                return item;
            }

            var cached = retailPreloadedProducts.find(function(p) {
                return String(p.id) === String(id);
            });
            if (cached) {
                item = normalizeProductSelectItem($.extend({}, cached, item));
            }

            if (item.purchasePrice === undefined || item.purchasePrice === null || item.purchasePrice === '') {
                var $opt = $('#product_id option[value="' + id + '"]');
                if ($opt.length) {
                    item = normalizeProductSelectItem({
                        id: id,
                        text: item.text || $opt.text(),
                        packsize: $opt.attr('data-packsize'),
                        purchasePrice: $opt.attr('data-purchasePrice'),
                        taxPercentage: $opt.attr('data-taxPercentage'),
                        allowPercentage: $opt.attr('data-allowPercentage')
                    });
                }
            }

            return item;
        }

        function applyRetailProductSelection(item) {
            item = resolveProductItemData(item);

            if (item.id) {
                syncSelectedRetailProductOption(item);
            }

            if (!$('#product_id').val()) {
                item_packSize = 0;
                $('#SalePrice').val('');
                $('#SalePrice_text').html('');
                return;
            }

            item_packSize = parseInt(item.packsize, 10) || 0;
            taxPercentage = item.taxPercentage || 0;
            currentAllowPercentage = item.allowPercentage || 100;

            var purchasePrice = item.purchasePrice;
            if (purchasePrice === undefined || purchasePrice === null || purchasePrice === '') {
                purchasePrice = $('#product_id option:selected').attr('data-purchasePrice');
            }

            if (purchasePrice !== undefined && purchasePrice !== null && purchasePrice !== '') {
                $('#SalePrice').val(purchasePrice);
                $('#SalePrice_text').html(purchasePrice);
            }

            var defaultUnit = item_packSize > 0 ? item_packSize : 1;
            $('#pack_quantity').val(1);
            $('#sale_quantity').val(defaultUnit);
        }

        function toggleQtyEntryMode() {
            var mode = $('input[name="qty_entry_mode"]:checked').val();
            var packSize = getSelectedProductPackSize();

            if (mode === 'unit') {
                var unitQty = unitQtyFromPackQty($('#pack_quantity').val(), packSize);
                if (!unitQty) {
                    unitQty = toIntQty($('#sale_quantity').val()) || 1;
                }
                $('#sale_quantity').val(unitQty);
                $('#pos-pack-qty-field-wrap').addClass('d-none');
                $('#pos-unit-qty-field-wrap').removeClass('d-none');
            } else {
                var unitQty = toIntQty($('#sale_quantity').val()) || 0;
                if (packSize > 0) {
                    $('#pack_quantity').val(packQtyDisplayFromUnitQty(unitQty, packSize));
                }
                $('#pos-pack-qty-field-wrap').removeClass('d-none');
                $('#pos-unit-qty-field-wrap').addClass('d-none');
            }
        }

        function getProductNameFromOptionText(text) {
            return (text || '').split('|')[0].trim();
        }

        function getGenericNameFromOptionText(text) {
            var parts = (text || '').split('|');
            return parts.length > 1 ? parts[1].trim() : '';
        }

        function productStartsWithTerm(item, term) {
            term = (term || '').toUpperCase().trim();
            if (!term) {
                return false;
            }
            var productName = getProductNameFromOptionText(item.text).toUpperCase();
            var genericName = getGenericNameFromOptionText(item.text).toUpperCase();
            return productName.indexOf(term) === 0 || genericName.indexOf(term) === 0;
        }

        function productSelect2Sorter(results) {
            var searchField = document.querySelector('.select2-container--open .select2-search__field');
            var term = searchField ? searchField.value.trim() : '';

            if (!term || !results || !results.length) {
                return results;
            }

            return results.slice().sort(function(a, b) {
                var aStarts = productStartsWithTerm(a, term) ? 0 : 1;
                var bStarts = productStartsWithTerm(b, term) ? 0 : 1;
                if (aStarts !== bStarts) {
                    return aStarts - bStarts;
                }
                return a.text.localeCompare(b.text);
            });
        }

        var posLoaderActiveRequests = 0;

        function showPosLoader(message) {
            if (message) {
                $('#pos-page-loader .pos-page-loader__text').text(message);
            }
            posLoaderActiveRequests++;
            $('#pos-page-loader').addClass('is-active').attr('aria-hidden', 'false');
            $('body').addClass('pos-loading');
        }

        function hidePosLoader() {
            posLoaderActiveRequests = Math.max(0, posLoaderActiveRequests - 1);
            if (posLoaderActiveRequests === 0) {
                $('#pos-page-loader').removeClass('is-active').attr('aria-hidden', 'true');
                $('body').removeClass('pos-loading');
            }
        }

        function setProductRefreshLoading(isLoading) {
            $('#product_id').prop('disabled', isLoading);
            var $link = $('#refresh_products');
            if (isLoading) {
                $link.addClass('is-loading').attr('aria-disabled', 'true');
            } else {
                $link.removeClass('is-loading').removeAttr('aria-disabled');
            }
        }

        var lastProductSearchTerm = '';
        var retailPreloadedProducts = [];
        const POS_SEARCH_MIN_AJAX_CHARS = 3;
        const POS_SEARCH_AJAX_MAX_RESULTS = 300;

        function searchAllProductsAjax(term, success, failure) {
            $.ajax({
                type: 'GET',
                url: "{{ route('pos.refresh_retail_products') }}",
                dataType: 'json',
                data: { q: term, search: 1 }
            }).done(function(response) {
                if (!response || !response.status) {
                    failure();
                    return;
                }
                success(response);
            }).fail(failure);
        }

        function mergeRetailPreloadedProducts(products) {
            (products || []).forEach(function(opt) {
                var id = String(opt.id);
                var exists = retailPreloadedProducts.some(function(p) {
                    return String(p.id) === id;
                });
                if (!exists) {
                    retailPreloadedProducts.push(opt);
                }
            });
        }

        function retailProductMatchesTerm(opt, term) {
            term = (term || '').toLowerCase().trim();
            if (!term) {
                return true;
            }
            var words = term.split(/\s+/).filter(Boolean);
            var productName = getProductNameFromOptionText(opt.text).toLowerCase();
            var genericName = getGenericNameFromOptionText(opt.text).toLowerCase();

            return words.every(function(word) {
                return productName.indexOf(word) !== -1 || genericName.indexOf(word) !== -1;
            });
        }

        function filterPreloadedRetailProducts(term, maxResults) {
            maxResults = maxResults || 50;
            var filtered = retailPreloadedProducts;

            if (term) {
                filtered = retailPreloadedProducts.filter(function(opt) {
                    return retailProductMatchesTerm(opt, term);
                });
            }

            return productSelect2Sorter(
                filtered.map(mapRetailProductToSelect2Item)
            ).slice(0, maxResults);
        }

        function appendRetailProductOptionsToSelect($select, products) {
            $select.html('<option value="">Select Product...</option>');

            (products || []).forEach(function(opt) {
                $('<option></option>')
                    .val(opt.id)
                    .attr('data-packsize', opt.packsize)
                    .attr('data-purchasePrice', opt.purchasePrice)
                    .attr('data-taxPercentage', opt.taxPercentage)
                    .attr('data-allowPercentage', opt.allowPercentage)
                    .text(opt.text)
                    .appendTo($select);
            });
        }

        function mapRetailProductToSelect2Item(opt) {
            return normalizeProductSelectItem({
                id: String(opt.id),
                text: opt.text,
                packsize: opt.packsize,
                purchasePrice: opt.purchasePrice,
                taxPercentage: opt.taxPercentage,
                allowPercentage: opt.allowPercentage
            });
        }

        function syncSelectedRetailProductOption(item) {
            item = normalizeProductSelectItem(item);
            if (!item || !item.id) {
                return;
            }

            var $select = $('#product_id');
            $select.find('option[value="' + item.id + '"]').remove();

            $('<option></option>')
                .val(item.id)
                .attr('data-packsize', item.packsize)
                .attr('data-purchasePrice', item.purchasePrice)
                .attr('data-taxPercentage', item.taxPercentage)
                .attr('data-allowPercentage', item.allowPercentage)
                .text(item.text)
                .prop('selected', true)
                .appendTo($select);
        }

        function getRetailProductLoadParams() {
            return {
                initial: 1,
                context: 'retail',
                patient_id: $('#SID').val() || '',
                bill_limit: 100
            };
        }

        function loadInitialRetailProducts(done, options) {
            options = $.extend({
                showLoader: true,
                loaderMessage: 'Loading products...',
                showToast: false
            }, options || {});

            if (options.showLoader) {
                showPosLoader(options.loaderMessage);
            }
            setProductRefreshLoading(true);

            return $.ajax({
                type: 'GET',
                url: "{{ route('pos.refresh_retail_products') }}",
                data: getRetailProductLoadParams(),
                dataType: 'json'
            }).done(function(response) {
                if (!response || !response.status) {
                    popupMsg('Failed to load products', 'error');
                    return;
                }

                retailPreloadedProducts = response.products || [];
                appendRetailProductOptionsToSelect($('#product_id'), retailPreloadedProducts);
                lastProductSearchTerm = '';
                initProductSelect2();
                $('#product_id').val(null).trigger('change');

                if (options.showToast) {
                    popupMsg('Loaded ' + (response.count || 0) + ' products', 'success');
                }

                if (typeof done === 'function') {
                    done(response);
                }
            }).fail(function() {
                popupMsg('Failed to load products', 'error');
            }).always(function() {
                if (options.showLoader) {
                    hidePosLoader();
                }
                setProductRefreshLoading(false);
            });
        }

        function repopulateRetailProductSelect(done, options) {
            options = $.extend({
                showLoader: true,
                loaderMessage: 'Refreshing products...',
                showToast: false,
                q: '',
                initial: false
            }, options || {});

            var useInitial = options.initial || !options.q || options.q.length < POS_SEARCH_MIN_AJAX_CHARS;
            var $link = $('#refresh_products');
            var originalText = $link.text();

            if (options.showLoader) {
                showPosLoader(options.loaderMessage);
            }
            setProductRefreshLoading(true);
            $link.text('Updating...');

            var ajaxData = useInitial ? getRetailProductLoadParams() : { q: options.q };

            return $.ajax({
                type: 'GET',
                url: "{{ route('pos.refresh_retail_products') }}",
                data: ajaxData,
                dataType: 'json'
            }).done(function(response) {
                if (!response || !response.status) {
                    popupMsg('Failed to refresh products', 'error');
                    return;
                }

                if (options.showLoader) {
                    $('#pos-page-loader .pos-page-loader__text').text('Updating product list...');
                }

                var $select = $('#product_id');

                if (useInitial) {
                    retailPreloadedProducts = response.products || [];
                    appendRetailProductOptionsToSelect($select, retailPreloadedProducts);
                    lastProductSearchTerm = '';
                } else {
                    mergeRetailPreloadedProducts(response.products);
                    appendRetailProductOptionsToSelect($select, retailPreloadedProducts);
                    lastProductSearchTerm = options.q;
                }

                initProductSelect2();
                $select.val(null).trigger('change');

                if (options.showToast) {
                    popupMsg('Products refreshed (' + (response.count || 0) + ' items)', 'success');
                }

                if (typeof done === 'function') {
                    done(response);
                }
            }).fail(function() {
                popupMsg('Failed to refresh products', 'error');
            }).always(function() {
                if (options.showLoader) {
                    hidePosLoader();
                }
                setProductRefreshLoading(false);
                $link.text(originalText);
            });
        }

        function initProductSelect2() {
            if ($('#product_id').data('select2')) {
                $('#product_id').select2('destroy');
            }

            $('#product_id').select2({
                placeholder: 'Select Product...',
                allowClear: true,
                minimumInputLength: 0,
                sorter: productSelect2Sorter,
                language: {
                    inputTooShort: function() {
                        return 'Type more than 2 characters to search all products (up to ' + POS_SEARCH_AJAX_MAX_RESULTS + ')';
                    },
                    searching: function() {
                        return 'Searching...';
                    }
                },
                ajax: {
                    url: "{{ route('pos.refresh_retail_products') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return { q: params.term || '' };
                    },
                    transport: function(params, success, failure) {
                        var term = ((params.data && params.data.q) || params.term || '').trim();

                        if (term.length <= 2) {
                            var localShort = filterPreloadedRetailProducts(term, term.length === 0 ? 150 : 50);
                            success({ results: localShort });
                            return;
                        }

                        searchAllProductsAjax(term, function(response) {
                            lastProductSearchTerm = term;
                            mergeRetailPreloadedProducts(response.products);
                            var results = productSelect2Sorter(
                                (response.products || []).map(mapRetailProductToSelect2Item)
                            ).slice(0, POS_SEARCH_AJAX_MAX_RESULTS);
                            success({ results: results });
                        }, failure);
                    },
                    processResults: function(data) {
                        if (data && data.results) {
                            return data;
                        }
                        return { results: [] };
                    }
                }
            });

            $('#product_id').off('select2:select.retailProduct').on('select2:select.retailProduct', function(e) {
                applyRetailProductSelection(e.params.data);
                getItemDetails();
            });
        }

        $(document).ready(function() {
            loadInitialRetailProducts({ showLoader: true, showToast: false }).always(function() {
                toggleQtyEntryMode();
            });

            $(document).on('change', 'input[name="qty_entry_mode"]', toggleQtyEntryMode);

            $('#refresh_products').on('click', function(e) {
                e.preventDefault();
                if ($(this).hasClass('is-loading')) {
                    return;
                }
                var useSearch = lastProductSearchTerm && lastProductSearchTerm.length >= POS_SEARCH_MIN_AJAX_CHARS;
                repopulateRetailProductSelect(null, {
                    showLoader: true,
                    loaderMessage: 'Refreshing products...',
                    showToast: true,
                    initial: !useSearch,
                    q: useSearch ? lastProductSearchTerm : ''
                });
            });
            $("#SID").select2();
            // $("#appointment_id").select2();
            $('#appointment_id').select2({
                placeholder: "Please Select Appointment...",
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    transport: function(params, success, failure) {
                        // Check if local options already match
                        var term = params.data.q ? params.data.q.toLowerCase() : '';
                        var localMatch = [];

                        $('#appointment_id option').each(function() {
                            var text = $(this).text().toLowerCase();
                            if (text.indexOf(term) > -1) {
                                localMatch.push({
                                    id: $(this).val(),
                                    text: $(this).text()
                                });
                            }
                        });

                        // If found locally → return without ajax
                        if (localMatch.length > 0) {
                            success(localMatch);
                            return;
                        }

                        // Else → send ajax request
                        $.ajax(params, success, failure);
                    },
                    url: "{{ route('pos.search_appointment') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });




            // $("#product_id").select2();

            $("body").on("change", "#medicine_type", function() {
                var value = $(this).val();
                window.location = "{{route('pos.retail_pharmacy_sale')}}?type=" + value;
            });

            $("body").on("change", "#discount_id", function() {
                validateAndApplyDiscount();
            });
            $("body").on("change", "#SID", function() {
                patient_admission_id = $('#SID option:selected').attr('data-admission_id');
                get_prev_balance();
                loadInitialRetailProducts({ showLoader: false, showToast: false });
            });

            $("body").on("change", "#barcode", function() {

                var values = $(this).val();
                $(this).val('');

                var barcode_number = values.trim();
                $.ajax({
                    type: "post",
                    dataType: "json",
                    data: {
                        barcode: barcode_number,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{route('pos.get_items_by_barcode')}}",
                    success: function(response) {
                        if (response != false) {
                            $.each(response.data, function(key, value) {

                                ProductID = value.ProductID;
                                Product = value.ProductName;
                                UnitePrice = value.SalePrice;
                                Name = value.Specification_name;
                                AvailableQuantity = value.AvailableQuantity;
                                taxPercentage = value.taxPercentage;


                            });
                            if (AvailableQuantity <= 0) {
                                alert("Item is out of stock");
                                return false;
                            } else {
                                setTimeout(function() {
                                    var dose_type = '';
                                    add_item_to_grid(ProductID, Product, UnitePrice, Name, AvailableQuantity, 1, taxPercentage, dose_type);
                                    //clearForm();
                                    $("#barcode").focus();
                                }, 300);

                            }

                        } else {
                            popupMsg("Item is Not Registered in Inventory", "error");
                            $("#PID").data('kendoDropDownList').value('');
                            return;
                        }
                    }
                });
            });
            // Inline grid inputs — event handlers (fields are always visible in the table)

            $(document).on('focus', '.pos-grid-cell-input', function() {
                var $input = $(this);
                posGridInputPrevious[getPosGridInputKey($input)] = $input.val();
                setTimeout(function() {
                    $input.select();
                }, 0);
            });

            $(document).on('change', '.pos-grid-cell-input', function() {
                applyPosGridCellChange($(this));
            });

            $(document).on('blur', '.pos-grid-cell-input[data-field="row_discount"]', function() {
                var $input = $(this);
                var key = getPosGridInputKey($input);
                if ($.trim($input.val()) !== String(posGridInputPrevious[key] || '')) {
                    applyPosGridCellChange($input);
                }
            });

            $(document).on('keydown', '.pos-grid-cell-input', function(e) {
                var $input = $(this);
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $input.trigger('change');
                    var $rowInputs = $input.closest('tr').find('.pos-grid-cell-input');
                    var idx = $rowInputs.index($input);
                    if (idx > -1 && idx < $rowInputs.length - 1) {
                        $rowInputs.eq(idx + 1).focus();
                    }
                }
                if (e.key === 'Escape') {
                    e.preventDefault();
                    var key = getPosGridInputKey($input);
                    if (posGridInputPrevious[key] !== undefined) {
                        $input.val(posGridInputPrevious[key]);
                    }
                    $input.blur();
                }
            });

            $(document).on("change", "#product_id", function() {
                if (!$('#product_id').val()) {
                    item_packSize = 0;
                    return;
                }
                applyRetailProductSelection();
                getItemDetails();
            });

            $("body").on("keyup", "#pack_quantity", function() {
                 var pack_qty = toIntQty($(this).val()) || 0;
                 $(this).val(pack_qty);
                 var packSize = getSelectedProductPackSize();
                 var unitQty = unitQtyFromPackQty(pack_qty, packSize);
                 $("#sale_quantity").val(unitQty);
            });

            $("body").on("keyup", "#sale_quantity", function() {
                var unitQty = toIntQty($(this).val()) || 0;
                $(this).val(unitQty);
                var packSize = getSelectedProductPackSize();
                if (packSize > 0) {
                    $("#pack_quantity").val(packQtyDisplayFromUnitQty(unitQty, packSize));
                }
            });

            $("body").on("blur", "#SalePrice", function() {
                saveItemToBill();
            });

            $("body").on("click", ".remove_item", function() {
                removeProductByID($(this).attr("data-id"));

            });


            $("body").on("click", "#save_bill", function() {
                var count_error_items = $('.horizontal-menu li').length;
                var invoice_discount = $("#invoice_discount").val();
                if (invoice_discount == '') {
                    invoice_discount = 0;
                }
                if (invoice_discount > 9) {
                    alert("Invoice Discount Limit Exceed. Limit Rs:9 only/-");
                    return false;
                }

                var return_amount = $("#ReturnToCustomer").val();
                if (return_amount < 0) {
                    alert("Please collect amount from customer");
                    $("#ReceivedAmountFromCustomer").focus();
                    return false;
                }

                if (count_error_items > 0) {
                    popupMsg("Please Add grn of pending item of KIT or Skip low quantity Items", "error");
                    return false;
                }


                SID = $("#SID").val();
                company_name = $("#company_name").val();
                invoice_number = $("#invoice_number").val();
                discount_amount = $("#discount_amount").val();
                medicine_type = $("#medicine_type").val();
                appointment_id = $("#appointment_id").val();
                currency_type = $("#currency_type").val();
                bill_date = $("#bill_date").val();
                customer_name = $("#customer_name").val();
                previous_balance = $("#previous_balance").val();
                ReceivedAmount = $("#ReceivedAmount").val();
                BillDiscription = $("#BillDiscription").val();
                BillAmount = $("#BillAmount").val();
                ReceivedAmountFromCustomer = $("#ReceivedAmountFromCustomer").val();
                bill_address = '';
                discount_percentage = 0;

                var net_Billamount = (parseFloat(BillAmount) || 0) - (parseFloat(discount_amount) || 0);


                if (SID == '') {
                    popupMsg("Please Select Customer", "error");
                    $("#SID").focus();
                    $("#SID").select2('open');
                    $("#save_bill").show();
                    return false;
                }

                /*if(ReceivedAmount < net_Billamount){
                    alert("Please collect full amount from customer.");
                    $("#ReceivedAmount").focus();
                    $("#save_bill").show();
                    return false;
                }*/


                if (medicine_type == '') {
                    popupMsg("Please Select Medicine Type", "error");
                    $("#medicine_type").focus();
                    $("#save_bill").show();
                    return false;
                }
                if (invoice_number == '') {
                    popupMsg("enter invoice number", "error");

                    $("#invoice_number").focus();
                    $("#save_bill").show();
                    return false;
                }
                if (company_name == '') {
                    popupMsg("Enter Company Name", "error");
                    $("#company_name").focus();
                    $("#save_bill").show();
                    return false;
                }
                if (currency_type == '') {
                    popupMsg("Select Currency Type", "error");
                    $("#currency_type").focus();
                    $("#currency_type").trigger('click');
                    $("#save_bill").show();
                    return false;
                }
                if (bill_date == '') {
                    popupMsg("Enter Bill Date", "error");
                    $("#bill_date").focus();
                    $("#save_bill").show();
                    return false;
                }

                if (ProductList.length <= 0) {
                    popupMsg("Please Add Items to Bill", "error");
                    return false;
                }

                showSaveBillConfirmModal(net_Billamount, function() {
                    $("#save_bill").hide();
                    retailPrintWindow = window.open('about:blank', '_blank');

                    var patient_id = SID;

                    var ProductListForSave = ProductList.map(function(p) {
                        var copy = $.extend({}, p);
                        delete copy.pack_qty;
                        delete copy.pack_size;
                        return copy;
                    });
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            SID,
                            patient_id,
                            ReceivedAmountFromCustomer,
                            ward_request_id,
                            patient_admission_id,
                            discount_percentage,
                            company_name,
                            invoice_number,
                            medicine_type,
                            appointment_id,
                            discount_amount,
                            currency_type,
                            bill_date,
                            customer_name,
                            previous_balance,
                            bill_address,
                            ReceivedAmount,
                            BillDiscription,
                            BillAmount,
                            invoice_discount,
                            retail_print_mode: getRetailPrintMode(),
                            ProductList: ProductListForSave,
                            "_token": "{{ csrf_token() }}"
                        },
                        url: "{{ route('pos.save_retail_sale') }}",
                        success: function(response) {
                            sale_id_for_print = response.id;
                            openRetailBillPrint(sale_id_for_print);
                            window.location.reload();
                        },
                        error: function() {
                            if (retailPrintWindow && !retailPrintWindow.closed) {
                                retailPrintWindow.close();
                            }
                            $("#save_bill").show();
                        }
                    });
                });

            });



            $("body").on("click", ".print_bill", function() {

                SID = $("#SID").val();
                company_name = $("#company_name").val();
                invoice_number = $("#invoice_number").val();
                currency_type = $("#currency_type").val();
                bill_date = $("#bill_date").val();
                customer_name = $("#customer_name").val();
                previous_balance = $("#previous_balance").val();
                ReceivedAmount = $("#ReceivedAmount").val();
                BillDiscription = $("#BillDiscription").val();
                BillAmount = $("#BillAmount").val();
                bill_address = '';
                $("#save_bill").hide();
                if (SID == '') {
                    popupMsg("Please Select Customer", "error");
                    $("#SID").focus();
                    $("#SID").select2('open');
                    $("#save_bill").show();
                    return false;
                }
                if (invoice_number == '') {
                    popupMsg("enter invoice number", "error");
                    $("#invoice_number").focus();
                    $("#save_bill").show();
                    return false;
                }
                if (company_name == '') {
                    popupMsg("Enter Company Name", "error");
                    $("#company_name").focus();
                    $("#save_bill").show();
                    return false;
                }
                if (currency_type == '') {
                    popupMsg("Select Currency Type", "error");
                    $("#currency_type").focus();
                    $("#currency_type").trigger('click');
                    $("#save_bill").show();
                    return false;
                }
                if (bill_date == '') {
                    popupMsg("Enter Bill Date", "error");
                    $("#bill_date").focus();
                    $("#save_bill").show();
                    return false;
                }

                if (ProductList.length <= 0) {
                    popupMsg("Please Add Items to Bill", "error");
                    $("#save_bill").show();
                    return false;
                }




                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        SID,
                        company_name,
                        invoice_number,
                        currency_type,
                        bill_date,
                        customer_name,
                        previous_balance,
                        bill_address,
                        ReceivedAmount,
                        BillDiscription,
                        BillAmount,
                        ProductList,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('pos.temp_save_sale') }}",
                    success: function(response) {
                        $("#save_bill").show();
                        sale_id_for_print = response.id;


                        // window.location.reload();
                        var date = "{{ date('Y - m - d ')}}";
                        url = "{{route('pos.print_temp_sale')}}/1/" + SID + "/" + date + "/" + ReceivedAmount;
                        window.open(url, 'Direct Bill', 'width=1200,height=600,scrollbars=yes');



                        //window.location.reload();



                    }
                });
                //console.log(CKEDITOR.getData());

            });
        });

        function removeProductByID(productID) {
            ProductList = ProductList.filter(product => product.ProductID != productID);
            reload_table();
        }

    function updateProductByID(product_id, quantity, rate, total, pack_qty = null, skipReload) {

            let product = findProductInList(product_id);
            if (product) {

                product.Quantity = quantity;
                product.UnitePrice = rate;
                product.Total = total;

                if (pack_qty !== null) {
                    product.pack_qty = pack_qty;
                }

                recalculateRetailLine(product);

                if (skipReload) {
                    refreshProductRowInGrid(product_id);
                    syncBillTotalsFromProductList();
                } else {
                    reload_table();
                }
            } else {
                reload_table();
            }
        }

        function updateProductTaxByID(product_id, field, value, skipReload) {
            let product = findProductInList(product_id);
            if (product) {
                product[field] = parseFloat(value) || 0;
                recalculateRetailLine(product);
                if (skipReload) {
                    refreshProductRowInGrid(product_id);
                    syncBillTotalsFromProductList();
                } else {
                    reload_table();
                }
            }
        }

        function refreshProductRowInGrid(productId) {
            var product = findProductInList(productId);
            if (!product) {
                return;
            }
            var $tr = $('#product_table').find('td[data-id="' + productId + '"]').closest('tr');
            if (!$tr.length) {
                return;
            }
            var line = recalculateRetailLine(product);
            var packSize = parseInt(product.pack_size, 10) || 0;
            var unitQty = toIntQty(line.actualQuantity);
            var packDisplay = packQtyDisplayFromUnitQty(unitQty, packSize);
            $tr.find('[data-field="pack_qty"]').val(packDisplay);
            $tr.find('[data-field="quantity"]').val(unitQty);
            $tr.find('[data-field="rate"]').val((parseFloat(product.UnitePrice) || 0).toFixed(2));
            $tr.find('[data-field="row_discount"]').val(product.discount_percentage === undefined || product.discount_percentage === null ? 0 : product.discount_percentage);
            $tr.find('td:eq(6)').text(line.finalItemAmount.toFixed(2));
            $tr.find('[data-field="sale_tax"]').val(product.sale_tax === undefined || product.sale_tax === null ? 0 : product.sale_tax);
            $tr.find('[data-field="income_tax"]').val(product.income_tax === undefined || product.income_tax === null ? 0 : product.income_tax);
        }

        function syncBillTotalsFromProductList() {
            var total_amount = 0;
            var total_product_discount_amount = 0;

            ProductList.forEach(function(product) {
                var line = recalculateRetailLine(product);
                total_amount += line.finalItemAmount;
                total_product_discount_amount += parseFloat(product.discount_percentage_amount) || 0;
            });

            var invoice_discount = $("#invoice_discount").val();
            if (invoice_discount == '') {
                invoice_discount = 0;
            }
            invoice_discount = parseFloat(invoice_discount);

            $("#discount_amount").val(total_product_discount_amount.toFixed(2));

            var grossBill = Math.ceil(total_amount + total_product_discount_amount);
            $("#BillAmount").val(grossBill);

            var netBeforeTax = Math.ceil(total_amount - invoice_discount);
            if (netBeforeTax < 0) {
                netBeforeTax = 0;
            }
            var taxBreakdown = calculateBillTaxFromRows();
            var billTaxAmount = taxBreakdown.total;
            var netAmountWithTax = netBeforeTax + billTaxAmount;

            $("#amount_after_discount").val(netBeforeTax);
            $("#bill_tax_amount").val(billTaxAmount);
            $("#ReceivedAmount").val(netAmountWithTax);
            renderBillCalculationDisplay(
                grossBill,
                total_product_discount_amount,
                invoice_discount,
                netBeforeTax,
                taxBreakdown.lines,
                billTaxAmount,
                netAmountWithTax
            );
            syncBillDiscountDropdownFromRows();
            calculateBalance();
        }

        function getDefaultTaxPercentages() {
            return {
                sale_tax: parseFloat(defaultRowTaxes.sale_tax) || 0,
                income_tax: parseFloat(defaultRowTaxes.income_tax) || 0
            };
        }

        function getLineTaxableAmount(product) {
            var lineTotal = parseFloat(product.Total) || 0;
            var lineDiscount = parseFloat(product.discount_percentage_amount) || 0;
            return Math.max(0, lineTotal - lineDiscount);
        }

        function calculateRowTaxAmounts(product) {
            var base = getLineTaxableAmount(product);
            var salePct = parseFloat(product.sale_tax) || 0;
            var incomePct = parseFloat(product.income_tax) || 0;
            product.sale_tax_amount = Math.round((base * salePct) / 100);
            product.income_tax_amount = Math.round((base * incomePct) / 100);
            return product;
        }

        $("body").on("click", ".remove_low_qty_item", function() {
            var id = $(this).attr("data-item_id");
            $(`#${id}`).remove();
        });

        function getItemDetails() {
            currentAvailableQuantity = 0;
            var p_id = $("#product_id").val();
            if (p_id == '') {
                return false;
            }
            $.ajax({
                type: "post",
                dataType: "json",
                data: {
                    p_id: p_id,
                    _token: '{{ csrf_token() }}'
                },
                url: "{{route('pos.get_items_by_product_id')}}",
                success: function(response) {

                    if (response.status == true) {
                        $.each(response.data, function(key, value) {
                            if (value.is_product_kit) {
                                if (value.AvailableQuantity < value.qty) {
                                    $(".horizontal-menu").append(`<li id='product_id_${value.product.ProductID}'>${value.product.ProductName} (Qty: ${value.AvailableQuantity}) <span data-item_id="product_id_${value.product.ProductID}" class="btn btn-warning remove_low_qty_item">x</span></li>`);
                                }

                                add_item_to_grid(value.product.ProductID, value.product.ProductName, value.product.unit_sale_price, value.product.name, value.AvailableQuantity, value.qty, '');
                            } else {

                                currentAvailableQuantity = value.AvailableQuantity ? value.AvailableQuantity : 0;
                                if (currentAvailableQuantity > 0) {
                                    $(`#product_id_${value.ProductID}`).remove();

                                }
                            }

                        });

                    } else {
                        popupMsg("Item is Not Registered in Inventory", "error");
                        return;
                    }
                }
            });

        }

        function saveItemToBill() {
            var medicine_type = "{{$type}}";
            var dose_type = '';
            if (medicine_type == 'Home' || medicine_type == "Ward") {
                dose_type = $("#dose_type").val();
                if (dose_type == '') {
                    popupMsg("Please Select Dose Type. ", "error");
                    return false;
                }
            }

            ProductID = $('#product_id').val();
            Product = $('#product_id option:selected').text();

            Name = $('#product_id option:selected').text();
            AvailableQuantity = currentAvailableQuantity;

            var quantity = $("#sale_quantity").val();
            UnitePrice = $("#SalePrice").val();
            var pack_size = parseInt($('#product_id option:selected').attr('data-packsize'), 10) || 0;
            var qtyMode = $('input[name="qty_entry_mode"]:checked').val();

            if (qtyMode === 'unit') {
                quantity = toIntQty($("#sale_quantity").val()) || 0;
            } else {
                quantity = unitQtyFromPackQty($("#pack_quantity").val(), pack_size);
                $("#sale_quantity").val(quantity);
            }

            if (ProductID == '' || Name == '' || quantity === '' || quantity < 1 || UnitePrice == '') {
                popupMsg("Please Fill All required fields.. ", "error");
                return false;
            }

            add_item_to_grid(ProductID, Product, UnitePrice, Name, AvailableQuantity, quantity, dose_type);
            clearForm();
            return true;


        }

        function clearForm() {
            $("#product_name").val('');


            $("#sale_quantity").val(1);
            $("#SalePrice").val('');
            $("#SalePrice_text").html('');

            $("#product_id").val(null).trigger('change');
            $("#product_id").focus();
            $("#product_id").select2('open');
            $("#dose_type").val('-');
            $("#pack_quantity").val('1');
            item_packSize = 0;
            taxPercentage = 0;
            currentAvailableQuantity = 0;

        }

        var retailSaleDefaults = {
            billDate: $('#bill_date').val(),
            medicineType: $('#medicine_type').val() || 'Home',
            defaultSid: (function() {
                var sid = '';
                $('#SID option').each(function() {
                    if (!$(this).val()) {
                        return;
                    }
                    if ($(this).text().toLowerCase().indexOf('walking') !== -1) {
                        sid = $(this).val();
                        return false;
                    }
                });
                if (!sid) {
                    sid = $('#SID option[value!=""]').first().val() || '';
                }
                return sid;
            })()
        };

        function resetRetailSaleFormForNewBill(response) {
            ProductList = [];
            currentProductAllowPercentages = {};
            ward_request_id = 0;
            patient_admission_id = 0;
            PreviousBalance = 0;
            item_packSize = 0;
            currentAllowPercentage = 100;

            $('.horizontal-menu').empty();

            if (response && response.invoice_no) {
                $('#invoice_number').val(response.invoice_no);
            }

            $('#bill_date').val(new Date().toISOString().slice(0, 10));
            $('#previous_balance').val('');
            $('#appointment_id').val(null).trigger('change');
            $('#medicine_type').val(retailSaleDefaults.medicineType);

            if (retailSaleDefaults.defaultSid) {
                $('#SID').val(retailSaleDefaults.defaultSid).trigger('change');
            } else {
                $('#SID').val('').trigger('change');
            }

            $('#discount_id').val('0');
            $('#invoice_discount').val('0');
            $('#ReceivedAmountFromCustomer').val('0');
            $('#ReturnToCustomer').val('0');
            $('#BillDiscription').val('-');

            $("#sale_quantity").val(1);
            $("#SalePrice").val('');
            $("#SalePrice_text").html('');
            $("#product_id").val(null).trigger('change');
            $("#dose_type").val('-');
            $("#pack_quantity").val('1');
            taxPercentage = 0;
            currentAvailableQuantity = 0;

            reload_table();

            if (previous_bill_table) {
                previous_bill_table.ajax.reload(null, false);
            }

            appendRetailProductOptionsToSelect($('#product_id'), []);
            lastProductSearchTerm = '';
            $("#product_id").val(null).trigger('change');
            $('#product_id').select2('close');
            $('#product_id').focus();
        }

        function get_prev_balance(e) {

            var value = $("#SID").val();
            var name = $('#SID').select2('data')[0]['text'];
            var serverBaseUrl = "";
            $("#page_title").text(name);
            if (value != '') {
                $.ajax({
                    type: "get",
                    url: "{{route('pos.customer_previous_balance')}}/" + value,
                    success: function(response) {

                        PreviousBalance = parseFloat(response).toFixed(2);
                        $("#previous_balance").val(PreviousBalance);
                        calculateBalance();

                    }
                });
            } else {
                $("#prev_balance").val(0);
            }
        }

        function calculateBalance() {
            $("#prv_balance").text(PreviousBalance);
            $("#current_bill").text($("#BillAmount").val());
            var total_bill = (parseFloat($("#BillAmount").val()) + parseFloat(PreviousBalance)).toFixed(2);
            $("#total_bill").text(total_bill);
        }

        function add_item_to_grid(ProductID, Product, UnitPrice, Name, AvailableQuantity, quantity = '', dose_type = '') {

            if (AvailableQuantity == 0) {
                popupMsg(Product + " Is not Available in Stock", "error");
                return false;
            }
            if (quantity > AvailableQuantity) {
                popupMsg("You are Exceeding Available Quantity.", "error");
                return false;
            }

            Quantity = 1;
            if (quantity != '') {
                Quantity = quantity;
            }
            let exists = ProductList.some(item => item.ProductID === ProductID);

            if (!exists) {
                Quantity = 1;
                if (quantity != '') {
                    Quantity = quantity;
                }
                var defaultTaxes = getDefaultTaxPercentages();

                var pack_size = parseInt($("#product_id option:selected").attr('data-packsize'), 10) || parseInt(item_packSize, 10) || 0;
                var unitQty = toIntQty(Quantity);
                var pack_qty = packQtyDisplayFromUnitQty(unitQty, pack_size);

                var data_array = {
                    ProductID: ProductID,
                    Product: Product,
                    Name: Product,
                    UnitePrice: UnitPrice,
                    Quantity: unitQty,
                    pack_qty: pack_qty,
                    pack_size: pack_size,
                    Total: unitQty * UnitPrice,
                    AvailableQuantity: AvailableQuantity,
                    taxPercentage: 0,
                    taxAmount: 0,
                    sale_tax: defaultTaxes.sale_tax,
                    income_tax: defaultTaxes.income_tax,
                    sale_tax_amount: 0,
                    income_tax_amount: 0,
                    currentAvailableQuantity: currentAvailableQuantity,
                    dose_type: dose_type,
                    allow_percentage: currentAllowPercentage,
                    discount_percentage: 0,
                    discount_percentage_amount: 0

                };
                currentProductAllowPercentages[ProductID] = currentAllowPercentage;
                ProductList.push(data_array);

                // Apply current discount to the new product if any discount is selected
                var currentDiscount = parseInt($("#discount_id").val()) || 0;
                if (currentDiscount > 0) {
                    validateAndApplyDiscount();
                } else {
                    reload_table();
                }
            } else {
                popupMsg("Select Product already exist in list", "error");
                return false;
            }


        }

        function calculateBillTaxFromRows() {
            var totalSaleTax = 0;
            var totalIncomeTax = 0;
            var lines = [];

            ProductList.forEach(function(product) {
                calculateRowTaxAmounts(product);
                totalSaleTax += parseFloat(product.sale_tax_amount) || 0;
                totalIncomeTax += parseFloat(product.income_tax_amount) || 0;
            });

            if (totalSaleTax > 0) {
                lines.push({ name: 'Sale Tax', percentage: null, amount: totalSaleTax });
            }
            if (totalIncomeTax > 0) {
                lines.push({ name: 'Income Tax', percentage: null, amount: totalIncomeTax });
            }

            return { total: totalSaleTax + totalIncomeTax, lines: lines };
        }

        function renderBillTaxBreakdown(lines, totalTax) {
            var $container = $("#bill_tax_breakdown");
            $container.empty();

            if (!lines || !lines.length) {
                $container.html('<div class="pos-tax-line text-muted">No tax applied</div>');
                return;
            }

            lines.forEach(function(line) {
                var pctHtml = '';
                if (line.percentage !== null && line.percentage !== undefined) {
                    var pctLabel = (parseFloat(line.percentage) % 1 === 0)
                        ? parseInt(line.percentage, 10)
                        : parseFloat(line.percentage);
                    pctHtml = '<span class="pos-tax-pct">' + pctLabel + '%</span>';
                }
                var html = '<div class="pos-tax-line">' +
                    '<span class="pos-tax-name">' + line.name + '</span>' +
                    pctHtml +
                    '<span class="pos-tax-amt">' + formatBillDisplayAmount(line.amount) + '</span>' +
                    '</div>';
                $container.append(html);
            });

        }

        function formatBillDisplayAmount(value) {
            return Math.round(parseFloat(value) || 0).toLocaleString();
        }

        function renderBillCalculationDisplay(billAmount, itemDiscount, invoiceDiscount, afterDiscount, taxLines, totalTax, netAmount) {
            $('#display_bill_amount').text(formatBillDisplayAmount(billAmount));
            $('#display_item_discount').text(formatBillDisplayAmount(itemDiscount));
            $('#display_invoice_discount').text(formatBillDisplayAmount(invoiceDiscount));
            $('#display_after_discount').text(formatBillDisplayAmount(afterDiscount));
            $('#display_total_tax').text(formatBillDisplayAmount(totalTax));
            $('#display_net_amount').text(formatBillDisplayAmount(netAmount));
            renderBillTaxBreakdown(taxLines, totalTax);
        }

        function reload_table() {
            $("#product_table").html('');
            var total_amount = 0;
            ProductList.forEach((value, key) => {
                var line = recalculateRetailLine(value);
                var saleTaxVal = (value.sale_tax === undefined || value.sale_tax === null) ? 0 : value.sale_tax;
                var incomeTaxVal = (value.income_tax === undefined || value.income_tax === null) ? 0 : value.income_tax;
                var rowDiscVal = (value.discount_percentage === undefined || value.discount_percentage === null) ? 0 : value.discount_percentage;
                var unitQty = toIntQty(line.actualQuantity);
                var packSize = parseInt(value.pack_size, 10) || 0;
                var packQtyVal = packQtyDisplayFromUnitQty(unitQty, packSize);
                var html = `<tr>
                        <td data-id="${value.ProductID}">${key+1}</td>
                        <td class="col-product">${value.Name}</td>
                        <td class="pos-cell-editable">${buildPosGridCellInput('pack_qty', packQtyVal, value.ProductID)}</td>
                        <td class="pos-cell-editable">${buildPosGridCellInput('quantity', unitQty, value.ProductID)}</td>
                        <td class="pos-cell-editable">${buildPosGridCellInput('rate', value.UnitePrice, value.ProductID)}</td>
                        <td class="pos-cell-editable col-disc">${buildPosGridCellInput('row_discount', rowDiscVal, value.ProductID)}</td>
                        <td>${line.finalItemAmount.toFixed(2)}</td>
                        <td class="pos-cell-editable">${buildPosGridCellInput('sale_tax', saleTaxVal, value.ProductID)}</td>
                        <td class="pos-cell-editable">${buildPosGridCellInput('income_tax', incomeTaxVal, value.ProductID)}</td>
                        <td>${value.AvailableQuantity}</td>
                        <td class="col-actions">
                            <a class="btn btn-sm btn-danger remove_item" data-id="${value.ProductID}">x</a>
                        </td>
                    </tr>`;
                total_amount = parseFloat(total_amount) + line.finalItemAmount;
                $("#product_table").prepend(html);
                setTimeout(function() {
                    calculateBalance();

                }, 1000);

            });
            syncBillTotalsFromProductList();

            if (ProductList.length < 15) {
                var length = (15) - (ProductList.length);
                var i = 1;
                for (i = 1; i <= length; i++) {
                    var html = `<tr>
                        <td>&nbsp;</td>
                        <td class="col-product"></td>
                        <td></td>
                        <td></td>
                        <td data-field="rate"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="col-actions"></td>
                    </tr>`;
                    $("#product_table").append(html);
                }
            }

            calculateReturnAmount();

        }


        function validateAndApplyDiscount() {
            var discountPercentage = parseFloat($("#discount_id").val()) || 0;

            ProductList.forEach(function(item) {
                if (discountPercentage <= 0) {
                    item.discount_percentage = 0;
                    item.discount_percentage_amount = 0;
                } else {
                    item.discount_percentage = normalizeRowDiscountPercentage(discountPercentage);
                }
                recalculateRetailLine(item);
            });

            reload_table();
        }

        function popupMsg(msg, msgtype) {
            var color = '#dd1144';
            if (msgtype.toLowerCase() == 'success') {
                var color = '#00CC00';
            }

            $("#popu-message").css('background-color', color).html(msg).slideDown().delay(2000).slideUp();

        }
    </script>

</body>

</html>