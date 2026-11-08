    // Navbar blur on scroll
    window.addEventListener("scroll", () => {
        const navbar = document.querySelector(".navbar");
        navbar.classList.toggle("scrolled", window.scrollY > 20);
    });

    // Adaptive font color detector
    const heroTitle = document.getElementById("heroTitle");
    const heroText = document.getElementById("heroText");

    setInterval(() => {
        const gradientColors = ["#0f0f0f", "#111", "#007bff", "#00f0ff"];
        const index = Math.floor((Date.now() / 3000) % gradientColors.length);
        const color = gradientColors[index];

        const darkColors = ["#0f0f0f", "#111"];
        if (darkColors.includes(color)) {
            heroTitle.style.color = "#fff";
            heroText.style.color = "#fff";
        } else {
            heroTitle.style.color = "#fff";
            heroText.style.color = "#fff";
        }
    }, 1000);


    // BOX

