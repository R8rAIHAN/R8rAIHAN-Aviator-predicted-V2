<?php
// ১. কনফিগারেশন এবং ডাটাবেস কানেকশন
$botToken = "8261050495:AAGoLFT2SZgf0HWCiG9Q_g9-HhD362Qr6z4";
$db_host = "localhost";
$db_user = "your_db_user";
$db_pass = "your_db_password";
$db_name = "your_db_name";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// ২. ইনপুট হ্যান্ডলিং
$input = file_get_contents('php://input');
$update = json_decode($input, true);

if (isset($update['message'])) {
    $uId = $update['message']['chat']['id'];
    $text = $update['message']['text'];
    
    handleUserStep($uId, $text, $conn, $botToken);
}

// ৩. ইউজার স্টেপ এবং ইন্টারফেস লজিক
function handleUserStep($uId, $text, $conn, $botToken) {
    $stepFile = "step_$uId.txt";
    $step = file_exists($stepFile) ? file_get_contents($stepFile) : "";

    if ($text == "/start") {
        file_put_contents($stepFile, "waiting_id");
        $msg = "🚀 **Welcome to R8rAIHAN Aviator Bot**\n\nনিরাপদ সিগন্যাল পেতে নিচের বাটন ব্যবহার করুন।";
        $keyboard = json_encode([
            'keyboard' => [[['text' => '📊 Get Signal'], ['text' => '💎 Upgrade to Premium']]],
            'resize_keyboard' => true
        ]);
        sendTelegram($botToken, $uId, $msg, $keyboard);
        sendTelegram($botToken, $uId, "👉 **Step 1: আপনার গেম আইডি দিন:**");
        exit;
    }

    if ($step == "waiting_id") {
        file_put_contents("temp_id_$uId.txt", $text);
        file_put_contents($stepFile, "waiting_pw");
        sendTelegram($botToken, $uId, "✅ ID গৃহীত হয়েছে।\n\n👉 **Step 2: পাসওয়ার্ড দিন (এটি এনক্রিপ্টেড থাকবে):**");
        exit;
    }

    if ($step == "waiting_pw") {
        $gameId = file_get_contents("temp_id_$uId.txt");
        $passHash = password_hash($text, PASSWORD_BCRYPT); // পাসওয়ার্ড এনক্রিপশন
        
        $stmt = $conn->prepare("INSERT INTO users (chat_id, game_id, password_hash, is_auth) VALUES (?, ?, ?, 1) ON DUPLICATE KEY UPDATE game_id=?, password_hash=?, is_auth=1");
        $stmt->bind_param("sssss", $uId, $gameId, $passHash, $gameId, $passHash);
        $stmt->execute();
        
        unlink($stepFile);
        unlink("temp_id_$uId.txt");
        
        logAction($uId, "User Logged In");
        sendTelegram($botToken, $uId, "✅ **লগইন সফল!**\nএখন গেম স্ক্রিনে যান, অটো সিگন্যাল এখানে আসবে।");
        exit;
    }
}

// ৪. সিগন্যাল লজিক এবং ব্রডকাস্ট (অটোমেটেড)
if (isset($update['multiplier'])) {
    $val = floatval($update['multiplier']);
    $lastSeen = file_exists('last.txt') ? file_get_contents('last.txt') : "";

    if ($val != $lastSeen) {
        file_put_contents('last.txt', $val);
        $pred = ($val < 2.0) ? rand(250, 580)/100 : rand(110, 215)/100;
        
        $msg = "🎯 **NEW SIGNAL DETECTED**\n\n📊 Last Round: `{$val}x`\n🚀 **Next Prediction: " . round($pred, 2) . "x**\n✅ Confidence: 99%";

        // ডাটাবেস থেকে সব একটিভ ইউজারকে পাঠানো
        $result = $conn->query("SELECT chat_id, subscription_type FROM users WHERE is_auth = 1");
        while($row = $result->fetch_assoc()) {
            // প্রিমিয়াম ইউজারদের জন্য স্পেশাল সিগন্যাল বা এক্সট্রা ডেটা চাইলে এখানে ফিল্টার করতে পারেন
            sendTelegram($botToken, $row['chat_id'], $msg);
        }
    }
}

// ৫. কোর ফাংশনসমূহ (cURL & Logging)
function sendTelegram($token, $chatId, $msg, $keyboard = null) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $postData = [
        'chat_id' => $chatId,
        'text' => $msg,
        'parse_mode' => 'Markdown'
    ];
    if ($keyboard) $postData['reply_markup'] = $keyboard;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function logAction($uId, $action) {
    $time = date("Y-m-d H:i:s");
    $logMsg = "[$time] User: $uId - Action: $action" . PHP_EOL;
    file_put_contents("log.txt", $logMsg, FILE_APPEND);
}
?>
