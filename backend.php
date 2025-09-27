<?php

ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60);
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);
session_start();
if (empty($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
date_default_timezone_set('Europe/Moscow');
$theme = $_COOKIE['site_theme'] ?? 'default';

require_once __DIR__ . '/env.php';
loadEnv(__DIR__);

require_once __DIR__ . '/db.php';

$current_ip = $_SERVER['REMOTE_ADDR'];

$stored_ip = $_COOKIE['user_ip'] ?? '';

if (isLoggedIn()) {
	if (!empty($stored_ip) && $stored_ip !== $current_ip) {
		setcookie('user_ip', '', time() - 3600, '/');
		$_SESSION = [];
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
		$_SESSION['error'] = 'IP address changed. Please log in again.';
		header('Location: ?page=login');
		exit;
	}
	if (empty($stored_ip)) {
		setcookie('user_ip', $current_ip, time() + 365*24*60*60, '/', '', false, true);
	}
}

$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        avatar_url VARCHAR(255),
        banner_url VARCHAR(255),
        bio TEXT,
        verification_code VARCHAR(10),
        is_active BOOLEAN DEFAULT 0,
        is_moderator BOOLEAN DEFAULT 0,
        is_admin BOOLEAN DEFAULT 0,
        is_verified BOOLEAN DEFAULT 0,
        is_banned BOOLEAN DEFAULT 0,
        created_at DATETIME,
        updated_at DATETIME,
        last_login DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE IF NOT EXISTS user_badges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        badge_type VARCHAR(50) NOT NULL,
        created_at DATETIME,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY (user_id, badge_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        views INT DEFAULT 0,
        created_at DATETIME,
        updated_at DATETIME,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE IF NOT EXISTS post_files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        file_url VARCHAR(255) NOT NULL,
        file_type VARCHAR(50) NOT NULL,
        created_at DATETIME,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE IF NOT EXISTS likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at DATETIME,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY (post_id, user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE IF NOT EXISTS followers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        follower_id INT NOT NULL,
        following_id INT NOT NULL,
        created_at DATETIME,
        FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY (follower_id, following_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE IF NOT EXISTS comment_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        comment_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at DATETIME,
        FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY (comment_id, user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE IF NOT EXISTS password_reset_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        token VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at DATETIME,
        UNIQUE KEY (email, token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$moderator_email = "y52s@yandex.com";
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$moderator_email]);

if (!$stmt->fetch()) {
    $hashed_password = password_hash("Pro228333123123123", PASSWORD_DEFAULT);
    $pdo->prepare("
        INSERT INTO users (username, email, password, is_active, is_moderator, created_at, last_login)
        VALUES (?, ?, ?, 1, 1, NOW(), NOW())
    ")->execute(["y52s", $moderator_email, $hashed_password]);
}

function sendSMTPEmail($email, $subject, $message) {
    require_once 'vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.blin-x.space';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@yandex.com';
        $mail->Password = 'your-password';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        
        $mail->setFrom('no-reply@blin-x.space', 'BlinX');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}

function generateCode($length = 6) {
    return str_pad(mt_rand(0, pow(10, $length)-1), $length, '0', STR_PAD_LEFT);
}

function sendEmail($email, $subject, $message) {
    $headers = "From: BlinX <no-reply@blin-x.space>\r\n";
    $headers .= "Reply-To: no-reply@blin-x.space\r\n";
    $headers .= "Return-Path: no-reply@blin-x.space\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-MSMail-Priority: Normal\r\n";
    $headers .= "Importance: Normal\r\n";
    
    $headers .= "List-Unsubscribe: <mailto:unsubscribe@blin-x.space?subject=Unsubscribe>\r\n";
    $headers .= "Precedence: bulk\r\n";
    
    $text_message = strip_tags($message);
    $boundary = uniqid('np');
    
    $full_message = "--$boundary\r\n";
    $full_message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $full_message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $full_message .= $text_message . "\r\n\r\n";
    $full_message .= "--$boundary\r\n";
    $full_message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $full_message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $full_message .= $message . "\r\n\r\n";
    $full_message .= "--$boundary--";
    
    $headers = "From: BlinX <no-reply@blin-x.space>\r\n";
    $headers .= "Reply-To: no-reply@blin-x.space\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($email, $subject, $full_message, $headers, "-f no-reply@blin-x.space");
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['logged_in']);
}

function getUser($id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT u.*, 
               (SELECT COUNT(*) FROM followers WHERE following_id = u.id) as followers_count,
               (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as posts_count,
               (SELECT COUNT(*) FROM followers WHERE follower_id = u.id) as following_count,
               EXISTS(SELECT 1 FROM user_badges WHERE user_id = u.id AND badge_type = 'beta_tester') as is_beta_tester,
               EXISTS(SELECT 1 FROM user_badges WHERE user_id = u.id AND badge_type = 'supporter') as is_supporter,
               EXISTS(SELECT 1 FROM user_badges WHERE user_id = u.id AND badge_type = 'vip') as is_vip,
               u.is_employee as is_employee,
               u.has_premium as has_premium
        FROM users u
        WHERE u.id = ?
    ");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user) {
        $user['formatted_followers'] = formatFollowersCount($user['followers_count']);
    }
    
    return $user;
}

function formatFollowersCount($count) {
    if ($count >= 1000000) {
        return round($count / 1000000, 1) . 'M+';
    } elseif ($count >= 100000) {
        return round($count / 1000) . 'K+';
    } elseif ($count >= 1000) {
        return round($count / 1000, 1) . 'K+';
    } else {
        return $count;
    }
}

function getCurrentUser() {
    return isLoggedIn() ? getUser($_SESSION['user_id']) : null;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function processTags($content) {
    return preg_replace_callback(
        '/#([\w\x{0410}-\x{044F}\d\-_]+)/u',
        function($matches) {
            $tag = $matches[1];
            return '<a href="?page=tag&tag='.urlencode($tag).'" class="tag">#'.$tag.'</a>';
        },
        $content
    );
}

function getPosts($user_id = null, $limit = null, $offset = 0) {
	global $pdo;
	$params = [];
	$where = "";

	if ($user_id) {
		$where = "WHERE p.user_id = ?";
		$params[] = $user_id;
	}

	$sql = "
		SELECT p.*, u.username, u.email, u.avatar_url, u.is_moderator, u.is_admin, u.is_verified, u.has_premium, 
		       (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
		       (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
		       p.views
		FROM posts p
		JOIN users u ON p.user_id = u.id
		$where
		ORDER BY p.created_at DESC
	";

	if ($limit !== null) {
		$sql .= "\n\t\tLIMIT ? OFFSET ?\n\t";
		$params[] = $limit;
		$params[] = $offset;
	}

	$stmt = $pdo->prepare($sql);
	$stmt->execute($params);
	$posts = $stmt->fetchAll();

	foreach ($posts as &$post) {
		$post['content'] = formatPostContent($post['content']);
		$post['files'] = getPostFiles($post['id']);
	}

	return $posts;
}
function formatPostContent($content) {
    $content = preg_replace_callback(
        '/(https?:\/\/[^\s<]+|www\.[^\s<]+)/i',
        function($matches) {
            $url = $matches[0];
            if (!preg_match('/^https?:\/\//i', $url)) {
                $url = 'http://' . $url;
            }
            return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') .
                '" target="_blank" rel="noopener noreferrer">' .
                htmlspecialchars($matches[0], ENT_QUOTES, 'UTF-8') . '</a>';
        },
        $content
    );

    $replacements = [
        '/\*\*(.*?)\*\*/' => '<strong>$1</strong>',       // **жирный**
        '/--(.*?)--/'      => '<small>$1</small>',        // --маленький--
        '/##(.*?)##/'      => '<h4 class="post-header">$1</h4>', // ##заголовок##
        '/\n/'             => '<br>'                     // перенос строк
    ];

    foreach ($replacements as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }

    $content = processTags($content);

    $allowed_tags = '<a><strong><em><b><i><u><small><h4><br><span><div><p>';
    $content = strip_tags($content, $allowed_tags);

    return $content;
}

function getPost($id) {
    global $pdo;
    $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$id]);

    $stmt = $pdo->prepare("
        SELECT p.*, u.username, u.email, u.avatar_url, u.is_moderator, u.is_admin, u.is_verified,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
               p.views
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);

    if ($post = $stmt->fetch()) {
        $post['content'] = processTags($post['content']);
        $post['files'] = getPostFiles($post['id']);
        return $post;
    }

    return null;
}

function getPostFiles($post_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM post_files WHERE post_id = ?");
    $stmt->execute([$post_id]);
    return $stmt->fetchAll();
}

function isLiked($post_id, $user_id, $is_community_post = false) {
    global $pdo;
    $table = $is_community_post ? 'community_likes' : 'likes';
    $stmt = $pdo->prepare("SELECT id FROM $table WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    return (bool)$stmt->fetch();
}

function getPopularComments($post_id, $limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT c.*, u.username, u.email, u.avatar_url, u.is_moderator, u.is_admin, u.is_verified, u.has_premium,
               (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id) as likes_count
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY likes_count DESC, c.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$post_id, $limit]);
    $comments = $stmt->fetchAll();

    foreach ($comments as &$comment) {
        $comment['content'] = processTags($comment['content']);
    }

    return $comments;
}

function getRecentComments($post_id, $limit = 5, $offset = 0) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT c.*, u.username, u.email, u.avatar_url, u.is_moderator, u.is_admin, u.is_verified, u.has_premium,
               (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id) as likes_count
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY c.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$post_id, $limit, $offset]);
    $comments = $stmt->fetchAll();

    foreach ($comments as &$comment) {
        $comment['content'] = processTags($comment['content']);
    }

    return $comments;
}

function isCommentLiked($comment_id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?");
    $stmt->execute([$comment_id, $user_id]);
    return (bool)$stmt->fetch();
}

function searchUsers($query, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT u.*, 
               (SELECT COUNT(*) FROM followers WHERE following_id = u.id) as followers_count,
               EXISTS(SELECT 1 FROM user_badges WHERE user_id = u.id AND badge_type = 'beta_tester') as is_beta_tester,
               EXISTS(SELECT 1 FROM user_badges WHERE user_id = u.id AND badge_type = 'supporter') as is_supporter,
               EXISTS(SELECT 1 FROM user_badges WHERE user_id = u.id AND badge_type = 'vip') as is_vip,
               u.has_premium as has_premium
        FROM users u
        WHERE (u.username LIKE ? OR u.email LIKE ?) AND u.is_banned = 0
        ORDER BY followers_count DESC
        LIMIT ?
    ");
    $search_term = "%$query%";
    $stmt->execute([$search_term, $search_term, $limit]);
    $users = $stmt->fetchAll();

    foreach ($users as &$user) {
        $user['formatted_followers'] = formatFollowersCount((int)$user['followers_count']);
    }

    return $users;
}

function isFollowing($follower_id, $following_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM followers WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$follower_id, $following_id]);
    return (bool)$stmt->fetch();
}

