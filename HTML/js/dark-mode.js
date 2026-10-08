document.addEventListener('DOMContentLoaded', () => {
    const isDark = localStorage.getItem('darkMode') === 'true';
    if (isDark) {
        document.body.classList.add('dark');
    }

    const toggleBtn = document.getElementById('dark-mode-toggle');
    if (toggleBtn) {
        toggleBtn.innerText = isDark ? '☀ Light Mode' : '🌙 Dark Mode';
        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark');
            const darkEnabled = document.body.classList.contains('dark');
            localStorage.setItem('darkMode', darkEnabled);
            toggleBtn.innerText = darkEnabled ? '☀ Light Mode' : '🌙 Dark Mode';
        });
    }
});
