document.getElementById("registerForm").onsubmit = async e => {
  e.preventDefault();
  let f = e.target;

  if (f.password.value !== f.password2.value) {
    alert("Le password non coincidono!");
    return;
  }

  if (f.password.value.length < 6) {
    alert("Password troppo corta (min 6 caratteri)");
    return;
  }

  let data = {
    nome: f.nome.value,
    cognome: f.cognome.value,
    email: f.email.value,
    telefono: f.telefono.value,
    username: f.username.value,
    password: f.password.value
  };

  let r = await fetch("api/account.php", {
    method: "POST",
    body: JSON.stringify(data)
  });

  let j = await r.json();

  if (j.status == "ok") {
    alert("Registrazione completata!");
    location.href = "login.html";
  } else {
    alert("Errore registrazione (username già usato?)");
  }
};
