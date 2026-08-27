/* =========================================================
   storage.js
   Lapisan data bersama untuk tampilan Admin & Super Admin.

   PENTING soal "data hilang saat refresh":
   Saat file ini dijalankan di dalam pratinjau/preview Claude,
   localStorage biasa/sessionStorage TIDAK persisten antar refresh
   (dibersihkan setiap kali preview dimuat ulang). Untuk mengatasi
   ini, lapisan Persist di bawah akan otomatis memakai penyimpanan
   bawaan Claude (window.storage) jika tersedia — yang memang
   didesain untuk bertahan antar sesi/refresh. Jika file ini dibuka
   sebagai HTML biasa di browser (di luar Claude), window.storage
   tidak ada, sehingga otomatis jatuh ke localStorage seperti biasa.

   Karena window.storage bersifat asynchronous, seluruh lapisan DB
   di bawah ini juga dibuat asynchronous (pakai async/await), baik
   dipanggil dari admin.js maupun dashboard.js.
   ========================================================= */

const Persist = (() => {
  const useArtifactStorage = typeof window !== "undefined" && !!window.storage;

  async function get(key) {
    if (useArtifactStorage) {
      try {
        const res = await window.storage.get(key, false);
        return res ? res.value : null;
      } catch (e) {
        // key belum pernah diset -> storage API melempar error, bukan null
        return null;
      }
    }
    try {
      return localStorage.getItem(key);
    } catch (e) {
      console.error("Gagal membaca localStorage:", e);
      return null;
    }
  }

  async function set(key, value) {
    if (useArtifactStorage) {
      try {
        const res = await window.storage.set(key, value, false);
        return !!res;
      } catch (e) {
        console.error("Gagal menyimpan ke storage:", e);
        return false;
      }
    }
    try {
      localStorage.setItem(key, value);
      return true;
    } catch (e) {
      console.error("Gagal menyimpan ke localStorage:", e);
      return false;
    }
  }

  return { get, set, isArtifact: useArtifactStorage };
})();

const DB = (() => {
  const KEY = "transaksi-data";
  let cache = null; // cache in-memory supaya tidak perlu fetch berulang-ulang

  function uid() {
    return "tx_" + Date.now().toString(36) + "_" + Math.random().toString(36).slice(2, 8);
  }

  async function _load() {
    if (cache) return cache;
    const raw = await Persist.get(KEY);
    try {
      cache = raw ? JSON.parse(raw) : [];
    } catch (e) {
      cache = [];
    }
    return cache;
  }

  async function _save() {
    return Persist.set(KEY, JSON.stringify(cache));
  }

  function monthKeyOf(dateStr) {
    const d = new Date(dateStr);
    if (isNaN(d)) return null;
    return d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0");
  }

  return {
    async getAll() {
      const all = await _load();
      return [...all].sort((a, b) => new Date(b.tanggalTransfer) - new Date(a.tanggalTransfer));
    },

    async getById(id) {
      const all = await _load();
      return all.find((t) => t.id === id) || null;
    },

    async add(entry) {
      await _load();
      const record = {
        id: uid(),
        nama: entry.nama.trim(),
        tanggalMain: entry.tanggalMain,
        jenisTransfer: entry.jenisTransfer,
        tanggalTransfer: entry.tanggalTransfer,
        nominal: Number(entry.nominal) || 0,
        catatan: (entry.catatan || "").trim(),
        jamMulai: entry.jamMulai || "",
        jamSelesai: entry.jamSelesai || "",
        okupansiJam: Number(entry.okupansiJam) || 0,
        gambarBukti: entry.gambarBukti || null,
        createdAt: new Date().toISOString(),
      };
      cache.push(record);
      const ok = await _save();
      if (!ok) cache.pop();
      return ok ? record : null;
    },

    async update(id, patch) {
      await _load();
      const idx = cache.findIndex((t) => t.id === id);
      if (idx === -1) return false;
      const backup = cache[idx];
      cache[idx] = { ...cache[idx], ...patch };
      const ok = await _save();
      if (!ok) cache[idx] = backup;
      return ok;
    },

    async remove(id) {
      await _load();
      const backup = cache;
      cache = cache.filter((t) => t.id !== id);
      const ok = await _save();
      if (!ok) cache = backup;
      return ok;
    },

    async clearAll() {
      cache = [];
      return _save();
    },

    // ---- Agregasi per bulan (dipakai dashboard super admin) ----
    // key bulan format: "YYYY-MM", urut ASC (cocok untuk grafik garis)
    async groupByMonth() {
      const all = await _load();
      const map = {};
      all.forEach((t) => {
        const key = monthKeyOf(t.tanggalTransfer);
        if (!key) return;
        if (!map[key]) {
          map[key] = { key, count: 0, totalNominal: 0, totalOkupansi: 0 };
        }
        map[key].count += 1;
        map[key].totalNominal += Number(t.nominal) || 0;
        map[key].totalOkupansi += Number(t.okupansiJam) || 0;
      });
      return Object.values(map).sort((a, b) => (a.key > b.key ? 1 : -1));
    },

    monthLabel(key) {
      const [y, m] = key.split("-").map(Number);
      const bulan = [
        "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
        "Jul", "Agu", "Sep", "Okt", "Nov", "Des",
      ];
      return `${bulan[m - 1]} ${y}`;
    },

    // ---- Ringkasan total (opsional filter per bulan "YYYY-MM") ----
    async summary(filterMonthKey) {
      let all = await _load();
      if (filterMonthKey) {
        all = all.filter((t) => monthKeyOf(t.tanggalTransfer) === filterMonthKey);
      }
      const totalNominal = all.reduce((s, t) => s + (Number(t.nominal) || 0), 0);
      const totalOkupansi = all.reduce((s, t) => s + (Number(t.okupansiJam) || 0), 0);
      const count = all.length;
      const rataRata = count ? Math.round(totalNominal / count) : 0;
      return { count, totalNominal, totalOkupansi, rataRata };
    },
  };
})();

