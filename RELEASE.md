## 🔒 CC Lock Core Updates – v1.1.0

This release introduces **enterprise-grade hardening** and extensibility improvements.

### ✨ Added
- WP-CLI bypass for core update access
- Email whitelist via constant `CC_LCU_ALLOWED_EMAILS`
- New filter `cc_lcu_allowed_emails` for dynamic control
- Admin-only hardening guard (`is_admin()`)

### 🛡️ Security
- Direct access to `update-core.php` now fully restricted
- Non-authorized users receive HTTP 403

### 🧠 Notes
- MU-plugin only ( auto-loaded )
- Designed for managed / CI-CD / Docker environments
- Fully backward compatible

---

**Author:** CodeCorn™  
**License:** GPL-2.0+
