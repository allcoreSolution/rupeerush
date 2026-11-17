/**
 * mockData.js
 * Manages official slot schedules (matching playbhaglaxmi.com)
 * and generates historical/current slot values.
 */

// Official slot timings from playbhaglaxmi.com
const OFFICIAL_SLOTS = [
  // 15-minute intervals (Morning)
  "09:00 AM", "09:15 AM", "09:30 AM", "09:45 AM",
  "10:00 AM", "10:15 AM", "10:30 AM", "10:45 AM", "11:00 AM",
  // 20-minute intervals (Day & Evening)
  "11:20 AM", "11:40 AM", "12:00 PM", "12:20 PM", "12:40 PM",
  "01:00 PM", "01:20 PM", "01:40 PM", "02:00 PM", "02:20 PM", "02:40 PM",
  "03:00 PM", "03:20 PM", "03:40 PM", "04:00 PM", "04:20 PM", "04:40 PM",
  "05:00 PM", "05:20 PM", "05:40 PM", "06:00 PM", "06:20 PM", "06:40 PM",
  "07:00 PM", "07:20 PM", "07:40 PM", "08:00 PM", "08:20 PM", "08:40 PM",
  "09:00 PM", "09:20 PM", "09:40 PM"
];

const SLOT_KEYS = ["XA", "XB", "XC", "XD", "XE", "XF", "XG", "XH", "XI", "XJ"];

/**
 * Parses time string like "04:40 PM" on a given Date object into full Date.
 */
function parseSlotToDate(timeStr, baseDate = new Date()) {
  const parts = timeStr.match(/(\d+):(\d+)\s*(AM|PM)/i);
  if (!parts) return null;
  let hours = parseInt(parts[1], 10);
  const minutes = parseInt(parts[2], 10);
  const ampm = parts[3].toUpperCase();

  if (ampm === "PM" && hours < 12) hours += 12;
  if (ampm === "AM" && hours === 12) hours = 0;

  const d = new Date(baseDate);
  d.setHours(hours, minutes, 0, 0);
  return d;
}

/**
 * Formats date into "07 September 2026"
 */
function formatFullDate(date) {
  const months = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];
  const day = String(date.getDate()).padStart(2, "0");
  const month = months[date.getMonth()];
  const year = date.getFullYear();
  return `${day} ${month} ${year}`;
}

/**
 * Formats date into "07-09-2026" (playbhaglaxmi format)
 */
function formatDashDate(date) {
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  return `${day}-${month}-${year}`;
}

/**
 * Formats Date into "04:20 PM"
 */
function formatTimeAMPM(date) {
  let hours = date.getHours();
  const minutes = String(date.getMinutes()).padStart(2, "0");
  const ampm = hours >= 12 ? "PM" : "AM";
  hours = hours % 12;
  hours = hours ? hours : 12; // 0 is 12
  const hoursStr = String(hours).padStart(2, "0");
  return `${hoursStr}:${minutes} ${ampm}`;
}

/**
 * Pseudo-random deterministic number generation based on date, slot, and key
 * ensures consistent numbers for the same slot, but generates 2-digit strings (00 - 99)
 */
function generateNumbersForSlot(dateStr, slotTime) {
  const seedStr = `${dateStr}_${slotTime}`;
  let hash = 0;
  for (let i = 0; i < seedStr.length; i++) {
    hash = (hash << 5) - hash + seedStr.charCodeAt(i);
    hash |= 0;
  }

  const values = {};
  SLOT_KEYS.forEach((key, idx) => {
    // Produce 0 to 99
    const val = Math.abs(Math.sin(hash + idx * 97) * 100);
    const num = Math.floor(val) % 100;
    values[key] = String(num).padStart(2, "0");
  });

  return values;
}

window.LotteryData = {
  OFFICIAL_SLOTS,
  SLOT_KEYS,
  parseSlotToDate,
  formatFullDate,
  formatDashDate,
  formatTimeAMPM,
  generateNumbersForSlot
};
