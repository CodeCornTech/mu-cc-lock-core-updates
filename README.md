# 🔒 CC Lock Core Updates ( MU Plugin )

![CodeCorn](https://img.shields.io/badge/CodeCorn™-TECHNOLOGY-%23d4af37?style=flat&logo=github&labelColor=111111)
![WordPress](https://img.shields.io/badge/WordPress-tested%20up%20to%206.9-21759B?logo=wordpress&logoColor=white)
![MU Plugin](https://img.shields.io/badge/MU%20Plugin-must--use-111111)
![License](https://img.shields.io/badge/License-GPL--2.0%2B-blue)


**CodeCorn™ Must-Use Plugin**  
Blocca completamente gli aggiornamenti del core WordPress , sia automatici che manuali .

Pensato per ambienti **gestiti , enterprise , Docker , CI/CD** dove gli update devono essere **controllati centralmente** e non lasciati alla UI WordPress .

---

## ✨ Features

-   ❌ Disabilita il check degli aggiornamenti core
-   ❌ Disabilita gli aggiornamenti automatici
-   ❌ Nasconde la pagina _Bacheca → Aggiornamenti_
-   ❌ Blocca l’accesso diretto a `update-core.php`
-   ✅ Zero configurazione
-   ✅ Zero overhead
-   ✅ MU-Plugin ( non disattivabile da admin )

---

## 📦 Installazione

```bash
mkdir -p wp-content/mu-plugins || exit 1
cd wp-content/mu-plugins || exit 1
curl -O https://raw.githubusercontent.com/CodeCornTech/mu-cc-lock-core-updates/main/mu-cc-lock-core-updates.php
```

Oppure copia manualmente il file :

```
wp-content/mu-plugins/mu-cc-lock-core-updates.php
```

> Non serve attivarlo : i MU-plugin vengono caricati automaticamente .

---

## 🧠 Quando usarlo

Questo plugin è consigliato se :

-   usi **Docker / Kubernetes**
-   deployi via **CI/CD**
-   hai **update gestiti esternamente**
-   lavori su **hosting enterprise**
-   vuoi evitare update accidentali in produzione

❌ **Non consigliato** su siti entry-level o hosting condivisi senza controllo versioni .

---

## 🛡️ Comportamento

Una volta attivo :

-   WordPress **non rileva** aggiornamenti core
-   WordPress **non esegue** aggiornamenti automatici
-   Gli admin **non vedono** la pagina aggiornamenti
-   L’accesso diretto viene bloccato con **HTTP 403**

---

## 🧩 Compatibilità

-   WordPress ≥ 5.8
-   Testato fino a **WordPress 6.9**
-   PHP ≥ 7.4
-   Multisite ✅

---

## 🧪 Versioning

Seguendo **Semantic Versioning** :

```
MAJOR.MINOR.PATCH
```

---

## 👤 Autore

**CodeCorn™**
👉 [https://github.com/CodeCornTech](https://github.com/CodeCornTech)

---

## 📄 License

GPL-2.0+
[https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)