function createPasswordResetToken($email) {
    global $pdo;
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $pdo->prepare("DELETE FROM password_reset_tokens WHERE email = ?")->execute([$email]);
    $pdo->prepare("INSERT INTO password_reset_tokens (email, token, expires_at, created_at) VALUES (?, ?, ?, NOW())")
        ->execute([$email, $token, $expires_at]);

    return $token;
}

function validatePasswordResetToken($email, $token) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM password_reset_tokens WHERE email = ? AND token = ? AND expires_at > NOW()");
    $stmt->execute([$email, $token]);
    return $stmt->fetch();
}

function deletePasswordResetToken($email, $token) {
    global $pdo;
    $pdo->prepare("DELETE FROM password_reset_tokens WHERE email = ? AND token = ?")
        ->execute([$email, $token]);
}

function getUserPreview($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.avatar_url, u.is_verified, u.is_moderator, u.is_admin,
               (SELECT COUNT(*) FROM followers WHERE following_id = u.id) as followers_count,
               (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as posts_count,
               EXISTS(SELECT 1 FROM user_badges WHERE user_id = u.id AND badge_type = 'beta_tester') as is_beta_tester,
               EXISTS(SELECT 1 FROM user_badges WHERE user_id = u.id AND badge_type = 'supporter') as is_supporter,
               EXISTS(SELECT 1 FROM user_badges WHERE user_id = u.id AND badge_type = 'vip') as is_vip
        FROM users u
        WHERE u.id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function banUser($user_id, $admin_id) {
    global $pdo;
    $pdo->prepare("UPDATE users SET is_banned = 1, updated_at = NOW() WHERE id = ?")
        ->execute([$user_id]);
}

function unbanUser($user_id, $admin_id) {
    global $pdo;
    $pdo->prepare("UPDATE users SET is_banned = 0, updated_at = NOW() WHERE id = ?")
        ->execute([$user_id]);
}

$action = $_GET['action'] ?? '';
$page = $_GET['page'] ?? 'home';
$current_user = getCurrentUser();

if ($action === 'register' && !isLoggedIn()) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($username)) {
        $errors['username'] = 'Enter username';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'The name must contain at least 3 characters';
    }

    if (empty($email)) {
        $errors['email'] = 'Enter email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address';
    }

    if (empty($password)) {
        $errors['password'] = 'Enter password';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'The password must contain at least 8 characters';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords dont match';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors['email'] = 'The user with this email already exists';
        } else {
            $verification_code = generateCode();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password, verification_code, created_at)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$username, $email, $hashed_password, $verification_code]);

                $subject = "Your confirmation code for BlinX";
                $to = $email;

                $message = '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlinX | Verify</title>
    <style>
        :root {
            --bg-color: #0f0f15;
            --card-bg: #1a1a24;
            --text-color: #f0f0ff;
            --text-secondary: #b0b0c0;
            --accent-color: #6a5acd;
            --border-radius: 12px;
        }
        body {
            font-family: Inter, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 20px;
            text-align: center;
        }
        .code {
            font-size: 28px;
            font-weight: bold;
            color: var(--accent-color);
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: rgba(106, 90, 205, 0.1);
            border-radius: 8px;
        }
        .footer {
            margin-top: 30px;
            color: var(--text-secondary);
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="logo">BlinX</div>
        <h2>Registration Verify</h2>
        <p>Your verify code:</p>
        <div class="code">'.$verification_code.'</div>
        <p>Enter this code on site for finish registration.</p>
        <div class="footer">
            <p>If you havent registered on BlinX, ignore this email</p>
            <p>© '.date('Y').' BlinX</p>
        </div>
    </div>
</body>
</html>';

                $headers = "From: BlinX <no-reply@blin-x.space>\r\n";
                $headers .= "Reply-To: no-reply@blin-x.space\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                $mailSent = mail($to, $subject, $message, $headers);

                if ($mailSent) {
                    $_SESSION['verify_email'] = $email;
                    $pdo->commit();
                    redirect("?page=verify&type=register");
                } else {
                    $pdo->rollBack();
                    $errors['general'] = 'Ошибка при отправке письма с подтверждением';
                    error_log("Failed to send email to: $to");
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                $errors['general'] = 'Ошибка при регистрации';
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
}

if ($action === 'verify') {
    $code = trim($_POST['code'] ?? '');
    $type = $_GET['type'] ?? '';

    if (!preg_match('/^\d{6}$/', $code)) {
        $error = "Код должен состоять из 6 цифр";
    }
    elseif (empty($_SESSION['verify_email'])) {
        redirect("?page=home");
    }
    else {
        error_log("Проверка кода: email={$_SESSION['verify_email']}, введён код=$code");

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ?");
        $stmt->execute([$_SESSION['verify_email'], $code]);

        if ($user = $stmt->fetch()) {
            if ($type === 'register') {
                $pdo->prepare("UPDATE users SET is_active = 1, verification_code = NULL, last_login = NOW() WHERE id = ?")
                    ->execute([$user['id']]);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['logged_in'] = true;
                // Set long-lived IP-bound cookie
                setcookie('user_ip', $_SERVER['REMOTE_ADDR'] ?? '', time() + 365*24*60*60, '/', '', false, true);
                unset($_SESSION['verify_email']);
                $_SESSION['success'] = "Registration is completed! Welcome, {$user['username']}!";
                redirect("?page=feed");
            } elseif ($type === 'login') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['logged_in'] = true;
                $pdo->prepare("UPDATE users SET verification_code = NULL, last_login = NOW() WHERE id = ?")
                    ->execute([$user['id']]);
                // Set long-lived IP-bound cookie
                setcookie('user_ip', $_SERVER['REMOTE_ADDR'] ?? '', time() + 365*24*60*60, '/', '', false, true);
                unset($_SESSION['verify_email']);
                $_SESSION['success'] = "Welcome, {$user['username']}!";
                redirect("?page=feed");
            }
        } else {
            $error = "Invalid confirmation code";
        }
    }
}

