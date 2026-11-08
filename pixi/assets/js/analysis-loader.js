(() => {
  "use strict";

  const statusBox = document.getElementById("analysisStatus");
  const analyzeBtn = document.getElementById("analyzeBtn");

  function setStatus(message, type = "info") {
    if (!statusBox) return;
    statusBox.textContent = message;
    statusBox.className = `analysis-status ${type}`;
  }

  function loadScript(url) {
    return new Promise((resolve, reject) => {
      const script = document.createElement("script");
      script.src = url;
      script.async = false;
      script.crossOrigin = "anonymous";
      script.onload = () => resolve(url);
      script.onerror = () => reject(new Error(`Gagal memuat ${url}`));
      document.head.appendChild(script);
    });
  }

  async function loadWithFallback(name, globalName, urls, required = true) {
    if (typeof window[globalName] !== "undefined") return true;

    let lastError = null;
    for (const url of urls) {
      try {
        console.log(`[AI loader] Memuat ${name}:`, url);
        await loadScript(url);
        if (typeof window[globalName] !== "undefined") {
          console.log(`[AI loader] ${name} berhasil dimuat.`);
          return true;
        }
        lastError = new Error(
          `${name} selesai diunduh tetapi global ${globalName} tidak tersedia.`,
        );
      } catch (err) {
        lastError = err;
        console.warn(`[AI loader] ${name} gagal dari CDN ini.`, err);
      }
    }

    if (required) throw lastError || new Error(`${name} gagal dimuat.`);
    console.warn(`[AI loader] ${name} opsional tidak tersedia.`);
    return false;
  }

  async function boot() {
    if (analyzeBtn) analyzeBtn.disabled = true;
    setStatus("Menyiapkan library analisis...", "info");

    try {
      await loadWithFallback("TensorFlow.js", "tf", [
        "https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.14.0/dist/tf.min.js",
        "https://unpkg.com/@tensorflow/tfjs@4.14.0/dist/tf.min.js",
      ]);

      await loadWithFallback("MoveNet / Pose Detection", "poseDetection", [
        "https://cdn.jsdelivr.net/npm/@tensorflow-models/pose-detection@2.1.3/dist/pose-detection.min.js",
        "https://unpkg.com/@tensorflow-models/pose-detection@2.1.3/dist/pose-detection.min.js",
      ]);

      // Gunakan API body-segmentation yang menggantikan package BodyPix lama.
      // Library ini membantu estimasi lebar pinggang untuk membedakan hourglass vs rectangle.
      await loadWithFallback(
        "Body Segmentation",
        "bodySegmentation",
        [
          "https://cdn.jsdelivr.net/npm/@tensorflow-models/body-segmentation@1.0.2",
          "https://cdn.jsdelivr.net/npm/@tensorflow-models/body-segmentation",
        ],
        false,
      );

      await loadScript("assets/js/analyze5.js?v=10");
      setStatus("Library analisis siap. Pilih foto untuk mulai.", "success");
    } catch (err) {
      console.error("[AI loader]", err);
      setStatus(
        "Library inti analisis gagal dimuat. Buka F12 > Console untuk melihat detail CDN yang gagal.",
        "error",
      );
      alert(
        "Library inti analisis gagal dimuat. Pastikan internet aktif, lalu tekan Ctrl+F5. Jika masih gagal, kirim error Console.",
      );
    } finally {
      if (analyzeBtn) analyzeBtn.disabled = false;
    }
  }

  boot();
})();
