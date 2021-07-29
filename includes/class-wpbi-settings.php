<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Settings class
 */
if ( !class_exists( 'WordPress_Bulk_Install_Settings' ) ) {

	class WordPress_Bulk_Install_Settings {
		
		/**
		 * Constructor
		 */
		public function __construct() {		
			// Define default variables
			$this->settings_page = 'wordpress-bulk-install';
			
			// Load the hooks
			// add_action( 'admin_init', array( $this, 'load_admin_hooks' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
			add_action( 'admin_menu', array( $this, 'load_admin_page' ) );
		}
		
		/**
		 * Load the admin hooks
		 */
		public function load_admin_hooks() {

            // add_filter( 'woocommerce_get_sections_shipping', array( $this, 'add_shipping_section' ), 10, 1 );
            // add_filter( 'woocommerce_get_settings_shipping', array( $this, 'add_shipping_settings' ), 10, 2 );
		}
		
		/**
		 * Load the admin scripts
		 */
		public function load_admin_scripts() {
			
			if ( is_admin() ){ // for Admin Dashboard Only

				// Embed the Script on our Plugin's Option Page Only
				if ( isset($_GET['page']) && $_GET['page'] == $this->settings_page ) {
					
					// Scripts
					wp_register_script( WordPress_Bulk_Install::$plugin_prefix . 'admin-script', WordPress_Bulk_Install::$plugin_url . 'assets/js/admin.js', array( 'jquery-ui-core', 'jquery-ui-tabs' ), WordPress_Bulk_Install::$plugin_version, true );
					wp_localize_script( WordPress_Bulk_Install::$plugin_prefix . 'admin-script', 'plugin_prefix', WordPress_Bulk_Install::$plugin_prefix );
					wp_enqueue_script( WordPress_Bulk_Install::$plugin_prefix . 'admin-script' );

					// Styles
					wp_enqueue_style( WordPress_Bulk_Install::$plugin_prefix . 'admin-style', WordPress_Bulk_Install::$plugin_url . 'assets/css/admin.css', array(), WordPress_Bulk_Install::$plugin_version );
				
				}
			}

		}
		
		/**
		 * Load the admin page
		 */
		public function load_admin_page() {
			add_submenu_page( 'tools.php', 'Bulk Install', 'Bulk Install', 'administrator', $this->settings_page, array( $this, 'settings_page'), 11 );
		}
		
		public function settings_page() {

			$defaults = parse_ini_file( WordPress_Bulk_Install::$plugin_path . 'defaults.ini' );
			
			?>

			<div class="wrap">
				<h1>Install plugins & themes</h1>

				<h2>Themes</h2>
				<table class="form-table" role="presentation"><tbody>
					
					<tr>
						<th scope="row">Main theme</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Main theme</span></legend>
								<label for="install_main_theme">
									<input name="install_main_theme" type="checkbox" id="install_main_theme" value="1" <?= $defaults['theme']['main'] ? 'checked="checked"' : '' ?>>
									Install a main theme
								</label>
							</fieldset>
							<div class="hide-if-js main-theme-only">
								<input name="theme" type="text" id="theme" value="<?= $defaults['theme']['main'] ?>" class="regular-text">
								<p class="description" id="theme-description">Enter the url to a .zip file.</p>
							</div>
						</td>
					</tr>
					
					<tr>
						<th scope="row">Child theme</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Main theme</span></legend>
								<label for="install_child_theme">
									<input name="install_child_theme" type="checkbox" id="install_child_theme" value="1" <?= $defaults['theme']['child'] ? 'checked="checked"' : '' ?>>
									Install a child theme
								</label>
							</fieldset>
							<div class="hide-if-js child-theme-only">
								<input name="child-theme" type="text" id="child-theme" value="<?= $defaults['theme']['child'] ?>" class="regular-text">
								<p class="description" id="child-theme-description">Enter the url to a .zip file.</p>
							</div>
						</td>
					</tr>

					<tr class="hide-if-js child-theme-only">
						<th scope="row"><label for="child-theme">Overwrite child theme name</label></th>
						<td>
							<input name="child-theme-name" type="text" id="child-theme-name" class="regular-text">
							<p class="description" id="child-theme-description">Enter name to ovewrite the child theme folder name. Leave empty to use a default name.</p>
						</td>
					</tr>
					
					<!-- <tr>
						<th scope="row">Themes cleanup</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Uninstall unused themes</span></legend>
								<label for="uninstall_unused_themes">
									<input name="uninstall_unused_themes" type="checkbox" id="uninstall_unused_themes" value="1">
									Uninstall unused themes
								</label>
							</fieldset>
						</td>
					</tr> -->

				</tbody></table>
				
				<h2>Plugins</h2>
				<table class="form-table" role="presentation"><tbody>
					<tr>
						<th scope="row"><label for="plugins">Plugin list</label></th>
						<td>
							<textarea name="plugins" id="plugins" class="regular-text" rows="10"><?= join("&#13", $defaults['plugins']) ?></textarea>
							<p class="description" id="plugins-description">Enter the plugin slugs. One per line.</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">WooCommerce</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>WooCommerce</span></legend>
								<label for="install_woocommerce">
									<input name="install_woocommerce" type="checkbox" id="install_woocommerce" value="1" <?= $defaults['woocommerce'] ? 'checked="checked"' : '' ?>>
									Install WooCommerce and WooCommerce related plugins
								</label>
							</fieldset>
						</td>
					</tr>
					
					<tr class="hide-if-js woocommerce-only">
						<th scope="row"><label for="woocommerce_plugins">WooCommerce Plugins</label></th>
						<td>
							<textarea name="woocommerce_plugins" id="woocommerce_plugins" class="regular-text" rows="10"><?= join("&#13", $defaults['woocommerce']) ?></textarea>
							<p class="description" id="woocommerce_plugins-description">Enter the plugins slugs. One per line.</p>
						</td>
					</tr>
				</tbody></table>
				
				<input type="submit" name="install" id="install" class="button button-primary" value="Install" disabled="disabled">
				<div id="results"></div>
			</div>
				
			<?php

		}
		
		private function upgrade_plugin( $api ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			wp_cache_flush();

			$skin = new WP_Ajax_Upgrader_Skin($api);
			$upgrader = new Plugin_Upgrader($skin);
			$upgraded = $upgrader->upgrade( $api->download_link );
			
			return $upgraded;
		}
		
		private function activate_plugins( $slug ) {

			$errors = false;

			$plugin_files =  $this->get_plugin_files( $slug );
			foreach( $plugin_files as $plugin ) {
				$activated = activate_plugin( $plugin );
				if( !is_null($activated) ) {
					$errors = true;
				}
			}

			return !$errors;
			
		}

		private function get_plugin_files( $plugin_name ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';

			$files = array();

			$plugins = get_plugins("/".$slug);
			foreach( $plugins as $plugin_file => $plugin_info ) {
				$files[] = trailingslashit( $slug ) . $plugin_file;
			}
			return $files;
		}

	}
}

?>