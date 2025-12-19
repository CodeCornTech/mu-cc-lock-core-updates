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
-   ✅ Zero overhead
-   ✅ **Kill-switch centralizzato via ENV / wp-config**
-   ✅ WP-CLI sempre consentito

---

## 🔧 Toggle Globale ( Enterprise )

Il comportamento del plugin può essere **abilitato / disabilitato centralmente** senza toccare il file.

### `.env`

```env
CC_LCU_ENABLED=true
```

## 🧪 Esempi di uso ( chiari )

### 🔴 Spegni tutto ( bypass totale MU )

```php
define('CC_LCU_ENABLED', false);
```

---

### 🟡 Blocca solo plugin

```php
define('CC_LCU_LOCK_CORE', false);
```

---

### 🟡 Blocca solo core

```php
define('CC_LCU_LOCK_PLUGINS', false);
```

---

### 🟢 Default ( tutto attivo )

```php
// nessuna define
```

---

## 🔌 Filtro `cc_lcu_blocked_plugin_update_ui`

### ✔️ Modo consigliato ( unico filtro )

```php
add_filter('cc_lcu_blocked_plugin_update_ui', function ($plugins) {

    $plugins = array_merge($plugins, [
        'altro-plugin/altro-plugin.php',
        'secondo-plugin/secondo-plugin.php',
        'terzo-plugin/terzo-plugin.php',
    ]);

    return array_unique($plugins);
});
```

---

## 📧 Filtro `cc_lcu_allowed_emails`

### Statico

```php
add_filter('cc_lcu_allowed_emails', function ($emails, $user) {

    $emails[] = 'admin@codecorn.it';
    return $emails;

}, 10, 2);
```

### Dinamico per ruolo

```php
add_filter('cc_lcu_allowed_emails', function ($emails, $user) {

    if (in_array('administrator', (array) $user->roles, true)) {
        $emails[] = $user->user_email;
    }

    return array_unique($emails);

}, 10, 2);
```

---

## ✅ Strategia consigliata CodeCorn™

### 1️⃣ Flag globale di bypass

```php
CC_LCU_ENABLED
```

| Valore  | Effetto                        |
| ------- | ------------------------------ |
| `true`  | MU **attivo**                  |
| `false` | MU **completamente bypassato** |

---

## 🔐 Implementazione env-driven ( sicura )

### `wp-config.php / WORDPRESS_CONFIG_EXTRA`

```php
defined('CC_LCU_ENABLED')
    || define(
        'CC_LCU_ENABLED',
        filter_var(
            getenv('CC_LCU_ENABLED') ?: true,
            FILTER_VALIDATE_BOOLEAN
        )
    );
```

### `.env`

```env
CC_LCU_ENABLED=true
```

---

## 🧩 Extra ( opzionali )

### 🔁 Toggle via filtro

```php
if (! apply_filters('cc_lcu_enabled', CC_LCU_ENABLED)) {
    return;
}
```

Uso :

```php
add_filter('cc_lcu_enabled', '__return_false');
```

---

### 🛡️ Hook di logging

```php
do_action(
    'cc_lcu_blocked_access',
    $user,
    $pagenow
);
```

---

### 🧠 Safe-mode staging

```php
if (
    defined('WP_ENVIRONMENT_TYPE') &&
    WP_ENVIRONMENT_TYPE !== 'production'
) {
    return;
}
```

---

### 📥 Download diretto ( MU Plugin )

👉 [https://github.com/CodeCornTech/mu-cc-lock-core-updates/releases/latest/download/mu-cc-lock-core-updates.php](https://github.com/CodeCornTech/mu-cc-lock-core-updates/releases/latest/download/mu-cc-lock-core-updates.php)

---

---

## 📦 Installazione

### 🔹 Installazione iniziale

```bash
mkdir -p wp-content/mu-plugins || exit 1
cd wp-content/mu-plugins || exit 1
curl -O https://raw.githubusercontent.com/CodeCornTech/mu-cc-lock-core-updates/main/mu-cc-lock-core-updates.php
```

Oppure copia manualmente :

```
wp-content/mu-plugins/mu-cc-lock-core-updates.php
```

> I **MU-plugin** vengono caricati automaticamente da WordPress.
> Non è necessario attivarli dalla UI admin.

---

### 🔄 Aggiornamento ( consigliato )

Per aggiornare il plugin all’ultima versione disponibile **senza rimuovere il file** :

```bash
cd wp-content/mu-plugins || exit 1
curl -L -o mu-cc-lock-core-updates.php \
https://github.com/CodeCornTech/mu-cc-lock-core-updates/releases/latest/download/mu-cc-lock-core-updates.php
```

✔ sovrascrive il file esistente
✔ mantiene permessi e path
✔ compatibile con Docker / CI-CD
✔ zero downtime

---

### 🧠 Aggiornamento via CI / Docker ( esempio )

```bash
curl -fsSL \
https://github.com/CodeCornTech/mu-cc-lock-core-updates/releases/latest/download/mu-cc-lock-core-updates.php \
-o /var/www/html/wp-content/mu-plugins/mu-cc-lock-core-updates.php
```

---

## ✅ Best practice CodeCorn™

-   usare sempre **`releases/latest`** ( non `main` )
-   versionamento controllato
-   update **idempotente**
-   rollback immediato possibile

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

## 🧩 TODO Variante ultra-strict ( opzionale )

Se si vuole essere **ancora più paranoico** ( staging , plugin terzi aggressivi ) :

```php
static $ran = false;
if ($ran) {
    return;
}
$ran = true;
```

Usabile **insieme** o **al posto** di `did_action`.

---

## 👤 Autore

**CodeCorn™**
👉 [https://github.com/CodeCornTech](https://github.com/CodeCornTech)

---

## 📄 License

GPL-2.0+
[https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)
