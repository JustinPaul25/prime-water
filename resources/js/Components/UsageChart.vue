<template>
    <canvas id="myUsageChart" width="400" height="200"></canvas>
</template>

<script>
    import Chart from 'chart.js/auto';

    export default {
        props: ['datas'],
        methods: {
            convertRawData() {
                let dataArr = [];
                this.datas.forEach(element => {
                    const toPush = {
                        x: element.month,
                        y: element.readings
                    }
                    dataArr.push(toPush)
                });

                return dataArr
            }
        },
        mounted() {
            const ctx = document.getElementById('myUsageChart');
            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [
                        {
                            label: 'Usage',
                            data: this.convertRawData(),
                            backgroundColor: '#23408E',
                        }
                    ]
                },
                options: {
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            title: {
                                display: true
                            },
                            ticks: {
                                callback: function(value, index, ticks) {
                                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                                    return months[value-1];
                                },
                                stepSize: 1
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Consumed Water per cuM'
                            },
                            ticks: {
                                callback: function(value, index, ticks) {
                                    return value+' mᶟ';
                                },
                            }
                        }
                    }
                }
            });
        }
    }
</script>
