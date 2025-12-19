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
 * Version: 1.4.0
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
 *  CORE UPDATES LOCK
 * -------------------------------------------------------------
 */
if (!defined('CC_LCU_LOCK_CORE') || CC_LCU_LOCK_CORE !== false) {

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

        // Safety : evita double execution
        if (did_action('admin_init') > 1) {
            return;
        }

        global $pagenow;

        // Solo  admin area e pagina update core
        if (!is_admin() || $pagenow !== 'update-core.php') {
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

        if (!in_array($user->user_email, (array) $allowed_emails, true)) {
            wp_die(
                'Aggiornamenti del core disabilitati dall’amministratore di sistema .',
                'Accesso negato',
                ['response' => 403]
            );
        }

    });
}
/**
 * -------------------------------------------------------------
 *  PLUGIN UPDATES LOCK
 * -------------------------------------------------------------
 */
if (!defined('CC_LCU_LOCK_PLUGINS') || CC_LCU_LOCK_PLUGINS !== false) {

    /**
     * -------------------------------------------------------------
     *  Lista plugin con UI update da bloccare
     *  ( filtrabile )
     * -------------------------------------------------------------
     */
    function cc_lcu_ui_blocked_plugins()
    {
        return apply_filters(
            'cc_lcu_blocked_plugin_update_ui',
            []
        );
    }

    /**
     * -------------------------------------------------------------
     *  Guard helper
     * -------------------------------------------------------------
     *  Verifica se esiste almeno un plugin dichiarato
     *  per il blocco degli aggiornamenti.
     *
     *  Se la lista è vuota :
     *  - nessun hook viene registrato
     *  - nessuna UI viene toccata
     *  - nessun transient viene alterato
     *
     *  Serve come short-circuit per evitare
     *  operazioni inutili e side-effect.
     * -------------------------------------------------------------
     */
    function cc_lcu_has_blocked_plugins()
    {
        $plugins = cc_lcu_ui_blocked_plugins();
        return is_array($plugins) && !empty($plugins);
    }

    /**
     * -------------------------------------------------------------
     *  Rimuove hook UI che iniettano update nella pagina plugin
     * -------------------------------------------------------------
     */
    function cc_lcu_remove_plugin_update_ui()
    {

        foreach (cc_lcu_ui_blocked_plugins() as $plugin) {
            remove_all_actions("in_plugin_update_message-$plugin");
            remove_all_actions("after_plugin_row_$plugin");
        }
    }

    /**
     * -------------------------------------------------------------
     *  Rimuove gli update dal transient ( engine level )
     * -------------------------------------------------------------
     */
    function cc_lcu_remove_plugin_update_transient($transient)
    {

        if (!is_object($transient) || empty($transient->response)) {
            return $transient;
        }

        foreach (cc_lcu_ui_blocked_plugins() as $plugin) {
            unset($transient->response[$plugin]);
        }

        return $transient;
    }

    /**
     * -------------------------------------------------------------
     *  Plugin Update Locker – Bootstrap
     * -------------------------------------------------------------
     *  Punto di ingresso unico per il blocco degli aggiornamenti
     *  dei plugin.
     *
     *  La funzione :
     *  - verifica preventivamente se esistono plugin da bloccare
     *  - in caso contrario esce immediatamente ( short-circuit )
     *  - registra solo gli hook strettamente necessari
     *
     *  Questo approccio evita :
     *  - hook inutili
     *  - overhead in fase di init
     *  - side-effect su installazioni non configurate
     * -------------------------------------------------------------
     */
    function cc_plugin_locker()
    {
        // Safety : init già passato → non registrare hook
        if (did_action('init')) {
            return;
        }

        // Verifica se esiste almeno un plugin dichiarato , altrimenti esce
        if (!cc_lcu_has_blocked_plugins()) {
            return;
        }

        // Kill UI injection ( serve priority bassa )
        add_action('init', 'cc_lcu_remove_plugin_update_ui', 1);

        // Kill update engine response ( serve priority alta )
        add_filter(
            'site_transient_update_plugins',
            'cc_lcu_remove_plugin_update_transient',
            9999
        );
    }

    /**
     * -------------------------------------------------------------
     *  Hook registration
     * -------------------------------------------------------------
     *  Registra il bootstrap del plugin locker in fase di init.
     *  La logica interna gestisce autonomamente
     *  l’attivazione o meno del sistema.
     * -------------------------------------------------------------
     */
    add_action('init', 'cc_plugin_locker', 1);

}
