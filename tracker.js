{/* <script> */}
// Track user activities
document.addEventListener('DOMContentLoaded', function() {
    // Send pageview data
    fetch('tracker.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            page: window.location.pathname,
            referrer: document.referrer
        })
    }).catch(error => console.log('Tracking error:', error));
    
    // Track clicks (optional)
    document.addEventListener('click', function(e) {
        const target = e.target;
        if (target.tagName === 'A' || target.closest('a')) {
            const link = target.href || target.closest('a').href;
            fetch('tracker.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'click',
                    target: link,
                    page: window.location.pathname
                })
            }).catch(error => console.log('Click tracking error:', error));
        }
    });
});
{/* </script> */}