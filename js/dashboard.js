/* =========================================================
   dashboard.js — logic tampilan Super Admin (SuperAdminView)
   ========================================================= */

const SuperAdminView = (() => {
  const ROLE = "superadmin";
  let bound = false;
  let chartInstance = null;

  function init() {
    if (bound) return;
    bound = true;

    const gateEl = document.getElementById("gateSuper");
    const appEl = document.getElementById("appSuper");
    const loginForm = document.getElementById("superLoginForm");
    const pwInput = document.getElementById("superPw");
    const gateError = document.getElementById("superGateError");
    const logoutBtn = document.getElementById("superLogoutBtn");

    const monthFilter = document.getElementById("monthFilter");
    const exportBtn = document.getElementById("superExportBtn");
    const searchDetail = document.getElementById("searchDetail");
    const filterJenisDetail = document.getElementById("filterJenisDetail");
    const lightbox = document.getElementById("lightbox");
    const lightboxImg = document.getElementById("lightboxImg");

    function refreshGateVisibility() {
      const loggedIn = AUTH.isLoggedIn(ROLE);
      gateEl.classList.toggle("hidden", loggedIn);
      appEl.classList.toggle("hidden", !loggedIn);
      return loggedIn;
    }

    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();
      if (AUTH.login(ROLE, pwInput.value)) {
        gateError.textContent = "";
        pwInput.value = "";
        refreshGateVisibility();
        renderAll();
      } else {
        gateError.textContent = "Kata sandi salah. Coba lagi.";
      }
    });

    logoutBtn.addEventListener("click", () => {
      AUTH.logout(ROLE);
      refreshGateVisibility();
      showToast("Berhasil keluar dari akun Super Admin.", "ok");
    });

    monthFilter.addEventListener("change", renderAll);
    searchDetail.addEventListener("input", renderDetailTable);
    filterJenisDetail.addEventListener("change", renderDetailTable);
    exportBtn.addEventListener("click", exportSpreadsheet);

    document.getElementById("closeLightbox").addEventListener("click", () => lightbox.classList.remove("open"));
    lightbox.addEventListener("click", (e) => {
      if (e.target === lightbox) lightbox.classList.remove("open");
    });

    async function populateMonthFilter() {
      const months = await DB.groupByMonth();
      const prevValue = monthFilter.value;
      monthFilter.innerHTML = `<option value="">Semua Bulan</option>`;
      months
        .slice()
        .reverse()
        .forEach((m) => {
          const opt = document.createElement("option");
          opt.value = m.key;
          opt.textContent = DB.monthLabel(m.key);
          monthFilter.appendChild(opt);
        });
      if ([...monthFilter.options].some((o) => o.value === prevValue)) {
        monthFilter.value = prevValue;
      }
    }

    async function renderAll() {
      await populateMonthFilter();
      await renderStats();
      await renderMonthlyTable();
      await renderChart();
      await renderDetailTable();
    }

    async function renderStats() {
      const key = monthFilter.value;
      const s = await DB.summary(key || null);
      document.getElementById("statCount").textContent = s.count;
      document.getElementById("statNominal").textContent = formatRupiah(s.totalNominal);
      document.getElementById("statOkupansi").textContent = formatJam(s.totalOkupansi);
      document.getElementById("statRata").textContent = formatRupiah(s.rataRata);

      const note = key ? DB.monthLabel(key) : "Sepanjang waktu";
      document.getElementById("statCountNote").textContent = note;
      document.getElementById("statNominalNote").textContent = note;
      document.getElementById("statOkupansiNote").textContent = note;
    }

    async function renderMonthlyTable() {
      const tbody = document.getElementById("monthlyTableBody");
      const emptyState = document.getElementById("emptyStateMonthly");
      let months = (await DB.groupByMonth()).slice().reverse();

      const key = monthFilter.value;
      if (key) months = months.filter((m) => m.key === key);

      tbody.innerHTML = "";
      emptyState.classList.toggle("hidden", months.length > 0);
      if (!months.length) return;

      months.forEach((m) => {
        const rata = m.count ? Math.round(m.totalNominal / m.count) : 0;
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${DB.monthLabel(m.key)}</td>
          <td>${m.count}</td>
          <td class="mono">${formatRupiah(m.totalNominal)}</td>
          <td>${formatJam(m.totalOkupansi)}</td>
          <td class="mono">${formatRupiah(rata)}</td>
        `;
        tbody.appendChild(tr);
      });
    }

    async function renderChart() {
      const months = await DB.groupByMonth(); // urut ASC, cocok untuk grafik garis
      const labels = months.map((m) => DB.monthLabel(m.key));
      const nominalData = months.map((m) => m.totalNominal);
      const okupansiData = months.map((m) => m.totalOkupansi);

      const canvas = document.getElementById("trendChart");
      const chartEmpty = document.getElementById("chartEmptyState");
      if (!months.length) {
        canvas.classList.add("hidden");
        chartEmpty.classList.remove("hidden");
        if (chartInstance) {
          chartInstance.destroy();
          chartInstance = null;
        }
        return;
      }
      canvas.classList.remove("hidden");
      chartEmpty.classList.add("hidden");

      const ctx = canvas.getContext("2d");
      if (chartInstance) chartInstance.destroy();

      chartInstance = new Chart(ctx, {
        type: "line",
        data: {
          labels,
          datasets: [
            {
              label: "Pendapatan (Rp)",
              data: nominalData,
              borderColor: "#e8ac52",
              backgroundColor: "rgba(232,172,82,.12)",
              yAxisID: "y",
              tension: 0.35,
              fill: true,
              pointRadius: 4,
              pointBackgroundColor: "#e8ac52",
            },
            {
              label: "Okupansi (jam)",
              data: okupansiData,
              borderColor: "#35b3a3",
              backgroundColor: "rgba(53,179,163,.12)",
              yAxisID: "y1",
              tension: 0.35,
              fill: true,
              pointRadius: 4,
              pointBackgroundColor: "#35b3a3",
            },
          ],
        },
        options: {
          responsive: true,
          interaction: { mode: "index", intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: "#1a222b",
              borderColor: "#2c3945",
              borderWidth: 1,
              titleColor: "#eef2f5",
              bodyColor: "#96a5b3",
              padding: 10,
              callbacks: {
                label: (item) => {
                  if (item.dataset.yAxisID === "y") return " Pendapatan: " + formatRupiah(item.raw);
                  return " Okupansi: " + formatJam(item.raw);
                },
              },
            },
          },
          scales: {
            x: { grid: { color: "#232e39" }, ticks: { color: "#96a5b3" } },
            y: {
              position: "left",
              grid: { color: "#232e39" },
              ticks: { color: "#96a5b3", callback: (v) => "Rp " + v.toLocaleString("id-ID") },
            },
            y1: {
              position: "right",
              grid: { display: false },
              ticks: { color: "#96a5b3", callback: (v) => v + " jam" },
            },
          },
        },
      });
    }

    /* ---- Tabel detail transaksi + bukti transfer ---- */
    async function renderDetailTable() {
      const tbody = document.getElementById("detailTableBody");
      const emptyState = document.getElementById("emptyStateDetail");
      const key = monthFilter.value;
      const q = searchDetail.value.trim().toLowerCase();
      const jenis = filterJenisDetail.value;

      let data = await DB.getAll();
      if (key) {
        data = data.filter((t) => {
          const d = new Date(t.tanggalTransfer);
          const k = d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0");
          return k === key;
        });
      }
      if (q) {
        data = data.filter(
          (t) => t.nama.toLowerCase().includes(q) || (t.catatan || "").toLowerCase().includes(q)
        );
      }
      if (jenis) data = data.filter((t) => t.jenisTransfer === jenis);

      tbody.innerHTML = "";
      emptyState.classList.toggle("hidden", data.length > 0);

      data.forEach((t) => {
        const tagClass =
          t.jenisTransfer === "Qris" ? "tag-qris" : t.jenisTransfer === "Transfer" ? "tag-transfer" : "tag-cash";
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${
            t.gambarBukti
              ? `<img src="${t.gambarBukti}" class="thumb" data-img="${t.gambarBukti}" alt="Bukti transfer" />`
              : `<div class="thumb-empty">—</div>`
          }</td>
          <td>${escapeHtml(t.nama)}</td>
          <td>${formatTanggal(t.tanggalMain)}</td>
          <td><span class="tag ${tagClass}">${t.jenisTransfer}</span></td>
          <td>${formatTanggal(t.tanggalTransfer)}</td>
          <td class="mono">${formatRupiah(t.nominal)}</td>
          <td>${t.jamMulai && t.jamSelesai ? `${t.jamMulai}–${t.jamSelesai}` : "-"}</td>
          <td>${formatJam(t.okupansiJam)}</td>
        `;
        tbody.appendChild(tr);
      });

      tbody.querySelectorAll("img.thumb").forEach((img) => {
        img.addEventListener("click", () => {
          lightboxImg.src = img.dataset.img;
          lightbox.classList.add("open");
        });
      });
    }

    function escapeHtml(str) {
      const div = document.createElement("div");
      div.textContent = str || "";
      return div.innerHTML;
    }

    /* ---- Ekspor spreadsheet (rekap bulanan + detail) ---- */
    async function exportSpreadsheet() {
      const months = await DB.groupByMonth();
      if (!months.length) {
        showToast("Belum ada data untuk diekspor.", "err");
        return;
      }

      const rekapRows = months.map((m) => ({
        Bulan: DB.monthLabel(m.key),
        "Jumlah Transaksi": m.count,
        "Total Nominal": m.totalNominal,
        "Total Okupansi (jam)": m.totalOkupansi,
        "Rata-rata / Transaksi": m.count ? Math.round(m.totalNominal / m.count) : 0,
      }));

      const detailRows = (await DB.getAll()).map((t) => ({
        Nama: t.nama,
        "Tanggal Main": t.tanggalMain,
        "Jenis Transfer": t.jenisTransfer,
        "Tanggal Transfer": t.tanggalTransfer,
        Nominal: t.nominal,
        "Jam Mulai": t.jamMulai,
        "Jam Selesai": t.jamSelesai,
        "Okupansi (jam)": t.okupansiJam,
        Catatan: t.catatan,
        "Ada Bukti Transfer": t.gambarBukti ? "Ya" : "Tidak",
      }));

      const wb = XLSX.utils.book_new();
      const wsRekap = XLSX.utils.json_to_sheet(rekapRows);
      wsRekap["!cols"] = [{ wch: 14 }, { wch: 16 }, { wch: 16 }, { wch: 18 }, { wch: 18 }];
      XLSX.utils.book_append_sheet(wb, wsRekap, "Rekap Bulanan");

      const wsDetail = XLSX.utils.json_to_sheet(detailRows);
      wsDetail["!cols"] = [
        { wch: 20 }, { wch: 14 }, { wch: 14 }, { wch: 16 }, { wch: 14 },
        { wch: 10 }, { wch: 10 }, { wch: 14 }, { wch: 30 }, { wch: 16 },
      ];
      XLSX.utils.book_append_sheet(wb, wsDetail, "Detail Transaksi");

      const filename = `dashboard-rekap-${new Date().toISOString().slice(0, 10)}.xlsx`;
      XLSX.writeFile(wb, filename);
      showToast("Berhasil diekspor ke spreadsheet.", "ok");
    }

    SuperAdminView._refreshGateVisibility = refreshGateVisibility;
    SuperAdminView._renderAll = renderAll;
  }

  function onEnter() {
    init();
    const loggedIn = SuperAdminView._refreshGateVisibility();
    if (loggedIn) SuperAdminView._renderAll();
  }

  return { init, onEnter };
})();
