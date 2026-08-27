/* =========================================================
   admin.js — logic tampilan Admin (AdminView)
   Diekspos sebagai window.AdminView dengan dua fungsi:
   - init()    : pasang semua event listener SEKALI saat halaman dimuat
   - onEnter() : dipanggil setiap kali route "/admin" aktif
                 (cek status login, refresh data terbaru)
   ========================================================= */

const AdminView = (() => {
  const ROLE = "admin";
  let bound = false;
  let currentImageBase64 = null;
  let editingId = null; // null = mode tambah baru, terisi = mode edit

  function init() {
    if (bound) return;
    bound = true;

    const gateEl = document.getElementById("gateAdmin");
    const appEl = document.getElementById("appAdmin");
    const loginForm = document.getElementById("adminLoginForm");
    const pwInput = document.getElementById("adminPw");
    const gateError = document.getElementById("adminGateError");
    const logoutBtn = document.getElementById("adminLogoutBtn");

    const form = document.getElementById("txForm");
    const uploadBox = document.getElementById("uploadBox");
    const fileInput = document.getElementById("fileInput");
    const previewWrap = document.getElementById("previewWrap");
    const previewImg = document.getElementById("previewImg");
    const removeImgBtn = document.getElementById("removeImgBtn");
    const jenisGroup = document.getElementById("jenisTransferGroup");
    const searchInput = document.getElementById("searchInput");
    const filterJenis = document.getElementById("filterJenis");
    const exportBtn = document.getElementById("adminExportBtn");
    const lightbox = document.getElementById("lightbox");
    const lightboxImg = document.getElementById("lightboxImg");
    const jamMulaiInput = document.getElementById("jamMulai");
    const jamSelesaiInput = document.getElementById("jamSelesai");
    const durasiPreview = document.getElementById("durasiPreview");
    const editBanner = document.getElementById("editBanner");
    const editBannerText = document.getElementById("editBannerText");
    const cancelEditBtn = document.getElementById("cancelEditBtn");
    const submitBtn = document.getElementById("submitBtn");

    /* ---------------- LOGIN GATE ---------------- */
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
        renderTable();
      } else {
        gateError.textContent = "Kata sandi salah. Coba lagi.";
      }
    });

    logoutBtn.addEventListener("click", () => {
      AUTH.logout(ROLE);
      resetEditMode();
      form.reset();
      refreshGateVisibility();
      showToast("Berhasil keluar dari akun Admin.", "ok");
    });

    /* ---------------- Tanggal default ---------------- */
    const today = new Date().toISOString().slice(0, 10);
    document.getElementById("tanggalMain").value = today;
    document.getElementById("tanggalTransfer").value = today;

    /* ---------------- Radio chip jenis transfer ---------------- */
    function updateChipStyles() {
      jenisGroup.querySelectorAll(".radio-chip").forEach((chip) => {
        const input = chip.querySelector("input");
        chip.classList.toggle("checked", input.checked);
      });
    }
    jenisGroup.addEventListener("change", updateChipStyles);
    jenisGroup.querySelectorAll(".radio-chip").forEach((chip) => {
      chip.addEventListener("click", (e) => {
        const input = chip.querySelector("input");
        if (input.checked && e.target === input) return;
        input.checked = true;
        input.dispatchEvent(new Event("change", { bubbles: true }));
      });
      chip.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          const input = chip.querySelector("input");
          input.checked = true;
          input.dispatchEvent(new Event("change", { bubbles: true }));
        }
      });
    });
    updateChipStyles();

    /* ---------------- Jam mulai / jam selesai -> hitung durasi otomatis ---------------- */
    function updateDurasiPreview() {
      const jm = jamMulaiInput.value;
      const js = jamSelesaiInput.value;
      if (jm && js) {
        const durasi = hitungDurasiJam(jm, js);
        durasiPreview.textContent = `${jm} – ${js}  •  Durasi: ${formatJam(durasi)}`;
        durasiPreview.classList.remove("hint-muted");
      } else {
        durasiPreview.textContent = "Isi jam mulai & jam selesai untuk menghitung durasi otomatis.";
        durasiPreview.classList.add("hint-muted");
      }
    }
    jamMulaiInput.addEventListener("input", updateDurasiPreview);
    jamSelesaiInput.addEventListener("input", updateDurasiPreview);
    updateDurasiPreview();

    /* ---------------- Upload gambar bukti transfer ---------------- */
    uploadBox.addEventListener("click", () => fileInput.click());
    fileInput.addEventListener("change", handleFile);

    function handleFile() {
      const file = fileInput.files[0];
      if (!file) return;
      if (!file.type.startsWith("image/")) {
        showToast("File harus berupa gambar.", "err");
        return;
      }
      if (file.size > 4 * 1024 * 1024) {
        showToast("Ukuran gambar maksimal 4MB.", "err");
        fileInput.value = "";
        return;
      }
      const reader = new FileReader();
      reader.onload = (e) => {
        compressImage(e.target.result, 900, 0.72).then((compressed) => {
          currentImageBase64 = compressed;
          previewImg.src = compressed;
          previewWrap.style.display = "block";
          uploadBox.style.display = "none";
        });
      };
      reader.readAsDataURL(file);
    }

    removeImgBtn.addEventListener("click", () => {
      currentImageBase64 = null;
      fileInput.value = "";
      previewWrap.style.display = "none";
      uploadBox.style.display = "flex";
    });

    function compressImage(dataUrl, maxWidth, quality) {
      return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => {
          const scale = Math.min(1, maxWidth / img.width);
          const canvas = document.createElement("canvas");
          canvas.width = img.width * scale;
          canvas.height = img.height * scale;
          const ctx = canvas.getContext("2d");
          ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
          resolve(canvas.toDataURL("image/jpeg", quality));
        };
        img.onerror = () => resolve(dataUrl);
        img.src = dataUrl;
      });
    }

    /* ---------------- Mode edit ---------------- */
    function resetEditMode() {
      editingId = null;
      currentImageBase64 = null;
      editBanner.classList.add("hidden");
      submitBtn.textContent = "Simpan Transaksi";
      previewWrap.style.display = "none";
      uploadBox.style.display = "flex";
      fileInput.value = "";
    }

    async function enterEditMode(id) {
      const t = await DB.getById(id);
      if (!t) return;
      editingId = id;

      document.getElementById("nama").value = t.nama;
      document.getElementById("tanggalMain").value = t.tanggalMain;
      document.getElementById("tanggalTransfer").value = t.tanggalTransfer;
      document.getElementById("nominal").value = t.nominal;
      document.getElementById("catatan").value = t.catatan || "";
      jamMulaiInput.value = t.jamMulai || "";
      jamSelesaiInput.value = t.jamSelesai || "";
      updateDurasiPreview();

      jenisGroup.querySelectorAll("input").forEach((inp) => {
        inp.checked = inp.value === t.jenisTransfer;
      });
      updateChipStyles();

      currentImageBase64 = t.gambarBukti || null;
      if (currentImageBase64) {
        previewImg.src = currentImageBase64;
        previewWrap.style.display = "block";
        uploadBox.style.display = "none";
      } else {
        previewWrap.style.display = "none";
        uploadBox.style.display = "flex";
      }

      editBannerText.textContent = `Mengedit transaksi: ${t.nama} — ${formatTanggal(t.tanggalTransfer)}`;
      editBanner.classList.remove("hidden");
      submitBtn.textContent = "Update Transaksi";

      document.getElementById("formCard").scrollIntoView({ behavior: "smooth", block: "start" });
    }

    cancelEditBtn.addEventListener("click", () => {
      form.reset();
      document.getElementById("tanggalMain").value = today;
      document.getElementById("tanggalTransfer").value = today;
      updateChipStyles();
      updateDurasiPreview();
      resetEditMode();
    });

    /* ---------------- Submit form (tambah / update) ---------------- */
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const jenisTransfer = jenisGroup.querySelector("input:checked").value;
      const jamMulai = jamMulaiInput.value;
      const jamSelesai = jamSelesaiInput.value;

      const entry = {
        nama: document.getElementById("nama").value,
        tanggalMain: document.getElementById("tanggalMain").value,
        jenisTransfer,
        tanggalTransfer: document.getElementById("tanggalTransfer").value,
        nominal: document.getElementById("nominal").value,
        catatan: document.getElementById("catatan").value,
        jamMulai,
        jamSelesai,
        okupansiJam: hitungDurasiJam(jamMulai, jamSelesai),
        gambarBukti: currentImageBase64,
      };

      if (!entry.nama || !entry.tanggalMain || !entry.tanggalTransfer || entry.nominal === "" || !jamMulai || !jamSelesai) {
        showToast("Lengkapi semua kolom wajib.", "err");
        return;
      }

      submitBtn.disabled = true;
      let ok;
      if (editingId) {
        ok = await DB.update(editingId, entry);
      } else {
        ok = await DB.add(entry);
      }
      submitBtn.disabled = false;

      if (ok) {
        showToast(editingId ? "Transaksi berhasil diperbarui." : "Transaksi berhasil disimpan.", "ok");
        form.reset();
        document.getElementById("tanggalMain").value = today;
        document.getElementById("tanggalTransfer").value = today;
        updateChipStyles();
        updateDurasiPreview();
        resetEditMode();
        renderTable();
      } else {
        showToast("Gagal menyimpan — penyimpanan penuh atau bermasalah. Coba kurangi ukuran gambar.", "err");
      }
    });

    /* ---------------- Tabel riwayat ---------------- */
    async function renderTable() {
      const tbody = document.getElementById("txTableBody");
      const emptyState = document.getElementById("emptyState");
      const q = searchInput.value.trim().toLowerCase();
      const jenis = filterJenis.value;

      let data = await DB.getAll();
      if (q) {
        data = data.filter(
          (t) => t.nama.toLowerCase().includes(q) || (t.catatan || "").toLowerCase().includes(q)
        );
      }
      if (jenis) data = data.filter((t) => t.jenisTransfer === jenis);

      tbody.innerHTML = "";
      emptyState.classList.toggle("hidden", data.length > 0);

      data.forEach((t) => {
        const tr = document.createElement("tr");
        const tagClass =
          t.jenisTransfer === "Qris" ? "tag-qris" : t.jenisTransfer === "Transfer" ? "tag-transfer" : "tag-cash";

        tr.innerHTML = `
          <td>${
            t.gambarBukti
              ? `<img src="${t.gambarBukti}" class="thumb" data-img="${t.gambarBukti}" alt="Bukti" />`
              : `<div class="thumb-empty">—</div>`
          }</td>
          <td>${escapeHtml(t.nama)}</td>
          <td>${formatTanggal(t.tanggalMain)}</td>
          <td><span class="tag ${tagClass}">${t.jenisTransfer}</span></td>
          <td>${formatTanggal(t.tanggalTransfer)}</td>
          <td class="mono">${formatRupiah(t.nominal)}</td>
          <td>${t.jamMulai && t.jamSelesai ? `${t.jamMulai}–${t.jamSelesai}<br><span style="color:var(--text-faint);font-size:11.5px;">${formatJam(t.okupansiJam)}</span>` : formatJam(t.okupansiJam)}</td>
          <td style="max-width:160px;white-space:normal;color:var(--text-dim);">${escapeHtml(t.catatan) || "-"}</td>
          <td>
            <div class="flex gap-8">
              <button class="btn btn-ghost btn-sm" data-edit="${t.id}">Edit</button>
              <button class="btn btn-danger btn-sm" data-del="${t.id}">Hapus</button>
            </div>
          </td>
        `;
        tbody.appendChild(tr);
      });

      tbody.querySelectorAll("img.thumb").forEach((img) => {
        img.addEventListener("click", () => {
          lightboxImg.src = img.dataset.img;
          lightbox.classList.add("open");
        });
      });

      tbody.querySelectorAll("[data-edit]").forEach((btn) => {
        btn.addEventListener("click", () => enterEditMode(btn.dataset.edit));
      });

      tbody.querySelectorAll("[data-del]").forEach((btn) => {
        btn.addEventListener("click", async () => {
          if (confirm("Hapus transaksi ini?")) {
            if (editingId === btn.dataset.del) resetEditMode();
            await DB.remove(btn.dataset.del);
            showToast("Transaksi dihapus.", "ok");
            renderTable();
          }
        });
      });
    }

    document.getElementById("closeLightbox").addEventListener("click", () => lightbox.classList.remove("open"));
    lightbox.addEventListener("click", (e) => {
      if (e.target === lightbox) lightbox.classList.remove("open");
    });

    searchInput.addEventListener("input", renderTable);
    filterJenis.addEventListener("change", renderTable);

    /* ---------------- Ekspor spreadsheet ---------------- */
    exportBtn.addEventListener("click", async () => {
      const data = await DB.getAll();
      if (!data.length) {
        showToast("Belum ada data untuk diekspor.", "err");
        return;
      }
      const rows = data.map((t) => ({
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
      const ws = XLSX.utils.json_to_sheet(rows);
      ws["!cols"] = [
        { wch: 20 }, { wch: 14 }, { wch: 14 }, { wch: 16 },
        { wch: 14 }, { wch: 10 }, { wch: 10 }, { wch: 14 },
        { wch: 30 }, { wch: 16 },
      ];
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, "Transaksi");
      const filename = `transaksi-uang-masuk-${new Date().toISOString().slice(0, 10)}.xlsx`;
      XLSX.writeFile(wb, filename);
      showToast("Berhasil diekspor ke spreadsheet.", "ok");
    });

    function escapeHtml(str) {
      const div = document.createElement("div");
      div.textContent = str || "";
      return div.innerHTML;
    }

    // simpan referensi supaya bisa dipanggil dari onEnter()
    AdminView._refreshGateVisibility = refreshGateVisibility;
    AdminView._renderTable = renderTable;
  }

  function onEnter() {
    init();
    const loggedIn = AdminView._refreshGateVisibility();
    if (loggedIn) AdminView._renderTable();
  }

  return { init, onEnter };
})();
