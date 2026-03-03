const RENDER_API_URL = "https://your-app-name.onrender.com/api.php";

function scrapeAviator() {
    // pkok/spribe গেমের কমন হিস্ট্রি সিলেক্টর
    const bubbles = document.querySelectorAll('.payouts-block .bubble-multiplier, .stats-list .app-bubble');
    
    if (bubbles.length > 0) {
        const latestValue = bubbles[0].innerText.replace('x', '').trim();
        
        chrome.storage.local.get(['lastValue'], function(result) {
            if (result.lastValue !== latestValue) {
                console.log("New Round Detected: " + latestValue);
                
                // আপনার Render সার্ভারে ডেটা পাঠানো
                fetch(RENDER_API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ multiplier: latestValue })
                })
                .then(() => {
                    chrome.storage.local.set({ lastValue: latestValue });
                })
                .catch(err => console.log("Server Sync Failed"));
            }
        });
    }
}

// প্রতি ২ সেকেন্ডে একবার চেক করবে
setInterval(scrapeAviator, 2000);
