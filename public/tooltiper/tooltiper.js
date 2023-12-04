// https://github.com/KaazDW/Tooltiper

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
