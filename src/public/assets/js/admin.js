// public/assets/js/admin.js
document.addEventListener('DOMContentLoaded', () => {
    // Toggle sidebar on mobile
    const sidebarToggleTop = document.getElementById('sidebarToggleTop');
    if (sidebarToggleTop) {
        sidebarToggleTop.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });
    }

    // Chart.js revenue chart
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6'],
                datasets: [{
                    label: 'Doanh thu',
                    data: [120, 150, 180, 200, 170, 220],
                    borderColor: '#081c58',
                    backgroundColor: 'rgba(8,28,88,0.1)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });
    }
});
