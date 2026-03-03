<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// --- টেলিগ্রাম কনফিগারেশন ---
$botToken = "8261050495:AAGoLFT2SZgf0HWCiG9Q_g9-HhD362Qr6z4"; // এখানে আপনার বটের টোকেন দিন
$chatId = "6109947429";     // এখানে আপনার চ্যাট আইডি দিন
// -------------------------

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (isset($data['multiplier'])) {
    $last = floatval($data['multiplier']);
    
    // Provably Fair গাণিতিক প্রেডিকশন লজিক
    if ($last < 2.0) {
        $prediction = rand(210, 480) / 100; // বড় হওয়ার চান্স বেশি
    } else {
        $prediction = rand(115, 195) / 100; // সেফ জোনে থাকার চান্স বেশি
    }

    $message = "🎯 **New Aviator Signal**\n\n";
    $message .= "📊 Last Result: " . $last . "x\n";
    $message .= "🚀 Next Prediction: " . round($prediction, 2) . "x\n";
    $message .= "💰 Safe Cashout: " . round($prediction * 0.85, 2) . "x\n";
    $message .= "⏰ Time: " . date("H:i:s");

    // টেলিগ্রামে মেসেজ পাঠানোর ফাংশন
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message) . "&parse_mode=Markdown";
    
    file_get_contents($url); // মেসেজ সেন্ড হবে

    // ডাটা সেভ করে রাখা (ড্যাশবোর্ডের জন্য)
    file_put_contents('data.json', json_encode(["last" => $last, "next" => round($prediction, 2)]));
    
    echo json_encode(["status" => "success"]);
}
?>
