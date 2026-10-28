import {
    CategoryScale,
    Chart,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    CategoryScale,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
);

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const gridColor = 'rgba(255, 255, 255, 0.06)';
const mutedColor = '#8e92ab';
const accentSoft = '#8fb4ff';
const purple = '#a56bf0';

Chart.defaults.color = mutedColor;
Chart.defaults.borderColor = gridColor;
Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
Chart.defaults.font.size = 11;

const dashedGrid = {
    id: 'dashedGrid',
    beforeDatasetsDraw(chart) {
        const { ctx, chartArea } = chart;

        if (!chartArea) {
            return;
        }

        const xAxis = chart.scales.x;
        const yAxis = chart.scales.y;

        if (!xAxis || !yAxis) {
            return;
        }

        ctx.save();
        ctx.setLineDash([3, 5]);
        ctx.lineWidth = 1;
        ctx.strokeStyle = gridColor;

        xAxis.getTicks().forEach((tick, index) => {
            const pixel = xAxis.getPixelForTick(index);

            ctx.beginPath();
            ctx.moveTo(pixel, chartArea.top);
            ctx.lineTo(pixel, chartArea.bottom);
            ctx.stroke();
        });

        yAxis.getTicks().forEach((tick, index) => {
            const pixel = yAxis.getPixelForTick(index);

            ctx.beginPath();
            ctx.moveTo(chartArea.left, pixel);
            ctx.lineTo(chartArea.right, pixel);
            ctx.stroke();
        });

        ctx.restore();
    },
};

Chart.register(dashedGrid);

function areaGradient(topColor, bottomColor) {
    return (context) => {
        const { ctx, chartArea } = context.chart;

        if (!chartArea) {
            return 'transparent';
        }

        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
        gradient.addColorStop(0, topColor);
        gradient.addColorStop(1, bottomColor);

        return gradient;
    };
}

const blueFill = areaGradient('rgba(99, 102, 241, 0.42)', 'rgba(99, 102, 241, 0)');
const purpleFill = areaGradient('rgba(165, 107, 240, 0.34)', 'rgba(165, 107, 240, 0)');

const tooltip = {
    backgroundColor: 'rgba(99, 102, 241, 0.96)',
    borderColor: 'transparent',
    borderWidth: 0,
    titleColor: '#ffffff',
    bodyColor: '#eef1ff',
    padding: 10,
    cornerRadius: 8,
    caretSize: 5,
    displayColors: false,
    callbacks: {
        title: () => '',
    },
};

const animation = reducedMotion ? false : { duration: 650, easing: 'easeOutQuart' };

const tickFont = { size: 10.5, weight: 500 };

const baseOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation,
    layout: { padding: { top: 8, right: 4 } },
    interaction: { mode: 'index', intersect: false },
    plugins: {
        legend: {
            position: 'top',
            align: 'start',
            labels: {
                usePointStyle: true,
                pointStyle: 'circle',
                boxWidth: 8,
                boxHeight: 8,
                padding: 16,
                color: '#c5c9dc',
                font: { size: 11, weight: 500 },
            },
        },
        tooltip,
    },
};

function xScale() {
    return {
        border: { display: false },
        grid: { display: false },
        ticks: {
            color: mutedColor,
            padding: 8,
            font: tickFont,
        },
    };
}

function yScale(unit = '') {
    return {
        beginAtZero: false,
        border: { display: false },
        grid: { display: false },
        ticks: {
            color: mutedColor,
            padding: 8,
            maxTicksLimit: 5,
            font: tickFont,
            callback: (value) => `${number(value)}${unit ? ` ${unit}` : ''}`,
        },
    };
}

const number = (value) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 }).format(value);

function paddedBounds(values) {
    const numbers = values.filter((value) => Number.isFinite(value));

    if (numbers.length === 0) {
        return {};
    }

    return {
        suggestedMin: Math.floor(Math.min(...numbers) - 1),
        suggestedMax: Math.ceil(Math.max(...numbers) + 1),
    };
}

function sparsePointRadius(context) {
    const points = context.dataset.data.filter((value) => value !== null && value !== undefined);

    return points.length <= 2 ? 3.5 : 0;
}

function renderWeight(canvas, payload) {
    const bounds = paddedBounds(payload.actual ?? []);

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: payload.labels ?? [],
            datasets: [
                {
                    label: 'Berat aktual',
                    data: payload.actual ?? [],
                    borderColor: accentSoft,
                    backgroundColor: blueFill,
                    borderWidth: 2.5,
                    pointRadius: sparsePointRadius,
                    pointHitRadius: 16,
                    pointBackgroundColor: accentSoft,
                    pointBorderColor: '#0a0a0a',
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: accentSoft,
                    pointHoverBorderColor: '#0a0a0a',
                    pointHoverBorderWidth: 2,
                    tension: 0.42,
                    fill: 'origin',
                    spanGaps: true,
                },
            ],
        },
        options: {
            ...baseOptions,
            plugins: {
                ...baseOptions.plugins,
                tooltip: {
                    ...tooltip,
                    callbacks: {
                        ...tooltip.callbacks,
                        label: (context) => `${context.dataset.label}: ${number(context.parsed.y)} kg`,
                    },
                },
            },
            scales: {
                x: xScale(),
                y: {
                    ...bounds,
                    ...yScale('kg'),
                },
            },
        },
    });
}

function renderCombination(canvas, payload) {
    const lineValues = (payload.line ?? []).filter((value) => value !== null);
    const bounds = paddedBounds(lineValues);
    const unit = /\(([^)]+)\)/.exec(payload.lineLabel ?? '')?.[1] ?? 'cm';

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: payload.labels ?? [],
            datasets: [
                {
                    label: payload.lineLabel ?? 'Lingkar tubuh',
                    data: payload.line ?? [],
                    borderColor: purple,
                    backgroundColor: purpleFill,
                    borderWidth: 2.5,
                    pointRadius: sparsePointRadius,
                    pointHitRadius: 16,
                    pointBackgroundColor: purple,
                    pointBorderColor: '#0a0a0a',
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: purple,
                    pointHoverBorderColor: '#0a0a0a',
                    pointHoverBorderWidth: 2,
                    tension: 0.42,
                    fill: 'origin',
                    spanGaps: true,
                },
            ],
        },
        options: {
            ...baseOptions,
            plugins: {
                ...baseOptions.plugins,
                tooltip: {
                    ...tooltip,
                    callbacks: {
                        ...tooltip.callbacks,
                        label: (context) => `${context.dataset.label}: ${number(context.parsed.y)} ${unit}`,
                    },
                },
            },
            scales: {
                x: xScale(),
                y: {
                    ...bounds,
                    ...yScale(unit),
                },
            },
        },
    });
}

export function initCharts() {
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        if (canvas.dataset.chartInitialized === 'true') {
            return;
        }

        let payload = {};

        try {
            payload = JSON.parse(canvas.dataset.payload ?? '{}');
        } catch {
            return;
        }

        if (canvas.dataset.chart === 'weight') {
            renderWeight(canvas, payload);
            canvas.dataset.chartInitialized = 'true';
        }

        if (canvas.dataset.chart === 'combination') {
            renderCombination(canvas, payload);
            canvas.dataset.chartInitialized = 'true';
        }
    });
}
