<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WorkPulse – Platform Asesmen Budaya Kerja & Risiko Burnout Startup')</title>

    <!-- Google Fonts: Source Serif 4 & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&display=swap" rel="stylesheet">

    <!-- Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <style>
        :root {
            --bg-page: #FAF8F5;
            --bg-surface: #FFFFFF;
            --bg-subtle: #F3EFEA;
            --bg-track: #EDE9E4;

            --ink-primary: #1A1817;
            --ink-hover: #332F2D;
            --ink-secondary: #5C5855;
            --ink-muted: #78726C;

            --border-hairline: #E6E2DC;
            --border-control: #D6D1CA;
            --border-control-hover: #B8B1A8;

            --accent-plum: #6D597A;
            --accent-plum-soft: #EDE9F2;
            --accent-forest: #1F5E4B;
            --accent-forest-soft: #EBF3F0;

            --status-positif-bg: #ECFDF3;
            --status-positif-text: #166534;
            --status-positif-border: #BBF7D0;

            --status-stabil-bg: #F0F9FF;
            --status-stabil-text: #075985;
            --status-stabil-border: #BAE6FD;

            --status-waspada-bg: #FEFCE8;
            --status-waspada-text: #854D0E;
            --status-waspada-border: #FEF08A;

            --status-tinggi-bg: #FEF2F2;
            --status-tinggi-text: #991B1B;
            --status-tinggi-border: #FECACA;

            --font-serif: 'Source Serif 4', Georgia, serif;
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;

            --radius-md: 10px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-2xl: 20px;
            --shadow-editorial: 0 1px 2px rgba(26, 24, 23, 0.04);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background-color: var(--bg-page);
            color: var(--ink-primary);
            font-family: var(--font-sans);
            font-size: 1rem;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, p, ul, ol, fieldset, legend {
            margin: 0;
        }

        a {
            color: inherit;
        }

        /* Editorial Typography */
        .font-serif {
            font-family: var(--font-serif);
            letter-spacing: -0.02em;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        .wp-kicker {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--ink-muted);
            margin-bottom: 0.75rem;
        }

        /* Canonical Visually Hidden (preserves keyboard & screen-reader access) */
        .visually-hidden {
            position: absolute;
            clip-path: inset(50%);
            overflow: hidden;
            width: 1px;
            height: 1px;
            margin: -1px;
            padding: 0;
            border: 0;
            white-space: nowrap;
        }

        /* Focus Indicator */
        :focus-visible {
            outline: 2px solid var(--ink-primary);
            outline-offset: 2px;
        }

        /* Container */
        .wp-container {
            width: 100%;
            max-width: 1160px;
            margin-inline: auto;
            padding-inline: 2rem;
        }

        /* Buttons */
        .wp-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            background-color: var(--ink-primary);
            color: var(--bg-surface);
            border: 1px solid var(--ink-primary);
            font-family: var(--font-sans);
            font-size: 0.9375rem;
            font-weight: 600;
            line-height: 1.25;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 150ms ease, border-color 150ms ease;
        }

        .wp-btn-primary:hover {
            background-color: var(--ink-hover);
            border-color: var(--ink-hover);
        }

        .wp-btn-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .wp-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            background-color: var(--bg-surface);
            color: var(--ink-primary);
            border: 1px solid var(--border-control);
            font-family: var(--font-sans);
            font-size: 0.9375rem;
            font-weight: 600;
            line-height: 1.25;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 150ms ease, border-color 150ms ease;
        }

        .wp-btn-secondary:hover {
            background-color: var(--bg-subtle);
            border-color: var(--border-control-hover);
        }

        .wp-btn-sm {
            padding: 0.5625rem 1.125rem;
            font-size: 0.875rem;
        }

        .wp-btn-full {
            width: 100%;
        }

        /* Cards & Alerts */
        .wp-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-hairline);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-editorial);
        }

        .wp-alert-error {
            background-color: var(--status-tinggi-bg);
            border: 1px solid var(--status-tinggi-border);
            color: var(--status-tinggi-text);
            padding: 0.875rem 1rem;
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .wp-alert-error ul {
            margin-top: 0.375rem;
            padding-left: 1.125rem;
        }

        .wp-alert-success {
            background-color: var(--status-positif-bg);
            border: 1px solid var(--status-positif-border);
            color: var(--status-positif-text);
            padding: 0.875rem 1rem;
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
        }

        /* Editorial Burnout Status Badges */
        .wp-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.6875rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.3;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .wp-status-positif {
            background-color: var(--status-positif-bg);
            color: var(--status-positif-text);
            border-color: var(--status-positif-border);
        }

        .wp-status-stabil {
            background-color: var(--status-stabil-bg);
            color: var(--status-stabil-text);
            border-color: var(--status-stabil-border);
        }

        .wp-status-waspada {
            background-color: var(--status-waspada-bg);
            color: var(--status-waspada-text);
            border-color: var(--status-waspada-border);
        }

        .wp-status-tinggi {
            background-color: var(--status-tinggi-bg);
            color: var(--status-tinggi-text);
            border-color: var(--status-tinggi-border);
        }

        /* Forms & Controls */
        .wp-field {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0.375rem;
            margin-bottom: 1.25rem;
        }

        .wp-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .wp-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--ink-primary);
        }

        .wp-helper {
            font-size: 0.8125rem;
            color: var(--ink-muted);
            line-height: 1.45;
        }

        .wp-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .wp-input,
        .wp-select {
            width: 100%;
            min-height: 46px;
            padding: 0.6875rem 0.875rem;
            font: inherit;
            font-size: max(1rem, 0.9375rem);
            color: var(--ink-primary);
            background-color: var(--bg-surface);
            border: 1px solid var(--border-control);
            border-radius: var(--radius-md);
            transition: border-color 150ms ease, background-color 150ms ease;
        }

        .wp-input::placeholder {
            color: var(--ink-muted);
        }

        .wp-input:hover,
        .wp-select:hover {
            border-color: var(--border-control-hover);
        }

        .wp-input:focus,
        .wp-select:focus {
            outline: none;
            border-color: var(--ink-primary);
        }

        .wp-input:focus-visible,
        .wp-select:focus-visible {
            outline: 2px solid var(--ink-primary);
            outline-offset: 2px;
        }

        .wp-input:user-invalid,
        .wp-select:user-invalid {
            border-color: var(--status-tinggi-text);
        }

        .wp-input-with-toggle {
            padding-right: 3rem;
        }

        .wp-pwd-toggle {
            position: absolute;
            right: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: var(--ink-secondary);
            cursor: pointer;
            transition: background-color 150ms ease, color 150ms ease;
        }

        .wp-pwd-toggle:hover {
            background-color: var(--bg-subtle);
            color: var(--ink-primary);
        }

        /* Role Selector (Accessible Radio Cards) */
        .wp-fieldset-plain {
            border: 0;
            padding: 0;
            margin: 0 0 1.375rem 0;
        }

        .wp-legend {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--ink-primary);
            margin-bottom: 0.5rem;
            padding: 0;
        }

        .wp-role-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .wp-role-option {
            position: relative;
            display: block;
            cursor: pointer;
        }

        .wp-role-card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
            padding: 0.9375rem 1rem;
            border: 1px solid var(--border-hairline);
            border-radius: var(--radius-lg);
            background-color: var(--bg-surface);
            transition: border-color 150ms ease, background-color 150ms ease;
        }

        .wp-role-card:hover {
            border-color: var(--border-control-hover);
        }

        .wp-role-title {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--ink-primary);
        }

        .wp-role-desc {
            font-size: 0.8125rem;
            color: var(--ink-secondary);
            line-height: 1.4;
        }

        .wp-role-input:checked + .wp-role-card {
            border-color: var(--ink-primary);
            background-color: var(--bg-subtle);
            box-shadow: inset 0 0 0 1px var(--ink-primary);
        }

        .wp-role-input:focus-visible + .wp-role-card {
            outline: 2px solid var(--ink-primary);
            outline-offset: 2px;
        }

        /* Editorial Brand Mark */
        .wp-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--ink-primary);
        }

        .wp-brand-text {
            font-family: var(--font-serif);
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        @media (max-width: 640px) {
            .wp-container {
                padding-inline: 1.25rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    @yield('content')

    @stack('scripts')
</body>
</html>
