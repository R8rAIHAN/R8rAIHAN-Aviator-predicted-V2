const RENDER_URL = "https://r8raihan-aviator-predicted-v2.onrender.com/api.php";

function startScraping() {
    // pkok.com এর মাল্টিপ্লায়ার এলিমেন্ট খোঁজা
    const bubbles = document.querySelectorAll('.payouts-block .bubble-multiplier, .stats-list .multiplier, .v-chip');
    
    if (bubbles.length > 0) {
        let currentVal = bubbles[0].innerText.replace('x', '').trim();
        
        if (!isNaN(currentVal) && currentVal !== "") {
            chrome.storage.local.get(['lastSent'], function(res) {
                if (res.lastSent !== currentVal) {
                    console.log("Sending Data: " + currentVal);
                    
                    fetch(RENDER_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ multiplier: currentVal })
                    });
                    
                    chrome.storage.local.set({ lastSent: currentVal });
                }
            });
        }
    }
}

// প্রতি ১.৫ সেকেন্ডে একবার চেক করবে
setInterval(startScraping, 1500);
