let currentDate = new Date();
let PROMEMORIA = [];
let APPUNTAMENTI = [];

const giorni = ["Lun","Mar","Mer","Gio","Ven","Sab","Dom"];

// ---------------- AUTH ----------------
async function logout(){
  await fetch("api/logout.php");
  location.href = "login.html";
}

// ---------------- LOAD DATA ----------------
async function loadData(){
  PROMEMORIA = await (await fetch("api/promemoria.php")).json();
  APPUNTAMENTI = await (await fetch("api/appuntamento.php")).json();
  renderCalendar(); // 🔥 SOLO QUI
}

// ---------------- TOGGLE FORM ----------------
function togglePromemoria(){
  formPromemoria.style.display =
    formPromemoria.style.display === "none" ? "block" : "none";
}

function toggleAppuntamento(){
  formAppuntamento.style.display =
    formAppuntamento.style.display === "none" ? "block" : "none";
}

// ---------------- ADD PROMEMORIA ----------------
async function addPromemoria(){
  await fetch("api/promemoria.php",{
    method:"POST",
    body: JSON.stringify({
      descrizione: pdesc.value,
      data: pdata.value,
      ora: pora.value,
      durata: pdur.value,
      ricorrenza: pric.value
    })
  });
  togglePromemoria();
  loadData();
}

// ---------------- ADD APPUNTAMENTO ----------------
async function addAppuntamento(){
  let emails = autenti.value
    .split(",")
    .map(e => e.trim())
    .filter(e => e.length > 0);

  let r = await fetch("api/appuntamento.php",{
    method:"POST",
    body: JSON.stringify({
      descrizione: adesc.value,
      data: adata.value,
      ora: aora.value,
      durata: adur.value,
      emails: emails
    })
  });

  let j = await r.json();

  if(j.status === "busy"){
    alert("Uno degli utenti è occupato");
    return;
  }
  if(j.status === "notfound"){
    alert("Una o più email non sono registrate");
    return;
  }

  toggleAppuntamento();
  loadData();
}

// ---------------- RICORRENZA ----------------
function ricorre(p, day){
  if(p.data === day) return true;

  let d1 = new Date(p.data + "T00:00:00");
  let d2 = new Date(day + "T00:00:00");
  let diff = Math.floor((d2 - d1) / (1000*60*60*24));
  if(diff < 0) return false;

  if(p.ricorrenza === "settimanale") return diff % 7 === 0;
  if(p.ricorrenza === "mensile") return p.data.slice(8) === day.slice(8);
  if(p.ricorrenza === "annuale") return p.data.slice(5) === day.slice(5);

  return false;
}

// ---------------- CALENDAR ----------------
function renderCalendar(){
  calendar.innerHTML = "";
  weekdays.innerHTML = "";
  giorni.forEach(g => weekdays.innerHTML += `<div>${g}</div>`);

  let y = currentDate.getFullYear();
  let m = currentDate.getMonth();

  monthLabel.innerText =
    currentDate.toLocaleString("it-IT",{month:"long",year:"numeric"});

  let firstDay = (new Date(y,m,1).getDay() + 6) % 7;
  let days = new Date(y,m+1,0).getDate();

  for(let i=0;i<firstDay;i++){
    calendar.innerHTML += "<div></div>";
  }

  for(let d=1; d<=days; d++){
    let day = `${y}-${String(m+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`;

    let hasEvent =
      PROMEMORIA.some(p => ricorre(p, day)) ||
      APPUNTAMENTI.some(a => a.data === day);

    let div = document.createElement("div");
    div.className = "day";
    if(hasEvent) div.classList.add("has-event");

    div.innerText = d;
    div.onclick = () => caricaGiorno(day);

    calendar.appendChild(div);
  }
}

// ---------------- DAY VIEW ----------------
function caricaGiorno(day){
  eventi.innerHTML = `<h3>${day}</h3>`;
  let found = false;

  PROMEMORIA
    .filter(p => ricorre(p, day))
    .forEach(p => {
      eventi.innerHTML += `📌 ${p.ora || ""} ${p.descrizione}<br>`;
      found = true;
    });

  APPUNTAMENTI
    .filter(a => a.data === day)
    .forEach(a => {
      eventi.innerHTML += `👥 ${a.ora || ""} ${a.descrizione}<br>`;
      found = true;
    });

  if(!found){
    eventi.innerHTML += "Nessun evento";
  }
}

// ---------------- NAV ----------------
function prevMonth(){
  currentDate.setMonth(currentDate.getMonth()-1);
  renderCalendar();
}

function nextMonth(){
  currentDate.setMonth(currentDate.getMonth()+1);
  renderCalendar();
}

// ---------------- INIT ----------------
loadData();
