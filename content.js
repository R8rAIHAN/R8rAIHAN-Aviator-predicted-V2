const API_ENDPOINT = "https://r8raihan-aviator-predicted-v2.onrender.com/api.php";

function scrapePkok() {
    // pkok.online এর নতুন মাল্টিপ্লায়ার এলিমেন্ট খোঁজা
    // সাধারণত এটি 'payouts-block' অথবা 'bubble' ক্লাসে থাকে
    let elements = document.querySelectorAll('.payouts-block .bubble-multiplier, .stats-list .multiplier, .history-item');
    
    if (elements.length > 0) {
        let currentVal = elements[0].innerText.replace('x', '').trim();
        
        chrome.storage.local.get(['prevVal'], function(data) {
            if (data.prevVal !== currentVal && !isNaN(currentVal) && currentVal !== "") {
                console.log("New Aviator Data: " + currentVal);
                
                fetch(API_ENDPOINT, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        multiplier: currentVal,
                        merchant: "pkokbdtf6" // আপনার Reqable আইডি
                    })
                }).catch(e => console.log("Sync Error"));

                chrome.storage.local.set({ prevVal: currentVal });
            }
        });
    }
}

// প্রতি ২.৫ সেকেন্ড পর পর চেক করবে যাতে সার্ভারে বেশি লোড না পড়ে
setInterval(scrapePkok, 2500);
