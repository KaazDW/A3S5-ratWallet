// https://github.com/KaazDW/Tooltiper

const styleSheet = document.createElement('link');
styleSheet.rel = 'stylesheet';
styleSheet.type = 'text/css';
styleSheet.href = 'https://raw.githubusercontent.com/KaazDW/Tooltiper/v1/tooltiper.css'; 
document.head.appendChild(styleSheet);

document.addEventListener('DOMContentLoaded', function () {
    let labels = document.querySelectorAll('.tooltip-container');

    labels.forEach(function (label) {
        let tooltipText = label.getAttribute('data-tooltip');

        let tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = tooltipText;

        label.appendChild(tooltip);

        label.addEventListener('mouseover', function () {
            tooltip.style.opacity = '1';
        });

        label.addEventListener('mouseout', function () {
            tooltip.style.opacity = '0';
        });
    });
});