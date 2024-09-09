const logo = document.getElementById("logo");
const barraLateral = document.querySelector(".barra-lateral");
const spans = document.querySelectorAll("span");
const palanca = document.querySelector(".switch");
const circulo = document.querySelector(".circulo");
const menu = document.querySelector(".menu");
const main = document.querySelector("main");

// Función para aplicar el modo oscuro basado en el almacenamiento local
function applyDarkMode() {
    if (localStorage.getItem("dark-mode") === "enabled") {
        document.body.classList.add("dark-mode");
        circulo.classList.add("prendido");
    } else {
        document.body.classList.remove("dark-mode");
        circulo.classList.remove("prendido");
    }
}

// Inicializa el modo oscuro al cargar la página
applyDarkMode();

menu.addEventListener("click", () => {
    barraLateral.classList.toggle("max-barra-lateral");
    const isMaxBarraLateral = barraLateral.classList.contains("max-barra-lateral");
    menu.children[0].style.display = isMaxBarraLateral ? "none" : "block";
    menu.children[1].style.display = isMaxBarraLateral ? "block" : "none";

    adjustForSmallScreens();
});

palanca.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");
    circulo.classList.toggle("prendido");

    // Guardar la preferencia en localStorage
    if (document.body.classList.contains("dark-mode")) {
        localStorage.setItem("dark-mode", "enabled");
    } else {
        localStorage.setItem("dark-mode", "disabled");
    }
});

logo.addEventListener("click", () => {
    barraLateral.classList.toggle("mini-barra-lateral");
    main.classList.toggle("min-main");
    spans.forEach((span) => span.classList.toggle("oculto"));
});

function adjustForSmallScreens() {
    if (window.innerWidth <= 320) {
        barraLateral.classList.add("mini-barra-lateral");
        main.classList.add("min-main");
        spans.forEach((span) => span.classList.add("oculto"));
    } else {
        barraLateral.classList.remove("mini-barra-lateral");
        main.classList.remove("min-main");
        spans.forEach((span) => span.classList.remove("oculto"));
    }
}

// Inicializa el ajuste para el tamaño de ventana actual
adjustForSmallScreens();

// Ajusta el diseño cuando se cambia el tamaño de la ventana
window.addEventListener("resize", adjustForSmallScreens);

//Funciones mapas
