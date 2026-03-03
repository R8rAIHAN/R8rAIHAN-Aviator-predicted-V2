// আপনার Render URL
const API_URL = "https://r8raihan-aviator-predicted-v2.onrender.com/api.php";

// গেমের নেটওয়ার্ক রিকোয়েস্ট ইন্টারসেপ্ট করা
const constantMock = window.fetch;
window.fetch = function() {
    return constantMock.apply(this, arguments).then((response) => {
        if (response.url.includes('payouts') || response.url.includes('history') || response.url.includes('asset-manifest')) {
            const clone = response.clone();
            clone.json().then(data => {
                // Reqable থেকে পাওয়া ডেটা স্ট্রাকচার অনুযায়ী ভ্যালু বের করা
                let lastMultiplier = data[0]?.multiplier || data.multiplier || data.last_payout;
                if (lastMultiplier) {
                    sendToServer(lastMultiplier);
                }
            }).catch(e => {});
        }
        return response;
    });
};

function sendToServer(val) {
    fetch(API_URL, {
        method: 'POST',
        mode: 'no-cors',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ multiplier: val })
    });
}

// ব্যাকআপ হিসেবে টেক্সট স্ক্যানারও চালু রাখলাম
setInterval(() => {
    let el = document.querySelector('.payouts-block .bubble-multiplier, .stats-list .multiplier');
    if (el) sendToServer(el.innerText.replace('x',''));
}, 2000);
