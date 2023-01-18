<template>
    <canvas id="myChart" width="400" height="400"></canvas>
</template>

<script>
    import Chart from 'chart.js/auto';

    export default {
        props: ['rawData', 'result', 'label'],
        methods: {
            setTheoryData() {
                var theoryData = [];
                const points = this.result.points;

                points.forEach(element => {
                    theoryData.push(element[1]);
                });

                const difference = Number(points[points.length-1][1]) - Number(points[points.length-2][1])

                theoryData.push(Number(points[points.length-1][1]) + difference)
                theoryData.push(Number(points[points.length-1][1]) + difference + difference)

                return theoryData;
            },
            formula(coeff, x) {
                var result = null;
                for (var i = 0, j = coeff.length - 1; i < coeff.length; i++, j--) {
                    result += Number(coeff[i]) * Math.pow(Number(x), Number(j));
                }
                return result;
            },
            currentData() {
                let incomes = []
                this.rawData.forEach(element => {
                    incomes.push(element[1])
                });

                return incomes;
            }
        },
        mounted() {
            const ctx = document.getElementById('myChart');

            const NUMBER_CFG = this.currentData();
            const YEAR_MONTH_LABEL = this.label;
            const REGRESSION = this.setTheoryData();
            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                labels: YEAR_MONTH_LABEL,
                    datasets: [
                        {
                            label: 'Income per month',
                            data: NUMBER_CFG,
                            fill: false,
                            borderColor: '#23408E',
                            backgroundColor: '#2DAAE2',
                        },
                        {
                            label: 'Regression',
                            data: REGRESSION,
                            fill: false,
                            borderColor: '#DDDDDD',
                            backgroundColor: '#EEEEEE',
                        }
                    ],
                },
                options: {
                    scales: {
                        y: {
                            ticks: {
                                // Include a dollar sign in the ticks
                                callback: function(value, index, ticks) {
                                    return '₱ ' + (Number(value)).toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
    }
</script>