if ($action === 'login' && !isLoggedIn()) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($user = $stmt->fetch()) {
        if ($user['is_banned']) {
            $error = "Your account is blocked";
        } elseif (password_verify($password, $user['password'])) {
            if ($user['is_active']) {
                $verification_code = generateCode();

                try {
                    $pdo->prepare("UPDATE users SET verification_code = ? WHERE id = ?")
                        ->execute([$verification_code, $user['id']]);

                    $subject = "Code for login in BlinX";

                    $message = '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code for login | BlinX</title>
    <style>
        :root {
            --bg-color: #0f0f15;
            --card-bg: #1a1a24;
            --text-color: #f0f0ff;
            --text-secondary: #b0b0c0;
            --accent-color: #6a5acd;
            --border-radius: 12px;
        }
        body {
            font-family: Inter, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 30px;
        }
        
        .footer-links a {
            margin: 0 10px;
            color: #aaa;
            text-decoration: none;
        }

        .footer-links a:hover {
            color: #fff;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 20px;
            text-align: center;
        }
        .code {
            font-size: 28px;
            font-weight: bold;
            color: var(--accent-color);
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: rgba(106, 90, 205, 0.1);
            border-radius: 8px;
        }
        .footer {
            margin-top: 30px;
            color: var(--text-secondary);
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="logo">BlinX</div>
        <h2>Code for login</h2>
        <p>Your verify code:</p>
        <div class="code">'.$verification_code.'</div>
        <p>Enter this code for login in your account.</p>
        <div class="footer">
            <p>If you did not request a password reset, ignore this email.</p>
            <p>© '.date('Y').' BlinX</p>
        </div>
    </div>
</body>
</html>';

                    $headers = "From: BlinX <no-reply@blin-x.space>\r\n";
                    $headers .= "Reply-To: no-reply@blin-x.space\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                    if (mail($email, $subject, $message, $headers)) {
                        $_SESSION['verify_email'] = $email;
                        redirect("?page=verify&type=login");
                    } else {
                        $error = "Ошибка при отправке письма с кодом";
                    }
                } catch (PDOException $e) {
                    $error = "Error when logging in: " . $e->getMessage();
                }
            } else {
                $error = "The account has not been activated. Check your email for confirmation";
            }
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
}

if ($action === 'logout' && isLoggedIn()) {
    session_destroy();
    $_SESSION['success'] = "You have successfully logged out";
    // Clear IP cookie
    setcookie('user_ip', '', time() - 3600, '/');
    redirect("?page=home");
}

if ($action === 'forgot_password') {
    $email = trim($_POST['email'] ?? '');

    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $token = createPasswordResetToken($email);
            $reset_link = "https://{$_SERVER['HTTP_HOST']}/app/?page=reset_password&email=".urlencode($email)."&token=$token";

            $subject = "🔑 Restoring access to BlinX";

            $message = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>🔑 Восстановление доступа к BlinX</title>
                <style type="text/css">
                    body { 
                        font-family: Arial, sans-serif; 
                        background-color: #f5f5f5; 
                        margin: 0; 
                        padding: 0; 
                        color: #333333;
                        line-height: 1.4;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 0 auto; 
                        background: #ffffff; 
                        border-radius: 8px;
                        overflow: hidden;
                        border: 1px solid #e1e1e1;
                    }
                    .header {
                        background: #6a5acd;
                        padding: 25px;
                        text-align: center;
                    }
                    .logo {
                        font-size: 28px;
                        font-weight: bold;
                        color: white;
                        margin: 0;
                        text-decoration: none;
                    }
                    .content {
                        padding: 25px;
                    }
                    .btn {
                        display: block;
                        width: 200px;
                        background: #6a5acd;
                        color: white;
                        text-align: center;
                        padding: 12px 0;
                        text-decoration: none;
                        border-radius: 6px;
                        font-weight: bold;
                        margin: 20px auto;
                    }
                    .footer {
                        padding: 15px;
                        text-align: center;
                        color: #999999;
                        font-size: 13px;
                        border-top: 1px solid #eeeeee;
                    }
                    .link {
                        color: #6a5acd;
                        word-break: break-all;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <p class="logo">BlinX</p>
                    </div>
                    
                    <div class="content">
                        <h2 style="margin-top: 0;">Password recovering</h2>
                        <p>We get your password recovering request.</p>
                        
                        <p style="text-align: center;">
                            <a href="'.$reset_link.'" class="btn" style="color: white;">Recover password</a>
                        </p>
                        
                        <p>or copy this link in browser:</p>
                        <p><a href="'.$reset_link.'" class="link">'.$reset_link.'</a></p>
                        
                        <p><small>The link is valid for 1 hour.</small></p>
                    </div>
                    
                    <div class="footer">
                        <p>If you did not request a password reset, ignore this email.</p>
                        <p>© BlinX '.date('Y').'</p>
                    </div>
                </div>
            </body>
            </html>';

            $headers = "From: BlinX <no-reply@blink-web.ru>\r\n";
            $headers .= "Reply-To: no-reply@blink-web.ru\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();

            if (mail($email, $subject, $message, $headers)) {
                $_SESSION['success'] = "On your email send the mail with the guide";
                redirect("?page=login");
            } else {
                $error = "Failed to send mail";
            }
        } else {
            $error = "User with that email dont finded";
        }
    }
}

