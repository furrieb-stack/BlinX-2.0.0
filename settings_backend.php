<?php
require_once __DIR__ . '/db.php';
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}


$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$current_user = $stmt->fetch(PDO::FETCH_ASSOC);
$marketplace_themes = [];

file_put_contents('payment_log.txt', date('Y-m-d H:i:s')." - Webhook received\n", FILE_APPEND);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    file_put_contents('payment_log.txt', "Raw data: ".$input."\n", FILE_APPEND);

    $event = json_decode($input, true);
    file_put_contents('payment_log.txt', "Decoded: ".print_r($event, true)."\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_blinks'])) {
    $blinks_amount = (int)$_POST['blinks_amount'];

    if ($blinks_amount < 1) {
        $_SESSION['error'] = "Invalid amount of blinks";
        header("Location: ?page=settings&tab=topup");
        exit;
    }

    $amount = $blinks_amount * 1.3;
    $amount = number_format($amount, 2, '.', '');

    try {
        $response = $yookassa->createPayment(
            $amount,
            "Purchase of {$blinks_amount} blinks",
            [
                'user_id' => $current_user['id'],
                'blinks_amount' => $blinks_amount
            ],
            '../app/settings.php?page=settings&tab=topup&success=1'
        );

        $stmt = $db->prepare("INSERT INTO payments (user_id, payment_id, amount, blinks_amount, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $current_user['id'],
            $response->id,
            $amount,
            $blinks_amount,
            'pending'
        ]);

        header('Location: ' . $response->getConfirmation()->getConfirmationUrl());
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = "Payment creation error: " . $e->getMessage();
        header("Location: ?page=settings&tab=topup");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event'])) {
    $logFile = __DIR__ . '/yookassa_payments.log';
    $logMessage = function($message) use ($logFile) {
        file_put_contents($logFile, settings . phpdate('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
    };

    try {
        $logMessage('Received webhook from YooKassa');
        $input = file_get_contents('php://input');
        $logMessage('Raw input: ' . $input);

        $event = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decoding error: ' . json_last_error_msg());
        }

        $logMessage('Event type: ' . $event['event']);

        $yookassa = require __DIR__ . '/YooKassaHelper.php';

        if ($event['event'] === 'payment.waiting_for_capture') {
            $logMessage('Processing payment.waiting_for_capture');
            $payment = $yookassa->getPaymentInfo($event['object']['id']);

            if ($payment->getStatus() === 'waiting_for_capture') {
                $logMessage('Confirming payment: ' . $payment->getId());
                $response = $yookassa->capturePayment([
                    'amount' => $payment->getAmount(),
                ], $payment->getId());

                $logMessage('Payment confirmed: ' . $response->getStatus());
            }
        }

        if ($event['event'] === 'payment.succeeded') {
            $logMessage('Processing payment.succeeded');
            $payment = $event['object'];

            if (!isset($payment['metadata']['user_id']) || !isset($payment['metadata']['blinks_amount'])) {
                throw new Exception('Required metadata missing in payment');
            }

            $metadata = $payment['metadata'];
            $logMessage('Metadata: ' . print_r($metadata, true));

            $stmt = $db->prepare("SELECT * FROM payments WHERE payment_id = ? AND status = 'succeeded'");
            $stmt->execute([$payment['id']]);

            if ($stmt->rowCount() === 0) {
                $logMessage('New payment, starting processing');

                $db->beginTransaction();

                try {
                    $stmt = $db->prepare("UPDATE payments SET status = 'succeeded', paid_at = NOW() WHERE payment_id = ?");
                    $stmt->execute([$payment['id']]);
                    $logMessage('Payment status updated');

                    $stmt = $db->prepare("UPDATE users SET blinks = blinks + ? WHERE id = ?");
                    $stmt->execute([$metadata['blinks_amount'], $metadata['user_id']]);
                    $logMessage('Blinks added');

                    $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
                    $stmt->execute([$metadata['user_id']]);
                    $user = $stmt->fetch();
                    $username = $user ? $user['username'] : 'Unknown';

                    $discordMessage = [
                        'content' => "Blinks purchase via YooKassa",
                        'embeds' => [
                            [
                                'title' => "Payment details",
                                'fields' => [
                                    ['name' => 'User', 'value' => $username . " (ID: {$metadata['user_id']})", 'inline' => true],
                                    ['name' => 'Amount', 'value' => $payment['amount']['value'] . ' ' . $payment['amount']['currency'], 'inline' => true],
                                    ['name' => 'Blinks', 'value' => $metadata['blinks_amount'], 'inline' => true],
                                    ['name' => 'Payment ID', 'value' => $payment['id'], 'inline' => true],
                                    ['name' => 'Status', 'value' => $payment['status'], 'inline' => true]
                                ],
                                'color' => 15844367,
                                'timestamp' => date('c')
                            ]
                        ]
                    ];

                    $ch = curl_init("https://discord.com/api/webhooks/1400060158499098756/Hahm7LgcvzuhtnqGUSemfUQRylKMTFFDvyGotYTqJNz2Q8rodC6b-BgRTiZiONCtB9lQ");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($discordMessage));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_exec($ch);
                    curl_close($ch);
                    $logMessage('Notification sent to Discord');

                    $db->commit();
                    $logMessage('Transaction completed successfully');

                } catch (Exception $e) {
                    $db->rollBack();
                    $logMessage('Transaction error: ' . $e->getMessage());
                    throw $e;
                }
            } else {
                $logMessage('Payment was already processed');
            }
        }

        http_response_code(200);
        $logMessage('Webhook processing completed successfully');
        exit;

    } catch (Exception $e) {
        $errorMessage = 'Webhook processing error: ' . $e->getMessage();
        $logMessage($errorMessage);
        error_log($errorMessage);
        http_response_code(400);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    handleSettingsForm($db, $current_user['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_theme') {
    if (!$current_user['is_verified']) {
        $_SESSION['error'] = 'Only verified users can upload themes';
        header("Location: ?page=settings&tab=appearance");
        exit;
    }
    
    $theme_name = trim($_POST['theme_name']);
    $theme_price = (int)$_POST['theme_price'];
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    
    
    $_SESSION['success'] = 'Theme uploaded successfully!';
    header("Location: ?page=settings&tab=appearance");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'purchase_theme') {
    $theme_id = (int)$_POST['theme_id'];

    
    $_SESSION['success'] = 'Theme purchased successfully!';
    header("Location: ?page=settings&tab=appearance");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_premium_blinks'])) {
    try {
        $purchase_type = $_POST['purchase_premium_blinks'];
        $required_blinks = 0;
        $interval = '';

        switch ($purchase_type) {
            case '1month':
                $required_blinks = 2500;
                $interval = '1 MONTH';
                break;
            case '3months':
                $required_blinks = 5000;
                $interval = '3 MONTH';
                break;
            case '1year':
                $required_blinks = 10000;
                $interval = '1 YEAR';
                break;
            default:
                throw new Exception('Invalid premium subscription type');
        }

        if ($current_user['blinks'] < $required_blinks) {
            $_SESSION['error'] = "Not enough blinks! Required: $required_blinks, you have: {$current_user['blinks']}";
            header("Location: ?page=settings&tab=premium");
            exit;
        }

        $stmt = $db->prepare("SELECT premium_until FROM users WHERE id = ?");
        $stmt->execute([$current_user['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $new_premium_until = date('Y-m-d H:i:s', strtotime("+$interval"));
        if (!empty($user['premium_until']) && strtotime($user['premium_until']) > time()) {
            $new_premium_until = date('Y-m-d H:i:s', strtotime($user['premium_until'] . " +$interval"));
        }

        $stmt = $db->prepare("UPDATE users SET has_premium = 1, premium_until = ?, blinks = blinks - ? WHERE id = ?");
        $stmt->execute([$new_premium_until, $required_blinks, $current_user['id']]);

        $discordWebhook = "https://discord.com/api/webhooks/1400060158499098756/Hahm7LgcvzuhtnqGUSemfUQRylKMTFFDvyGotYTqJNz2Q8rodC6b-BgRTiZiONCtB9lQ";
        $discordMessage = [
            'content' => "New Premium subscription purchase",
            'embeds' => [
                [
                    'title' => "Purchase details",
                    'fields' => [
                        ['name' => 'User', 'value' => $current_user['username'] . " (ID: {$current_user['id']})", 'inline' => true],
                        ['name' => 'Subscription type', 'value' => $purchase_type, 'inline' => true],
                        ['name' => 'Cost', 'value' => "$required_blinks blinks", 'inline' => true],
                        ['name' => 'Registration date', 'value' => $current_user['created_at'], 'inline' => true],
                        ['name' => 'Subscription until', 'value' => $new_premium_until, 'inline' => true]
                    ],
                    'color' => 15844367,
                    'timestamp' => date('c')
                ]
            ]
        ];

        $ch = curl_init($discordWebhook);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($discordMessage));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);

        $_SESSION['success'] = "Premium subscription activated for $required_blinks blinks!";
        header("Location: ?page=settings&tab=premium");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Premium activation error: " . $e->getMessage();
        header("Location: ?page=settings&tab=premium");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ?page=settings&tab=premium");
        exit;
    }
}

$tab = $_GET['tab'] ?? 'account';
$validTabs = ['account', 'privacy', 'notifications', 'appearance', 'security', 'premium', 'topup'];
if (!in_array($tab, $validTabs)) {
    $tab = 'account';
}

function getUserCountry($ip) {
    $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode");
    if ($response) {
        $data = json_decode($response, true);
        return $data['countryCode'] ?? null;
    }
    return null;
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

$userIP = getUserIP();
$userCountry = getUserCountry($userIP);

file_put_contents(__DIR__ . '/ip_logs.txt', date('Y-m-d H:i:s') . " - IP: $userIP - Country: $userCountry\n", FILE_APPEND);

function handleSettingsForm($db, $user_id) {
    global $current_user;
    $action = $_POST['action'];

    try {
        switch ($action) {
            case 'update_account':
                $username = $_POST['username'];
                $email = $_POST['email'];
                $bio = $_POST['bio'] ?? '';

                $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, bio = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$username, $email, $bio, $user_id]);

                $_SESSION['success'] = "Account data updated";
                break;

            case 'update_privacy':
                $show_email = isset($_POST['show_email']) ? 1 : 0;
                $show_last_seen = isset($_POST['show_last_seen']) ? 1 : 0;

                $stmt = $db->prepare("UPDATE users SET show_email = ?, show_last_seen = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$show_email, $show_last_seen, $user_id]);

                $_SESSION['success'] = "Privacy settings updated";
                break;

            case 'update_notifications':
                $notify_email = isset($_POST['notify_email']) ? 1 : 0;
                $notify_push = isset($_POST['notify_push']) ? 1 : 0;

                $stmt = $db->prepare("UPDATE users SET notify_email = ?, notify_push = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$notify_email, $notify_push, $user_id]);

                $_SESSION['success'] = "Notification settings updated";
                break;

            case 'update_appearance':
                $theme = $_POST['theme'];

                if ($theme === 'theme5' && !$current_user['has_premium']) {
                    $_SESSION['error'] = "Premium subscription required for this theme";
                    header("Location: ?page=settings&tab=appearance");
                    exit;
                }

                if ($theme === 'theme7' && !$current_user['has_premium']) {
                    $_SESSION['error'] = "Premium subscription required for this theme";
                    header("Location: ?page=settings&tab=appearance");
                    exit;
                }

                if ($theme === 'theme8' && !$current_user['has_premium']) {
                    $_SESSION['error'] = "Premium subscription required for this theme";
                    header("Location: ?page=settings&tab=appearance");
                    exit;
                }

                if ($theme === 'theme6') {
                    if ($current_user['blinks'] < 1500) {
                        $_SESSION['error'] = "You need 1500 blinks to purchase this animated wallpaper";
                        header("Location: ?page=settings&tab=appearance");
                        exit;
                    }

                    try {
                        $db->beginTransaction();

                        $stmt = $db->prepare("UPDATE users SET blinks = blinks - 1500 WHERE id = ?");
                        $stmt->execute([$current_user['id']]);

                        setcookie('site_theme', $theme, time() + 60*60*24*30, '/');

                        $db->commit();

                        $_SESSION['success'] = "Animated wallpaper purchased for 1500 blinks! Your new balance: " . ($current_user['blinks'] - 1500) . " blinks";
                    } catch (PDOException $e) {
                        $db->rollBack();
                        $_SESSION['error'] = "Transaction failed: " . $e->getMessage();
                        header("Location: ?page=settings&tab=appearance");
                        exit;
                    }
                } else {
                    setcookie('site_theme', $theme, time() + 60*60*24*30, '/');
                    $_SESSION['success'] = "Appearance settings saved";
                }
                break;

            case 'update_avatar':
                if (isset($_FILES['avatar'])) {
                    $upload = handleAvatarUpload($_FILES['avatar'], $user_id);
                    if ($upload['success']) {
                        $stmt = $db->prepare("UPDATE users SET avatar_url = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$upload['path'], $user_id]);
                        $_SESSION['success'] = "Avatar updated successfully";
                    } else {
                        $_SESSION['error'] = $upload['error'];
                    }
                }
                break;

            case 'update_banner':
                if (isset($_FILES['banner'])) {
                    $upload = handleBannerUpload($_FILES['banner'], $user_id);
                    if ($upload['success']) {
                        $stmt = $db->prepare("UPDATE users SET banner_url = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$upload['path'], $user_id]);
                        $_SESSION['success'] = "Banner updated successfully";
                    } else {
                        $_SESSION['error'] = $upload['error'];
                    }
                }
                break;

            case 'change_password':
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if (!password_verify($current_password, $user['password'])) {
                    $_SESSION['error'] = "Current password is incorrect";
                    return;
                }

                if ($new_password !== $confirm_password) {
                    $_SESSION['error'] = "Passwords don't match";
                    return;
                }

                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);

                $_SESSION['success'] = "Password changed successfully";
                break;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    header("Location: ?page=settings&tab=" . $_POST['tab']);
    exit;
}

function handleBannerUpload($file, $user_id) {
    global $current_user;
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 10 * 1024 * 1024;

    if ($file['type'] === 'image/gif' && !$current_user['has_premium']) {
        return ['success' => false, 'error' => 'GIF banners are available only for premium users'];
    }

    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Allowed formats: JPG, PNG, GIF'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Maximum file size 10MB'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "banner_{$user_id}_" . time() . ".$ext";
    $upload_dir = __DIR__ . '/uploads/banners/';

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $destination = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'path' => "http://localhost/app/uploads/avatars/$filename"];
    }

    return ['success' => false, 'error' => 'File upload error'];
}

function handleAvatarUpload($file, $user_id) {
    global $current_user;
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024;

    // Проверка на GIF для не-премиум пользователей
    if ($file['type'] === 'image/gif' && !$current_user['has_premium']) {
        return ['success' => false, 'error' => 'GIF avatars are available only for premium users'];
    }

    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Allowed formats: JPG, PNG, GIF'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Maximum file size 5MB'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "avatar_{$user_id}_" . time() . ".$ext";
    $upload_dir = __DIR__ . '/uploads/avatars/';

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $destination = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'path' => "http://localhost/app/uploads/avatars/$filename"];
    }

    return ['success' => false, 'error' => 'File upload error'];
}
?>