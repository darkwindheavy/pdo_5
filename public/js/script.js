document.addEventListener("click", function (e) {
    if (e.target && e.target.hasAttribute("data-enlargeable")) {
        const src = e.target.getAttribute("src");
        
        // Crear el overlay
        const overlay = document.createElement("div");
        overlay.classList.add("enlarged-overlay");
        
        // Insertar la imagen ampliada
        const img = document.createElement("img");
        img.src = src;
        overlay.appendChild(img);
        
        // Añadir el overlay al body
        document.body.appendChild(overlay);
        
        // Cerrar al hacer clic en el overlay
        overlay.addEventListener("click", function () {
            document.body.removeChild(overlay);
        });
    }
});
