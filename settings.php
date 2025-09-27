<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/settings_backend.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | <?= htmlspecialchars($current_user['username']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="../../favicon.svg">
    <style>

        :root {
            --bg-primary: #36393f;
            --bg-secondary: #2f3136;
            --bg-tertiary: #202225;
            --text-normal: #dcddde;
            --text-muted: #72767d;
            --accent: #5865f2;
            --interactive: #b9bbbe;
            --premium: #f8d64e;
            --danger: #f04747;
            --success: #3ba55c;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Whitney', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            color: var(--text-normal);
            display: flex;
            min-height: 100vh;
            background-attachment: fixed;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;

            /* <-- THEMES --> */

        <?php
        $theme = $_COOKIE['site_theme'] ?? 'default';
        if ($theme === 'theme1') echo "background-image: url('../app/themes/wallpapers.jpg');";
        elseif ($theme === 'theme2') echo "background-image: url('../app/themes/wallpapers2.jpg');";
        elseif ($theme === 'theme3') echo "background-image: url('../app/themes/wallpapers3.jpg');";
        elseif ($theme === 'theme4') echo "background-image: url('../app/themes/wallpapers4.jpg');";
        elseif ($theme === 'theme5') echo "background-image: url('../app/themes/wallpapers5.jpg');";
        elseif ($theme === 'theme6') echo "background-image: url('../app/themes/wallpapers8.jpg');"; # <-- это аниме тян за 1500 блинксов -->
        elseif ($theme === 'theme7') echo "background-image: url('../app/themes/win11_white.jpg');";
        elseif ($theme === 'theme8') echo "background-image: url('../app/themes/win11_black.jpg'); background-size: 100% 100%;"
        ?>
        }
        .settings-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .settings-sidebar {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: white;
            width: 240px;
            padding: 20px 0;
            overflow-y: auto;
            position: relative;
            z-index: 10;
        }

        .settings-category {
            margin-bottom: 20px;
        }

        .category-title {
            padding: 8px 16px;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .settings-item {
            padding: 10px 16px;
            display: flex;
            align-items: center;
            cursor: pointer;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            color: var(--text-normal);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .settings-item:hover {
            background-color: var(--bg-tertiary);
        }

        .settings-item.active {
            background-color: var(--bg-tertiary);
            border-left-color: var(--accent);
        }

        .settings-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            color: var(--interactive);
            font-size: 15px;
        }

        .settings-item.active i {
            color: var(--accent);
        }

        .settings-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            position: relative;
        }

        .settings-header {
            margin-bottom: 30px;
        }

        .settings-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .settings-header p {
            color: var(--text-muted);
            margin: 0;
            font-size: 14px;
        }

        .settings-section {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            background-color: var(--bg-tertiary);
            border: 1px solid rgba(0, 0, 0, 0.3);
            border-radius: 4px;
            color: var(--text-normal);
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 2px rgba(88, 101, 242, 0.3);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .checkbox-group input {
            margin-right: 10px;
        }

        .btn {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #4752c4;
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-premium {
            background-color: var(--premium);
            color: #000;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .btn-premium:hover {
            background-color: #e6c945;
            transform: translateY(-1px);
        }

        .btn-premium:active {
            transform: translateY(0);
        }

        .btn-back {
            background-color: transparent;
            color: var(--text-normal);
            border: 1px solid var(--text-muted);
            padding: 10px 16px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: var(--interactive);
        }

        .btn-back i {
            margin-right: 8px;
        }

        .divider {
            height: 1px;
            background-color: rgba(79, 84, 92, 0.48);
            margin: 20px 0;
        }

        .avatar-banner-container {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .avatar-container, .banner-container {
            flex: 1;
            min-width: 300px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid var(--accent);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .banner-preview {
            width: 100%;
            height: 150px;
            background-size: cover;
            background-position: center;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 2px solid var(--accent);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .avatar-container .form-group,
        .banner-container .form-group {
            margin-bottom: 15px;
        }

        .avatar-container .btn,
        .banner-container .btn {
            width: 100%;
            margin-top: 10px;
        }

        .theme-preview {
            width: 150px;
            height: 100px;
            margin: 10px;
            border-radius: 5px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .theme-preview:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .theme-preview.selected {
            border-color: var(--accent);
            box-shadow: 0 0 10px var(--accent);
        }

        .theme-preview.premium {
            border: 2px solid var(--premium);
        }

        .theme-preview.premium.selected {
            border-color: var(--premium);
            box-shadow: 0 0 10px var(--premium);
        }

        .theme-label {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            font-size: 13px;
            font-weight: 500;
        }

        .premium-badge {
            display: inline-block;
            background-color: var(--premium);
            color: #000;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            margin-left: 5px;
            text-transform: uppercase;
        }

        .beta-badge {
            display: inline-block;
            background-color: var(--accent);
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 3px;
            margin-left: 8px;
            vertical-align: middle;
            text-transform: uppercase;
        }

        .themes-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background-color: var(--success);
            color: white;
        }

        .tg-buy-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            background-color: #0088cc;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .tg-buy-btn:hover {
            background-color: #0077b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .tg-buy-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .tg-buy-btn i {
            margin-right: 8px;
            font-size: 18px;
        }

        .alert-error {
            background-color: var(--danger);
            color: white;
        }

        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }

        .premium-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 10px;
        }

        .premium-overlay i {
            color: var(--premium);
            font-size: 24px;
            margin-bottom: 5px;
        }

        .premium-plans {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .premium-plan {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            padding: 30px;
            width: 300px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .premium-plan:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .premium-plan.popular {
            border: 2px solid var(--premium);
        }

        .popular-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--premium);
            color: #000;
            font-weight: bold;
            padding: 5px 15px;
            font-size: 12px;
            border-bottom-left-radius: 8px;
        }

        .plan-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--premium);
        }

        .plan-price {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .plan-period {
            font-size: 16px;
            color: var(--text-muted);
        }

        .plan-features {
            margin-bottom: 30px;
        }

        .plan-feature {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .plan-feature i {
            color: var(--premium);
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .current-status {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid var(--premium);
        }

        .status-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--premium);
            display: flex;
            align-items: center;
        }

        .status-title i {
            margin-right: 10px;
        }

        .status-text {
            margin: 0;
            font-size: 14px;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .benefit-icon {
            color: var(--premium);
            font-size: 20px;
            margin-right: 15px;
            margin-top: 3px;
        }

        .benefit-text h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }

        .benefit-text p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        .balance-indicator {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 20px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            font-size: 14px;
            font-weight: 600;
            backdrop-filter: blur(5px);
        }

        .balance-indicator i {
            color: var(--premium);
            margin-right: 8px;
        }

        .topup-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .topup-option {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .topup-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .topup-amount {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--premium);
        }

        .topup-description {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--bg-primary);
            border-radius: 8px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 24px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: var(--text-normal);
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        @media (max-width: 768px) {
            body {
                height: auto;
                min-height: 100vh;
                background: linear-gradient(180deg, rgba(43, 43, 43, 1) 0%, rgba(74, 4, 4, 1) 50%, rgba(64, 64, 64, 1) 100%);
            }

            .settings-container {
                flex-direction: column;
                height: auto;
            }

            .settings-sidebar {
                width: 100%;
                padding: 10px 0;
                position: fixed;
                bottom: 0;
                left: 0;
                z-index: 100;
                border-top: 1px solid var(--bg-tertiary);
                display: flex;
                overflow-x: auto;
            }

            .settings-category {
                display: flex;
                margin-bottom: 0;
                min-width: max-content;
            }

            .avatar-banner-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .avatar-container, .banner-container {
                min-width: 100%;
            }
            
            .avatar-preview {
                width: 100px;
                height: 100px;
            }
            
            .banner-preview {
                height: 120px;
            }

            .category-title {
                display: none;
            }

            .settings-item {
                padding: 10px 16px;
                display: flex;
                align-items: center;
                cursor: pointer;
                border-left: 3px solid transparent;
                transition: all 0.2s ease;
                color: var(--text-normal);
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                gap: 12px;
            }

            .settings-item.active {
                border-left: none;
                border-top: 3px solid var(--accent);
            }

            .settings-item i {
                width: 20px;
                text-align: center;
                color: var(--interactive);
                font-size: 15px;
            }

            .settings-content {
                padding: 20px 15px;
                margin-bottom: 70px;
            }

            .settings-header h1 {
                font-size: 20px;
            }

            .settings-section {
                padding: 15px;
            }

            .avatar-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .avatar-preview {
                width: 80px;
                height: 80px;
                margin-right: 0;
                margin-bottom: 15px;
            }

            .theme-preview {
                width: calc(50% - 20px);
                height: 80px;
                margin: 5px;
            }

            .theme-label {
                font-size: 11px;
                padding: 5px;
            }

            .form-control {
                padding: 12px;
            }

            .btn {
                width: 100%;
                padding: 12px;
            }

            .beta-badge {
                font-size: 10px;
                padding: 1px 4px;
                top: 0;
            }

            .premium-plan {
                width: 100%;
                max-width: 350px;
            }

            .balance-indicator {
                position: static;
                margin-bottom: 20px;
                justify-content: center;
            }

            .btn-back {
                margin-bottom: 15px;
            }
        }

    </style>
</head>
<body>
<div class="settings-container">
    <div class="settings-sidebar">
        <div class="settings-category">
            <div class="category-title">User Settings</div>
            <a href="?page=settings&tab=account" class="settings-item <?= $tab === 'account' ? 'active' : '' ?>">
                <i class="fas fa-user-cog"></i> My Account
            </a>
            <a href="?page=settings&tab=security" class="settings-item <?= $tab === 'security' ? 'active' : '' ?>">
                <i class="fas fa-shield-alt"></i> Security
            </a>
        </div>

        <div class="settings-category">
            <div class="category-title">App Settings</div>
            <a href="?page=settings&tab=appearance" class="settings-item <?= $tab === 'appearance' ? 'active' : '' ?>">
                <i class="fas fa-palette"></i> Appearance <span class="beta-badge">BETA</span>
            </a>
        </div>

        <div class="settings-category">
            <div class="category-title">Premium</div>
            <a href="?page=settings&tab=premium" class="settings-item <?= $tab === 'premium' ? 'active' : '' ?>">
                <i class="fas fa-crown"></i> Premium <span class="beta-badge">BETA</span>
            </a>
            <a href="?page=settings&tab=topup" class="settings-item <?= $tab === 'topup' ? 'active' : '' ?>">
                <i class="fas fa-coins"></i> Top Up
            </a>
        </div>
        <div class="settings-category">
            <a href="index.php" class="settings-item">
                <i class="fas fa-arrow-left"></i> Return
            </a>
        </div>
    </div>

    <div class="settings-content">
        <div class="balance-indicator">
            <i class="fas fa-coins"></i>
            Balance: <?= $current_user['blinks'] ?? 0 ?> blinks
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($tab === 'account'): ?>
    <div class="settings-header">
        <h1>My Account</h1>
        <p>Manage your profile settings</p>
    </div>

    <div class="settings-section">
        <h2 class="section-title">Profile</h2>
        
        <div class="avatar-banner-container">
            <!-- Аватар -->
            <div class="avatar-container">
                <img src="<?= $current_user['avatar_url'] ?: '/assets/default-avatar.jpg' ?>" class="avatar-preview" id="avatarPreview">
                <form method="POST" action="?page=settings" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_avatar">
                    <input type="hidden" name="tab" value="account">
                    <div class="form-group">
                        <label for="avatar">
                            Upload new avatar 
                            <?php if (!$current_user['has_premium']): ?>
                                <span class="beta-badge">JPG/PNG</span>
                            <?php else: ?>
                                <span class="premium-badge">JPG/PNG/GIF</span>
                            <?php endif; ?>
                        </label>
                        <input type="file" id="avatar" name="avatar" class="form-control" 
                            accept="<?= $current_user['has_premium'] ? 'image/*' : 'image/jpeg,image/png' ?>">
                        <?php if (!$current_user['has_premium']): ?>
                            <small style="color: var(--text-muted); font-size: 12px;">
                                GIF avatars available for <a href="?page=settings&tab=premium" style="color: var(--premium);">premium users</a>
                            </small>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn">Update Avatar</button>
                </form>
            </div>

            <!-- Баннер -->
            <div class="banner-container">
                <div class="banner-preview" style="background-image: url('<?= $current_user['banner_url'] ?: '/assets/default-banner.jpg' ?>')"></div>
                <form method="POST" action="?page=settings" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_banner">
                    <input type="hidden" name="tab" value="account">
                    <div class="form-group">
                        <label for="banner">
                            Upload new banner 
                            <?php if (!$current_user['has_premium']): ?>
                                <span class="beta-badge">JPG/PNG</span>
                            <?php else: ?>
                                <span class="premium-badge">JPG/PNG/GIF</span>
                            <?php endif; ?>
                        </label>
                        <input type="file" id="banner" name="banner" class="form-control" 
                            accept="<?= $current_user['has_premium'] ? 'image/*' : 'image/jpeg,image/png' ?>">
                        <?php if (!$current_user['has_premium']): ?>
                            <small style="color: var(--text-muted); font-size: 12px;">
                                GIF banners available for <a href="?page=settings&tab=premium" style="color: var(--premium);">premium users</a>
                            </small>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn">Update Banner</button>
                </form>
            </div>
                        </div>

        <!-- Основная информация -->
        <form method="POST" action="?page=settings">
            <input type="hidden" name="action" value="update_account">
            <input type="hidden" name="tab" value="account">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($current_user['username']) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($current_user['email']) ?>">
            </div>
            <div class="form-group">
                <label for="bio">About Me <span class="beta-badge">BETA</span></label>
                <textarea id="bio" name="bio" class="form-control" rows="3"><?= htmlspecialchars($current_user['bio'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>

        <?php elseif ($tab === 'appearance'): ?>
            <div class="settings-header">
                <h1>Appearance <span class="beta-badge">BETA</span></h1>
                <p>Customize the look of the application</p>
            </div>

            <div class="settings-section">
                <h2 class="section-title">Theme</h2>
                <form method="POST" action="?page=settings" id="themeForm">
                    <input type="hidden" name="action" value="update_appearance">
                    <input type="hidden" name="tab" value="appearance">
                    <input type="hidden" name="theme" id="selectedTheme" value="<?= $_COOKIE['site_theme'] ?? 'default' ?>">

                    <div class="themes-grid">
                        <div class="theme-preview <?= ($_COOKIE['site_theme'] ?? 'default') === 'default' ? 'selected' : '' ?>"
                             onclick="selectTheme('default')"
                             style="    background: #0f2027;
background-image:
    linear-gradient(30deg, #203a43 12%, transparent 12.5%, transparent 87%, #203a43 87.5%, #203a43),
    linear-gradient(150deg, #203a43 12%, transparent 12.5%, transparent 87%, #203a43 87.5%, #203a43),
    linear-gradient(30deg, #203a43 12%, transparent 12.5%, transparent 87%, #203a43 87.5%, #203a43),
    linear-gradient(150deg, #203a43 12%, transparent 12.5%, transparent 87%, #203a43 87.5%, #203a43),
    linear-gradient(60deg, #2c536477 25%, transparent 25.5%, transparent 75%, #2c536477 75%, #2c536477),
    linear-gradient(60deg, #2c536477 25%, transparent 25.5%, transparent 75%, #2c536477 75%, #2c536477);">
                            <div class="theme-label">Default</div>
                        </div>

                        <div class="theme-preview <?= ($_COOKIE['site_theme'] ?? '') === 'theme1' ? 'selected' : '' ?>"
                             onclick="selectTheme('theme1')"
                             style="background: url('../app/themes/wallpapers.jpg') center/cover;">
                            <div class="theme-label">The dark forest</div>
                        </div>

                        <div class="theme-preview <?= ($_COOKIE['site_theme'] ?? '') === 'theme2' ? 'selected' : '' ?>"
                             onclick="selectTheme('theme2')"
                             style="background: url('../app/themes/wallpapers2.jpg') center/cover;">
                            <div class="theme-label">Helldiver</div>
                        </div>

                        <div class="theme-preview <?= ($_COOKIE['site_theme'] ?? '') === 'theme3' ? 'selected' : '' ?>"
                             onclick="selectTheme('theme3')"
                             style="background: url('../app/themes/wallpapers3.jpg') center/cover;">
                            <div class="theme-label">Night City</div>
                        </div>

                        <div class="theme-preview <?= ($_COOKIE['site_theme'] ?? '') === 'theme4' ? 'selected' : '' ?>"
                             onclick="selectTheme('theme4')"
                             style="background: url('../app/themes/wallpapers4.jpg') center/cover;">
                            <div class="theme-label">Tyans Duo</div>
                        </div>

                        <div class="theme-preview premium <?= ($_COOKIE['site_theme'] ?? '') === 'theme5' ? 'selected' : '' ?>"
                             onclick="<?= $current_user['has_premium'] ? "selectTheme('theme5')" : "showPremiumModal()" ?>"
                             style="background: url('../app/themes/wallpapers5.jpg') center/cover;">
                            <div class="theme-label">
                                Japan <span class="premium-badge">Premium</span>
                            </div>
                            <?php if (!$current_user['has_premium']): ?>
                                <div class="premium-overlay">
                                    <i class="fas fa-lock"></i>
                                    Premium Only
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="theme-preview premium <?= ($_COOKIE['site_theme'] ?? '') === 'theme7' ? 'selected' : '' ?>"
                             onclick="<?= $current_user['has_premium'] ? "selectTheme('theme7')" : "showPremiumModal()" ?>"
                             style="background: url('../app/themes/win11_white.jpg') center/cover;">
                            <div class="theme-label">
                                Win11 White <span class="premium-badge">Premium</span>
                            </div>
                            <?php if (!$current_user['has_premium']): ?>
                                <div class="premium-overlay">
                                    <i class="fas fa-lock"></i>
                                    Premium Only
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="theme-preview premium <?= ($_COOKIE['site_theme'] ?? '') === 'theme8' ? 'selected' : '' ?>"
                             onclick="<?= $current_user['has_premium'] ? "selectTheme('theme8')" : "showPremiumModal()" ?>"
                             style="background: url('../app/themes/win11_black.jpg') center/cover;">
                            <div class="theme-label">
                                Win11 Black <span class="premium-badge">Premium</span>
                            </div>
                            <?php if (!$current_user['has_premium']): ?>
                                <div class="premium-overlay">
                                    <i class="fas fa-lock"></i>
                                    Premium Only
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Theme 6 (Animated) -->
                        <div class="theme-preview premium <?= ($_COOKIE['site_theme'] ?? '') === 'theme6' ? 'selected' : '' ?>"
                             onclick="<?= $current_user['blinks'] >= 1500 ? "selectTheme('theme6')" : "alert('You need at least 1500 blinks to use this theme')" ?>"
                             style="background: url('../app/themes/wallpapers8.jpg') center/cover no-repeat;">
                            <div class="theme-label">
                                BestTyan <span style="color: var(--premium)">(1500 blinks)</span>
                            </div>
                            <?php if ($current_user['blinks'] < 1500): ?>
                                <div class="premium-overlay">
                                    <i class="fas fa-lock"></i>
                                    <?= (1500 - $current_user['blinks']) ?> blinks needed
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
            </div>
                    <div class="divider"></div>
                    <button type="submit" class="btn">Save Changes</button>
                </form>
            </div>

            <div class="divider"></div>

        <?php elseif ($tab === 'security'): ?>
            <div class="settings-header">
                <h1>Security</h1>
                <p>Manage your account security</p>
            </div>

            <div class="settings-section">
                <h2 class="section-title">Change Password</h2>
                <form method="POST" action="?page=settings">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="tab" value="security">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn">Change Password</button>
                </form>
            </div>

        <?php elseif ($tab === 'premium'): ?>
            <div class="settings-header">
                <h1>
                    <i class="fas fa-crown" style="margin-right: 10px;"></i>
                    Premium Subscription <span class="beta-badge">BETA</span>
                </h1>
                <p>Unlock all platform features with Premium subscription</p>
            </div>

            <?php if ($current_user['has_premium']): ?>
                <div class="current-status">
                    <h2 class="status-title"><i class="fas fa-crown"></i> You have an active Premium subscription</h2>
                    <p class="status-text">
                        Your subscription is valid until <?= date('d.m.Y', strtotime($current_user['premium_until'])) ?>.
                        <?php if (strtotime($current_user['premium_until']) < time()): ?>
                            <span style="color: var(--danger)">(Expired)</span>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="settings-section">
                <h2 class="section-title">Subscription Plans</h2>

                <div class="premium-plans">
                    <div class="premium-plan">
                        <h2 class="plan-name">1 Month</h2>
                        <div class="plan-price">2500 <span class="plan-period">blinks</span></div>

                        <div class="plan-features">
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>All Premium features</span>
                            </div>
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>Priority support</span>
                            </div>
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>Exclusive themes</span>
                            </div>
                        </div>

                        <form method="POST" action="?page=settings&tab=premium">
                            <input type="hidden" name="purchase_premium_blinks" value="1month">
                            <button type="submit" class="btn-premium">Buy for 2500 blinks</button>
                        </form>
                    </div>

                    <div class="premium-plan popular">
                        <div class="popular-badge">Popular</div>
                        <h2 class="plan-name">3 Months</h2>
                        <div class="plan-price">5000 <span class="plan-period">blinks</span></div>
                        <div style="color: var(--premium); font-weight: 600; margin-bottom: 15px;">Save 15%</div>

                        <div class="plan-features">
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>All Premium features</span>
                            </div>
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>Priority support</span>
                            </div>
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>Exclusive themes</span>
                            </div>
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>Bonus features</span>
                            </div>
                        </div>

                        <form method="POST" action="?page=settings&tab=premium">
                            <input type="hidden" name="purchase_premium_blinks" value="3months">
                            <button type="submit" class="btn-premium">Buy for 5000 blinks</button>
                        </form>
                    </div>

                    <div class="premium-plan">
                        <h2 class="plan-name">1 Year</h2>
                        <div class="plan-price">10000 <span class="plan-period">blinks</span></div>
                        <div style="color: var(--premium); font-weight: 600; margin-bottom: 15px;">Save 30%</div>

                        <div class="plan-features">
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>All Premium features</span>
                            </div>
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>Priority support</span>
                            </div>
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>Exclusive themes</span>
                            </div>
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>Bonus features</span>
                            </div>
                            <div class="plan-feature">
                                <i class="fas fa-check"></i>
                                <span>Personal manager</span>
                            </div>
                        </div>

                        <form method="POST" action="?page=settings&tab=premium">
                            <input type="hidden" name="purchase_premium_blinks" value="1year">
                            <button type="submit" class="btn-premium">Buy for 1000 blinks</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h2 class="section-title"><i class="fas fa-star"></i> Premium Benefits <span class="beta-badge">BETA</span></h2>

                <div class="benefits-grid">
                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-palette"></i></div>
                        <div class="benefit-text">
                            <h3>Exclusive themes</h3>
                            <p>Access to unique themes not available to regular users</p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="benefit-text">
                            <h3>Enhanced security</h3>
                            <p>Additional privacy and account security settings</p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="benefit-text">
                            <h3>Increased storage</h3>
                            <p>10 GB cloud storage instead of standard 2 GB</p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-headset"></i></div>
                        <div class="benefit-text">
                            <h3>Priority support</h3>
                            <p>Your requests are processed first</p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-bolt"></i></div>
                        <div class="benefit-text">
                            <h3>Early access to features</h3>
                            <p>Test new features before official release</p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-user-tie"></i></div>
                        <div class="benefit-text">
                            <h3>Personal manager</h3>
                            <p>Available for annual subscriptions</p>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($tab === 'topup'): ?>
            <button onclick="window.history.back()" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </button>

        <div class="settings-header">
            <h1>
                <i class="fas fa-coins" style="margin-right: 10px;"></i>
                Balance Top Up
            </h1>
            <p>Top up your blinks balance to purchase Premium subscription</p>
        </div>

        <div class="settings-section">
            <a href="https://t.me/devblox_bot" class="tg-buy-btn">
                <i class="fab fa-telegram"></i> Buy In Telegram
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal" id="premiumModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fas fa-crown"></i> Premium Subscription Required</h2>
            <button class="modal-close" onclick="closeModal('premiumModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p>This theme is only available for users with Premium subscription.</p>
            <p>Purchase Premium subscription to get access to all exclusive features including this theme.</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="closeModal('premiumModal')">Close</button>
            <a href="?page=settings&tab=premium" class="btn-premium">Go to Premium</a>
        </div>
    </div>
</div>

<script src="settings_backend.js"></script>
</body>
</html>