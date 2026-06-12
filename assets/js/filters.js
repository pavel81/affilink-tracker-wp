
document.addEventListener('DOMContentLoaded', function () {
    const labels = document.querySelectorAll('.affilink-network-label');
    labels.forEach(label => {
        label.addEventListener('click', () => {
            const selected = label.getAttribute('data-network');
            document.querySelectorAll('.affilink-network-label').forEach(l => l.classList.remove('active'));
            label.classList.add('active');

            // Základní filtrování tabulky (předpoklad: data-network atribut u každého řádku)
            document.querySelectorAll('.affilink-url-row').forEach(row => {
                if (selected === 'unknown') {
                    row.style.display = row.dataset.network === '' ? '' : 'none';
                } else {
                    row.style.display = row.dataset.network === selected ? '' : 'none';
                }
            });
        });
    });
});
