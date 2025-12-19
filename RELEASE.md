# 📦 Releases – CC Lock Core Updates

---

## 🔖 v1.4.0 – 2025-12-19

### ✨ Added

-   Plugin Updates Lock ( UI + engine level )
-   Filtro `cc_lcu_blocked_plugin_update_ui`
-   Bootstrap unico per il blocco plugin updates
-   Guard helper `cc_lcu_has_blocked_plugins()`

### 🔒 Security & Stability

-   Safety `did_action()` su `init` e `admin_init`
-   Short-circuit automatico se nessun plugin è dichiarato
-   Nessun hook registrato inutilmente
-   Zero side-effect su installazioni non configurate

### 🧠 Architecture

-   Separazione netta Core Updates / Plugin Updates
-   Bootstrap deterministico e idempotente
-   MU-plugin enterprise-grade
-   Pronto per Docker / CI-CD / Multisite

### 🧪 DX / Dev Experience

-   Esempi chiari di utilizzo via filtri
-   Strategia consigliata CodeCorn™ documentata
-   README esteso e allineato al codice

---

## 🔖 v1.3.0 – 2025-12-19

### ✨ Added

-   Kill-switch globale `CC_LCU_ENABLED`
-   Supporto ENV / wp-config / CI-CD
-   Bypass totale del MU senza rimozione file
-   Compatibilità completa Docker & WP-CLI

### 🔒 Security

-   Nessun accesso UI agli update core
-   WP-CLI sempre consentito
-   Caricamento condizionale idempotente

### 🧠 Notes

Questa release rende il plugin **enterprise-ready** permettendo
il controllo centralizzato del comportamento senza modificare il codice.

---

## 🔖 v1.2.0 – 2025-12-04

-   Blocco completo update core
-   Nascondi UI aggiornamenti
-   Accesso diretto bloccato
-   Allowlist email admin

---

## 🔖 v1.1.0

-   Miglioramenti sicurezza
-   Refactor admin guard

---

## 🔖 v1.0.0

-   First stable release
