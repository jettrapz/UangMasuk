/* =========================================================
   main.js — menyambungkan Router ke tampilan yang sesuai
   ========================================================= */

Router.onChange((route) => {
  document.querySelectorAll(".screen").forEach((s) => s.classList.add("hidden"));

  if (route === "/admin") {
    document.getElementById("screen-admin").classList.remove("hidden");
    AdminView.onEnter();
  } else if (route === "/superadmin") {
    document.getElementById("screen-superadmin").classList.remove("hidden");
    SuperAdminView.onEnter();
  } else {
    document.getElementById("screen-landing").classList.remove("hidden");
  }

  window.scrollTo(0, 0);
});
