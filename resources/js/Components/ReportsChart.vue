<template>
    <canvas id="myReportChart" width="100" height="20"></canvas>
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
                        y: element.amount
                    }
                    dataArr.push(toPush)
                });

                return dataArr
            },
            renderData() {
                var datas = []
                this.datas.forEach(read => {
                    datas.push(read.amount)
                })

                console.log(datas);

                return datas;
            },
            renderLabel() {
                var label = [];
                const months = {
                    '1': 'Jan',
                    '2': 'Feb',
                    '3': 'Mar',
                    '4': 'Apr',
                    '5': 'May',
                    '6': 'Jun',
                    '7': 'Jul',
                    '8': 'Aug',
                    '9': 'Sep',
                    '10': 'Oct',
                    '11': 'Nov',
                    '12': 'Dec'
                }

                this.datas.forEach(read => {
                    label.push([months[String(read.month)], String(read.year)])
                })

                return label;
            }
        },
        mounted() {
            const ctx = document.getElementById('myReportChart');
            const YEAR_MONTH_LABEL = this.renderLabel();
            const NUMBER_CFG = this.renderData();
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
                    ]
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
