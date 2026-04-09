// ====================================
// BASE_URL Configuration
// Dynamically detects the WebBasic root path
// ====================================

const BASE_URL = (() => {
    const href = window.location.href;
    const match = href.match(/(.*WebBasic)\//);
    return match ? match[1] : '';
})();

console.log('BASE_URL configured as:', BASE_URL);
