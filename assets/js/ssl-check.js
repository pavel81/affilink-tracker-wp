
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".affilink-ssl-check-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      const url = btn.dataset.url;
      const rowId = btn.dataset.rowId || null;
      const resultCell = document.querySelector(`#ssl-result-${rowId}`);
      const spinner = document.querySelector(`#ssl-spinner-${rowId}`);

      if (!url || !resultCell || !spinner) return;

      spinner.style.display = "inline-block";
      resultCell.innerHTML = "";

      fetch(ajaxurl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "affilink_check_ssl",
          url: url,
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          spinner.style.display = "none";
          if (data.success && data.status === "valid") {
            resultCell.innerHTML = "✅";
          } else if (data.status === "invalid") {
            resultCell.innerHTML = "❌";
          } else {
            resultCell.innerHTML = "⚠️";
          }
        })
        .catch((err) => {
          spinner.style.display = "none";
          resultCell.innerHTML = "⚠️ chyba";
        });
    });
  });
});
