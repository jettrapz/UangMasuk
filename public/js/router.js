/* =========================================================
   router.js — router hash sederhana.
   Admin & Super Admin sekarang jadi SATU halaman (SPA). Berpindah
   tampilan hanya mengganti hash URL (#/admin, #/superadmin), TANPA
   memuat ulang dokumen — sehingga data & sesi login selalu
   konsisten antar tampilan.
   ========================================================= */

const Router = (() => {
  const listeners = [];

  function parseHash() {
    const h = location.hash.replace(/^#/, "");
    return h || "/";
  }

  function onChange(cb) {
    listeners.push(cb);
  }

  function navigate(route) {
    if (location.hash === "#" + route) {
      emit(); // hash sama -> tetap trigger render ulang (refresh data)
    } else {
      location.hash = route;
    }
  }

  function emit() {
    const route = parseHash();
    listeners.forEach((cb) => cb(route));
  }

  window.addEventListener("hashchange", emit);
  window.addEventListener("DOMContentLoaded", emit);

  return { onChange, navigate };
})();