if ($action === 'get_user_preview' && isLoggedIn()) {
    $user_id = $_GET['user_id'] ?? 0;
    $user = getUserPreview($user_id);

    if ($user) {
        header('Content-Type: application/json');
        echo json_encode([
            'username' => $user['username'],
            'avatar_url' => $user['avatar_url'],
            'posts_count' => $user['posts_count'],
            'followers_count' => $user['followers_count'],
            'is_current_user' => $user['id'] == $current_user['id'],
            'is_following' => isFollowing($current_user['id'], $user['id'])
        ]);
        exit;
    }
}

if ($action === 'like_community_post' && isLoggedIn()) {
    $post_id = $_POST['post_id'] ?? 0;

    if ($post_id) {
        try {
            if (isLiked($post_id, $current_user['id'], true)) {
                $pdo->prepare("DELETE FROM community_likes WHERE post_id = ? AND user_id = ?")
                    ->execute([$post_id, $current_user['id']]);
            } else {
                $pdo->prepare("INSERT INTO community_likes (post_id, user_id, created_at) VALUES (?, ?, NOW())")
                    ->execute([$post_id, $current_user['id']]);
            }

        } catch (PDOException $e) {
        }
    }
}

if ($action === 'reset_password') {
    $email = $_POST['email'] ?? '';
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($email) && !empty($token) && !empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $error = "Passwords dont matchт";
        } elseif (strlen($new_password) < 8) {
            $error = "The password must contain at least 8 characters.";
        } elseif ($token_data = validatePasswordResetToken($email, $token)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?")
                ->execute([$hashed_password, $email]);

            deletePasswordResetToken($email, $token);
            $_SESSION['success'] = "The password has been successfully changed. You can now log in with a new password.";
            redirect("?page=login");
        } else {
            $error = "Invalid or outdated password reset link";
        }
    }
}

