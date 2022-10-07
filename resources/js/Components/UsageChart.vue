<template>
    <canvas id="myChartUsage" width="400" height="400"></canvas>
</template>

<script>
    import Chart from 'chart.js/auto';

    export default {
        mounted() {
            console.log('test mount')

            const ctx = document.getElementById('myChartUsage');

            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Consumed',
                        data: [65, 59, 80, 81, 56, 55, 40],
                        fill: false,
                        borderColor: '#23408E',
                        tension: 0.1
                    },
                    {
                        label: 'My second Dataset',
                        data: [65, 500, 80, 81, 56, 55, 40],
                        fill: false,
                        borderColor: '#2DAAE2',
                        tension: 0.1
                    },
                    {
                        label: 'My third Dataset',
                        data: [65, 100, 80, 81, 56, 55, 40],
                        fill: false,
                        borderColor: '#8ED7F0',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
</script>