/* ---------- Helper format umum ---------- */
function formatRupiah(num) {
  return "Rp " + Number(num || 0).toLocaleString("id-ID");
}

function formatTanggal(dateStr) {
  if (!dateStr) return "-";
  const d = new Date(dateStr);
  if (isNaN(d)) return dateStr;
  return d.toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" });
}

function formatJam(n) {
  const v = Number(n) || 0;
  return v % 1 === 0 ? `${v} jam` : `${v.toFixed(1)} jam`;
}

// Hitung durasi (jam, desimal) dari jam mulai & jam selesai (format "HH:MM").
// Jika jam selesai <= jam mulai, dianggap lewat tengah malam (+24 jam).
function hitungDurasiJam(jamMulai, jamSelesai) {
  if (!jamMulai || !jamSelesai) return 0;
  const [h1, m1] = jamMulai.split(":").map(Number);
  const [h2, m2] = jamSelesai.split(":").map(Number);
  if ([h1, m1, h2, m2].some((n) => Number.isNaN(n))) return 0;
  let menit = h2 * 60 + m2 - (h1 * 60 + m1);
  if (menit <= 0) menit += 24 * 60;
  return Math.round((menit / 60) * 100) / 100;
}

function formatDurasiLabel(jamMulai, jamSelesai) {
  if (!jamMulai || !jamSelesai) return "-";
  const durasi = hitungDurasiJam(jamMulai, jamSelesai);
  return `${jamMulai}–${jamSelesai} (${formatJam(durasi)})`;
}

/* ---------- Toast notifikasi kecil, dipakai di semua tampilan ---------- */
function showToast(message, type = "ok") {
  let el = document.getElementById("toast");
  if (!el) {
    el = document.createElement("div");
    el.id = "toast";
    document.body.appendChild(el);
  }
  el.className = `toast-${type}`;
  el.textContent = message;
  requestAnimationFrame(() => el.classList.add("show"));
  clearTimeout(el._t);
  el._t = setTimeout(() => el.classList.remove("show"), 2600);
}

/* ---------- Gerbang login sederhana (client-side only) ----------
   PENTING: Ini BUKAN autentikasi yang aman untuk produksi -
   password dicek di browser. Cocok untuk mencegah akses tidak
   sengaja di lingkungan internal / prototipe. Status login dicek
   ulang tiap kali tampilan dibuka, jadi tombol keluar langsung
   membawa balik ke halaman login tanpa perlu reload dokumen. */
const AUTH = {
  PASSWORDS: {
    admin: "admin123",
    superadmin: "super123",
  },
  isLoggedIn(role) {
    try {
      return sessionStorage.getItem("ptm_role_" + role) === "1";
    } catch (e) {
      return false;
    }
  },
  login(role, password) {
    if (password === this.PASSWORDS[role]) {
      try {
        sessionStorage.setItem("ptm_role_" + role, "1");
      } catch (e) {
        /* abaikan jika sessionStorage tidak tersedia */
      }
      return true;
    }
    return false;
  },
  logout(role) {
    try {
      sessionStorage.removeItem("ptm_role_" + role);
    } catch (e) {
      /* abaikan */
    }
  },
};