if ($action === 'update_profile' && isLoggedIn()) {
    $username = trim($_POST['username'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $avatar_url = trim($_POST['avatar_url'] ?? '');
    $banner_url = trim($_POST['banner_url'] ?? '');

    if (empty($username)) {
        $error = "The user's name is required";
    } else {
        try {
            $pdo->prepare("UPDATE users SET username = ?, bio = ?, avatar_url = ?, banner_url = ?, updated_at = NOW() WHERE id = ?")
                ->execute([$username, $bio, $avatar_url, $banner_url, $current_user['id']]);
            $_SESSION['success'] = "Profile has been successfully updated";
            redirect("?page=profile");
        } catch (PDOException $e) {
            $error = "Error updating the profile: " . $e->getMessage();
        }
    }
}

if ($action === 'create_post' && isLoggedIn()) {
    $content = trim($_POST['content'] ?? '');
    $files = $_FILES['files'] ?? [];

    if (empty($content)) {
        $error = "A post cannot be empty";
    } else {
        try {
            $pdo->beginTransaction();

            $pdo->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())")
                ->execute([$current_user['id'], $content]);
            $post_id = $pdo->lastInsertId();

            if (!empty($files['name'][0])) {
                $upload_dir = 'uploads/posts/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $file_name = uniqid() . '_' . basename($files['name'][$i]);
                        $file_path = $upload_dir . $file_name;

                        if (move_uploaded_file($files['tmp_name'][$i], $file_path)) {
                            $file_type = mime_content_type($file_path);
                            $pdo->prepare("INSERT INTO post_files (post_id, file_url, file_type, created_at) VALUES (?, ?, ?, NOW())")
                                ->execute([$post_id, $file_path, $file_type]);
                        }
                    }
                }
            }

            $pdo->commit();
            $_SESSION['success'] = "The post was successfully published";
            redirect("?page=feed");
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Error when publishing a post: " . $e->getMessage();
        }
    }
}

if ($action === 'delete_post' && isLoggedIn()) {
    try {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Security error: Invalid token');
        }

        $post_id = (int)($_POST['post_id'] ?? 0);
        if ($post_id <= 0) {
            throw new Exception('Invalid post ID');
        }

        $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();

        if (!$post) {
            throw new Exception('The post was not found');
        }

        $can_delete = ($post['user_id'] == $current_user['id'])
            || $current_user['is_moderator']
            || $current_user['is_admin'];

        if (!$can_delete) {
            throw new Exception('You dont have the rights to delete');
        }

        $pdo->beginTransaction();

        $pdo->prepare("DELETE FROM likes WHERE post_id = ?")->execute([$post_id]);
        $pdo->prepare("DELETE FROM comments WHERE post_id = ?")->execute([$post_id]);
        $pdo->prepare("DELETE FROM post_files WHERE post_id = ?")->execute([$post_id]);

        $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$post_id]);

        $pdo->commit();

        if (isAjaxRequest()) {
            echo json_encode(['success' => true]);
            exit;
        }

        $_SESSION['success'] = 'The post was successfully deleted';
        redirect($_SERVER['HTTP_REFERER'] ?? '?page=feed');

    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if (isAjaxRequest()) {
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }

        $_SESSION['error'] = $e->getMessage();
        redirect($_SERVER['HTTP_REFERER'] ?? '?page=feed');
    }
}

