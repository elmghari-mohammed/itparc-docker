document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le graphique
    initDonutChart();
    
    // Animation de chargement
    setTimeout(() => { 
        document.querySelectorAll('.loading').forEach(el => {
            el.style.opacity = '1'; 
            el.style.transform = 'translateY(0)';
        });
    }, 100);
});

function initDonutChart() {
    const ctx = document.getElementById('donutChart').getContext('2d');
    
    // Préparer les données pour le graphique
    const labels = [];
    const data = [];
    const backgroundColors = [];
    
    if (typeof reclamationData !== 'undefined') {
        Object.keys(reclamationData).forEach((type, index) => {
            if (reclamationData[type] > 0) {
                labels.push(type.charAt(0).toUpperCase() + type.slice(1));
                data.push(reclamationData[type]);
                
                // Assigner des couleurs en fonction du type
                switch(type) {
                    case 'hardware':
                        backgroundColors.push('#3b82f6');
                        break;
                    case 'software':
                        backgroundColors.push('#8b5cf6');
                        break;
                    default:
                        backgroundColors.push('#f59e0b');
                }
            }
        });
    }
    
    // Créer le graphique seulement s'il y a des données
    if (data.length > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: (value, ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            return total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                        }
                    }
                }
            }
        });
    } else {
        // Afficher un message si aucune donnée
        ctx.font = "16px Arial";
        ctx.fillStyle = "#666";
        ctx.textAlign = "center";
        ctx.fillText("Aucune donnée disponible", ctx.canvas.width / 2, ctx.canvas.height / 2);
    }
}