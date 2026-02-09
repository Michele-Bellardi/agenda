document.getElementById("loginForm").onsubmit = async e => {
  e.preventDefault();
  let f = e.target;

  let r = await fetch("api/login.php", {
    method: "POST",
    body: JSON.stringify({
      username: f.username.value,
      password: f.password.value
    })
  });

  let j = await r.json();

  if (j.status == "ok") {
    location.href = "index.html";
  } else {
    alert("Username o password sbagliati");
  }
};