if ($action === 'toggle_employee_status' && isLoggedIn() && $current_user['is_admin']) {
    $user_id = (int)$_POST['user_id'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT is_employee FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $current_status = $stmt->fetchColumn();

        $new_status = $current_status ? 0 : 1;
        $pdo->prepare("UPDATE users SET is_employee = ? WHERE id = ?")
            ->execute([$new_status, $user_id]);

        $pdo->commit();

        $_SESSION['success'] = "Статус сотрудника " . ($new_status ? "выдан" : "снят");
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Ошибка изменения статуса";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

if ($action === 'create_community_post' && isLoggedIn()) {
    $community_id = (int)($_POST['community_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $files = $_FILES['files'] ?? [];

    if (empty($content)) {
        $error = "A post cannot be empty";
    } elseif (!$community_id) {
        $error = "The community is not specified";
    } elseif (!isCommunityMember($community_id, $current_user['id'])) {
        $error = "You are not a member of this community.";
    } else {
        try {
            $pdo->beginTransaction();

            $pdo->prepare("INSERT INTO community_posts (community_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())")
                ->execute([$community_id, $current_user['id'], $content]);
            $post_id = $pdo->lastInsertId();

            if (!empty($files['name'][0])) {
                $upload_dir = 'uploads/community_posts/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $file_name = uniqid() . '_' . basename($files['name'][$i]);
                        $file_path = $upload_dir . $file_name;

                        if (move_uploaded_file($files['tmp_name'][$i], $file_path)) {
                            $file_type = mime_content_type($file_path);
                            $pdo->prepare("INSERT INTO community_post_files (post_id, file_url, file_type, created_at) VALUES (?, ?, ?, NOW())")
                                ->execute([$post_id, $file_path, $file_type]);
                        }
                    }
                }
            }

            $pdo->commit();
            $_SESSION['success'] = "The post was successfully published";

            $stmt = $pdo->prepare("SELECT slug FROM communities WHERE id = ?");
            $stmt->execute([$community_id]);
            $community = $stmt->fetch();

            if ($community && isset($community['slug'])) {
                $slug = $community['slug'];
                redirect("?page=community&slug=$slug");
                exit;
            } else {
                $_SESSION['error'] = "Couldn't find a community for the redirect";
                redirect("?page=home");
                exit;
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Ошибка при публикации поста: " . $e->getMessage();
        }
    }
}

if ($action === 'edit_post' && isLoggedIn()) {
    $post_id = (int)($_POST['post_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(['error' => 'Security error: Invalid token']));
    }

    if (empty($content)) {
        die(json_encode(['error' => 'A post cannot be empty']));
    }

    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    $content = preg_replace('/<script.*?>.*?<\/script>/is', '', $content);

    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post || $post['user_id'] != $current_user['id']) {
        die(json_encode(['error' => 'You do not have the rights to edit this post']));
    }

    try {
        $pdo->prepare("UPDATE posts SET content = ?, updated_at = NOW() WHERE id = ?")
            ->execute([$content, $post_id]);

        echo json_encode([
            'success' => true,
            'content' => nl2br($content)
        ]);
        exit;
    } catch (PDOException $e) {
        die(json_encode(['error' => 'Error updating the post']));
    }
}

function createCommunity($name, $description, $creator_id) {
    global $pdo;

    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $slug = trim($slug, '-');

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO communities (name, slug, description, creator_id, created_at) 
                              VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $slug, $description, $creator_id]);
        $community_id = $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO community_members (community_id, user_id, is_admin, created_at) 
                      VALUES (?, ?, 1, NOW())")->execute([$community_id, $creator_id]);

        $pdo->commit();
        return $community_id;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

function getCommunity($id_or_slug) {
    global $pdo;

    $field = is_numeric($id_or_slug) ? 'id' : 'slug';
    $stmt = $pdo->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM community_members WHERE community_id = c.id) as members_count,
               (SELECT COUNT(*) FROM community_posts WHERE community_id = c.id) as posts_count,
               u.username as creator_name
        FROM communities c
        JOIN users u ON c.creator_id = u.id
        WHERE c.$field = ?
    ");
    $stmt->execute([$id_or_slug]);
    $community = $stmt->fetch();

    if ($community) {
        $community['is_member'] = isCommunityMember($community['id'], $_SESSION['user_id'] ?? 0);
        $community['is_admin'] = isCommunityAdmin($community['id'], $_SESSION['user_id'] ?? 0);
        $community['is_moderator'] = isCommunityModerator($community['id'], $_SESSION['user_id'] ?? 0);
    }

    return $community;
}

function isCommunityMember($community_id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM community_members WHERE community_id = ? AND user_id = ?");
    $stmt->execute([$community_id, $user_id]);
    return (bool)$stmt->fetch();
}

function isCommunityAdmin($community_id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM community_members WHERE community_id = ? AND user_id = ? AND is_admin = 1");
    $stmt->execute([$community_id, $user_id]);
    return (bool)$stmt->fetch();
}

function isCommunityModerator($community_id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM community_members WHERE community_id = ? AND user_id = ? AND is_moderator = 1");
    $stmt->execute([$community_id, $user_id]);
    return (bool)$stmt->fetch();
}

function joinCommunity($community_id, $user_id) {
    global $pdo;

    if (isCommunityMember($community_id, $user_id)) {
        return false;
    }

    try {
        $pdo->beginTransaction();

        $pdo->prepare("INSERT INTO community_members (community_id, user_id, created_at) 
                      VALUES (?, ?, NOW())")->execute([$community_id, $user_id]);

        $members_count = $pdo->query("SELECT COUNT(*) as count FROM community_members WHERE community_id = $community_id")->fetch()['count'];
        if ($members_count >= 100) {
            $pdo->prepare("UPDATE communities SET is_verified = 1 WHERE id = ?")->execute([$community_id]);
        }

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

function leaveCommunity($community_id, $user_id) {
    global $pdo;
    return $pdo->prepare("DELETE FROM community_members WHERE community_id = ? AND user_id = ?")
        ->execute([$community_id, $user_id]);
}

function getCommunityPosts($community_id, $limit = 10, $offset = 0) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.*, u.username, u.email, u.avatar_url, u.is_moderator, u.is_admin, u.is_verified, u.has_premium,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
               p.views
        FROM community_posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.community_id = ?
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$community_id, $limit, $offset]);
    $posts = $stmt->fetchAll();

    foreach ($posts as &$post) {
        $post['content'] = formatPostContent($post['content']);
        $post['files'] = getCommunityPostFiles($post['id']);
    }

    return $posts;
}

function getCommunityPostFiles($post_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM community_post_files WHERE post_id = ?");
    $stmt->execute([$post_id]);
    return $stmt->fetchAll();
}

function searchCommunities($query, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM community_members WHERE community_id = c.id) as members_count,
               u.username as creator_name
        FROM communities c
        JOIN users u ON c.creator_id = u.id
        WHERE c.name LIKE ?
        ORDER BY members_count DESC
        LIMIT ?
    ");
    $search_term = "%$query%";
    $stmt->execute([$search_term, $limit]);
    return $stmt->fetchAll();
}

