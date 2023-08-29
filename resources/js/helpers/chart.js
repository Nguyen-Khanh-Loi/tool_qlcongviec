export const optionChart = {
    option: () => option,
    optionProject: () => optionProject,
};
var option = {
    chart: {
        type: "radialBar",
        wight: 100,
        height: 100,
        sparkline: {
            enabled: true
        }
    },
    labels: ['title'],
    stroke: {
        lineCap: "round"
    },
    plotOptions: {
        radialBar: {
            hollow: {
                margin: 0,
                size: "90%"
            },
            track: {
                margin: 0
            },
            dataLabels: {
                show: true,
                name: {
                    offsetY: 0,
                    show: true,
                    color: "red",
                    fontSize: "15px",
                },
                value: {
                    color: "black",
                    fontSize: "20px",
                    show: true,
                },
            }
        }
    },
    colors: ['#042ecb']
};

var optionProject = {
    chart: {
        type: "radialBar",
        wight: 300,
        height: 300,
        sparkline: {
            enabled: true
        }
    },
    labels: ['title'],
    stroke: {
        lineCap: "round"
    },
    plotOptions: {
        radialBar: {
            // startAngle: -135,
            // endAngle: 225,
            hollow: {
                margin: 0,
                size: "70%",
                background: "#fff",
                image: undefined,
                imageOffsetX: 0,
                imageOffsetY: 0,
                position: "front",
                dropShadow: {
                enabled: true,
                    top: 3,
                    left: 0,
                    blur: 4,
                    opacity: 0.24,
                },
            },
            track: {
                background: "#fff",
                strokeWidth: "67%",
                margin: 0, // margin is in pixels
                dropShadow: {
                    enabled: true,
                    top: -3,
                    left: 0,
                    blur: 4,
                    opacity: 0.35,
                },
            },

            dataLabels: {
                show: true,
                name: {
                    offsetY: -10,
                    show: true,
                    color: "red",
                    fontSize: "25px",
                },
                value: {
                    color: "black",
                    fontSize: "36px",
                    show: true,
                },
            },
        },
    },
    colors: ['#042ecb']
};
