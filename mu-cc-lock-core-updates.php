<?php
/**
 * =============================================================
 *  CodeCorn™ MU Plugin
 * =============================================================
 *
 * Plugin Name: CC Lock Core Updates
 * Description: Disabilita completamente gli aggiornamenti del core WordPress ( automatici e manuali ) e ne impedisce l’accesso dalla UI admin .
 *
 * Type: Must-Use Plugin
 * Scope: WordPress Core Updates
 *
 * Plugin URI: https://github.com/CodeCornTech/mu-cc-lock-core-updates
 * Author: CodeCorn™
 * Author URI: https://github.com/CodeCornTech
 *
 * Since: 04-12-2025
 * Version: 1.3.0
 * Requires PHP: 7.2
 * Requires at least: 5.8
 * Tested up to: 6.9
 *
 * Update URI: https://github.com/CodeCornTech/mu-cc-lock-core-updates/releases/latest/download/mu-cc-lock-core-updates.php
 *
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * =============================================================
 *  ⚠️  NOTE
 * -------------------------------------------------------------
 *  Questo MU-plugin è pensato per ambienti:
 *   - Managed
 *   - Enterprise
 *   - Docker / CI-CD
 *   - Hosting con update centralizzati
 *
 *  Una volta attivo:
 *   - Nessun update core viene rilevato
 *   - Nessun update core può partire
 *   - La pagina "Aggiornamenti" è nascosta
 *   - Accesso diretto a update-core.php BLOCCATO
 *
 * =============================================================
 */

defined('ABSPATH') || exit;
/**
 * -------------------------------------------------------------
 *  Global Enable / Disable
 * -------------------------------------------------------------
 */
if (defined('CC_LCU_ENABLED') && CC_LCU_ENABLED === false) {
    return;
}
/**
 * -------------------------------------------------------------
 *  Allowed emails ( default )
 *  Filtrabile via hook
 * -------------------------------------------------------------
 */
defined('CC_LCU_ALLOWED_EMAILS') || define('CC_LCU_ALLOWED_EMAILS', [
    'f.girolami@codecorn.it',
]);

/**
 * -------------------------------------------------------------
 *  Disable Core Update Checks
 * -------------------------------------------------------------
 */
add_filter('pre_site_transient_update_core', '__return_null');

/**
 * -------------------------------------------------------------
 *  Disable Automatic Updater
 * -------------------------------------------------------------
 */
add_filter('automatic_updater_disabled', '__return_true');
add_filter('wp_auto_update_core', '__return_false');

/**
 * -------------------------------------------------------------
 *  Hide "Updates" submenu from Dashboard
 * -------------------------------------------------------------
 */
add_action('admin_menu', function () {
    remove_submenu_page('index.php', 'update-core.php');
});

/**
 * -------------------------------------------------------------
 *  Block direct access to update-core.php
 *  ( allow only specifics user email )
 * -------------------------------------------------------------
 */
add_action('admin_init', function () {
    global $pagenow;

    // Solo admin area
    if (!is_admin()) {
        return;
    }

    // Solo pagina update core
    if ($pagenow !== 'update-core.php') {
        return;
    }

    // Consenti sempre via WP-CLI
    if (defined('WP_CLI') && WP_CLI) {
        return;
    }

    // Utente corrente
    $user = wp_get_current_user();

    // Safety guard
    if (!$user || empty($user->user_email)) {
        wp_die(
            'Accesso negato .',
            'Accesso negato',
            ['response' => 403]
        );
    }
/**
     * ---------------------------------------------------------
     *  Allowed emails ( filtrabile )
     * ---------------------------------------------------------
     */
    $allowed_emails = apply_filters(
        'cc_lcu_allowed_emails',
        CC_LCU_ALLOWED_EMAILS,
        $user
    );

    if (! in_array($user->user_email, (array) $allowed_emails, true)) {
        wp_die(
            'Aggiornamenti del core disabilitati dall’amministratore di sistema .',
            'Accesso negato',
            ['response' => 403]
        );
    }

});


// // Filtro cc_lcu_allowed_emails ( esempio d’uso )

// // Da altro MU-plugin o plugin custom:

// add_filter('cc_lcu_allowed_emails', function ($emails, $user) {

//     $emails[] = 'admin@codecorn.it';

//     return $emails;

// }, 10, 2);


// // Oppure dinamico per ruolo:

// add_filter('cc_lcu_allowed_emails', function ($emails, $user) {

//     if (in_array('administrator', (array) $user->roles, true)) {
//         $emails[] = $user->user_email;
//     }

//     return array_unique($emails);

// }, 10, 2);

// ## ✅ STRATEGIA ( consigliata CodeCorn™ )

// ### 1️⃣ Flag globale di bypass

// ```php
// CC_LCU_ENABLED
// ```

// * `true`  → MU **attivo**
// * `false` → MU **totalmente bypassato**

// ---

// ## 🧠 IMPLEMENTAZIONE ( minimale e sicura )

// ### 🔐 Costante ( env-driven )

// In **wp-config / WORDPRESS_CONFIG_EXTRA**:

// ```php
// defined('CC_LCU_ENABLED')
//     || define(
//         'CC_LCU_ENABLED',
//         filter_var(
//             getenv('CC_LCU_ENABLED') ?: true,
//             FILTER_VALIDATE_BOOLEAN
//         )
//     );
// ```

// Nel `.env`:

// ```env
// CC_LCU_ENABLED=true
// ```


// ## 🧩 EXTRA ( opzionali ma top )

// ### 🔁 Toggle via filtro

// ```php
// if (! apply_filters('cc_lcu_enabled', CC_LCU_ENABLED)) {
//     return;
// }
// ```

// Uso:

// ```php
// add_filter('cc_lcu_enabled', '__return_false');
// ```

// ---

// ### 🛡️ Log quando qualcuno prova

// ```php
// do_action(
//     'cc_lcu_blocked_access',
//     $user,
//     $pagenow
// );
// ```

// ---

// ### 🧠 Safe mode per staging

// ```php
// if (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE !== 'production') {
//     return;
// }
// ```