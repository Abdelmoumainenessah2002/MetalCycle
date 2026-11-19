document.addEventListener("DOMContentLoaded", function () {
    const menuBtn = document.querySelector(".menu-btn");
    const sidebar = document.querySelector(".sidebar");

    if (menuBtn && sidebar) {
        menuBtn.addEventListener("click", function () {
            // عند الضغط على الزر، سيتم تبديل كلاس toggle-sidebar للغلق والفتح
            sidebar.classList.toggle("collapsed");
        });
    }
});