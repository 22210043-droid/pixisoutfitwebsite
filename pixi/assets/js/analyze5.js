(() => {
  "use strict";
  // Body-shape classifier V10: resolution-independent pose/mask coordinates.

  const state = {
    image: null,
    poseDetector: null,
    bodySegmenter: null,
    modelsLoading: null,
  };

  const fileInput = document.getElementById("photoUpload");
  const heightInput = document.getElementById("heightInput");
  const weightInput = document.getElementById("weightInput");
  const chestInput = document.getElementById("chestInput");
  const waistMeasureInput = document.getElementById("waistMeasureInput");
  const hipInput = document.getElementById("hipInput");
  const analyzeBtn = document.getElementById("analyzeBtn");
  const preview = document.getElementById("preview");
  const statusBox = document.getElementById("analysisStatus");

  if (
    !fileInput ||
    !heightInput ||
    !weightInput ||
    !chestInput ||
    !waistMeasureInput ||
    !hipInput ||
    !analyzeBtn ||
    !preview
  ) {
    console.error("Komponen halaman analisis tidak lengkap.");
    return;
  }

  function setStatus(message, type = "info") {
    if (!statusBox) return;
    statusBox.textContent = message;
    statusBox.className = `analysis-status ${type}`;
  }

  function setBusy(busy) {
    analyzeBtn.disabled = busy;
    analyzeBtn.textContent = busy ? "Menganalisis..." : "🔍 Analisis Sekarang";
  }

  function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
  }

  function getKeypoint(keypoints, name, minScore = 0.25) {
    const point = keypoints.find((k) => k.name === name);
    return point && (point.score ?? 0) >= minScore ? point : null;
  }

  function distance(a, b) {
    return Math.hypot(a.x - b.x, a.y - b.y);
  }

  function averagePoint(a, b) {
    return { x: (a.x + b.x) / 2, y: (a.y + b.y) / 2 };
  }

  // MoveNet mengembalikan koordinat sesuai ukuran elemen gambar yang dianalisis,
  // bukan selalu naturalWidth/naturalHeight file aslinya. Gunakan ukuran render
  // sebagai ruang koordinat pose dan natural size hanya sebagai fallback.
  function getPoseCoordinateSize(image) {
    const width = Number(
      image.width || image.clientWidth || image.naturalWidth || 0,
    );
    const height = Number(
      image.height || image.clientHeight || image.naturalHeight || 0,
    );

    if (width <= 0 || height <= 0) {
      throw new Error("Ukuran gambar tidak valid untuk analisis pose.");
    }
    return { width, height };
  }

  function scalePoint(point, scaleX, scaleY) {
    if (!point) return null;
    return {
      ...point,
      x: point.x * scaleX,
      y: point.y * scaleY,
    };
  }

  const BODY_SHAPE_THRESHOLDS = Object.freeze({
    triangleMax: 0.9,
    invertedMin: 1.14,
    hourglassFrameDifferenceMax: 0.12,
    // Segmentasi foto berpakaian cenderung membuat pinggang 2-5% lebih lebar
    // daripada kontur tubuh sebenarnya. Nilai 0.85 menjaga rectangle tetap
    // terpisah, sekaligus menerima lekukan hourglass yang masih terlihat jelas.
    hourglassWaistToFrameMax: 0.85,
  });

  // Jarak landmark hip MoveNet adalah jarak antarsendi, bukan lebar pinggul
  // terluar. Faktor ini hanya dipakai ketika segmentasi benar-benar tidak ada.
  const POSE_HIP_WIDTH_CALIBRATION = 1.28;

  function classifyBodyShape({
    upperLowerRatio,
    waistToFrame,
    frameDifference,
  }) {
    if (
      !Number.isFinite(upperLowerRatio) ||
      !Number.isFinite(waistToFrame) ||
      !Number.isFinite(frameDifference)
    ) {
      throw new Error("Rasio bentuk tubuh tidak valid.");
    }

    if (upperLowerRatio <= BODY_SHAPE_THRESHOLDS.triangleMax) {
      return {
        shape: "triangle",
        reason: "lower torso lebih lebar dari upper torso",
      };
    }

    if (upperLowerRatio >= BODY_SHAPE_THRESHOLDS.invertedMin) {
      return {
        shape: "inverted triangle",
        reason: "upper torso jauh lebih lebar dari lower torso",
      };
    }

    if (
      frameDifference <= BODY_SHAPE_THRESHOLDS.hourglassFrameDifferenceMax &&
      waistToFrame <= BODY_SHAPE_THRESHOLDS.hourglassWaistToFrameMax
    ) {
      return {
        shape: "hourglass",
        reason: "upper-lower seimbang dengan pinggang lebih kecil",
      };
    }

    return {
      shape: "rectangle",
      reason: "upper-lower relatif seimbang tanpa lekukan pinggang kuat",
    };
  }

  fileInput.addEventListener("change", () => {
    const file = fileInput.files[0];
    state.image = null;
    setStatus("");

    if (!file) {
      preview.innerHTML =
        '<div class="scan-text preview-placeholder">Preview foto akan muncul di sini</div>';
      return;
    }

    if (!file.type.startsWith("image/")) {
      fileInput.value = "";
      alert("File harus berupa gambar JPG, PNG, atau WebP.");
      return;
    }

    const reader = new FileReader();
    reader.onload = (event) => {
      preview.innerHTML = `
        <img id="uploadedImage" src="${event.target.result}" alt="Preview Foto">
        <div id="scanOverlay">
          <div class="scan-line"></div>
          <div class="scan-glow"></div>
          <div class="scan-text">🔍 ANALYZING...</div>
        </div>`;

      const image = document.getElementById("uploadedImage");
      image.onload = () => {
        state.image = image;
        setStatus("Foto siap dianalisis.", "success");
      };
    };
    reader.readAsDataURL(file);
  });

  async function ensureModels() {
    if (state.poseDetector) return;
    if (state.modelsLoading) return state.modelsLoading;

    state.modelsLoading = (async () => {
      const missing = [];
      if (typeof tf === "undefined") missing.push("TensorFlow.js");
      if (typeof poseDetection === "undefined")
        missing.push("MoveNet / pose-detection");
      if (missing.length) {
        throw new Error(
          "Library inti gagal dimuat: " +
            missing.join(", ") +
            ". Coba refresh halaman atau periksa akses CDN.",
        );
      }

      try {
        await tf.setBackend("webgl");
      } catch (_) {
        await tf.setBackend("cpu");
      }
      await tf.ready();

      setStatus("Memuat model analisis tubuh...", "info");

      const model = poseDetection.SupportedModels.MoveNet;
      state.poseDetector = await poseDetection.createDetector(model, {
        modelType: poseDetection.movenet.modelType.SINGLEPOSE_THUNDER,
        enableSmoothing: true,
      });

      // Body Segmentation (model BodyPix) opsional untuk mengukur garis pinggang.
      // Jika gagal, klasifikasi tetap berjalan menggunakan rasio bahu-pinggul MoveNet.
      if (typeof bodySegmentation !== "undefined") {
        try {
          const segmentationModel = bodySegmentation.SupportedModels.BodyPix;
          state.bodySegmenter = await bodySegmentation.createSegmenter(
            segmentationModel,
            {
              architecture: "MobileNetV1",
              outputStride: 16,
              multiplier: 0.5,
              quantBytes: 2,
            },
          );
        } catch (err) {
          console.warn(
            "Body Segmentation tidak dapat dimuat; melanjutkan tanpa segmentasi pinggang.",
            err,
          );
          state.bodySegmenter = null;
        }
      } else {
        console.warn(
          "Body Segmentation tidak tersedia; melanjutkan dengan MoveNet saja.",
        );
        state.bodySegmenter = null;
      }
    })();

    try {
      await state.modelsLoading;
    } finally {
      state.modelsLoading = null;
    }
  }

  function validatePhysicalData() {
    const height = Number(heightInput.value);
    const weight = Number(weightInput.value);

    if (!Number.isFinite(height) || height < 120 || height > 230) {
      throw new Error("Masukkan tinggi badan antara 120–230 cm.");
    }
    if (!Number.isFinite(weight) || weight < 30 || weight > 250) {
      throw new Error("Masukkan berat badan antara 30–250 kg.");
    }

    const manualFields = [
      {
        label: "lingkar dada / upper torso",
        input: chestInput,
        min: 50,
        max: 200,
      },
      {
        label: "lingkar pinggang",
        input: waistMeasureInput,
        min: 40,
        max: 200,
      },
      {
        label: "lingkar pinggul",
        input: hipInput,
        min: 50,
        max: 200,
      },
    ];
    const filledManualFields = manualFields.filter(
      ({ input }) => input.value.trim() !== "",
    );

    if (
      filledManualFields.length > 0 &&
      filledManualFields.length < manualFields.length
    ) {
      throw new Error(
        "Isi lengkap lingkar dada, pinggang, dan pinggul; atau kosongkan ketiganya.",
      );
    }

    let manualMeasurements = null;
    if (filledManualFields.length === manualFields.length) {
      const values = manualFields.map(({ label, input, min, max }) => {
        const value = Number(input.value);
        if (!Number.isFinite(value) || value < min || value > max) {
          throw new Error(`Masukkan ${label} antara ${min}–${max} cm.`);
        }
        return value;
      });

      manualMeasurements = {
        upper: values[0],
        waist: values[1],
        lower: values[2],
      };
    }

    return {
      height,
      weight,
      bmi: weight / Math.pow(height / 100, 2),
      manualMeasurements,
    };
  }

  function classifyManualMeasurements(measurements, bmi) {
    const upperWidth = measurements.upper;
    const waistWidth = measurements.waist;
    const lowerWidth = measurements.lower;
    const upperLowerRatio = upperWidth / lowerWidth;
    const averageFrame = (upperWidth + lowerWidth) / 2;
    const waistToFrame = waistWidth / averageFrame;
    const frameDifference =
      Math.abs(upperWidth - lowerWidth) / Math.max(upperWidth, lowerWidth);
    const { shape, reason } = classifyBodyShape({
      upperLowerRatio,
      waistToFrame,
      frameDifference,
    });

    console.table({
      source: "manual-measurements-v1",
      shape,
      upperWidth: Number(upperWidth.toFixed(2)),
      waistWidth: Number(waistWidth.toFixed(2)),
      lowerWidth: Number(lowerWidth.toFixed(2)),
      upperLowerRatio: Number(upperLowerRatio.toFixed(3)),
      waistToFrame: Number(waistToFrame.toFixed(3)),
      frameDifference: Number(frameDifference.toFixed(3)),
    });

    return {
      shape,
      metrics: {
        source: "manual-measurements-v1",
        upperWidth,
        waistWidth,
        lowerWidth,
        upperLowerRatio,
        waistToFrame,
        frameDifference,
        bmi,
        reason,
        thresholds: BODY_SHAPE_THRESHOLDS,
      },
    };
  }

  async function detectPose(image) {
    const poses = await state.poseDetector.estimatePoses(image, {
      flipHorizontal: false,
      maxPoses: 1,
    });

    if (!poses.length || !poses[0].keypoints) {
      throw new Error(
        "Tubuh tidak terdeteksi. Gunakan foto satu orang dengan tubuh terlihat jelas.",
      );
    }

    const keypoints = poses[0].keypoints;
    const tracked = [
      "nose",
      "left_shoulder",
      "right_shoulder",
      "left_hip",
      "right_hip",
    ];
    const scores = {};
    tracked.forEach((name) => {
      const p = keypoints.find((k) => k.name === name);
      scores[name] = p ? Number((p.score ?? 0).toFixed(3)) : 0;
    });
    console.log("[AI] Keypoint confidence:", scores);

    // Jangan langsung menggagalkan analisis hanya karena satu landmark memiliki
    // confidence rendah. Pada foto full-body, wajah/hip sering kecil. Tahap body
    // shape memiliki fallback berbasis Body Segmentation.
    const visibleTorsoPoints = [
      "left_shoulder",
      "right_shoulder",
      "left_hip",
      "right_hip",
    ].filter((name) => getKeypoint(keypoints, name, 0.07)).length;

    if (visibleTorsoPoints < 2) {
      console.warn(
        "[AI] Landmark torso kurang kuat; akan mencoba fallback segmentasi tubuh.",
      );
    }

    return keypoints;
  }

  function createImageCanvas(image) {
    const canvas = document.createElement("canvas");
    const width = image.naturalWidth || image.width;
    const height = image.naturalHeight || image.height;
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext("2d", { willReadFrequently: true });
    ctx.drawImage(image, 0, 0, width, height);
    return { canvas, ctx, width, height };
  }

  function isLikelySkin(r, g, b) {
    // Rule YCbCr sederhana untuk membuang rambut, latar, serta piksel sangat gelap/terang.
    const y = 0.299 * r + 0.587 * g + 0.114 * b;
    const cb = 128 - 0.168736 * r - 0.331264 * g + 0.5 * b;
    const cr = 128 + 0.5 * r - 0.418688 * g - 0.081312 * b;
    return y > 35 && y < 245 && cb >= 72 && cb <= 138 && cr >= 128 && cr <= 180;
  }

  function isLikelySkinLoose(r, g, b) {
    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    // Fallback yang lebih toleran terhadap pencahayaan dingin/hangat.
    return (
      r > 45 &&
      g > 30 &&
      b > 20 &&
      max - min > 8 &&
      r >= g * 0.88 &&
      r >= b * 0.95 &&
      Math.abs(r - g) > 5
    );
  }

  function samplePatch(ctx, centerX, centerY, radius, imageWidth, imageHeight) {
    const x0 = clamp(Math.round(centerX - radius), 0, imageWidth - 1);
    const y0 = clamp(Math.round(centerY - radius), 0, imageHeight - 1);
    const x1 = clamp(Math.round(centerX + radius), 1, imageWidth);
    const y1 = clamp(Math.round(centerY + radius), 1, imageHeight);
    const w = Math.max(1, x1 - x0);
    const h = Math.max(1, y1 - y0);
    const pixels = ctx.getImageData(x0, y0, w, h).data;

    function collect(predicate) {
      let r = 0,
        g = 0,
        b = 0,
        count = 0;
      for (let i = 0; i < pixels.length; i += 4) {
        const pr = pixels[i];
        const pg = pixels[i + 1];
        const pb = pixels[i + 2];
        if (!predicate(pr, pg, pb)) continue;
        r += pr;
        g += pg;
        b += pb;
        count++;
      }
      return count ? { r: r / count, g: g / count, b: b / count, count } : null;
    }

    return collect(isLikelySkin) || collect(isLikelySkinLoose);
  }

  function rgbToHsv(r, g, b) {
    r /= 255;
    g /= 255;
    b /= 255;
    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    const d = max - min;
    let h = 0;

    if (d !== 0) {
      if (max === r) h = ((g - b) / d) % 6;
      else if (max === g) h = (b - r) / d + 2;
      else h = (r - g) / d + 4;
      h *= 60;
      if (h < 0) h += 360;
    }

    const s = max === 0 ? 0 : d / max;
    return { h, s: s * 100, v: max * 100 };
  }

  function classifySkinTone(rgb) {
    const hsv = rgbToHsv(rgb.r, rgb.g, rgb.b);
    const rb = rgb.r - rgb.b;
    const rg = rgb.r - rgb.g;

    // Klasifikasi rule-based. Nilai ini merupakan estimasi visual, bukan diagnosis warna kulit klinis.
    if (
      (hsv.h >= 8 && hsv.h <= 42 && hsv.s >= 28 && rb >= 18) ||
      (rb >= 30 && rg >= 7)
    ) {
      return "warm";
    }
    if (hsv.s <= 18 && hsv.v >= 65) {
      return "neutral-cool";
    }
    if (hsv.s <= 25 && rb <= 16) {
      return "cool";
    }
    if (rb >= 16 && rb < 30) {
      return "neutral-warm";
    }
    return "neutral";
  }

  function detectSkinTone(image, keypoints) {
    const { ctx, width, height } = createImageCanvas(image);
    const poseSize = getPoseCoordinateSize(image);
    const pointScaleX = width / poseSize.width;
    const pointScaleY = height / poseSize.height;
    const scaledKeypoint = (name, minScore) =>
      scalePoint(
        getKeypoint(keypoints, name, minScore),
        pointScaleX,
        pointScaleY,
      );
    const nose = scaledKeypoint("nose", 0.05);
    const leftEye = scaledKeypoint("left_eye", 0.04);
    const rightEye = scaledKeypoint("right_eye", 0.04);
    const leftEar = scaledKeypoint("left_ear", 0.04);
    const rightEar = scaledKeypoint("right_ear", 0.04);
    const leftShoulder = scaledKeypoint("left_shoulder", 0.07);
    const rightShoulder = scaledKeypoint("right_shoulder", 0.07);

    let faceCenter = null;
    if (nose) {
      faceCenter = { x: nose.x, y: nose.y };
    } else if (leftEye && rightEye) {
      const eyeCenter = averagePoint(leftEye, rightEye);
      faceCenter = {
        x: eyeCenter.x,
        y: eyeCenter.y + distance(leftEye, rightEye) * 0.65,
      };
    } else if (leftShoulder && rightShoulder) {
      const shoulderCenter = averagePoint(leftShoulder, rightShoulder);
      const shoulderWidth = distance(leftShoulder, rightShoulder);
      faceCenter = {
        x: shoulderCenter.x,
        y: Math.max(height * 0.06, shoulderCenter.y - shoulderWidth * 0.78),
      };
    } else {
      // Fallback terakhir untuk foto full-body yang wajahnya kecil.
      faceCenter = { x: width * 0.5, y: height * 0.14 };
    }

    let faceWidth;
    if (leftEar && rightEar) faceWidth = distance(leftEar, rightEar);
    else if (leftEye && rightEye) faceWidth = distance(leftEye, rightEye) * 2.7;
    else if (leftShoulder && rightShoulder)
      faceWidth = distance(leftShoulder, rightShoulder) * 0.38;
    else faceWidth = width * 0.13;

    faceWidth = clamp(faceWidth, width * 0.07, width * 0.34);
    const faceHeight = faceWidth * 1.15;
    const radius = clamp(faceWidth * 0.11, 4, 30);

    const sampleCenters = [
      {
        x: faceCenter.x - faceWidth * 0.22,
        y: faceCenter.y + faceHeight * 0.04,
      },
      {
        x: faceCenter.x + faceWidth * 0.22,
        y: faceCenter.y + faceHeight * 0.04,
      },
      { x: faceCenter.x, y: faceCenter.y - faceHeight * 0.28 },
      {
        x: faceCenter.x - faceWidth * 0.15,
        y: faceCenter.y + faceHeight * 0.18,
      },
      {
        x: faceCenter.x + faceWidth * 0.15,
        y: faceCenter.y + faceHeight * 0.18,
      },
    ];

    let samples = sampleCenters
      .map((p) => samplePatch(ctx, p.x, p.y, radius, width, height))
      .filter(Boolean);

    // Jika patch kecil gagal, coba area wajah yang sedikit lebih besar.
    if (!samples.length) {
      const broad = samplePatch(
        ctx,
        faceCenter.x,
        faceCenter.y,
        clamp(faceWidth * 0.22, 8, 45),
        width,
        height,
      );
      if (broad) samples = [broad];
    }

    if (!samples.length) {
      throw new Error(
        "Warna kulit belum terbaca dari foto ini. Gunakan foto dengan wajah terlihat dan pencahayaan cukup.",
      );
    }

    let total = 0,
      r = 0,
      g = 0,
      b = 0;
    for (const sample of samples) {
      r += sample.r * sample.count;
      g += sample.g * sample.count;
      b += sample.b * sample.count;
      total += sample.count;
    }

    const average = { r: r / total, g: g / total, b: b / total };
    return {
      tone: classifySkinTone(average),
      rgb: average,
      sampleCount: total,
    };
  }

  function rowBodyWidth(segmentation, y, centerX, maxHalfSpan) {
    const width = segmentation.width;
    const height = segmentation.height;
    const row = clamp(Math.round(y), 0, height - 1);
    const cx = clamp(Math.round(centerX), 0, width - 1);
    const half = Math.max(5, Math.round(maxHalfSpan));
    const leftLimit = Math.max(0, cx - half);
    const rightLimit = Math.min(width - 1, cx + half);

    let bestStart = null;
    let bestEnd = null;
    let currentStart = null;

    for (let x = leftLimit; x <= rightLimit; x++) {
      const isPerson = segmentation.data[row * width + x] === 1;
      if (isPerson && currentStart === null) currentStart = x;
      const isLast = x === rightLimit;
      if ((!isPerson || isLast) && currentStart !== null) {
        const end = isPerson && isLast ? x : x - 1;
        if (currentStart <= cx && end >= cx) {
          bestStart = currentStart;
          bestEnd = end;
          break;
        }
        if (bestStart === null || end - currentStart > bestEnd - bestStart) {
          bestStart = currentStart;
          bestEnd = end;
        }
        currentStart = null;
      }
    }

    if (bestStart === null || bestEnd === null) return 0;
    return bestEnd - bestStart + 1;
  }

  // Untuk zona pinggul, ukur bingkai dari piksel paling kiri sampai paling kanan.
  // Ini tetap stabil ketika kedua paha mulai terpisah oleh celah kecil di tengah.
  function rowFrameWidth(segmentation, y, centerX, maxHalfSpan) {
    const width = segmentation.width;
    const height = segmentation.height;
    const row = clamp(Math.round(y), 0, height - 1);
    const cx = clamp(Math.round(centerX), 0, width - 1);
    const half = Math.max(5, Math.round(maxHalfSpan));
    const leftLimit = Math.max(0, cx - half);
    const rightLimit = Math.min(width - 1, cx + half);

    let first = -1;
    let last = -1;
    for (let x = leftLimit; x <= rightLimit; x++) {
      if (segmentation.data[row * width + x] !== 1) continue;
      if (first < 0) first = x;
      last = x;
    }

    return first >= 0 && last >= first ? last - first + 1 : 0;
  }

  function isTorsoObstructionPart(label) {
    if (typeof label !== "string") return false;
    const normalized = label.toLowerCase();
    return (
      normalized.includes("arm") ||
      normalized.includes("hand") ||
      normalized.includes("face")
    );
  }

  // Upper arm tetap dipakai untuk membentuk frame bahu. Lower arm, tangan,
  // dan wajah dibuang karena dapat melintas di depan/di samping torso.
  function isFrameObstructionPart(label) {
    if (typeof label !== "string") return false;
    const normalized = label.toLowerCase();
    return (
      normalized.includes("lower_arm") ||
      normalized.includes("hand") ||
      normalized.includes("face")
    );
  }

  function percentile(values, p) {
    const clean = values
      .filter((v) => Number.isFinite(v) && v > 0)
      .sort((a, b) => a - b);
    if (!clean.length) return 0;
    if (clean.length === 1) return clean[0];
    const index = (clean.length - 1) * clamp(p, 0, 1);
    const low = Math.floor(index);
    const high = Math.ceil(index);
    if (low === high) return clean[low];
    const weight = index - low;
    return clean[low] * (1 - weight) + clean[high] * weight;
  }

  async function detectBodyShape(image, keypoints, physical) {
    const leftShoulder = getKeypoint(keypoints, "left_shoulder", 0.07);
    const rightShoulder = getKeypoint(keypoints, "right_shoulder", 0.07);
    const leftHip = getKeypoint(keypoints, "left_hip", 0.07);
    const rightHip = getKeypoint(keypoints, "right_hip", 0.07);

    const havePoseTorso = leftShoulder && rightShoulder && leftHip && rightHip;
    let shoulderJointWidth = 0;
    let hipJointWidth = 0;
    let shoulderCenter = null;
    let hipCenter = null;
    let torsoHeight = 0;
    let torsoCenterX = 0;

    if (havePoseTorso) {
      shoulderJointWidth = distance(leftShoulder, rightShoulder);
      hipJointWidth = distance(leftHip, rightHip);
      shoulderCenter = averagePoint(leftShoulder, rightShoulder);
      hipCenter = averagePoint(leftHip, rightHip);
      torsoHeight = hipCenter.y - shoulderCenter.y;
      torsoCenterX = (shoulderCenter.x + hipCenter.x) / 2;
    }

    let upperWidth = 0;
    let upperTorsoWidth = 0;
    let shoulderFrameWidth = 0;
    let waistWidth = 0;
    let lowerWidth = 0;
    let segmentationUsed = false;
    let segmentationMode = "none";
    let upperSamples = [];
    let shoulderFrameSamples = [];
    let waistSamples = [];
    let lowerSamples = [];
    let bodyPartMaskUsed = false;
    let excludedPartPixels = 0;
    let poseCoordinateWidth = 0;
    let poseCoordinateHeight = 0;
    let maskWidth = 0;
    let maskHeight = 0;
    let coordinateScaleX = 0;
    let coordinateScaleY = 0;

    if (state.bodySegmenter) {
      try {
        const people = await state.bodySegmenter.segmentPeople(image, {
          multiSegmentation: false,
          // Body-part mask memungkinkan lengan/tangan dikeluarkan dari ukuran
          // torso. Tanpa ini, pose tangan di samping badan membuat upper torso
          // terlihat terlalu lebar dan hasil mudah terkunci ke inverted triangle.
          segmentBodyParts: true,
          flipHorizontal: false,
          internalResolution: "medium",
          segmentationThreshold: 0.5,
          maxDetections: 1,
          scoreThreshold: 0.25,
          nmsRadius: 20,
        });

        if (people && people.length && people[0].mask) {
          const maskImage = await people[0].mask.toImageData();
          const fullBinary = new Uint8Array(maskImage.width * maskImage.height);
          const frameBinary = new Uint8Array(
            maskImage.width * maskImage.height,
          );
          const torsoBinary = new Uint8Array(
            maskImage.width * maskImage.height,
          );
          let minX = maskImage.width,
            maxX = -1,
            minY = maskImage.height,
            maxY = -1;
          for (let y = 0; y < maskImage.height; y++) {
            for (let x = 0; x < maskImage.width; x++) {
              const p = y * maskImage.width + x;
              const alpha = maskImage.data[p * 4 + 3];
              if (alpha >= 105) {
                fullBinary[p] = 1;

                let partLabel = null;
                if (typeof people[0].maskValueToLabel === "function") {
                  try {
                    partLabel = people[0].maskValueToLabel(
                      maskImage.data[p * 4],
                    );
                  } catch (_) {
                    partLabel = null;
                  }
                }

                if (isTorsoObstructionPart(partLabel)) {
                  excludedPartPixels++;
                } else {
                  torsoBinary[p] = 1;
                }

                if (!isFrameObstructionPart(partLabel)) {
                  frameBinary[p] = 1;
                }

                if (x < minX) minX = x;
                if (x > maxX) maxX = x;
                if (y < minY) minY = y;
                if (y > maxY) maxY = y;
              }
            }
          }

          if (maxX > minX && maxY > minY) {
            bodyPartMaskUsed = excludedPartPixels > 0;
            const torsoSegmentation = {
              width: maskImage.width,
              height: maskImage.height,
              data: bodyPartMaskUsed ? torsoBinary : fullBinary,
            };
            const frameSegmentation = {
              width: maskImage.width,
              height: maskImage.height,
              data: bodyPartMaskUsed ? frameBinary : fullBinary,
            };
            const poseSize = getPoseCoordinateSize(image);
            poseCoordinateWidth = poseSize.width;
            poseCoordinateHeight = poseSize.height;
            maskWidth = maskImage.width;
            maskHeight = maskImage.height;
            coordinateScaleX = maskImage.width / poseSize.width;
            coordinateScaleY = maskImage.height / poseSize.height;
            const scaleX = coordinateScaleX;
            const scaleY = coordinateScaleY;

            let cx, upperYs, upperTorsoYs, waistYs, lowerYs, span;

            if (
              havePoseTorso &&
              torsoHeight > 8 &&
              shoulderJointWidth > 4 &&
              hipJointWidth > 4
            ) {
              cx = torsoCenterX * scaleX;
              const shoulderY = shoulderCenter.y * scaleY;
              const torsoH = torsoHeight * scaleY;

              // Lebar siluet dibatasi di sekitar torso agar tangan/lengan yang menempel
              // di sisi badan tidak terlalu memperbesar area bahu.
              span =
                Math.max(shoulderJointWidth, hipJointWidth) * scaleX * 1.25;

              // Upper mengambil frame bahu (upper arm tetap dipertahankan).
              // Waist mencari lekukan antara rusuk bawah dan high hip.
              // Lower bergerak melewati landmark hip sampai full-hip/upper thigh;
              // landmark hip MoveNet sering berada terlalu tinggi untuk siluet rok.
              upperYs = [0, 0.04, 0.08, 0.12, 0.16, 0.2].map(
                (f) => shoulderY + torsoH * f,
              );
              upperTorsoYs = [0.18, 0.24, 0.3, 0.36, 0.42].map(
                (f) => shoulderY + torsoH * f,
              );
              waistYs = [0.48, 0.56, 0.64, 0.72, 0.8].map(
                (f) => shoulderY + torsoH * f,
              );
              lowerYs = [1.02, 1.08, 1.14, 1.2, 1.26, 1.32].map(
                (f) => shoulderY + torsoH * f,
              );
              segmentationMode = bodyPartMaskUsed
                ? "segmentation+pose+parts-v10"
                : "segmentation+pose-v10";
            } else {
              // Fallback murni dari siluet jika confidence bahu/pinggul rendah.
              const bodyH = maxY - minY + 1;
              cx = (minX + maxX) / 2;
              span = (maxX - minX + 1) * 0.62;
              upperYs = [0.18, 0.2, 0.22, 0.24, 0.26].map(
                (f) => minY + bodyH * f,
              );
              upperTorsoYs = [0.22, 0.25, 0.28, 0.31, 0.34].map(
                (f) => minY + bodyH * f,
              );
              waistYs = [0.3, 0.33, 0.36, 0.39, 0.42].map(
                (f) => minY + bodyH * f,
              );
              lowerYs = [0.43, 0.46, 0.49, 0.52, 0.55].map(
                (f) => minY + bodyH * f,
              );
              segmentationMode = bodyPartMaskUsed
                ? "segmentation-only+parts-v10"
                : "segmentation-only-v10";
            }

            const sampleRows = (segmentation, rows, measure = rowBodyWidth) =>
              rows
                .map((y) => measure(segmentation, y, cx, span) / scaleX)
                .filter((v) => Number.isFinite(v) && v > 5);

            shoulderFrameSamples = sampleRows(
              frameSegmentation,
              upperYs,
              rowFrameWidth,
            );
            upperSamples = sampleRows(torsoSegmentation, upperTorsoYs);
            waistSamples = sampleRows(torsoSegmentation, waistYs);
            lowerSamples = sampleRows(
              frameSegmentation,
              lowerYs,
              rowFrameWidth,
            );

            // Lebar torso atas menjadi dasar. Sebagian kecil lebar frame bahu
            // ditambahkan agar bahu tidak hilang saat upper-arm dikeluarkan, tetapi
            // dibatasi agar pose lengan tidak mendominasi hasil.
            upperTorsoWidth = percentile(upperSamples, 0.4);
            shoulderFrameWidth = percentile(shoulderFrameSamples, 0.5);
            const blendedUpperWidth =
              upperTorsoWidth +
              Math.max(0, shoulderFrameWidth - upperTorsoWidth) * 0.28;
            upperWidth = clamp(
              blendedUpperWidth,
              upperTorsoWidth,
              upperTorsoWidth * 1.25,
            );
            waistWidth = percentile(waistSamples, 0.25);
            lowerWidth = percentile(lowerSamples, 0.7);
            segmentationUsed =
              upperSamples.length >= 3 &&
              waistSamples.length >= 3 &&
              lowerSamples.length >= 3 &&
              upperWidth > 5 &&
              waistWidth > 5 &&
              lowerWidth > 5;
          }
        }
      } catch (err) {
        console.warn(
          "Body Segmentation gagal, mencoba landmark MoveNet sebagai fallback.",
          err,
        );
      }
    }

    // Jika segmentasi tidak tersedia, kalibrasi lebar sendi pinggul sebelum
    // membandingkannya dengan bahu. Tanpa kalibrasi, hampir semua foto memiliki
    // rasio >= 1.18 hanya karena hip MoveNet berada di pusat sendi.
    if (!segmentationUsed) {
      if (!havePoseTorso) {
        throw new Error(
          "Proporsi tubuh belum terbaca. Gunakan foto satu orang dengan bahu sampai pinggul terlihat jelas.",
        );
      }
      if (shoulderJointWidth < 5 || hipJointWidth < 5 || torsoHeight <= 8) {
        throw new Error("Proporsi tubuh tidak dapat dihitung dari foto ini.");
      }
      upperWidth = shoulderJointWidth;
      lowerWidth = hipJointWidth * POSE_HIP_WIDTH_CALIBRATION;
      waistWidth = Math.min(upperWidth, lowerWidth) * 0.92;
      segmentationMode = "pose-fallback-calibrated-v10";
    }

    const upperLowerRatio = upperWidth / lowerWidth;
    const averageFrame = (upperWidth + lowerWidth) / 2;
    const waistToFrame = waistWidth / averageFrame;
    const frameDifference =
      Math.abs(upperWidth - lowerWidth) / Math.max(upperWidth, lowerWidth);
    const bmi = physical.bmi;

    const { shape, reason } = classifyBodyShape({
      upperLowerRatio,
      waistToFrame,
      frameDifference,
    });

    console.table({
      source: segmentationMode,
      shape,

      // Tambahan data diagnostik
      shoulderJointWidth: Number(shoulderJointWidth.toFixed(2)),
      hipJointWidth: Number(hipJointWidth.toFixed(2)),
      poseJointRatio: Number((shoulderJointWidth / hipJointWidth).toFixed(3)),
      calibratedPoseRatio: Number(
        (
          shoulderJointWidth /
          (hipJointWidth * POSE_HIP_WIDTH_CALIBRATION)
        ).toFixed(3),
      ),

      // Kode lama tetap dipertahankan
      upperTorsoWidth: Number(upperTorsoWidth.toFixed(2)),
      shoulderFrameWidth: Number(shoulderFrameWidth.toFixed(2)),
      upperWidth: Number(upperWidth.toFixed(2)),
      waistWidth: Number(waistWidth.toFixed(2)),
      lowerWidth: Number(lowerWidth.toFixed(2)),
      upperLowerRatio: Number(upperLowerRatio.toFixed(3)),
      waistToFrame: Number(waistToFrame.toFixed(3)),
      frameDifference: Number(frameDifference.toFixed(3)),
      bodyPartMaskUsed,
      poseCoordinateWidth,
      poseCoordinateHeight,
      maskWidth,
      maskHeight,
      coordinateScaleX: Number(coordinateScaleX.toFixed(3)),
      coordinateScaleY: Number(coordinateScaleY.toFixed(3)),
    });

    return {
      shape,
      metrics: {
        source: segmentationMode,
        shoulderJointWidth,
        hipJointWidth,
        upperTorsoWidth,
        shoulderFrameWidth,
        upperWidth,
        waistWidth,
        lowerWidth,
        upperLowerRatio,
        waistToFrame,
        frameDifference,
        bmi,
        reason,
        bodyPartMaskUsed,
        excludedPartPixels,
        poseCoordinateWidth,
        poseCoordinateHeight,
        maskWidth,
        maskHeight,
        coordinateScaleX,
        coordinateScaleY,
        thresholds: BODY_SHAPE_THRESHOLDS,
        samples: {
          upper: upperSamples.map((v) => Number(v.toFixed(1))),
          shoulderFrame: shoulderFrameSamples.map((v) => Number(v.toFixed(1))),
          waist: waistSamples.map((v) => Number(v.toFixed(1))),
          lower: lowerSamples.map((v) => Number(v.toFixed(1))),
        },
      },
    };
  }

  analyzeBtn.addEventListener("click", async () => {
    const file = fileInput.files[0];
    if (!file || !state.image) {
      alert("Silakan pilih foto terlebih dahulu.");
      return;
    }

    let physical;
    try {
      physical = validatePhysicalData();
    } catch (err) {
      alert(err.message);
      return;
    }

    const overlay = document.getElementById("scanOverlay");
    setBusy(true);
    preview.classList.add("glow-active");
    if (overlay) overlay.style.display = "block";

    try {
      await ensureModels();
      setStatus("Mendeteksi titik tubuh...", "info");
      const keypoints = await detectPose(state.image);

      setStatus("Menganalisis warna kulit...", "info");
      const skin = detectSkinTone(state.image, keypoints);

      setStatus("Menghitung proporsi bentuk tubuh...", "info");
      let photoBody = null;
      try {
        photoBody = await detectBodyShape(state.image, keypoints, physical);
      } catch (err) {
        if (!physical.manualMeasurements) throw err;
        console.warn(
          "Analisis proporsi dari foto gagal; menggunakan ukuran manual.",
          err,
        );
      }
      const body = physical.manualMeasurements
        ? classifyManualMeasurements(physical.manualMeasurements, physical.bmi)
        : photoBody;

      console.log("Hasil skin tone:", skin);
      if (physical.manualMeasurements && photoBody) {
        console.log("Hasil body shape dari foto:", photoBody);
      }
      console.log("Hasil body shape:", body);

      const formData = new FormData();
      formData.append("photo", file);
      formData.append("height_cm", physical.height.toFixed(1));
      formData.append("weight_kg", physical.weight.toFixed(1));
      formData.append("skin_tone", skin.tone);
      formData.append("body_shape", body.shape);
      formData.append("skin_tone_label", skin.tone);
      formData.append("body_shape_label", body.shape);
      formData.append("dataset_type", "testing");

      setStatus(
        `Hasil: ${skin.tone} • ${body.shape}. Menyimpan data...`,
        "success",
      );

      const response = await fetch("save_result.php", {
        method: "POST",
        body: formData,
      });

      let data;
      try {
        data = await response.json();
      } catch (_) {
        throw new Error("Server tidak mengembalikan respons JSON yang valid.");
      }

      if (!response.ok || !data.success) {
        throw new Error(data.message || "Gagal menyimpan hasil analisis.");
      }

      window.location.href = "result.php?slug=" + encodeURIComponent(data.slug);
    } catch (err) {
      console.error(err);
      setStatus(err.message || "Analisis gagal.", "error");
      alert(err.message || "Terjadi kesalahan saat analisis.");
    } finally {
      if (overlay) overlay.style.display = "none";
      preview.classList.remove("glow-active");
      setBusy(false);
    }
  });
})();
