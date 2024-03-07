document.addEventListener('DOMContentLoaded', function() {
    var programCtx = document.getElementById('programChart').getContext('2d');
    var backgroundColors = [
        'rgba(153, 102, 255, 0.2)', // purple
        'rgba(0, 188, 212, 0.2)', // cyan
        'rgba(233, 30, 99, 0.2)', // magenta
        'rgba(255, 206, 86, 0.2)', // yellow
        'rgba(75, 192, 192, 0.2)', // green
        'rgba(255, 99, 132, 0.2)', // red
        'rgba(54, 162, 235, 0.2)', // blue
        'rgba(255, 159, 64, 0.2)', // orange
        'rgba(255, 99, 132, 0.2)', // pink
        'rgba(205, 220, 57, 0.2)' // lime
    ];
    
    var borderColors = [
        'rgba(153, 102, 255, 1)', // purple
        'rgba(0, 188, 212, 1)', // cyan
        'rgba(233, 30, 99, 1)', // magenta
        'rgba(255, 206, 86, 1)', // yellow
        'rgba(75, 192, 192, 1)', // green
        'rgba(255, 99, 132, 1)', // red
        'rgba(54, 162, 235, 1)', // blue
        'rgba(255, 159, 64, 1)', // orange
        'rgba(255, 99, 132, 1)', // pink
        'rgba(205, 220, 57, 1)' // lime
    ];

    var programChart = new Chart(programCtx, {
        type: 'bar',
        data: {
            labels: window.programData.labels,
            datasets: [{
                label: 'Enrollments by Program',
                data: window.programData.data,
                backgroundColor: backgroundColors.slice(0, window.programData.data.length), 
                borderColor: borderColors.slice(0, window.programData.data.length), 
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false 
                },
                title: {
                    display: true,
                    text: 'Enrollments by Program',
                    font: {
                        size: 14,
                        family: 'Figtree',
                        style: 'normal',
                        weight: 'bold'
                    },
                    color: '#000'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    var trendCtx = document.getElementById('trendChart').getContext('2d');
    var trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: window.trendData.labels,
            datasets: [{
                label: 'Enrollment Trends',
                data: window.trendData.data,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false 
                },
                title: {
                    display: true,
                    text: 'Enrollments Trend',
                    font: {
                        size: 14,
                        family: 'Figtree',
                        style: 'normal',
                        weight: 'bold'
                    },
                    color: '#000'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
