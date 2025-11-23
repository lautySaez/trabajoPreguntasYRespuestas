document.addEventListener('DOMContentLoaded', () => {
    console.log('Dashboard Editor Pro: Inicializado.');

    const currentUrl = window.location.href;
    const navItems = document.querySelectorAll('.dashboard-nav .nav-item');

    navItems.forEach(item => {
        if (currentUrl.includes(item.getAttribute('href'))) {
            item.classList.add('active');
        }
    });

    const bubbleContainer = document.getElementById('bubblesContainer');

    if(bubbleContainer) {
        function createBubble() {
            const bubble = document.createElement('div');
            bubble.classList.add('bubble');

            const size = Math.random() * 60 + 20;
            bubble.style.width = `${size}px`;
            bubble.style.height = `${size}px`;

            bubble.style.left = `${Math.random() * 100}%`;

            const duration = Math.random() * 15 + 10;
            bubble.style.animationDuration = `${duration}s`;

            bubble.style.opacity = Math.random() * 0.3 + 0.1;

            bubbleContainer.appendChild(bubble);

            setTimeout(() => {
                bubble.remove();
            }, duration * 1000);
        }

        setInterval(createBubble, 500);
    }
});