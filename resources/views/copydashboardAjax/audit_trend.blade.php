<div id="container" style="height: 400px; width: 100%; max-width: 100%; margin: auto;"></div>

<script>
    const rawData = [
    {
        month: "Jun-2025",
        audit_score: 97,
        audit_count: 46,
        repeat_issue_count: 9,
        products: [
            {
                name: "Retail",
                audit_score: 80,
                audit_count: 60,
                repeat_issue_count: 6,
                parameters: [{
                        name: "AGENCY MANAGEMENT",
                        audit_score: 78,
                        audit_count: 30,
                        repeat_issue_count: 3,
                        sub_parameters: [{
                                name: "Name Board of The Collection Agency With Complete Address",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 4
                            },
                            {
                                name: "Agency Location Address Matches With The CVMU Tracker ",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 5
                            }
                        ]
                    },
                    {
                        name: "PROCESS MANAGEMENT",
                        audit_score: 75,
                        audit_count: 33,
                        repeat_issue_count: 5,
                        sub_parameters: [{
                                name: "Agency Performance Letter Printed On Agency Letter Head",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "NDC Printed On Agency Letter Head",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    },
                    {
                        name: "CASH RISK MANAGEMENT",
                        audit_score: 78,
                        audit_count: 32,
                        repeat_issue_count: 8,
                        sub_parameters: [{
                                name: "Cash / Cheque Deposition Within 48 Hours Of Receipt cut",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "Lock And Key Storage Facility Available At The Agency",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    }
                ]
            },
            {
                name: "Card",
                audit_score: 40,
                audit_count: 3,
                repeat_issue_count: 4,
                parameters: [{
                        name: "AGENCY MANAGEMENT",
                        audit_score: 45,
                        audit_count: 3,
                        repeat_issue_count: 3,
                        sub_parameters: [{
                                name: "Name Board of The Collection Agency With Complete Address",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "Agency Location Address Matches With The CVMU Tracker",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    },
                    {
                        name: "PROCESS MANAGEMENT",
                        audit_score: 45,
                        audit_count: 3,
                        repeat_issue_count: 5,
                        sub_parameters: [{
                                name: "Agency Performance Letter Printed On Agency Letter Head",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "NDC Printed On Agency Letter Head",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    },
                    {
                        name: "CASH RISK MANAGEMENT",
                        audit_score: 33,
                        audit_count: 3,
                        repeat_issue_count: 8,
                        sub_parameters: [{
                                name: "Cash / Cheque Deposition Within 48 Hours Of Receipt cut",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "Lock And Key Storage Facility Available At The Agency",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    }
                ]
            }
        ]
    },{
        month: "Jul-2025",
        audit_score: 67,
        audit_count: 40,
        repeat_issue_count: 8,
        products: [
            {
                name: "Retail",
                audit_score: 60,
                audit_count: 30,
                repeat_issue_count: 6,
                parameters: [{
                        name: "AGENCY MANAGEMENT",
                        audit_score: 78,
                        audit_count: 30,
                        repeat_issue_count: 3,
                        sub_parameters: [{
                                name: "Name Board of The Collection Agency With Complete Address",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "Agency Location Address Matches With The CVMU Tracker ",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    },
                    {
                        name: "PROCESS MANAGEMENT",
                        audit_score: 45,
                        audit_count: 33,
                        repeat_issue_count: 5,
                        sub_parameters: [{
                                name: "Agency Performance Letter Printed On Agency Letter Head",
                                audit_score: 75,
                                audit_count: 14,
                                repeat_issue_count: 1
                            },
                            {
                                name: "NDC Printed On Agency Letter Head",
                                audit_score: 81,
                                audit_count: 30,
                                repeat_issue_count: 2
                            }
                        ]
                    },
                    {
                        name: "CASH RISK MANAGEMENT",
                        audit_score: 68,
                        audit_count: 32,
                        repeat_issue_count: 3,
                        sub_parameters: [{
                                name: "Cash / Cheque Deposition Within 48 Hours Of Receipt cut",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "Lock And Key Storage Facility Available At The Agency",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    }
                ]
            },
            {
                name: "Card",
                audit_score: 40,
                audit_count: 3,
                repeat_issue_count: 1,
                parameters: [{
                        name: "AGENCY MANAGEMENT",
                        audit_score: 45,
                        audit_count: 3,
                        repeat_issue_count: 3,
                        sub_parameters: [{
                                name: "Name Board of The Collection Agency With Complete Address",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "Agency Location Address Matches With The CVMU Tracker ",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    },
                    {
                        name: "PROCESS MANAGEMENT",
                        audit_score: 65,
                        audit_count: 3,
                        repeat_issue_count: 5,
                        sub_parameters: [{
                                name: "Agency Performance Letter Printed On Agency Letter Head",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "NDC Printed On Agency Letter Head",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    },
                    {
                        name: "CASH RISK MANAGEMENT",
                        audit_score: 33,
                        audit_count: 3,
                        repeat_issue_count: 8,
                        sub_parameters: [{
                                name: "Cash / Cheque Deposition Within 48 Hours Of Receipt cut",
                                audit_score: 75,
                                audit_count: 10,
                                repeat_issue_count: 1
                            },
                            {
                                name: "Lock And Key Storage Facility Available At The Agency",
                                audit_score: 81,
                                audit_count: 20,
                                repeat_issue_count: 2
                            }
                        ]
                    }
                ]
            }
        ]
    }];

    const rootData = [];
    const drilldownSeries = [];

    rawData.forEach((monthData, monthIndex) => {
        const monthId = `month-${monthIndex}`;

        // Top level
        rootData.push({
            name: monthData.month,
            y: monthData.audit_count,
            drilldown: monthId,
            custom: {
                score: monthData.audit_score,
                repeat: monthData.repeat_issue_count
            }
        });

        const categories = [];
        const countSeries = [];
        const scoreSeries = [];
        const repeatSeries = [];

        monthData.products.forEach((product, prodIndex) => {
            const prodId = `${monthId}-product-${prodIndex}`;
            categories.push(product.name);

            countSeries.push({
                name: product.name,
                y: product.audit_count,
                drilldown: prodId,
                custom: {
                    score: product.audit_score,
                    repeat: product.repeat_issue_count
                }
            });
            scoreSeries.push(product.audit_score);
            repeatSeries.push(product.repeat_issue_count);

            const paramCategories = [];
            const paramCounts = [];
            const paramScores = [];
            const paramRepeats = [];

            product.parameters.forEach((param, paramIndex) => {
                const paramId = `${prodId}-param-${paramIndex}`;
                paramCategories.push(param.name);
                paramCounts.push({
                    name: param.name,
                    y: param.audit_count,
                    drilldown: paramId,
                    custom: {
                        score: param.audit_score,
                        repeat: param.repeat_issue_count
                    }
                });
                paramScores.push(param.audit_score);
                paramRepeats.push(param.repeat_issue_count);

                const subNames = [];
                const subCounts = [];
                const subScores = [];
                const subRepeats = [];

                param.sub_parameters.forEach(sub => {
                    subNames.push(sub.name);
                    subCounts.push({
                        name: sub.name,
                        y: sub.audit_count,
                        custom: {
                            score: sub.audit_score,
                            repeat: sub.repeat_issue_count
                        }
                    });
                    subScores.push(sub.audit_score);
                    subRepeats.push(sub.repeat_issue_count);
                });

                drilldownSeries.push({
                    id: paramId,
                    name: `Sub-parameters in ${param.name}`,
                    data: subCounts,
                    type: 'column',
                    events: {
                        afterAnimate: function() {
                            this.chart.addSeries({
                                    type: 'spline',
                                    name: 'Audit Score',
                                    data: subScores.map((score, i) => ({
                                        name: subNames[i],
                                        y: score,
                                        custom: {
                                        score: score,
                                        repeat: subRepeats[i]
                                        }
                                    }))
                                    }, false);

                            this.chart.addSeries({
                                        type: 'spline',
                                        name: 'Repeat Issues',
                                        data: subRepeats.map((repeat, i) => ({
                                            name: subNames[i],
                                            y: repeat,
                                            custom: {
                                            score: subScores[i],
                                            repeat: repeat
                                            }
                                        }))
                                        }, false);

                            this.chart.redraw();
                        }
                    }
                });
            });

            drilldownSeries.push({
                id: prodId,
                name: `Parameters in ${product.name}`,
                data: paramCounts,
                type: 'column',
                events: {
                    afterAnimate: function() {
                        this.chart.addSeries({
                            type: 'spline',
                            name: 'Audit Score',
                            data: paramScores
                        }, false);
                        this.chart.addSeries({
                            type: 'spline',
                            name: 'Repeat Issues',
                            data: paramRepeats
                        }, false);
                        this.chart.redraw();
                    }
                }
            });
        });

        drilldownSeries.push({
            id: monthId,
            name: `Products in ${monthData.month}`,
            data: countSeries,
            type: 'column',
            events: {
                afterAnimate: function() {
                    this.chart.addSeries({
                        type: 'spline',
                        name: 'Audit Score',
                        data: scoreSeries
                    }, false);
                    this.chart.addSeries({
                        type: 'spline',
                        name: 'Repeat Issues',
                        data: repeatSeries
                    }, false);
                    this.chart.redraw();
                }
            }
        });
    });

    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Multi-Level Drilldown: Audit Data'
        },
        subtitle: {
            text: 'Click the columns to drill down. Use breadcrumbs to go back.'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Audit Count'
            }
        },
        legend: {
            enabled: true
        },
        tooltip: {
            useHTML: true,
            formatter: function() {
                return `<b>${this.point.name}</b><br>
                Audit Count: ${this.point.y}<br>
                Audit Score: ${this.point.custom?.score ?? '-'}%<br>
                Repeat Issues: ${this.point.custom?.repeat ?? '-'}`;
            }
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    formatter: function () {
                        if (this.series.name === 'Audit Score') {
                            return this.y + '%';
                        }
                        if (this.series.name === 'Repeat Issues') {
                            return this.y + '(RI)';
                        }
                        return this.y;
                    }
                }
            }
        },
        series: [{
            name: 'Months',
            colorByPoint: true,
            data: rootData
        }],
        drilldown: {
            breadcrumbs: {
                position: {
                    align: 'right'
                }
            },
            series: drilldownSeries
        }
    });
</script>