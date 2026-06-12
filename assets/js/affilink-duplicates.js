document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.compare-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const box = btn.closest('.affilink-compare-box');
            const output = box.querySelector('.affilink-diff-output');
            if (output) output.classList.toggle('hidden');
        });
    });
});