function updateCommunity($community_id, $name, $description, $avatar_url = null, $banner_url = null) {
    global $pdo;

    $data = [
        'name' => $name,
        'description' => $description,
        'updated_at' => date('Y-m-d H:i:s'),
        'id' => $community_id
    ];

    if ($avatar_url !== null) {
        $data['avatar_url'] = $avatar_url;
    }

    if ($banner_url !== null) {
        $data['banner_url'] = $banner_url;
    }

    $fields = [];
    foreach ($data as $key => $value) {
        if ($key !== 'id') {
            $fields[] = "$key = :$key";
        }
    }

    $sql = "UPDATE communities SET " . implode(', ', $fields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

function formatContent($content) {
    $content = preg_replace_callback(
        '/(https?:\/\/[^\s]+|www\.[^\s]+)/i',
        function($matches) {
            $url = $matches[0];
            if (!preg_match('/^https?:\/\//i', $url)) {
                $url = 'http://' . $url;
            }
            return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($matches[0], ENT_QUOTES, 'UTF-8') . '</a>';
        },
        $content
    );

    $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);

    $content = preg_replace('/--(.*?)--/', '<small>$1</small>', $content);

    $content = preg_replace('/##(.*?)##/', '<h4>$1</h4>', $content);

    $content = nl2br($content);

    return $content;
}

if ($action === 'like_post' && isLoggedIn()) {
    $post_id = $_POST['post_id'] ?? 0;

    if ($post_id) {
        try {
            if (isLiked($post_id, $current_user['id'])) {
                $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?")
                    ->execute([$post_id, $current_user['id']]);
            } else {
                $pdo->prepare("INSERT INTO likes (post_id, user_id, created_at) VALUES (?, ?, NOW())")
                    ->execute([$post_id, $current_user['id']]);
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $likes_count = $pdo->query("SELECT COUNT(*) as count FROM likes WHERE post_id = $post_id")->fetch()['count'];
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'likes_count' => $likes_count]);
                exit;
            } else {
                redirect($_SERVER['HTTP_REFERER'] ?? "?page=feed");
            }
        } catch (PDOException $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            } else {
                $error = "Ошибка при обработке лайка: " . $e->getMessage();
            }
        }
    }
}

if ($action === 'create_community' && isLoggedIn()) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) {
        $error = "The community name is required";
    } else {
        $community_id = createCommunity($name, $description, $current_user['id']);
        if ($community_id) {
            $_SESSION['success'] = "The community has been successfully created";
            redirect("?page=community&slug=" . getCommunity($community_id)['slug']);
        } else {
            $error = "Error when creating a community";
        }
    }
}

if ($action === 'toggle_community_membership' && isLoggedIn()) {
    $community_id = (int)($_POST['community_id'] ?? 0);

    if ($community_id) {
        if (isCommunityMember($community_id, $current_user['id'])) {
            leaveCommunity($community_id, $current_user['id']);
            $action_text = "покинули";
        } else {
            joinCommunity($community_id, $current_user['id']);
            $action_text = "вступили";
        }

        header("Location: " . $_SERVER['HTTP_REFERER'] ?? "?page=community&id=$community_id");
        exit();
    }
}

