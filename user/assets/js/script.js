const lihatBtns = document.querySelectorAll(".lihat-jadwal");
const jadwalPopup = document.getElementById("jadwalPopup");
const closePopup = document.getElementById("closePopup");
const timeGrid = document.getElementById("timeGrid");
const datePicker = document.getElementById("datePicker");
const lanjutBtn = document.getElementById("lanjutBtn");

const loginPopup = document.getElementById("loginAlert");
const closeAlert = document.getElementById("closeAlert");

const isLoggedIn = document.body.dataset.loggedin === "true";

let selectedLapangan = null;
let selectedTimes = [];

// ===== POPUP LOGIN HANDLER =====
closeAlert.addEventListener("click", () => {
  loginPopup.style.display = "none";
});

window.addEventListener("click", (e) => {
  if (e.target === loginPopup) {
    loginPopup.style.display = "none";
  }
});

// ===== DATA WAKTU =====
const times = [];
for (let i = 8; i <= 23; i++) {
  const next = i + 1;
  const jam = (i < 10 ? "0" + i : i) + ":00-" + (next < 10 ? "0" + next : next) + ":00";
  times.push(jam);
}

// ===== POPUP JADWAL LAPANGAN =====
lihatBtns.forEach(btn => {
  btn.addEventListener("click", () => {
    selectedLapangan = btn.dataset.lapangan;
    jadwalPopup.style.display = "flex";
    const today = new Date().toISOString().split("T")[0];
    datePicker.value = today;
    loadJadwal(today);
  });
});

closePopup.onclick = () => jadwalPopup.style.display = "none";

window.addEventListener("click", (e) => {
  if (e.target === jadwalPopup) {
    jadwalPopup.style.display = "none";
  }
});

function loadJadwal(date) {
  timeGrid.innerHTML = "Memuat jadwal...";
  fetch(`../includes/get_jadwal.php?lapangan=${selectedLapangan}&tanggal=${date}`)
    .then(res => res.json())
    .then(booked => {
      timeGrid.innerHTML = "";
      selectedTimes = [];
      times.forEach(t => {
        const jamMulai = t.split("-")[0];
        const div = document.createElement("div");
        div.classList.add("time-slot");
        div.textContent = t;
        if (booked.includes(jamMulai)) {
          div.classList.add("booked");
        } else {
          div.addEventListener("click", () => {
            div.classList.toggle("selected");
            if (div.classList.contains("selected")) selectedTimes.push(t);
            else selectedTimes = selectedTimes.filter(x => x !== t);
          });
        }
        timeGrid.appendChild(div);
      });
    })
    .catch(() => timeGrid.innerHTML = "Gagal memuat jadwal.");
}

datePicker.onchange = e => loadJadwal(e.target.value);

lanjutBtn.onclick = () => {
  if (!isLoggedIn) {
    loginPopup.style.display = "flex";
    return;
  }

  if (selectedTimes.length === 0) {
    alert("Pilih minimal satu jam!");
    return;
  }

  const date = datePicker.value;
  const url = `bayar.php?lapangan=${selectedLapangan}&tanggal=${date}&jam=${encodeURIComponent(selectedTimes.join(','))}`;
  window.location.href = url;
};
