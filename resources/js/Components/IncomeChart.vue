<template>
    <canvas id="myChart" width="400" height="400"></canvas>
</template>

<script>
    import Chart from 'chart.js/auto';

    export default {
        props: ['rawData', 'result'],
        methods: {
            setTheoryData() {
                var theoryData = [];
                for (var i = 0; i < this.rawData.length; i++) {
                    const toPush = [this.rawData[i][0], this.formula(this.result.equation, this.rawData[i][0])];
                    theoryData.push({
                        x: toPush[0],
                        y: toPush[1]
                    })
                    if(i === this.rawData.length - 1) {
                        const toAdd = Number(theoryData[theoryData.length - 1].y) - Number(theoryData[theoryData.length - 2].y)
                        theoryData.push({
                            x: Number(this.rawData[i][0]) + 1,
                            y: Number(toPush[1]) + Number(toAdd)
                        })
                        theoryData.push({
                            x: Number(this.rawData[i][0]) + 2,
                            y: Number(toPush[1]) + Number(toAdd) + Number(toAdd)
                        })
                    }
                }

                return theoryData;
            },
            formula(coeff, x) {
                var result = null;
                for (var i = 0, j = coeff.length - 1; i < coeff.length; i++, j--) {
                    result += Number(coeff[i]) * Math.pow(x, j);
                }
                return result;
            },
            convertRawData() {
                let dataArr = [];
                this.rawData.forEach(element => {
                    const toPush = {
                        x: element[0],
                        y: element[1]
                    }
                    dataArr.push(toPush)
                });

                return dataArr
            }
        },
        mounted() {
            const ctx = document.getElementById('myChart');

            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        type: 'line',
                        label: 'Data',
                        data: this.convertRawData(),
                        backgroundColor: '#23408E',
                        showLine: false
                    },
                    {
                        type: 'line',
                        label: 'Prediction',
                        data: this.setTheoryData(),
                        backgroundColor: '#2DAAE2'
                    }],
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
