document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".affilink-compare-btn").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");
            const url1 = row.dataset.url1;
            const url2 = row.dataset.url2;
            const resultEl = row.querySelector(".affilink-compare-result");

            resultEl.innerHTML = "<em>Porovnávám...</em>";

            fetch(affilink_ajax.ajax_url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    action: "affilink_compare_urls",
                    url1: url1,
                    url2: url2,
                    _ajax_nonce: affilink_ajax.nonce,
                }),
            })
                .then((res) => res.text())
                .then((html) => {
                    resultEl.innerHTML = html;
                })
                .catch(() => {
                    resultEl.innerHTML = "<strong style='color:red'>Chyba při porovnání</strong>";
                });
        });
    });
});
