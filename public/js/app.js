/**
 * app.js
 * Core engine powering the 2-Minute Early Generation logic,
 * live countdown, UI rendering, and interactive test simulation.
 */

(function () {
  const {
    OFFICIAL_SLOTS,
    SLOT_KEYS,
    parseSlotToDate,
    formatFullDate,
    formatDashDate,
    formatTimeAMPM,
    generateNumbersForSlot
  } = window.LotteryData;

  // State Management
  let clockOffsetMs = 0; // offset from actual Date.now() for simulation
  let speedMultiplier = 1; // 1 = real time
  let lastRealTick = Date.now();
  let virtualNow = new Date();
  let lastGeneratedSlot = null;

  // DOM Elements
  const elCurrentSimTime = document.getElementById("currentSimTime");
  const elLastUpdatedTime = document.getElementById("lastUpdatedTime");
  const elHeroDate = document.getElementById("heroDate");
  const elHeroTime = document.getElementById("heroTime");
  const elNextSlotTime = document.getElementById("nextSlotTime");
  const elCountdownTimer = document.getElementById("countdownTimer");
  const elProgressCircle = document.getElementById("progressCircle");
  const elEarlyBadgeText = document.getElementById("earlyBadgeText");
  const elHistoryTableBody = document.getElementById("historyTableBody");
  const elStartDateInput = document.getElementById("startDateInput");
  const elEndDateInput = document.getElementById("endDateInput");
  const elSearchBtn = document.getElementById("searchBtn");
  const elToast = document.getElementById("liveToast");
  const elToastMsg = document.getElementById("toastMsg");

  // ---------------- AI Voice Announcer Engine ----------------
  const voiceAnnouncer = {
    isEnabled: false,
    lang: "en-IN",
    synth: window.speechSynthesis,

    init() {
      const saved = localStorage.getItem("vbl_voice_enabled");
      if (saved === "true") {
        this.isEnabled = true;
      }
      const savedLang = localStorage.getItem("vbl_voice_lang");
      if (savedLang) {
        this.lang = savedLang;
        const langSel = document.getElementById("voiceLangSelect");
        if (langSel) langSel.value = savedLang;
      }
      this.updateUI();
    },

    toggle() {
      this.isEnabled = !this.isEnabled;
      localStorage.setItem("vbl_voice_enabled", this.isEnabled ? "true" : "false");
      this.updateUI();

      if (this.isEnabled) {
        showToast("🔊 AI Voice Announcements Enabled!");
        this.speak("AI Voice announcement is now active.", true);
      } else {
        this.stop();
        showToast("🔇 AI Voice Announcements Muted.");
      }
    },

    setLanguage(lang) {
      this.lang = lang;
      localStorage.setItem("vbl_voice_lang", lang);
      showToast(`Voice language set to ${lang === 'hi-IN' ? 'हिन्दी (Hindi)' : 'English (India)'}`);
    },

    updateUI() {
      const btn = document.getElementById("voiceToggleBtn");
      const label = document.getElementById("voiceToggleLabel");
      const icon = document.getElementById("voiceIcon");

      if (btn && label && icon) {
        if (this.isEnabled) {
          btn.classList.add("active");
          label.textContent = "AI Voice: ON";
          icon.className = "fa-solid fa-volume-high";
        } else {
          btn.classList.remove("active");
          label.textContent = "AI Voice: OFF";
          icon.className = "fa-solid fa-volume-xmark";
        }
      }
    },

    setSpeakingBanner(isSpeaking, text = "") {
      const banner = document.getElementById("speakingBanner");
      const txtEl = document.getElementById("speakingText");
      if (banner) {
        if (isSpeaking) {
          banner.classList.add("active");
          if (txtEl) txtEl.textContent = text || "Announcing Results 3x...";
        } else {
          banner.classList.remove("active");
        }
      }
    },

    highlightSlotBox(key) {
      this.clearHighlights();
      const el = document.getElementById(`slot-${key}`);
      if (el) {
        el.classList.add("speaking-active-box");
      }
    },

    highlightTime() {
      this.clearHighlights();
      const pill = document.getElementById("heroTimePill");
      if (pill) {
        pill.classList.add("speaking-active-time");
      }
    },

    clearHighlights() {
      SLOT_KEYS.forEach((k) => {
        document.getElementById(`slot-${k}`)?.classList.remove("speaking-active-box");
      });
      document.getElementById("heroTimePill")?.classList.remove("speaking-active-time");
    },

    stop() {
      if (this.synth) {
        this.synth.cancel();
      }
      this.clearHighlights();
      this.setSpeakingBanner(false);
    },

    speak(text, priority = false) {
      if (!this.synth) return;
      if (priority) this.stop();

      const utterance = new SpeechSynthesisUtterance(text);
      utterance.lang = this.lang;
      utterance.rate = 0.92;
      utterance.pitch = 1.0;

      const voices = this.synth.getVoices();
      const voice = voices.find(v => v.lang === this.lang || v.lang.startsWith(this.lang.substring(0, 2)));
      if (voice) utterance.voice = voice;

      utterance.onstart = () => {
        this.setSpeakingBanner(true, text);
      };

      utterance.onend = () => {
        if (!this.synth.speaking) {
          this.setSpeakingBanner(false);
        }
      };

      utterance.onerror = () => {
        this.setSpeakingBanner(false);
      };

      this.synth.speak(utterance);
    },

    speakUtterance(text, onStartCb, onEndCb) {
      if (!this.synth) return;

      const utterance = new SpeechSynthesisUtterance(text);
      utterance.lang = this.lang;
      utterance.rate = 0.90;
      utterance.pitch = 1.0;

      const voices = this.synth.getVoices();
      const voice = voices.find(v => v.lang === this.lang || v.lang.startsWith(this.lang.substring(0, 2)));
      if (voice) utterance.voice = voice;

      utterance.onstart = () => {
        if (onStartCb) onStartCb();
      };

      utterance.onend = () => {
        if (onEndCb) onEndCb();
        if (!this.synth.speaking) {
          this.setSpeakingBanner(false);
          this.clearHighlights();
        }
      };

      utterance.onerror = () => {
        if (onEndCb) onEndCb();
        this.setSpeakingBanner(false);
        this.clearHighlights();
      };

      this.synth.speak(utterance);
    },

    // Repeats slot time 3 times, then each number 3 times with live visual highlight!
    announceResults3x(slotTime, numbers) {
      if (!this.isEnabled || !this.synth) return;

      this.stop(); // Clear any ongoing speech & highlights

      const isHindi = this.lang === "hi-IN";

      // 1. Announce slot time 3 times and highlight hero time
      let introText = "";
      if (isHindi) {
        introText = `ध्यान दीजिए! लॉटरी। टाइम स्लॉट ${slotTime}। ${slotTime}। ${slotTime}।`;
      } else {
        introText = `Attention please! Lottery. Time slot ${slotTime}... ${slotTime}... ${slotTime}.`;
      }
      
      this.speakUtterance(introText, () => {
        this.highlightTime();
        this.setSpeakingBanner(true, `Time Slot: ${slotTime} (3x)`);
      }, () => {
        this.clearHighlights();
      });

      // 2. Announce each of 10 slots 3 times each with active slot box highlight
      SLOT_KEYS.forEach((key) => {
        const val = numbers[key] || "00";
        let itemSpeech = "";
        if (isHindi) {
          itemSpeech = `${key}, ${val} ... ${val} ... ${val}।`;
        } else {
          itemSpeech = `${key}, ${val} ... ${val} ... ${val}.`;
        }
        
        this.speakUtterance(itemSpeech, () => {
          this.highlightSlotBox(key);
          this.setSpeakingBanner(true, `Speaking: ${key} = ${val} (3x)`);
        }, () => {
          this.clearHighlights();
        });
      });
    },

    testVoice() {
      if (!this.isEnabled) {
        this.isEnabled = true;
        localStorage.setItem("vbl_voice_enabled", "true");
        this.updateUI();
      }

      const { currentPublishedSlot } = calculateSlotStatus(getCurrentTime());
      const dateDash = formatDashDate(currentPublishedSlot.slotDate);
      const numbers = generateNumbersForSlot(dateDash, currentPublishedSlot.slotStr);

      showToast(`🔊 Playing 3x repetition announcement for slot ${currentPublishedSlot.slotStr}!`);
      this.announceResults3x(currentPublishedSlot.slotStr, numbers);
    }
  };

  window.voiceAnnouncer = voiceAnnouncer;

  // SVG Circle geometry
  const CIRCLE_RADIUS = 32;
  const CIRCLE_CIRCUMFERENCE = 2 * Math.PI * CIRCLE_RADIUS;

  if (elProgressCircle) {
    elProgressCircle.style.strokeDasharray = `${CIRCLE_CIRCUMFERENCE} ${CIRCLE_CIRCUMFERENCE}`;
    elProgressCircle.style.strokeDashoffset = "0";
  }

  /**
   * Returns the current active virtual Date (supports simulation).
   */
  function getCurrentTime() {
    return virtualNow;
  }

  /**
   * Finds:
   * 1. The latest slot that has ALREADY been generated (i.e. time >= slotTime - 2 mins)
   * 2. The next slot waiting to be generated
   */
  function calculateSlotStatus(now) {
    const today = new Date(now);
    const slotsToday = OFFICIAL_SLOTS.map((timeStr) => {
      const slotDate = parseSlotToDate(timeStr, today);
      const genDate = new Date(slotDate.getTime() - 2 * 60 * 1000); // 2 minutes before slot!
      return {
        slotStr: timeStr,
        slotDate,
        genDate,
        isGenerated: now.getTime() >= genDate.getTime()
      };
    });

    // Find latest generated slot
    const generatedSlots = slotsToday.filter((s) => s.isGenerated);
    const upcomingSlots = slotsToday.filter((s) => !s.isGenerated);

    let currentPublishedSlot = null;
    let nextUpcomingSlot = null;
    let prevGenTime = null;

    if (generatedSlots.length > 0) {
      currentPublishedSlot = generatedSlots[generatedSlots.length - 1];
    } else {
      // Before 08:58 AM (before first slot generation)
      // Show yesterday's final slot or morning first pending
      currentPublishedSlot = {
        slotStr: OFFICIAL_SLOTS[OFFICIAL_SLOTS.length - 1],
        slotDate: parseSlotToDate(OFFICIAL_SLOTS[OFFICIAL_SLOTS.length - 1], new Date(today.getTime() - 86400000)),
        genDate: new Date(parseSlotToDate(OFFICIAL_SLOTS[OFFICIAL_SLOTS.length - 1], new Date(today.getTime() - 86400000)).getTime() - 2 * 60 * 1000),
        isGenerated: true
      };
    }

    if (upcomingSlots.length > 0) {
      nextUpcomingSlot = upcomingSlots[0];
      const idx = slotsToday.indexOf(nextUpcomingSlot);
      prevGenTime = idx > 0 ? slotsToday[idx - 1].genDate : new Date(nextUpcomingSlot.genDate.getTime() - 20 * 60 * 1000);
    } else {
      // After all slots today: target tomorrow's 09:00 AM slot
      const tomorrow = new Date(today.getTime() + 86400000);
      const tomFirstSlotDate = parseSlotToDate(OFFICIAL_SLOTS[0], tomorrow);
      nextUpcomingSlot = {
        slotStr: OFFICIAL_SLOTS[0],
        slotDate: tomFirstSlotDate,
        genDate: new Date(tomFirstSlotDate.getTime() - 2 * 60 * 1000),
        isGenerated: false
      };
      prevGenTime = slotsToday[slotsToday.length - 1].genDate;
    }

    return {
      currentPublishedSlot,
      nextUpcomingSlot,
      prevGenTime,
      allSlotsToday: slotsToday
    };
  }

  /**
   * Shows a stylish floating toast notification
   */
  function showToast(message) {
    if (!elToast || !elToastMsg) return;
    elToastMsg.textContent = message;
    elToast.classList.add("show");
    setTimeout(() => {
      elToast.classList.remove("show");
    }, 4500);
  }

  /**
   * Updates the 10 slot boxes on the hero card
   */
  function renderHeroSlots(slotInfo, animate = false) {
    if (!slotInfo) return;
    const dateStr = formatDashDate(slotInfo.slotDate);
    const numbers = generateNumbersForSlot(dateStr, slotInfo.slotStr);

    elHeroDate.textContent = formatFullDate(slotInfo.slotDate);
    elHeroTime.textContent = slotInfo.slotStr;
    elLastUpdatedTime.textContent = slotInfo.slotStr;

    SLOT_KEYS.forEach((key) => {
      const box = document.getElementById(`slot-${key}`);
      const valElem = document.getElementById(`val-${key}`);
      if (valElem) {
        valElem.textContent = numbers[key] || "--";
      }
      if (animate && box) {
        box.classList.remove("newly-generated");
        void box.offsetWidth; // trigger reflow
        box.classList.add("newly-generated");
      }
    });
  }

  /**
   * Renders the Results History table
   */
  function renderHistoryTable(allSlotsToday, filterDate = null) {
    if (!elHistoryTableBody) return;
    const now = getCurrentTime();
    const todayStr = formatDashDate(now);
    const displayDateStr = filterDate || formatFullDate(now);

    let html = "";

    // Chronological order: starts at 09:00 AM morning downwards to current time and upcoming slots
    const sortedSlots = [...allSlotsToday].sort((a, b) => a.slotDate.getTime() - b.slotDate.getTime());

    sortedSlots.forEach((slot) => {
      const dateDash = formatDashDate(slot.slotDate);
      const isGen = now.getTime() >= slot.genDate.getTime();
      const isPastSlotTime = now.getTime() >= slot.slotDate.getTime();

      let statusBadge = "";
      let numbers = {};

      if (isGen) {
        numbers = generateNumbersForSlot(dateDash, slot.slotStr);
        if (!isPastSlotTime) {
          // It was generated 2 min before slot time, and the slot time itself hasn't arrived yet!
          statusBadge = `<span class="badge-status generated-early" title="Auto-generated 2 min prior to slot time"><span class="status-dot"></span> Published</span>`;
        } else {
          statusBadge = `<span class="badge-status published"><span class="status-dot"></span> Published</span>`;
        }
      } else {
        statusBadge = `<span class="badge-status pending"><span class="status-dot"></span> Pending</span>`;
      }

      html += `
        <tr>
          <td class="td-date">${displayDateStr}</td>
          <td class="td-time">${slot.slotStr}</td>
          ${SLOT_KEYS.map((k) => `<td class="td-slot-val">${isGen ? numbers[k] : "-"}</td>`).join("")}
          <td>${statusBadge}</td>
        </tr>
      `;
    });

    elHistoryTableBody.innerHTML = html;
  }

  /**
   * Main Engine Tick (Runs every ~100ms or 1000ms)
   */
  function tick() {
    const realNow = Date.now();
    const delta = realNow - lastRealTick;
    lastRealTick = realNow;

    // advance virtual clock according to speed multiplier
    clockOffsetMs += delta * (speedMultiplier - 1);
    virtualNow = new Date(realNow + clockOffsetMs);

    const now = virtualNow;

    // Update demo bar display
    if (elCurrentSimTime) {
      elCurrentSimTime.textContent = formatTimeAMPM(now) + ` : ${String(now.getSeconds()).padStart(2, "0")}s`;
    }

    // Determine current and next slot
    const { currentPublishedSlot, nextUpcomingSlot, prevGenTime, allSlotsToday } = calculateSlotStatus(now);

    // Check if a new generation just happened
    if (currentPublishedSlot) {
      const slotId = `${formatDashDate(currentPublishedSlot.slotDate)}_${currentPublishedSlot.slotStr}`;
      if (lastGeneratedSlot !== slotId) {
        const isFirstLoad = lastGeneratedSlot === null;
        lastGeneratedSlot = slotId;

        renderHeroSlots(currentPublishedSlot, !isFirstLoad);
        renderHistoryTable(allSlotsToday);

        if (!isFirstLoad) {
          showToast(`⚡ Numbers for ${currentPublishedSlot.slotStr} generated early (2 min prior)!`);
          
          // Auto announce new results 3 times if voice is enabled!
          const dateDash = formatDashDate(currentPublishedSlot.slotDate);
          const numbers = generateNumbersForSlot(dateDash, currentPublishedSlot.slotStr);
          voiceAnnouncer.announceResults3x(currentPublishedSlot.slotStr, numbers);
        }
      }
    }

    // Update Next Slot and Countdown Timer
    if (nextUpcomingSlot) {
      elNextSlotTime.textContent = nextUpcomingSlot.slotStr;

      // Time remaining until GENERATION TIME (which is 2 mins before slot)
      const msUntilGen = nextUpcomingSlot.genDate.getTime() - now.getTime();

      if (msUntilGen > 0) {
        const totalSec = Math.floor(msUntilGen / 1000);
        const mm = String(Math.floor(totalSec / 60)).padStart(2, "0");
        const ss = String(totalSec % 60).padStart(2, "0");
        elCountdownTimer.textContent = `${mm}:${ss}`;

        // Circular progress
        const totalSpan = nextUpcomingSlot.genDate.getTime() - prevGenTime.getTime();
        const elapsed = now.getTime() - prevGenTime.getTime();
        const pct = Math.min(Math.max(elapsed / totalSpan, 0), 1);
        const strokeOffset = CIRCLE_CIRCUMFERENCE - pct * CIRCLE_CIRCUMFERENCE;
        if (elProgressCircle) {
          elProgressCircle.style.strokeDashoffset = strokeOffset;
        }

        const genTimeStr = formatTimeAMPM(nextUpcomingSlot.genDate);
        if (elEarlyBadgeText) {
          elEarlyBadgeText.textContent = `Generates 2 min early at ${genTimeStr}`;
        }
      } else {
        elCountdownTimer.textContent = "00:00";
        if (elProgressCircle) {
          elProgressCircle.style.strokeDashoffset = "0";
        }
      }
    }
  }

  // Event Listeners for Demo Simulation Controls
  window.demoActions = {
    jumpTo10sBeforeNextGen: function () {
      const now = getCurrentTime();
      const { nextUpcomingSlot } = calculateSlotStatus(now);
      if (nextUpcomingSlot) {
        const targetTime = nextUpcomingSlot.genDate.getTime() - 10000; // 10s before generation!
        clockOffsetMs = targetTime - Date.now();
        lastRealTick = Date.now();
        showToast("⏳ Jumped to 10 seconds before next generation!");
      }
    },
    instantGenerateNext: function () {
      const now = getCurrentTime();
      const { nextUpcomingSlot } = calculateSlotStatus(now);
      if (nextUpcomingSlot) {
        const targetTime = nextUpcomingSlot.genDate.getTime() + 1000; // 1s past generation!
        clockOffsetMs = targetTime - Date.now();
        lastRealTick = Date.now();
      }
    },
    resetToRealTime: function () {
      clockOffsetMs = 0;
      speedMultiplier = 1;
      lastRealTick = Date.now();
      document.getElementById("btnSpeed1x")?.classList.add("active");
      document.getElementById("btnSpeed10x")?.classList.remove("active");
      showToast("🕒 Reset clock to actual Indian Standard Time (IST)!");
    },
    setSpeed: function (mult) {
      speedMultiplier = mult;
      lastRealTick = Date.now();
      showToast(`⚡ Simulation speed set to ${mult}x`);
    }
  };

  // Search History handler
  if (elSearchBtn) {
    elSearchBtn.addEventListener("click", function (e) {
      e.preventDefault();
      const startVal = elStartDateInput.value;
      if (!startVal) {
        showToast("Please pick a date to view history.");
        return;
      }
      const selectedDate = new Date(startVal + "T00:00:00");
      const { allSlotsToday } = calculateSlotStatus(getCurrentTime());
      renderHistoryTable(allSlotsToday, formatFullDate(selectedDate));
      showToast(`Showing results for ${formatFullDate(selectedDate)}`);
    });
  }

  // Initialize date picker inputs with current date
  const now = new Date();
  const yyyy = now.getFullYear();
  const mm = String(now.getMonth() + 1).padStart(2, "0");
  const dd = String(now.getDate()).padStart(2, "0");
  const dateInputValue = `${yyyy}-${mm}-${dd}`;

  if (elStartDateInput) elStartDateInput.value = dateInputValue;
  if (elEndDateInput) elEndDateInput.value = dateInputValue;

  // Initialize Voice Announcer
  voiceAnnouncer.init();

  // Run immediately and set 250ms interval for smooth timers
  tick();
  setInterval(tick, 250);
})();
