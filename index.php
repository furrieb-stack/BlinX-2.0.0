<?php
require_once("backend.php");

$page_title = "BlinX";

if ($page === 'profile') {
    $profile_user = isset($_GET['user_id']) ? getUser($_GET['user_id']) : $current_user;
    if ($profile_user) {
        $page_title = htmlspecialchars($profile_user['username']) . " | BlinX";
    }
} elseif ($page === 'community' && isset($_GET['slug'])) {
    $community = getCommunity($_GET['slug']);
    if ($community) {
        $page_title = htmlspecialchars($community['name']) . " | BlinX";
    }
} elseif ($page === 'communities') {
    $page_title = "Communities | BlinX";
} elseif ($page === 'create_community') {
    $page_title = "Create Community | BlinX";
} elseif ($page === 'feed') {
    $page_title = "Feed | BlinX";
} elseif ($page === 'search') {
    $page_title = "Search | BlinX";
} elseif ($page === 'admin') {
    $page_title = "Moderation | BlinX";
} elseif ($page === 'feedback') {
    $page_title = "Feedback | BlinX";
} elseif ($page === 'tag' && isset($_GET['tag'])) {
    $page_title = "#" . htmlspecialchars($_GET['tag']) . " | BlinX";
} elseif ($page === 'login') {
    $page_title = "Login | BlinX";
} elseif ($page === 'register') {
    $page_title = "Registration | BlinX";
} elseif ($page === 'verify') {
    $page_title = "Email Verification | BlinX";
} elseif ($page === 'forgot_password') {
    $page_title = "Password Recovery | BlinX";
} elseif ($page === 'reset_password') {
    $page_title = "Reset Password | BlinX";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <title><?= $page_title ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    
    <style>

        :root {
            --bg-color: #0f0f15;
            --card-bg: #1a1a24;
            --card-hover: #212130;
            --text-color: #f0f0ff;
            --text-secondary: #b0b0c0;
            --accent-color: #6a5acd;
            --accent-light: #7b68ee;
            --success-color: #4caf50;
            --error-color: #f44336;
            --warning-color: #ff9800;
            --info-color: #2196F3;
            --border-radius: 12px;
            --small-radius: 6px;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            --transition: all 0.3s ease;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {

            background: #1a1a1a url('wallpapers1.png') center/cover no-repeat fixed;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        <?php
        switch ($theme) {
            case 'theme1':
            echo "background: url('themes/wallpapers.jpg') center/cover no-repeat fixed;";
            break;
            case 'theme2':
            echo "background: url('themes/wallpapers2.jpg') center/cover no-repeat fixed;";
            break;
            case 'theme3':
            echo "background: url('themes/wallpapers3.jpg') center/cover no-repeat fixed;";
            break;
            case 'theme4':
            echo "background: url('themes/wallpapers4.jpg') center/cover no-repeat fixed;";
            break;
            case 'theme5':
            echo "background: url('themes/wallpapers5.jpg') center/cover no-repeat fixed;";
            break;
            case 'theme6':
            echo "background: url('themes/wallpapers8.jpg') center/cover no-repeat fixed;";
            break;
            case 'theme7':
            echo "background: url('themes/win11_white.jpg') center/cover no-repeat fixed;";
            break;
            case 'theme8':
            echo "background: url('themes/win11_black.jpg') center/cover no-repeat fixed;";
            break;
            default:
                    echo "background: #0f2027;
            background-image:
                    linear-gradient(30deg, #203a43 12%, transparent 12.5%, transparent 87%, #203a43 87.5%, #203a43),
                    linear-gradient(150deg, #203a43 12%, transparent 12.5%, transparent 87%, #203a43 87.5%, #203a43),
                    linear-gradient(30deg, #203a43 12%, transparent 12.5%, transparent 87%, #203a43 87.5%, #203a43),
                    linear-gradient(150deg, #203a43 12%, transparent 12.5%, transparent 87%, #203a43 87.5%, #203a43),
                    linear-gradient(60deg, #2c536477 25%, transparent 25.5%, transparent 75%, #2c536477 75%, #2c536477),
                    linear-gradient(60deg, #2c536477 25%, transparent 25.5%, transparent 75%, #2c536477 75%, #2c536477);
            background-size: 80px 140px;
            background-position: 0 0, 0 0, 40px 70px, 40px 70px, 0 0, 40px 70px;";
        }
            ?>
        }

        a {
            color: var(--accent-color);
            text-decoration: none;
            transition: var(--transition);
        }

        a:hover {
            color: var(--accent-light);
            text-decoration: underline;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: background-color 0.3s ease;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            overflow: hidden;
        }

        .user-context-menu {
            position: absolute;
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            z-index: 1000;
            display: none;
            min-width: 200px;
            overflow: hidden;
        }

        .user-context-menu-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h1 {

            user-select: text;

        }

        .user-context-menu-item:hover {
            background: rgba(106, 90, 205, 0.1);
        }

        .user-context-menu-item i {
            width: 20px;
            text-align: center;
        }

        .settings-menu {
            position: relative;
        }

        .settings-dropdown {
            position: absolute;
            right: 0;
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 15px;
            width: 250px;
            display: none;
            z-index: 100;
        }

        .settings-menu:hover .settings-dropdown {
            display: block;
        }

        .settings-item {
            padding: 8px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--accent-color);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .btn-premium {
            background: linear-gradient(45deg, #ffd700, #ff9800);
            color: #000;
            width: 100%;
            justify-content: center;
        }

        .premium-active {
            color: #ffd700;
            text-align: center;
            flex-direction: column;
            gap: 5px;
        }

        .premium-active small {
            color: var(--text-secondary);
            font-size: 12px;
        }

        .premium-badge {
            color: #ffd700;
            margin-left: 5px;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        nav a {
            color: var(--text-secondary);
            font-weight: 500;
            padding: 8px 12px;
            border-radius: var(--small-radius);
        }

        nav a:hover {
            color: var(--text-color);
            background: rgba(255, 255, 255, 0.05);
            text-decoration: none;
        }

        nav a.active {
            color: var(--accent-color);
            background: rgba(106, 90, 205, 0.1);
        }

        .premium-badge {
            position: relative;
            font-size: 1.5rem;
            color: gold;
            cursor: pointer;
        }

        .premium-badge[data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            font-size: 0.875rem;
            border-radius: 4px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0s 0.2s;
        }

        .premium-badge[data-tooltip]:hover::after {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.2s ease;
        }

        .card {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--shadow);
            user-select: none;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--text-color);
            text-align: center;
            user-select: none;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--small-radius);
            color: var(--text-color);
            font-size: 16px;
            transition: var(--transition);
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(106, 90, 205, 0.3);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: var(--small-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn:hover {
            background: var(--accent-light);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(106, 90, 205, 0.3);
            text-decoration: none;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #ffffff;
            color: #ffffff;
        }

        .btn-outline:hover {
            background: rgba(106, 90, 205, 0.1);
        }

        .btn-block {
            width: 100%;
            display: block;
        }

        .btn-danger {
            background: var(--error-color);
        }

        .btn-danger:hover {
            background: #e53935;
        }

        .btn-success {
            background: var(--success-color);
        }

        .btn-success:hover {
            background: #43A047;
        }

        .btn-info {
            background: var(--info-color);
        }

        .btn-info:hover {
            background: #1E88E5;
        }

        .alert {
            padding: 15px;
            border-radius: var(--small-radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: var(--success-color);
        }

        .alert-error {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: var(--error-color);
        }

        .alert-warning {
            background: rgba(255, 152, 0, 0.1);
            border: 1px solid rgba(255, 152, 0, 0.3);
            color: var(--warning-color);
        }

        .alert-info {
            background: rgba(33, 150, 243, 0.1);
            border: 1px solid rgba(33, 150, 243, 0.3);
            color: var(--info-color);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
            margin: 30px 0;
        }

        .sidebar {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: var(--border-radius);
            padding: 20px;
            height: fit-content;
            position: sticky;
            top: 80px;
        }

        .profile-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            color: white;
            margin-bottom: 15px;
            background-size: cover;
            background-position: center;
        }

        .profile-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-email {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 15px;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 10px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: var(--small-radius);
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(106, 90, 205, 0.1);
            color: var(--accent-color);
            text-decoration: none;
        }

        .sidebar-nav i {
            width: 20px;
            text-align: center;
        }

        .main-content {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: var(--border-radius);
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            user-select: none;
        }

        p {
            user-select: text;
        }

        .verification-input {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .verification-input input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        footer {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 30px 0;
            margin-top: auto;
            text-align: center;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .copyright {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .profile-banner {
            width: 100%;
            height: 200px;
            background-color: var(--accent-color);
            background-size: cover;
            background-position: center;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .profile-header {
            position: relative;
            margin-bottom: 80px;
        }

        .profile-avatar-container {
            position: absolute;
            bottom: -40px;
            left: 20px;
            display: flex;
            align-items: flex-end;
            gap: 20px;
        }

        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            color: white;
            background-size: cover;
            background-position: center;
            border: 4px solid var(--card-bg);
        }

        .profile-stats {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 15px;
            padding: 10px;
        }


        .profile-stat {
            text-align: center;
        }

        .profile-stat-value {
            font-size: 24px;
            font-weight: 700;
        }

        .profile-stat-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .verified-badge {
            color: #1DA1F2;
            margin-left: 5px;
        }

        .moderator-badge {
            color: #4CAF50;
            margin-left: 5px;
        }

        .admin-badge {
            color: #F44336;
            margin-left: 5px;
        }

        .beta-tester-badge {
            color: #FF9800;
            margin-left: 5px;
        }

        .supporter-badge {
            color: #9C27B0;
            margin-left: 5px;
        }

        .vip-badge {
            color: #FFD700;
            margin-left: 5px;
        }

        .follow-btn {
            margin-left: auto;
        }

        .tab-container {
            display: flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: var(--transition);
        }

        .tab.active {
            border-bottom-color: var(--accent-color);
            color: var(--accent-color);
        }

        .tab:hover {
            color: var(--accent-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-input {
            display: flex;
            gap: 10px;
        }

        .search-results {
            display: grid;
            gap: 15px;
        }

        .search-result {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: var(--card-bg);
            border-radius: var(--small-radius);
        }

        .search-result-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            color: white;
            background-size: cover;
            background-position: center;
        }

        .search-result-info {
            flex: 1;
        }

        .search-result-name {
            font-weight: 600;
        }

        .search-result-followers {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .post-options {
            margin-left: auto;
            position: relative;
        }

        .post-options-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 5px;
        }

        .post-options-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: var(--card-bg);
            border-radius: var(--small-radius);
            box-shadow: var(--shadow);
            padding: 10px 0;
            min-width: 150px;
            z-index: 100;
            display: none;
        }

        .post-options:hover .post-options-menu {
            display: block;
        }

        .post-option {
            padding: 8px 15px;
            cursor: pointer;
            transition: var(--transition);
        }

        .post-option:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .error-message {
            color: var(--error-color);
            font-size: 12px;
            margin-top: 5px;
            display: block;
            font-weight: 500;
        }

        .post-option.delete {
            color: var(--error-color);
        }

        .comment-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .comment-action {
            display: flex;
            align-items: center;
            gap: 3px;
            color: var(--text-secondary);
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
        }

        .comment-action:hover {
            color: var(--accent-color);
        }

        .comment-action.liked {
            color: var(--accent-color);
        }

        .views-count {
            color: var(--text-secondary);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .feed {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .post {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow);
            user-select: text;
        }


        .post-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .post-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            color: white;
            background-size: cover;
            background-position: center;
        }

        .post-user {
            font-weight: 600;
        }

        .post-time {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .post-content {
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .post-content a {
            color: #4a90e2;
            text-decoration: none;
        }

        .post-content a:hover {
            text-decoration: underline;
        }

        .verified-badge,
        .moderator-badge,
        .admin-badge {
            position: relative;
            cursor: help;
            margin-left: 3px;
            font-size: 0.9em;
        }

        .verified-badge::after,
        .moderator-badge::after,
        .admin-badge::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            pointer-events: none;
            z-index: 100;
        }

        .verified-badge:hover::after,
        .moderator-badge:hover::after,
        .admin-badge:hover::after {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 5px);
        }

        .verified-badge {
            color: #00d0ff;
        }

        .verified-badge::after {
            background-color: #1DA1F2;
            color: white;
            content: "Verified";
        }

        .moderator-badge {
            color: #4CAF50;
        }

        .moderator-badge::after {
            background-color: #4CAF50;
            color: white;
            content: "Moderator";
        }

        .admin-badge {
            color: #F44336;
        }

        .admin-badge::after {
            background-color: #F44336;
            color: white;
            content: "Administrator";
        }

        [class$="-badge"]::after {
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            font-weight: normal;
            text-transform: none;
            letter-spacing: normal;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        [class$="-badge"]:hover::after {
            animation: fadeIn 0.2s ease-out;
        }

        .post-content small {
            font-size: 0.8em;
            opacity: 0.8;
        }

        .post-header {
            font-size: 1.2em;
            margin: 10px 0;
            font-weight: bold;
        }

        .post-files {
            display: grid;
            gap: 10px;
            margin-bottom: 15px;
        }

        .post-file {
            max-width: 100%;
            border-radius: var(--small-radius);
        }

        .post-file-image {
            max-width: 100%;
            max-height: 400px;
            border-radius: var(--small-radius);
        }

        .post-file-document {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--small-radius);
        }

        .post-actions {
            display: flex;
            gap: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
        }

        .post-action {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .post-action:hover {
            color: var(--accent-color);
        }

        .post-action.liked {
            color: var(--accent-color);
        }

        .comments {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .comment {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-size: cover;
            background-position: center;
        }

        .comment-content {
            flex: 1;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .auth-card {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: var(--border-radius);
            padding: 40px 32px;
            box-shadow: var(--shadow);
            width: 100%;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-color);
            letter-spacing: -0.5px;
        }

        .auth-header p {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .auth-form .input-group {
            margin-bottom: 24px;
        }

        .auth-form label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            letter-spacing: 0.3px;
        }

        .auth-form input {
            width: 100%;
            padding: 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--small-radius);
            color: var(--text-color);
            font-size: 16px;
            transition: var(--transition);
            box-sizing: border-box;
        }

        .auth-form input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(106, 90, 205, 0.3);
        }

        .auth-form input:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-auth {
            width: 100%;
            padding: 16px;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: var(--small-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 8px;
        }

        .btn-auth:hover {
            background: var(--accent-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(106, 90, 205, 0.3);
        }

        .btn-auth:active {
            transform: translateY(0);
        }

        .auth-links {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .auth-links a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .auth-switch {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .auth-switch a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-switch a:hover {
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: var(--text-secondary);
            font-size: 12px;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .divider::before {
            margin-right: 12px;
        }

        .divider::after {
            margin-left: 12px;
        }

        .social-login {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px;
            border-radius: var(--small-radius);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            color: white;
        }

        .social-btn i {
            margin-right: 8px;
            font-size: 16px;
        }

        .social-btn.google {
            background-color: #4285F4;
        }

        .social-btn.google:hover {
            background-color: #357ae8;
            transform: translateY(-1px);
        }

        .social-btn.apple {
            background-color: #000;
        }

        .social-btn.apple:hover {
            background-color: #333;
            transform: translateY(-1px);
        }

        .social-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .social-btn:disabled:hover {
            background-color: inherit;
        }

        .auth-form .error-message {
            color: var(--error-color);
            font-size: 12px;
            margin-top: 5px;
            display: block;
            font-weight: 500;
        }

        .auth-form input:invalid {
            border-color: var(--error-color);
        }

        .auth-form input:valid {
            border-color: var(--success-color);
        }

        .btn-login {
            width: 100%;
            padding: 14px 24px;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: var(--small-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: var(--accent-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(106, 90, 205, 0.3);
            text-decoration: none;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        .forgot-password-link:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }

        .auth-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .auth-footer a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .comment-user {
            font-weight: 600;
            font-size: 14px;
        }

        .comment-text {
            margin-top: 5px;
        }

        .comment-time {
            color: var(--text-secondary);
            font-size: 12px;
            margin-top: 5px;
        }

        .new-comment {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .new-comment-input {
            flex: 1;
        }

        .tag {
            color: var(--accent-color);
            text-decoration: none;
        }

        .tag:hover {
            text-decoration: underline;
        }

        .user-preview {
            position: fixed;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: var(--border-radius);
            padding: 15px;
            box-shadow: var(--shadow);
            z-index: 1000;
            width: 300px;
            display: none;
            transition: all 0.3s ease;
            pointer-events: auto;
        }

        .employee-badge {
            color: #6a5acd;
            margin-left: 5px;
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .employee-badge::before {
            content: 'B';
            font-family: 'Inter', Arial, sans-serif;
            font-weight: 800;
            font-size: 0.8em;
            background: linear-gradient(135deg, #6a5acd, #7b68ee);
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .employee-badge[data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            background: #6a5acd;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            pointer-events: none;
            z-index: 100;
        }

        .employee-badge[data-tooltip]:hover::after {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 5px);
        }

        .user-preview::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 20px;
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid var(--card-bg);
        }

        .user-preview-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: white;
            background-size: cover;
            background-position: center;
            margin-bottom: 10px;
        }

        .user-preview-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .user-preview-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }

        .user-preview-stat {
            text-align: center;
        }

        .user-preview-stat-value {
            font-weight: 600;
        }

        .user-preview-stat-label {
            color: var(--text-secondary);
            font-size: 12px;
        }

        .user-preview-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .file-upload {
            margin-top: 15px;
        }

        .file-upload-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            background: rgba(106, 90, 205, 0.1);
            border-radius: var(--small-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .file-upload-label:hover {
            background: rgba(106, 90, 205, 0.2);
        }

        .file-upload-input {
            display: none;
        }

        .file-preview {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .file-preview-item {
            position: relative;
        }

        .file-preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: var(--small-radius);
        }

        .file-preview-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--error-color);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }

        #verification_code {
            padding: 15px;
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-color);
            font-weight: bold;
            margin: 0 auto;
            display: block;
        }

        #verification_code:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(106, 90, 205, 0.3);
        }

        .verification-code-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .verification-code-input {
            width: 40px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--small-radius);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-color);
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                margin-bottom: 20px;
            }

            .auth-container {
                padding: 0 15px;
            }
            
            .auth-card {
                padding: 24px;
            }
            
            .auth-header h1 {
                font-size: 20px;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            .profile-avatar-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .follow-btn {
                margin-left: 0;
                margin-top: 10px;
            }
        }

    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="?page=home" class="logo">
                <span class="logo-icon">
                    <img src="../favicon.svg" alt="Logo" />
                    </span>
                    <span>BlinX</span>
                </a>
                <nav>
                    <ul>
                <?php if (isLoggedIn()): ?>
                    <li><a href="?page=feed" class="<?= $page === 'feed' ? 'active' : '' ?>"><i class="fas fa-home"></i> <span class="sr-only">Main</span></a></li>
                    <li><a href="?page=profile" class="<?= $page === 'profile' ? 'active' : '' ?>"><i class="fas fa-user"></i> <span class="sr-only">Profile</span></a></li>
                    <li><a href="?page=communities" class="<?= $page === 'communities' || $page === 'community' || $page === 'create_community' ? 'active' : '' ?>"><i class="fas fa-users"></i> <span class="sr-only">Communities</span></a></li>
                    <li><a href="?page=feedback" class="<?= $page === 'feedback' ? 'active' : '' ?>"><i class="fas fa-comment-alt"></i> <span class="sr-only">Feedback</span></a></li>
                    <li><a href="?page=search" class="<?= $page === 'search' ? 'active' : '' ?>"><i class="fas fa-search"></i> <span class="sr-only">Search</span></a></li>
                    <li><a href="support" target="_blank" rel="noopener noreferrer"><i class="fas fa-question-circle"></i> <span class="sr-only">Support</span></a></li>
                    <li>
                        <a href="settings.php" class="<?= $page === 'settings' ? 'active' : '' ?>">
                            <i class="fas fa-cog"></i> <span class="sr-only">Settings</span>
                        </a>
                    </li>
                    <?php if ($current_user['is_admin'] || $current_user['is_moderator']): ?>
                        <li><a href="?page=admin" class="<?= $page === 'admin' ? 'active' : '' ?>"><i class="fas fa-shield-alt"></i> <span class="sr-only">Moderation</span></a></li>
                    <?php endif; ?>
                    <li><a href="?action=logout"><i class="fas fa-sign-out-alt"></i> <span class="sr-only">Logout</span></a></li>
                        <?php else: ?>
                            <li><a href="?page=home" class="<?= $page === 'home' ? 'active' : '' ?>">Main</a></li>
                            <li><a href="support" target="_blank" rel="noopener noreferrer">Support</a></li>
                            <li><a href="?page=login" class="<?= $page === 'login' ? 'active' : '' ?>">Login</a></li>
                            <li><a href="?page=register" class="<?= $page === 'register' ? 'active' : '' ?>">Registration</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($page === 'home'): ?>
            <div class="auth-container">
                <div class="card">
                    <h2 class="card-title">Welcome to BlinX</h2>
                    <p style="text-align: center; margin-bottom: 30px; color: var(--text-secondary);">
                        Join our community and chat with your friends!
                    </p>
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <a href="?page=register" class="btn">Registration</a>
                        <a href="?page=login" class="btn btn-outline">Login</a>
                    </div>
                </div>
            </div>
        
        <?php elseif ($page === 'register' && !isLoggedIn()): ?>
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <h1>Create account</h1>
                        <p>Join our Community!</p>
                    </div>

                    <form method="post" action="?action=register" class="auth-form" enctype="multipart/form-data">
                        <div class="input-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" 
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autocomplete="off">
                            <?php if (isset($errors['username'])): ?>
                                <small class="error-message"><?= htmlspecialchars($errors['username']) ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" 
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <small class="error-message"><?= htmlspecialchars($errors['email']) ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="input-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" required>
                            <?php if (isset($errors['password'])): ?>
                                <small class="error-message"><?= htmlspecialchars($errors['password']) ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="input-group">
                            <label for="confirm_password">Verify password</label>
                            <input type="password" name="confirm_password" id="confirm_password" required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <small class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></small>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn-auth">Register</button>
                    </form>

                    <p class="auth-switch">
                        Already have account? <a href="?page=login">Login</a>
                    </p>
                </div>
            </div>

        
        <?php elseif ($page === 'login' && !isLoggedIn()): ?>
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <h1>Welcome!</h1>
                        <p>We are so glad to see you again!</p>
                    </div>

                    <form method="post" action="?action=login" class="auth-form">
                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" 
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="off">
                        </div>

                        <div class="input-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" required>
                        </div>

                        <button type="submit" class="btn-auth">Login</button>

                        <div class="auth-footer">
                            <a href="?page=forgot_password">Forgot password?</a>
                        </div>

                        <div class="divider">OR</div>

                        <div class="social-login">
                            <button type="button" class="social-btn google" disabled>
                                <i class="fab fa-google"></i> Login with Google (Soon)
                            </button>
                            <button type="button" class="social-btn apple" disabled>
                                <i class="fab fa-apple"></i> Login with Apple (Soon)
                            </button>
                        </div>
                    </form>

                    <p class="auth-switch">
                        Dont have a account? <a href="?page=register">Register</a>
                    </p>
                </div>
            </div>
        
        <?php elseif ($page === 'forgot_password' && !isLoggedIn()): ?>
            <div class="auth-container">
                <div class="card">
                    <h2 class="card-title">Password Recovery</h2>
                    <form method="post" action="?action=forgot_password">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" required>
                            <small style="color: var(--text-secondary); margin-top: 5px; display: block;">
                                A password recovery link will be sent to your email.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-block">Send link</button>
                    </form>
                    <p style="text-align: center; margin-top: 20px; color: var(--text-secondary);">
                        <a href="?page=login">Go back to the entrance</a>
                    </p>
                </div>
            </div>
        
        <?php elseif ($page === 'reset_password' && !isLoggedIn()): ?>
            <?php
                $email = $_GET['email'] ?? '';
                $token = $_GET['token'] ?? '';
                
                if (empty($email) || empty($token)) {
                    echo '<div class="alert alert-error">Неверная ссылка для сброса пароля</div>';
                } elseif (!validatePasswordResetToken($email, $token)) {
                    echo '<div class="alert alert-error">Ссылка для сброса пароля недействительна или устарела</div>';
                } else {
            ?>
            <div class="auth-container">
                <div class="card">
                    <h2 class="card-title">Password Reset</h2>
                    <form method="post" action="?action=reset_password">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        
                        <div class="form-group">
                            <label for="new_password">New password</label>
                            <input type="password" name="new_password" id="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Password confirmation</label>
                            <input type="password" name="confirm_password" id="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-block">Change Password</button>
                    </form>
                </div>
            </div>
            <?php } ?>
        
            <?php elseif ($page === 'verify'): ?>
    <div class="auth-container">
        <div class="card">
            <h2 class="card-title">Email confirmation</h2>
            <p style="text-align: center; margin-bottom: 20px; color: var(--text-secondary);">
                We have sent a 6-digit code to <?= htmlspecialchars($_SESSION['verify_email'] ?? '') ?>
            </p>
            <form method="post" action="?action=verify&type=<?= htmlspecialchars($_GET['type'] ?? '') ?>">
                <div class="form-group" style="text-align: center;">
                    <input type="text" name="code" id="verification_code" 
                           maxlength="6" pattern="\d{6}" required
                           style="width: 200px; font-size: 24px; text-align: center; letter-spacing: 5px;"
                           placeholder="______">
                </div>
                <button type="submit" class="btn btn-block">Confirm</button>
            </form>
        </div>
    </div>

    <?php elseif ($page === 'communities'): ?>
    <div class="dashboard-grid">
        <div class="sidebar">
            <div class="profile-card">
                <div class="profile-avatar" style="<?= !empty($current_user['avatar_url']) ? "background-image: url('{$current_user['avatar_url']}')" : '' ?>">
                    <?= empty($current_user['avatar_url']) ? strtoupper(substr($current_user['username'], 0, 1)) : '' ?>
                </div>
            <h3 class="profile-name"><?= htmlspecialchars($current_user['username']) ?>
                <?php if ($current_user['is_verified']): ?>
                    <i class="fas fa-check-circle verified-badge"></i>
                <?php endif; ?>
                <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>

            </h3>
            </div>
            
            <ul class="sidebar-nav">
                <li><a href="?page=feed"><i class="fas fa-home"></i> Main</a></li>
                <li><a href="?page=profile"><i class="fas fa-user"></i> My profile</a></li>
                <li><a href="?page=communities" class="active"><i class="fas fa-users"></i> Communities</a></li>
                <li><a href="?page=create_community"><i class="fas fa-plus"></i> Create community</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="page-header">
                <h1 class="page-title">Communities</h1>
                <div class="search-container">
                    <form method="get" class="search-input">
                        <input type="hidden" name="page" value="communities">
                        <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" 
                               placeholder="Поиск сообществ">
                        <button type="submit" class="btn">Search</button>
                    </form>
                </div>
            </div>
            
            <?php 
            $search_query = trim($_GET['q'] ?? '');
            $communities = [];
            
            if (!empty($search_query)) {
                $communities = searchCommunities($search_query);
            } else {
                $stmt = $pdo->prepare("
                    SELECT c.*, 
                           (SELECT COUNT(*) FROM community_members WHERE community_id = c.id) as members_count,
                           u.username as creator_name
                    FROM communities c
                    JOIN users u ON c.creator_id = u.id
                    ORDER BY members_count DESC
                    LIMIT 10
                ");
                $stmt->execute();
                $communities = $stmt->fetchAll();
            }
            ?>
            
            <div class="communities-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                <?php foreach ($communities as $community): ?>
                    <a href="?page=community&slug=<?= htmlspecialchars($community['slug']) ?>" class="community-card" style="display: block; background: var(--card-bg); border-radius: var(--border-radius); overflow: hidden; transition: var(--transition);">
                        <div class="community-banner" style="height: 100px; background: <?= $community['banner_url'] ? "url('{$community['banner_url']}') center/cover" : 'var(--accent-color)' ?>"></div>
                        <div class="community-info" style="padding: 15px; text-align: center;">
                            <div class="community-avatar" style="width: 60px; height: 60px; border-radius: 50%; background: <?= $community['avatar_url'] ? "url('{$community['avatar_url']}') center/cover" : 'var(--accent-light)' ?>; margin: -30px auto 10px; border: 3px solid var(--card-bg);"></div>
                            <h3 style="margin-bottom: 5px;"><?= htmlspecialchars($community['name']) ?>
                                <?php if ($community['is_verified']): ?>
                                    <i class="fas fa-check-circle verified-badge" style="color: #1DA1F2; font-size: 14px;"></i>
                                <?php endif; ?>
                            </h3>
                            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 10px;"><?= $community['members_count'] ?> участников</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php elseif ($page === 'create_community' && isLoggedIn()): ?>
    <div class="dashboard-grid">
        <div class="sidebar">
            <div class="profile-card">
                <div class="profile-avatar" style="<?= !empty($current_user['avatar_url']) ? "background-image: url('{$current_user['avatar_url']}')" : '' ?>">
                    <?= empty($current_user['avatar_url']) ? strtoupper(substr($current_user['username'], 0, 1)) : '' ?>
                </div>
            <h3 class="profile-name"><?= htmlspecialchars($current_user['username']) ?>
                <?php if ($current_user['is_verified']): ?>
                    <i class="fas fa-check-circle verified-badge"></i>
                <?php endif; ?>
                <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
            </h3>
            </div>
            
            <ul class="sidebar-nav">
                <li><a href="?page=feed"><i class="fas fa-home"></i> Main</a></li>
                <li><a href="?page=profile"><i class="fas fa-user"></i> My profile</a></li>
                <li><a href="?page=communities"><i class="fas fa-users"></i> Communities</a></li>
                <li><a href="?page=create_community" class="active"><i class="fas fa-plus"></i> Create community</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="page-header">
                <h1 class="page-title">Create community</h1>
            </div>
            
            <div class="card">
                <form method="post" action="?action=create_community">
                    <div class="form-group">
                        <label for="name">Community name</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="4"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn">Create community</button>
                        <a href="?page=communities" class="btn btn-outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php elseif ($page === 'community' && isset($_GET['slug'])): ?>
    <?php 
    $community = getCommunity($_GET['slug']);
    if (!$community) {
        echo "<div class='alert alert-error'>Сообщество не найдено</div>";
    } else {
        $is_member = isCommunityMember($community['id'], $current_user['id'] ?? 0);
        $is_admin = isCommunityAdmin($community['id'], $current_user['id'] ?? 0);
        $is_moderator = isCommunityModerator($community['id'], $current_user['id'] ?? 0);
    ?>
    
    <div class="dashboard-grid">
        <div class="sidebar">
            <div class="profile-card">
                <div class="profile-avatar" style="<?= !empty($current_user['avatar_url']) ? "background-image: url('{$current_user['avatar_url']}')" : '' ?>">
                    <?= empty($current_user['avatar_url']) ? strtoupper(substr($current_user['username'], 0, 1)) : '' ?>
                </div>
                <h3 class="profile-name"><?= htmlspecialchars($current_user['username']) ?>
    <?php if ($current_user['is_verified']): ?>
        <i class="fas fa-check-circle verified-badge"></i>
    <?php endif; ?>
    <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
</h3>

            </div>
            
            <ul class="sidebar-nav">
                <li><a href="?page=feed"><i class="fas fa-home"></i> Main</a></li>
                <li><a href="?page=profile"><i class="fas fa-user"></i> My profile</a></li>
                <li><a href="?page=communities"><i class="fas fa-users"></i> Communities</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="community-header" style="position: relative; margin-bottom: 80px;">
                <div class="community-banner" style="height: 200px; background: <?= $community['banner_url'] ? "url('{$community['banner_url']}') center/cover" : 'var(--accent-color)' ?>; border-radius: var(--border-radius) var(--border-radius) 0 0;"></div>
                
                <div style="position: absolute; bottom: -40px; left: 20px; display: flex; align-items: flex-end; gap: 20px;">
                    <div class="community-avatar" style="width: 120px; height: 120px; border-radius: 50%; background: <?= $community['avatar_url'] ? "url('{$community['avatar_url']}') center/cover" : 'var(--accent-light)' ?>; border: 4px solid var(--card-bg);"></div>
                    
<?php if (isLoggedIn()): ?>
    <form method="POST" action="?action=toggle_community_membership">
        <input type="hidden" name="community_id" value="<?= $community['id'] ?>">
        <button type="submit" class="btn <?= $is_member ? 'btn-outline' : '' ?>">
            <?= $is_member ? 'Покинуть' : 'Вступить' ?>
        </button>
    </form>
<?php endif; ?>
                </div>
            </div>
            
            <div class="community-info" style="margin-top: 40px;">
                <h1 style="margin-bottom: 10px;">
                    <?= htmlspecialchars($community['name']) ?>
                    <?php if ($community['is_verified']): ?>
                        <i class="fas fa-check-circle verified-badge"></i>
                    <?php endif; ?>
                    <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                </h1>
                
                <?php if (!empty($community['description'])): ?>
                    <p style="margin-bottom: 20px;"><?= nl2br(htmlspecialchars($community['description'])) ?></p>
                <?php endif; ?>
                
                <div style="display: flex; gap: 30px; margin-bottom: 20px;">
                    <div>
                        <div style="font-size: 18px; font-weight: bold;"><?= $community['members_count'] ?></div>
                        <div style="color: var(--text-secondary);">Members</div>
                    </div>
                    <div>
                        <div style="font-size: 18px; font-weight: bold;"><?= $community['posts_count'] ?></div>
                        <div style="color: var(--text-secondary);">Posts</div>
                    </div>
                    <div>
                        <div style="font-size: 18px; font-weight: bold;"><?= date('d.m.Y', strtotime($community['created_at'])) ?></div>
                        <div style="color: var(--text-secondary);">Creation Date</div>
                    </div>
                </div>
                
                <div class="tab-container">
                    <div class="tab active" data-tab="posts">Posts</div>
                    <div class="tab" data-tab="members">Followers</div>
                    <?php if ($is_admin || $is_moderator): ?>
                        <div class="tab" data-tab="settings">Management</div>
                    <?php endif; ?>
                </div>
                
                <div class="tab-content active" id="posts-tab">
                        <?php if ($is_member || $is_admin || $is_moderator): ?>
                            <div class="card" style="margin-bottom: 20px;">
                                <form method="post" action="?action=create_community_post" enctype="multipart/form-data">
                                    <input type="hidden" name="community_id" value="<?= $community['id'] ?>">
                                    
                                    <div class="form-group">
                                        <textarea name="content" placeholder="Write something for the community..." required></textarea>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <div class="file-upload">
                                            <label for="post-files-<?= $community['id'] ?>" class="file-upload-label">
                                                <i class="fas fa-paperclip"></i> Attach files
                                            </label>
                                            <input type="file" name="files[]" id="post-files-<?= $community['id'] ?>" class="file-upload-input" multiple>
                                            <div class="file-preview" id="file-preview-<?= $community['id'] ?>"></div>
                                        </div>
                                        
                                        <button type="submit" class="btn">Publish</button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="feed">
                        <?php foreach (getCommunityPosts($community['id']) as $post): ?>
                            <div class="post" id="post-<?= $post['id'] ?>">
                                <div class="post-header">
                                    <div class="post-avatar" style="<?= !empty($post['avatar_url']) ? "background-image: url('{$post['avatar_url']}')" : '' ?>">
                                        <?= empty($post['avatar_url']) ? strtoupper(substr($post['username'], 0, 1)) : '' ?>
                                    </div>
                                    <div>
                                        <div class="post-user">
                                            <a href="?page=profile&user_id=<?= $post['user_id'] ?>" class="user-link" data-user-id="<?= $post['user_id'] ?>">
                                                <?= htmlspecialchars($post['username']) ?>
                                                <?php if ($post['is_verified']): ?>
                                                    <i class="fas fa-check-circle verified-badge"></i>
                                                <?php endif; ?>
                                                <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                                                <?php if ($post['is_moderator']): ?>
                                                    <i class="fas fa-shield-alt moderator-badge" title="Модератор"></i>
                                                <?php endif; ?>
                                                <?php if ($post['is_admin']): ?>
                                                    <i class="fas fa-crown admin-badge" title="Администратор"></i>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="post-time"><?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></div>
                                    </div>
                                </div>
                                <div class="post-content">
                                    <?= nl2br($post['content']) ?>
                                </div>
                                
                                <?php if (!empty($post['files'])): ?>
                                    <div class="post-files">
                                        <?php foreach ($post['files'] as $file): ?>
                                            <?php if (strpos($file['file_type'], 'image/') === 0): ?>
                                                <img src="<?= htmlspecialchars($file['file_url']) ?>" class="post-file post-file-image">
                                            <?php else: ?>
                                                <a href="<?= htmlspecialchars($file['file_url']) ?>" class="post-file post-file-document" target="_blank">
                                                    <i class="fas fa-file"></i>
                                                    <span>File: <?= basename($file['file_url']) ?></span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-actions">
                                    <div class="post-action like-btn <?= isLiked($post['id'], $current_user['id'] ?? 0) ? 'liked' : '' ?>" 
                                         data-post-id="<?= $post['id'] ?>">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count"><?= $post['likes_count'] ?></span>
                                    </div>
                                    <div class="post-action">
                                        <i class="fas fa-comment"></i>
                                        <span><?= $post['comments_count'] ?></span>
                                    </div>
                                    <div class="views-count">
                                        <i class="fas fa-eye"></i>
                                        <span><?= $post['views'] ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty(getCommunityPosts($community['id']))): ?>
                            <div class="card">
                                <p>There are no posts in this community yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="tab-content" id="members-tab">
                    <div class="card">
                        <h3 style="margin-bottom: 20px;">Community members</h3>
                        
                        <div class="search-results">
                            <?php 
                            $stmt = $pdo->prepare("
                                SELECT u.*, cm.is_admin, cm.is_moderator
                                FROM community_members cm
                                JOIN users u ON cm.user_id = u.id
                                WHERE cm.community_id = ?
                                ORDER BY cm.is_admin DESC, cm.is_moderator DESC, cm.created_at ASC
                            ");
                            $stmt->execute([$community['id']]);
                            $members = $stmt->fetchAll();
                            ?>
                            
                            <?php foreach ($members as $member): ?>
                                <div class="search-result">
                                    <div class="search-result-avatar" style="<?= !empty($member['avatar_url']) ? "background-image: url('{$member['avatar_url']}')" : '' ?>">
                                        <?= empty($member['avatar_url']) ? strtoupper(substr($member['username'], 0, 1)) : '' ?>
                                    </div>
                                    <div class="search-result-info">
                                        <div class="search-result-name">
                                            <a href="?page=profile&user_id=<?= $member['id'] ?>">
                                                <?= htmlspecialchars($member['username']) ?>
                                                <?php if ($member['is_verified']): ?>
                                                    <i class="fas fa-check-circle verified-badge"></i>
                                                <?php endif; ?>
                                                <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                                                <?php if ($member['is_admin']): ?>
                                                    <i class="fas fa-crown admin-badge"></i>
                                                <?php endif; ?>
                                                <?php if ($member['is_moderator']): ?>
                                                    <i class="fas fa-shield-alt moderator-badge"></i>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($is_admin || $is_moderator): ?>
                    <div class="tab-content" id="settings-tab">
                        <div class="card">
                            <h3 style="margin-bottom: 20px;">Community Management</h3>
                            
                            <form method="post" action="?action=update_community">
                                <input type="hidden" name="community_id" value="<?= $community['id'] ?>">
                                
                                <div class="form-group">
                                    <label for="community-name">Community Name</label>
                                    <input type="text" name="name" id="community-name" value="<?= htmlspecialchars($community['name']) ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="community-description">Description</label>
                                    <textarea name="description" id="community-description" rows="4"><?= htmlspecialchars($community['description']) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="community-avatar">Avatar URL</label>
                                    <input type="text" name="avatar_url" id="community-avatar" value="<?= htmlspecialchars($community['avatar_url'] ?? '') ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="community-banner">Banner URL</label>
                                    <input type="text" name="banner_url" id="community-banner" value="<?= htmlspecialchars($community['banner_url'] ?? '') ?>">
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php } ?>
        
        <?php elseif ($page === 'feed' && isLoggedIn()): ?>
            <div class="dashboard-grid">
                <div class="sidebar">
                    <div class="profile-card">
                        <div class="profile-avatar" style="<?= !empty($current_user['avatar_url']) ? "background-image: url('{$current_user['avatar_url']}')" : '' ?>">
                            <?= empty($current_user['avatar_url']) ? strtoupper(substr($current_user['username'], 0, 1)) : '' ?>
                        </div>
                        <h3 class="profile-name"><?= htmlspecialchars($current_user['username']) ?>
                            <?php if ($current_user['is_verified']): ?>
                                <i class="fas fa-check-circle verified-badge" data-tooltip="Подтвержденный аккаунт"></i>
                            <?php endif; ?>
                            <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                            <?php if ($current_user['is_moderator']): ?>
                                <i class="fas fa-shield-alt moderator-badge" data-tooltip="Модератор"></i>
                            <?php endif; ?>
                            <?php if ($current_user['is_admin']): ?>
                                <i class="fas fa-crown admin-badge" data-tooltip="Администратор"></i>
                            <?php endif; ?>
                            <?php if ($current_user['is_beta_tester']): ?>
                                <i class="fas fa-flask beta-tester-badge" title="Бета-тестер"></i>
                            <?php endif; ?>
                            <?php if ($current_user['is_supporter']): ?>
                                <i class="fas fa-heart supporter-badge" title="Поддержавший"></i>
                            <?php endif; ?>
                            <?php if ($current_user['is_vip']): ?>
                                <i class="fas fa-star vip-badge" title="VIP"></i>
                            <?php endif; ?>
                        </h3>
                        <p class="profile-email"><?= htmlspecialchars($current_user['email']) ?></p>
                        <a href="?page=profile" class="btn btn-outline">Edit</a>
                    </div>
                    
                    <ul class="sidebar-nav">
                        <li><a href="?page=feed" class="<?= $page === 'feed' ? 'active' : '' ?>"><i class="fas fa-home"></i> Main</a></li>
                        <li><a href="?page=profile" class="<?= $page === 'profile' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a href="?page=feedback" class="<?= $page === 'feedback' ? 'active' : '' ?>"><i class="fas fa-comment-alt"></i> Feedback</a></li>
                        <li><a href="?page=search" class="<?= $page === 'search' ? 'active' : '' ?>"><i class="fas fa-search"></i> Search</a></li>
                        <?php if ($current_user['is_admin'] || $current_user['is_moderator']): ?>
                            <li><a href="?page=admin" class="<?= $page === 'admin' ? 'active' : '' ?>"><i class="fas fa-shield-alt"></i> Moderation</a></li>
                        <?php endif; ?>
                        <li><a href="?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
                
                <div class="main-content">
                    <div class="page-header">
                        <h1 class="page-title">Tape</h1>
                    </div>
                    
                    <!-- Форма создания поста -->
                    <div class="card" style="margin-bottom: 20px;">
                        <form method="post" action="?action=create_post" enctype="multipart/form-data">
                            <div class="form-group">
                                <textarea name="content" placeholder="Whats new with you?" required></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <div class="file-upload">
                                    <label for="post-files" class="file-upload-label">
                                        <i class="fas fa-paperclip"></i> Attach files
                                    </label>
                                    <input type="file" name="files[]" id="post-files" class="file-upload-input" multiple>
                                    <div class="file-preview" id="file-preview"></div>
                                </div>
                                
                                <button type="submit" class="btn">Publish</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Лента постов -->
                    <div class="feed">
                        <?php foreach (getPosts() as $post): ?>
                            <div class="post" id="post-<?= $post['id'] ?>">
                                <div class="post-header">
                                    <div class="post-avatar" style="<?= !empty($post['avatar_url']) ? "background-image: url('{$post['avatar_url']}')" : '' ?>">
                                        <?= empty($post['avatar_url']) ? strtoupper(substr($post['username'], 0, 1)) : '' ?>
                                    </div>
                                    <div>
                                        <div class="post-user">
                                            <a href="?page=profile&user_id=<?= $post['user_id'] ?>" class="user-link" data-user-id="<?= $post['user_id'] ?>">
                                                <?= htmlspecialchars($post['username']) ?>
                                                <?php if ($post['is_verified']): ?>
                                                    <i class="fas fa-check-circle verified-badge" title="Подтвержденный аккаунт"></i>
                                                <?php endif; ?>
                                                <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                                                <?php if ($post['is_moderator']): ?>
                                                    <i class="fas fa-shield-alt moderator-badge" title="Модератор"></i>
                                                <?php endif; ?>
                                                <?php if ($post['is_admin']): ?>
                                                    <i class="fas fa-crown admin-badge" title="Администратор"></i>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="post-time"><?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></div>
                                    </div>
                                
                                </div>
                                <div class="post-content">
                                    <?= nl2br($post['content']) ?>
                                </div>
                                
                                <?php if (!empty($post['files'])): ?>
                                    <div class="post-files">
                                        <?php foreach ($post['files'] as $file): ?>
                                            <?php if (strpos($file['file_type'], 'image/') === 0): ?>
                                                <img src="<?= htmlspecialchars($file['file_url']) ?>" class="post-file post-file-image">
                                            <?php else: ?>
                                                <a href="<?= htmlspecialchars($file['file_url']) ?>" class="post-file post-file-document" target="_blank">
                                                    <i class="fas fa-file"></i>
                                                    <span>File: <?= basename($file['file_url']) ?></span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-actions">
                                    <div class="post-action like-btn <?= isLiked($post['id'], $current_user['id']) ? 'liked' : '' ?>" 
                                         data-post-id="<?= $post['id'] ?>">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count"><?= $post['likes_count'] ?></span>
                                    </div>
                                    <div class="post-action">
                                        <i class="fas fa-comment"></i>
                                        <span><?= $post['comments_count'] ?></span>
                                    </div>
                                    <div class="views-count">
                                        <i class="fas fa-eye"></i>
                                        <span><?= $post['views'] ?></span>
                                    </div>
                                </div>
                                
                                <!-- Комментарии -->
                                <div class="comments">
                                    <?php foreach (getPopularComments($post['id']) as $comment): ?>
                                        <div class="comment">
                                            <div class="comment-avatar" style="<?= !empty($comment['avatar_url']) ? "background-image: url('{$comment['avatar_url']}')" : '' ?>">
                                                <?= empty($comment['avatar_url']) ? strtoupper(substr($comment['username'], 0, 1)) : '' ?>
                                            </div>
                                            <div class="comment-content">
                                                <div class="comment-user">
                                                    <a href="?page=profile&user_id=<?= $comment['user_id'] ?>" class="user-link" data-user-id="<?= $comment['user_id'] ?>">
                                                        <?= htmlspecialchars($comment['username']) ?>
                                                        <?php if ($comment['is_verified']): ?>
                                                            <i class="fas fa-check-circle verified-badge"></i>
                                                        <?php endif; ?>
                                                        <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                                                        <?php if ($comment['is_moderator']): ?>
                                                            <i class="fas fa-shield-alt moderator-badge"></i>
                                                        <?php endif; ?>
                                                        <?php if ($comment['is_admin']): ?>
                                                            <i class="fas fa-crown admin-badge"></i>
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                                <div class="comment-text"><?= nl2br($comment['content']) ?></div>
                                                <div class="comment-time"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></div>
                                                <div class="comment-actions">
                                                    <div class="comment-action comment-like-btn <?= isCommentLiked($comment['id'], $current_user['id']) ? 'liked' : '' ?>" 
                                                         data-comment-id="<?= $comment['id'] ?>">
                                                        <i class="fas fa-heart"></i>
                                                        <span class="like-count"><?= $comment['likes_count'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <!-- Форма добавления комментария -->
                                    <form method="post" action="?action=add_comment" class="new-comment">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <div class="new-comment-input">
                                            <input type="text" name="content" placeholder="Write a comment..." required>
                                        </div>
                                        <button type="submit" class="btn btn-outline">Отправить</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        
        <?php elseif ($page === 'profile'): ?>
            <?php 
                $profile_user = isset($_GET['user_id']) ? getUser($_GET['user_id']) : $current_user;
                if (!$profile_user) {
                    echo "<div class='alert alert-error'>User has not find</div>";
                } else {
                    $is_own_profile = isLoggedIn() && $profile_user['id'] == $current_user['id'];
                    $is_following = isLoggedIn() && isFollowing($current_user['id'], $profile_user['id']);
            ?>
            
            <div class="profile-header">
                <div class="profile-banner" style="<?= !empty($profile_user['banner_url']) ? "background-image: url('{$profile_user['banner_url']}')" : '' ?>"></div>
                <div class="profile-avatar-container">
                    <div class="profile-avatar-large" style="<?= !empty($profile_user['avatar_url']) ? "background-image: url('{$profile_user['avatar_url']}')" : '' ?>">
                        <?= empty($profile_user['avatar_url']) ? strtoupper(substr($profile_user['username'], 0, 1)) : '' ?>
                    </div>
                    
                    <?php if (!$is_own_profile && isLoggedIn()): ?>
                        <button class="btn <?= $is_following ? 'btn-outline' : '' ?> follow-btn toggle-follow" data-user-id="<?= $profile_user['id'] ?>">
                            <?= $is_following ? 'Unfollow' : 'Follow' ?>
                        </button>
                        
                        <?php if ($current_user['is_admin'] || $current_user['is_moderator']): ?>
                            <?php if ($profile_user['is_banned']): ?>
                                <button class="btn btn-success admin-action unban-user" data-user-id="<?= $profile_user['id'] ?>">
                                    Unban
                                </button>
                            <?php else: ?>
                                <button class="btn btn-danger admin-action ban-user" data-user-id="<?= $profile_user['id'] ?>">
                                    Ban
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="profile-info">
                <h1 class="profile-name">
                    <?= htmlspecialchars($profile_user['username']) ?>
                    <?php if ($profile_user['is_verified']): ?>
                        <i class="fas fa-check-circle verified-badge" title="Подтвержденный аккаунт"></i>
                    <?php endif; ?>
                    <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                    <?php if ($profile_user['is_moderator']): ?>
                        <i class="fas fa-shield-alt moderator-badge" title="Модератор"></i>
                    <?php endif; ?>
                    <?php if ($profile_user['is_admin']): ?>
                        <i class="fas fa-crown admin-badge" title="Администратор"></i>
                    <?php endif; ?>
                    <?php if ($profile_user['is_beta_tester']): ?>
                        <i class="fas fa-flask beta-tester-badge" title="Бета-тестер"></i>
                    <?php endif; ?>
                    <?php if ($profile_user['is_supporter']): ?>
                        <i class="fas fa-heart supporter-badge" title="Поддержавший"></i>
                    <?php endif; ?>
                    <?php if ($profile_user['is_vip']): ?>
                        <i class="fas fa-star vip-badge" title="VIP"></i>
                    <?php endif; ?>
                </h1>
                
                <?php if (!empty($profile_user['bio'])): ?>
                    <p class="profile-bio"><?= nl2br(htmlspecialchars($profile_user['bio'])) ?></p>
                <?php endif; ?>
                
                <div class="profile-stats">
                    <div class="profile-stat">
                        <div class="profile-stat-value"><?= $profile_user['posts_count'] ?></div>
                        <div class="profile-stat-label">Posts</div>
                    </div>
                    <div class="profile-stat">
                        <div class="profile-stat-value"><?= $profile_user['formatted_followers'] ?></div>
                        <div class="profile-stat-label">Followers</div>
                    </div>
                    <div class="profile-stat">
                        <div class="profile-stat-value"><?= $profile_user['following_count'] ?></div>
                        <div class="profile-stat-label">Following</div>
                    </div>
                </div>
                
                <div class="tab-container">
                    <div class="tab active" data-tab="posts">Posts</div>
                    <?php if ($is_own_profile): ?>
                        <div class="tab" data-tab="edit">Edit</div>
                    <?php endif; ?>
                </div>
                
                <div class="tab-content active" id="posts-tab">
                    <div class="feed">
                        <?php foreach (getPosts($profile_user['id']) as $post): ?>
                            <div class="post" id="post-<?= $post['id'] ?>">
                                <div class="post-header">
                                    <div class="post-avatar" style="<?= !empty($post['avatar_url']) ? "background-image: url('{$post['avatar_url']}')" : '' ?>">
                                        <?= empty($post['avatar_url']) ? strtoupper(substr($post['username'], 0, 1)) : '' ?>
                                    </div>
                                    <div>
                                        <div class="post-user">
                                            <a href="?page=profile&user_id=<?= $post['user_id'] ?>" class="user-link" data-user-id="<?= $post['user_id'] ?>">
                                                <?= htmlspecialchars($post['username']) ?>
                                                <?php if ($post['is_verified']): ?>
                                                    <i class="fas fa-check-circle verified-badge" title="Подтвержденный аккаунт"></i>
                                                <?php endif; ?>
                                                <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                                                <?php if ($post['is_moderator']): ?>
                                                    <i class="fas fa-shield-alt moderator-badge" title="Модератор"></i>
                                                <?php endif; ?>
                                                <?php if ($post['is_admin']): ?>
                                                    <i class="fas fa-crown admin-badge" title="Администратор"></i>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="post-time"><?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></div>
                                    </div>
                                
                                </div>
                                <div class="post-content">
                                    <?= nl2br($post['content']) ?>
                                </div>
                                
                                <?php if (!empty($post['files'])): ?>
                                    <div class="post-files">
                                        <?php foreach ($post['files'] as $file): ?>
                                            <?php if (strpos($file['file_type'], 'image/') === 0): ?>
                                                <img src="<?= htmlspecialchars($file['file_url']) ?>" class="post-file post-file-image">
                                            <?php else: ?>
                                                <a href="<?= htmlspecialchars($file['file_url']) ?>" class="post-file post-file-document" target="_blank">
                                                    <i class="fas fa-file"></i>
                                                    <span>File: <?= basename($file['file_url']) ?></span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-actions">
                                    <div class="post-action like-btn <?= isLiked($post['id'], $current_user['id']) ? 'liked' : '' ?>" 
                                         data-post-id="<?= $post['id'] ?>">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count"><?= $post['likes_count'] ?></span>
                                    </div>
                                    <div class="post-action">
                                        <i class="fas fa-comment"></i>
                                        <span><?= $post['comments_count'] ?></span>
                                    </div>
                                    <div class="views-count">
                                        <i class="fas fa-eye"></i>
                                        <span><?= $post['views'] ?></span>
                                    </div>
                                </div>
                                
                                <!-- Комментарии -->
                                <div class="comments">
                                    <?php foreach (getPopularComments($post['id']) as $comment): ?>
                                        <div class="comment">
                                            <div class="comment-avatar" style="<?= !empty($comment['avatar_url']) ? "background-image: url('{$comment['avatar_url']}')" : '' ?>">
                                                <?= empty($comment['avatar_url']) ? strtoupper(substr($comment['username'], 0, 1)) : '' ?>
                                            </div>
                                            <div class="comment-content">
                                                <div class="comment-user">
                                                    <a href="?page=profile&user_id=<?= $comment['user_id'] ?>" class="user-link" data-user-id="<?= $comment['user_id'] ?>">
                                                        <?= htmlspecialchars($comment['username']) ?>
                                                        <?php if ($comment['is_verified']): ?>
                                                            <i class="fas fa-check-circle verified-badge"></i>
                                                        <?php endif; ?>
                                                        <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                                                        <?php if ($comment['is_moderator']): ?>
                                                            <i class="fas fa-shield-alt moderator-badge"></i>
                                                        <?php endif; ?>
                                                        <?php if ($comment['is_admin']): ?>
                                                            <i class="fas fa-crown admin-badge"></i>
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                                <div class="comment-text"><?= nl2br($comment['content']) ?></div>
                                                <div class="comment-time"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></div>
                                                <div class="comment-actions">
                                                    <div class="comment-action comment-like-btn <?= isCommentLiked($comment['id'], $current_user['id']) ? 'liked' : '' ?>" 
                                                         data-comment-id="<?= $comment['id'] ?>">
                                                        <i class="fas fa-heart"></i>
                                                        <span class="like-count"><?= $comment['likes_count'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if ($is_own_profile): ?>
                    <div class="tab-content" id="edit-tab">
                        <form method="post" action="?action=update_profile" class="card">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" id="username" 
                                       value="<?= htmlspecialchars($profile_user['username']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" 
                                    value="<?= htmlspecialchars($profile_user['email']) ?>" disabled>
                                <small style="color: var(--text-secondary);">To change your email address, contact support</small>
                            </div>

                            <div class="form-group">
                                <label for="avatar_url">Avatar URL</label>
                                <input type="text" id="avatar_url" 
                                    value="<?= htmlspecialchars($profile_user['avatar_url'] ?? '') ?>" disabled>
                                <small style="color: var(--text-secondary);">To change your avatar URL, please go to <a href="settings">settings</a></small>
                            </div>

                            <div class="form-group">
                                <label for="banner_url">Banner URL</label>
                                <input type="text" id="banner_url" 
                                    value="<?= htmlspecialchars($profile_user['banner_url'] ?? '') ?>" disabled>
                                <small style="color: var(--text-secondary);">To change your banner URL, please go to <a href="settings">settings</a></small>
                            </div>
                            
                            <div class="form-group">
                                <label for="bio">About</label>
                                <textarea name="bio" id="bio"><?= htmlspecialchars($profile_user['bio'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn">Save changes</button>
                                <a href="?page=feed" class="btn btn-outline">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php } ?>
        
        <?php elseif ($page === 'feedback' && isLoggedIn()): ?>
            <div class="dashboard-grid">
                <div class="sidebar">
                    <div class="profile-card">
                        <div class="profile-avatar" style="<?= !empty($current_user['avatar_url']) ? "background-image: url('{$current_user['avatar_url']}')" : '' ?>">
                            <?= empty($current_user['avatar_url']) ? strtoupper(substr($current_user['username'], 0, 1)) : '' ?>
                        </div>
                        <h3 class="profile-name"><?= htmlspecialchars($current_user['username']) ?>
                            <?php if ($current_user['is_verified']): ?>
                                <i class="fas fa-check-circle verified-badge"></i>
                            <?php endif; ?>
                            <?php if ($current_user['is_moderator']): ?>
                                <i class="fas fa-shield-alt moderator-badge"></i>
                            <?php endif; ?>
                            <?php if ($current_user['is_admin']): ?>
                                <i class="fas fa-crown admin-badge"></i>
                            <?php endif; ?>
                        </h3>
                        <p class="profile-email"><?= htmlspecialchars($current_user['email']) ?></p>
                    </div>
                    
                    <ul class="sidebar-nav">
                        <li><a href="?page=feed"><i class="fas fa-home"></i> Main</a></li>
                        <li><a href="?page=profile"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a href="?page=feedback" class="active"><i class="fas fa-comment-alt"></i> Feedback</a></li>
                    </ul>
                </div>
                
                <div class="main-content">
                    <div class="page-header">
                        <h1 class="page-title">Feedback</h1>
                    </div>
                    
                    <div class="card">
                        <p style="margin-bottom: 20px;">We appreciate your feedback! Please select the type of request and describe your suggestions or comments.</p>
                        
                        <form method="post" action="?action=send_feedback">
                            <div class="form-group">
                                <label>Type of request</label>
                                <div style="display: flex; gap: 15px;">
                                    <label style="display: flex; align-items: center; gap: 5px;">
                                        <input type="radio" name="type" value="review" checked> Feedback
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 5px;">
                                        <input type="radio" name="type" value="suggestion"> Offer
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Your message</label>
                                <textarea name="message" id="message" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        
            <?php elseif ($page === 'search'): ?>
    <?php
$search_query = trim($_GET['q'] ?? '');
$search_results = [];

if (!empty($search_query)) {
    $search_results = searchUsers($search_query);
}
?>

<div class="dashboard-grid">
    <div class="sidebar">
        <div class="profile-card">
            <div class="profile-avatar" style="<?= !empty($current_user['avatar_url']) ? "background-image: url('{$current_user['avatar_url']}')" : '' ?>">
                <?= empty($current_user['avatar_url']) ? strtoupper(substr($current_user['username'], 0, 1)) : '' ?>
            </div>
            <h3 class="profile-name"><?= htmlspecialchars($current_user['username']) ?>
                <?php if ($current_user['is_verified']): ?>
                    <i class="fas fa-check-circle verified-badge"></i>
                <?php endif; ?>
                <?php if ($current_user['is_moderator']): ?>
                    <i class="fas fa-shield-alt moderator-badge"></i>
                <?php endif; ?>
                <?php if ($current_user['is_admin']): ?>
                    <i class="fas fa-crown admin-badge"></i>
                <?php endif; ?>
            </h3>
            <p class="profile-email"><?= htmlspecialchars($current_user['email']) ?></p>
        </div>
        
        <ul class="sidebar-nav">
            <li><a href="?page=feed"><i class="fas fa-home"></i> Main</a></li>
            <li><a href="?page=profile"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="?page=search" class="active"><i class="fas fa-search"></i> Search</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Search user</h1>
        </div>
        
        <div class="search-container">
            <form method="get" action="" class="search-input">
                <input type="hidden" name="page" value="search">
                <input type="text" name="q" value="<?= htmlspecialchars($search_query) ?>" 
                       placeholder="Enter username or email" required>
                <button type="submit" class="btn">Search</button>
            </form>
            
            <?php if (!empty($search_query)): ?>
                <?php if (!empty($search_results)): ?>
                    <div class="search-results">
                        <?php foreach ($search_results as $user): ?>
                            <a href="?page=profile&user_id=<?= $user['id'] ?>" class="search-result">
                                <div class="search-result-avatar" style="<?= !empty($user['avatar_url']) ? "background-image: url('{$user['avatar_url']}')" : '' ?>">
                                    <?= empty($user['avatar_url']) ? strtoupper(substr($user['username'], 0, 1)) : '' ?>
                                </div>
                                <div class="search-result-info">
                                    <div class="search-result-name">
                                        <?= htmlspecialchars($user['username']) ?>
                                        <?php if ($user['is_verified']): ?>
                                            <i class="fas fa-check-circle verified-badge"></i>
                                        <?php endif; ?>
                                        <?php if ($user['is_moderator']): ?>
                                            <i class="fas fa-shield-alt moderator-badge"></i>
                                        <?php endif; ?>
                                        <?php if ($user['is_admin']): ?>
                                            <i class="fas fa-crown admin-badge"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="search-result-followers">
                                        <?= $user['formatted_followers'] ?> followers
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="margin-top: 20px;">Users for request "<?= htmlspecialchars($search_query) ?>" not finded</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
                        
                        <?php if (!empty($search_results)): ?>
                            <div class="search-results">
                                <?php foreach ($search_results as $user): ?>
                                    <a href="?page=profile&user_id=<?= $user['id'] ?>" class="search-result">
                                        <div class="search-result-avatar" style="<?= !empty($user['avatar_url']) ? "background-image: url('{$user['avatar_url']}')" : '' ?>">
                                            <?= empty($user['avatar_url']) ? strtoupper(substr($user['username'], 0, 1)) : '' ?>
                                        </div>
                                        <div class="search-result-info">
                                            <div class="search-result-name">
                                                <?= htmlspecialchars($user['username']) ?>
                                                <?php if ($user['is_verified']): ?>
                                                    <i class="fas fa-check-circle verified-badge"></i>
                                                <?php endif; ?>
                                                <?php if ($user['is_moderator']): ?>
                                                    <i class="fas fa-shield-alt moderator-badge"></i>
                                                <?php endif; ?>
                                                <?php if ($user['is_admin']): ?>
                                                    <i class="fas fa-crown admin-badge"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="search-result-followers">
                                                <?= $user['formatted_followers'] ?> followers
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (!empty($_GET['q'])): ?>
                            <p>Users dont finded</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        
            <?php elseif ($page === 'admin' && isLoggedIn() && ($current_user['is_admin'] || $current_user['is_moderator'])): ?>
    <div class="dashboard-grid">
        <div class="sidebar">
            <div class="profile-card">
                <div class="profile-avatar" style="<?= !empty($current_user['avatar_url']) ? "background-image: url('{$current_user['avatar_url']}')" : '' ?>">
                    <?= empty($current_user['avatar_url']) ? strtoupper(substr($current_user['username'], 0, 1)) : '' ?>
                </div>
                <h3 class="profile-name"><?= htmlspecialchars($current_user['username']) ?>
                    <?php if ($current_user['is_verified']): ?>
                        <i class="fas fa-check-circle verified-badge"></i>
                    <?php endif; ?>
                    <?php if ($current_user['is_moderator']): ?>
                        <i class="fas fa-shield-alt moderator-badge"></i>
                    <?php endif; ?>
                    <?php if ($current_user['is_admin']): ?>
                        <i class="fas fa-crown admin-badge"></i>
                    <?php endif; ?>
                </h3>
                <p class="profile-email"><?= htmlspecialchars($current_user['email']) ?></p>
            </div>
            
            <ul class="sidebar-nav">
                <li><a href="?page=feed"><i class="fas fa-home"></i> Main</a></li>
                <li><a href="?page=profile"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="?page=admin" class="active"><i class="fas fa-shield-alt"></i> Moderation</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="page-header">
                <h1 class="page-title">Moderator panel</h1>
            </div>
            
            <div class="card">
                <h2>Manage users</h2>
                <p>Here you can block and unblock users.</p>
                
                <div class="search-container" style="margin-top: 20px;">
                    <form method="get" class="search-input">
                        <input type="hidden" name="page" value="admin">
                        <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" 
                               placeholder="Поиск по имени или email" required>
                        <button type="submit" class="btn">Search</button>
                    </form>
                    
                    <?php 
                    $search_query = trim($_GET['q'] ?? '');
                    $users = [];
                    
                    if (!empty($search_query)) {
                        $users = searchUsers($search_query, 20);
                    }
                    ?>
                    
                    <?php if (!empty($users)): ?>
                        <div class="search-results" style="margin-top: 20px;">
                            <?php foreach ($users as $user): ?>
                                <div class="search-result">
                                    <div class="search-result-avatar" style="<?= !empty($user['avatar_url']) ? "background-image: url('{$user['avatar_url']}')" : '' ?>">
                                        <?= empty($user['avatar_url']) ? strtoupper(substr($user['username'], 0, 1)) : '' ?>
                                    </div>
                                    <div class="search-result-info">
                                        <div class="search-result-name">
                                            <a href="?page=profile&user_id=<?= $user['id'] ?>">
                                                <?= htmlspecialchars($user['username']) ?>
                                                <?php if ($user['is_verified']): ?>
                                                    <i class="fas fa-check-circle verified-badge"></i>
                                                <?php endif; ?>
                                                <?php if ($user['is_moderator']): ?>
                                                    <i class="fas fa-shield-alt moderator-badge"></i>
                                                <?php endif; ?>
                                                <?php if ($user['is_admin']): ?>
                                                    <i class="fas fa-crown admin-badge"></i>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="search-result-followers">
                                            <?= $user['formatted_followers'] ?> followers
                                        </div>
                                    </div>
                                    
                                    <?php if ($user['id'] != $current_user['id']): ?>
                                        <div class="user-actions">
                                            <?php if ($user['is_banned']): ?>
                                                <button class="btn btn-success admin-action unban-user" data-user-id="<?= $user['id'] ?>">
                                                    Unban
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-danger admin-action ban-user" data-user-id="<?= $user['id'] ?>">
                                                    Ban
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif (!empty($search_query)): ?>
                        <p style="margin-top: 20px;">No users found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
        
        <?php elseif ($page === 'tag' && isset($_GET['tag'])): ?>
            <div class="dashboard-grid">
                <div class="sidebar">
                    <div class="profile-card">
                        <div class="profile-avatar" style="<?= !empty($current_user['avatar_url']) ? "background-image: url('{$current_user['avatar_url']}')" : '' ?>">
                            <?= empty($current_user['avatar_url']) ? strtoupper(substr($current_user['username'], 0, 1)) : '' ?>
                        </div>
                        <h3 class="profile-name"><?= htmlspecialchars($current_user['username']) ?>
                            <?php if ($current_user['is_verified']): ?>
                                <i class="fas fa-check-circle verified-badge"></i>
                            <?php endif; ?>

                            <?php if ($current_user['is_moderator']): ?>
                                <i class="fas fa-shield-alt moderator-badge"></i>
                            <?php endif; ?>
                            <?php if ($current_user['is_admin']): ?>
                                <i class="fas fa-crown admin-badge"></i>
                            <?php endif; ?>
                        </h3>
                        <p class="profile-email"><?= htmlspecialchars($current_user['email']) ?></p>
                    </div>
                    
                    <ul class="sidebar-nav">
                        <li><a href="?page=feed"><i class="fas fa-home"></i> Main</a></li>
                        <li><a href="?page=profile"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a href="?page=search"><i class="fas fa-search"></i> Search</a></li>
                    </ul>
                </div>
                
                <div class="main-content">                
                    <div class="page-header">
                        <h1 class="page-title">Posts with a tag #<?= htmlspecialchars($_GET['tag']) ?></h1>
                    </div>
                    
                    <div class="feed">
                        <?php 
                            $tag = $_GET['tag'];
                            $stmt = $pdo->prepare("
                                SELECT p.*, u.username, u.email, u.avatar_url, u.is_moderator, u.is_admin, u.is_verified, u.has_premium,\n                                       (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,\n                                       (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,\n                                       p.views
                                FROM posts p
                                JOIN users u ON p.user_id = u.id
                                WHERE p.content LIKE ? AND u.is_banned = 0
                                ORDER BY p.created_at DESC
                            ");
                            $stmt->execute(["%#$tag%"]);
                            $posts = $stmt->fetchAll();
                            
                            foreach ($posts as $post):
                                $post['content'] = processTags($post['content']);
                                $post['files'] = getPostFiles($post['id']);
                        ?>
                            <div class="post" id="post-<?= $post['id'] ?>">
                                <div class="post-header">
                                    <div class="post-avatar" style="<?= !empty($post['avatar_url']) ? "background-image: url('{$post['avatar_url']}')" : '' ?>">
                                        <?= empty($post['avatar_url']) ? strtoupper(substr($post['username'], 0, 1)) : '' ?>
                                    </div>
                                    <div>
                                        <div class="post-user">
                                            <a href="?page=profile&user_id=<?= $post['user_id'] ?>" class="user-link" data-user-id="<?= $post['user_id'] ?>">
                                                <?= htmlspecialchars($post['username']) ?>
                                                <?php if ($post['is_verified']): ?>
                                                    <i class="fas fa-check-circle verified-badge"></i>
                                                <?php endif; ?>
                                                <?php if ($current_user['is_employee']): ?>
    <span class="employee-badge" data-tooltip="Customer BlinX"></span>
<?php endif; ?>
                                                <?php if ($post['is_moderator']): ?>
                                                    <i class="fas fa-shield-alt moderator-badge"></i>
                                                <?php endif; ?>
                                                <?php if ($post['is_admin']): ?>
                                                    <i class="fas fa-crown admin-badge"></i>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="post-time"><?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></div>
                                    </div>
                                </div>
                                <div class="post-content">
                                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                                </div>
                                
                                <?php if (!empty($post['files'])): ?>
                                    <div class="post-files">
                                        <?php foreach ($post['files'] as $file): ?>
                                            <?php if (strpos($file['file_type'], 'image/') === 0): ?>
                                                <img src="<?= htmlspecialchars($file['file_url']) ?>" class="post-file post-file-image">
                                            <?php else: ?>
                                                <a href="<?= htmlspecialchars($file['file_url']) ?>" class="post-file post-file-document" target="_blank">
                                                    <i class="fas fa-file"></i>
                                                    <span>File: <?= basename($file['file_url']) ?></span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-actions">
                                    <div class="post-action like-btn <?= isLiked($post['id'], $current_user['id']) ? 'liked' : '' ?>" 
                                         data-post-id="<?= $post['id'] ?>">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count"><?= $post['likes_count'] ?></span>
                                    </div>
                                    <div class="post-action">
                                        <i class="fas fa-comment"></i>
                                        <span><?= $post['comments_count'] ?></span>
                                    </div>
                                    <div class="views-count">
                                        <i class="fas fa-eye"></i>
                                        <span><?= $post['views'] ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($posts)): ?>
                            <div class="card">
                                <p>There are no posts with the tag #<?= htmlspecialchars($_GET['tag']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        
        <?php else: ?>
            <div style="text-align: center; padding: 50px 0;">
                <h2>Страница не найдена</h2>
                <p style="margin-top: 20px;"><a href="?page=home">Вернуться на главную</a></p>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p class="copyright">© 2025 BlinX. All rights reserved. <br>
                Product version: 2.0.0 (Beta)</p>
            <div class="footer-links">
                <a href="/legal/privacy">Privacy</a>
                <a href="/legal/terms">Terms</a>
                <a href="/legal/important">Important Info</a>
            </div>
        </div>
    </footer>

    <div class="user-preview" id="user-preview">
        <div class="user-preview-avatar" id="user-preview-avatar"></div>
        <div class="user-preview-name" id="user-preview-name"></div>
        <div class="user-preview-stats">
            <div class="user-preview-stat">
                <div class="user-preview-stat-value" id="user-preview-posts">0</div>
                <div class="user-preview-stat-label">Posts</div>
            </div>
            <div class="user-preview-stat">
                <div class="user-preview-stat-value" id="user-preview-followers">0</div>
                <div class="user-preview-stat-label">Followers</div>
            </div>
        </div>
        <div class="user-preview-actions">
            <a href="#" class="btn btn-outline" id="user-preview-profile-btn">Profile</a>
            <button class="btn" id="user-preview-follow-btn">Subscribe</button>
        </div>
    </div>
    <script src="backend.js"></script>
</body>
</html>