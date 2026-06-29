const API = "http://localhost:8001";

let CAPTCHA_QUESTION = null;

// Charge la question Captcha depuis captcha.php
async function loadCaptcha() {
    const response = await fetch(`${API}/captcha.php`, {
        credentials: "include"
    });

    const data = await response.json();
    CAPTCHA_QUESTION = data.question;

    // Affiche la question dans le formulaire
    document.getElementById("captcha-question").textContent = CAPTCHA_QUESTION;
}

// Charger le captcha au démarrage
loadCaptcha();

// Variable qui contiendra le token CSRF
let CSRF_TOKEN = null;

// Fonction qui récupère le token CSRF depuis csrf.php
async function loadCSRF() {
    const response = await fetch(`${API}/csrf.php`, {
        credentials: "include" // important pour envoyer le cookie de session
    });

    const data = await response.json();
    CSRF_TOKEN = data.csrf_token; // on stocke le token
}

// On charge le token CSRF dès le chargement du script
loadCSRF();

// Changer d’écran
document.getElementById("show-register").onclick = () => {
    document.getElementById("login-box").style.display = "none";
    document.getElementById("register-box").style.display = "block";
};

document.getElementById("show-login").onclick = () => {
    document.getElementById("register-box").style.display = "none";
    document.getElementById("login-box").style.display = "block";
};


// Connexion
document.getElementById("login-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const username = document.getElementById("login-username").value;
    const password = document.getElementById("login-password").value;

    const response = await fetch(`${API}/Auth.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
        credentials: "include"
    });

    if (response.ok) {
        window.location.href = "home.html";
        return; // IMPORTANT
    } else {
        document.getElementById("login-error").textContent = "Identifiants incorrects.";
        return; // IMPORTANT
    }
});


// Vérification de la force du mot de passe
document.getElementById("register-password").addEventListener("input", () => {
    const pwd = document.getElementById("register-password").value;
    const strengthBox = document.getElementById("password-strength");

    let strength = 0;

    if (pwd.length >= 12) strength++;
    if (/[A-Z]/.test(pwd)) strength++;
    if (/[a-z]/.test(pwd)) strength++;
    if (/\d/.test(pwd)) strength++;
    if (/[\W_]/.test(pwd)) strength++;

    switch (strength) {
        case 0:
            strengthBox.textContent = "Nul";
            strengthBox.style.color = "red";
            break;
        case 1:
            strengthBox.textContent = "Faible";
            strengthBox.style.color = "red";
            break;
        case 2:
            strengthBox.textContent = "Assez faible";
            strengthBox.style.color = "orange";
            break;
        case 3:
            strengthBox.textContent = "Moyen";
            strengthBox.style.color = "yellow";
            break;
        case 4:
            strengthBox.textContent = "Fort";
            strengthBox.style.color = "lightgreen";
            break;
        case 5:
            strengthBox.textContent = "Très fort";
            strengthBox.style.color = "green";
            break;
    }
});

// Inscription
document.getElementById("register-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const username = document.getElementById("register-username").value;
    const email = document.getElementById("register-email").value;
    const password = document.getElementById("register-password").value;
    const confirm = document.getElementById("register-password-confirm").value;
    const captcha = document.getElementById("captcha-answer").value;

    // Vérification des mots de passe identiques
    if (password !== confirm) {
        document.getElementById("password-match-error").textContent =
            "Les mots de passe ne correspondent pas.";
        return; // IMPORTANT
    } else {
        document.getElementById("password-match-error").textContent = "";
    }

    const response = await fetch(`${API}/register.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": CSRF_TOKEN // envoie le token CSRF
        },
        body: JSON.stringify({ username, email, password, captcha }),
        credentials: "include"
    });

    const result = await response.json(); // LIRE LE JSON

    if (!response.ok) {
        // Afficher l’erreur renvoyée par le serveur
        document.getElementById("register-error").textContent = result.error || "Erreur lors de l'inscription.";
        document.getElementById("register-success").textContent = "";
        return; // TRÈS IMPORTANT : bloque la redirection
    }

    // Succès
    document.getElementById("register-success").textContent = "Compte créé avec succès.";
    document.getElementById("register-error").textContent = "";
});