if ($action === 'update_community' && isLoggedIn()) {
    $community_id = (int)($_POST['community_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $avatar_url = trim($_POST['avatar_url'] ?? '');
    $banner_url = trim($_POST['banner_url'] ?? '');

    if (empty($name)) {
        $error = "The community name is required";
    } elseif (!isCommunityAdmin($community_id, $current_user['id'])) {
        $error = "You don't have the rights to edit this community.";
    } else {
        if (updateCommunity($community_id, $name, $description, $avatar_url, $banner_url)) {
            $_SESSION['success'] = "The community has been successfully updated";
            redirect("?page=community&id=$community_id");
        } else {
            $error = "Error updating the community";
        }
    }
}

if ($action === 'like_comment' && isLoggedIn()) {
    $comment_id = $_POST['comment_id'] ?? 0;

    if ($comment_id) {
        try {
            if (isCommentLiked($comment_id, $current_user['id'])) {
                $pdo->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?")
                    ->execute([$comment_id, $current_user['id']]);
            } else {
                $pdo->prepare("INSERT INTO comment_likes (comment_id, user_id, created_at) VALUES (?, ?, NOW())")
                    ->execute([$comment_id, $current_user['id']]);
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $likes_count = $pdo->query("SELECT COUNT(*) as count FROM comment_likes WHERE comment_id = $comment_id")->fetch()['count'];
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'likes_count' => $likes_count]);
                exit;
            } else {
                redirect($_SERVER['HTTP_REFERER'] ?? "?page=feed");
            }
        } catch (PDOException $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            } else {
                $error = "Ошибка при обработке лайка: " . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['scroll_to'])) {
    echo '<script>window.addEventListener("load", function() { window.scrollTo(0, ' . intval($_GET['scroll_to']) . '); });</script>';
}

if ($action === 'add_comment' && isLoggedIn()) {
    $post_id = $_POST['post_id'] ?? 0;
    $content = trim($_POST['content'] ?? '');
    $scroll_position = $_POST['scroll_position'] ?? 0;

    if (empty($content)) {
        $error = "A comment cannot be empty";
    } elseif ($post_id) {
        try {
            $safe_content = htmlspecialchars(processTags($content), ENT_QUOTES, 'UTF-8');
            $pdo->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())")
                ->execute([$post_id, $current_user['id'], $safe_content]);
            $_SESSION['success'] = "Comment added";

            $redirect_url = $_SERVER['HTTP_REFERER'] ?? "?page=feed";
            if ($scroll_position) {
                $redirect_url .= (strpos($redirect_url, '?') === false ? '?' : '&');
                $redirect_url .= "scroll_to=" . urlencode($scroll_position);
            }
            redirect($redirect_url);
        } catch (PDOException $e) {
            $error = "Error when adding a comment: " . $e->getMessage();
        }
    }
}

if ($action === 'toggle_follow' && isLoggedIn()) {
    $user_id = $_POST['user_id'] ?? 0;

    if ($user_id && $user_id != $current_user['id']) {
        try {
            if (isFollowing($current_user['id'], $user_id)) {
                $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?")
                    ->execute([$current_user['id'], $user_id]);
                $action_text = "отписан";
            } else {
                $pdo->prepare("INSERT INTO followers (follower_id, following_id, created_at) VALUES (?, ?, NOW())")
                    ->execute([$current_user['id'], $user_id]);
                $action_text = "подписан";

                $followers_count = $pdo->query("SELECT COUNT(*) as count FROM followers WHERE following_id = $user_id")->fetch()['count'];
                if ($followers_count >= 100) {
                    $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?")->execute([$user_id]);
                }
            }

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $followers_count = $pdo->query("SELECT COUNT(*) as count FROM followers WHERE following_id = $user_id")->fetch()['count'];
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'action' => $action_text, 'followers_count' => $followers_count]);
                exit;
            } else {
                redirect($_SERVER['HTTP_REFERER'] ?? "?page=profile&user_id=$user_id");
            }
        } catch (PDOException $e) {
            $error = "Ошибка при обработке подписки: " . $e->getMessage();
        }
    }
}

if ($page === 'search') {
    $search_query = trim($_GET['q'] ?? '');
    if (!empty($search_query)) {
        $search_results = searchUsers($search_query);
    } else {
        $error = "Enter your search query";
    }
}

if ($action === 'ban_user' && isLoggedIn() && ($current_user['is_admin'] || $current_user['is_moderator'])) {
    $user_id = $_POST['user_id'] ?? 0;

    if ($user_id && $user_id != $current_user['id']) {
        banUser($user_id, $current_user['id']);
        $_SESSION['success'] = "User has been baned";
        redirect($_SERVER['HTTP_REFERER'] ?? "?page=profile&user_id=$user_id");
    }
}

if ($action === 'unban_user' && isLoggedIn() && ($current_user['is_admin'] || $current_user['is_moderator'])) {
    $user_id = $_POST['user_id'] ?? 0;

    if ($user_id) {
        unbanUser($user_id, $current_user['id']);
        $_SESSION['success'] = "User has been unbaned";
        redirect($_SERVER['HTTP_REFERER'] ?? "?page=profile&user_id=$user_id");
    }
}

?>