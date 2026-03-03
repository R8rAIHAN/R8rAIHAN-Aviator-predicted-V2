// আপনার Render URL
const API_URL = "https://r8raihan-aviator-predicted-v2.onrender.com/api.php";

function getAviatorData() {
    // pkok.online এর জন্য স্পেসিফিক হিস্ট্রি ক্লাস্টার
    let historyElements = document.querySelectorAll('.payouts-block .bubble-multiplier, .stats-list .multiplier, .v-chip__content');
    
    if (historyElements.length > 0) {
        let val = historyElements[0].innerText.replace('x', '').trim();
        
        chrome.storage.local.get(['lastValue'], function(result) {
            if (result.lastValue !== val && !isNaN(val) && val !== "") {
                console.log("New Aviator Result Found: " + val);
                
                fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        multiplier: val,
                        merchant: "pkokbdtf6", // Reqable থেকে পাওয়া আপনার আইডি
                        bot_status: "active"
                    })
                }).catch(err => console.error("Sync Error:", err));
                
                chrome.storage.local.set({ lastValue: val });
            }
        });
    }
}

// প্রতি ২ সেকেন্ডে একবার স্ক্যান করবে
setInterval(getAviatorData, 2000);
