<?php
/*
 * Plugin Name: ExchangeWP - ConvertKit Add-on
 * Version: 2.2.3
 * Description: Adds the ConvertKit addon to ExchangeWP
 * Plugin URI: https://exchangewp.com/downloads/convertkit/
 * Author: ExchangeWP
 * Author URI: https://exchangewp.com
 * ExchangeWP Package: exchange-addon-convertkit

 * Installation:
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire plugin directory to your `/wp-content/plugins/` directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 * 5. Add license key to plugin settings page.
 *
*/

/**
 * This registers our plugin as a membership addon
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_register_convertkit_addon() {
	$versions         = get_option( 'it-exchange-versions', false );
	$current_version  = empty( $versions['current'] ) ? false: $versions['current'];

	if ( true || version_compare( $current_version, '1.35.0', '>' ) ) {

		$options = array(
			'name'              => __( 'ConvertKit', 'LION' ),
			'description'       => __( 'Add ConvertKit Opt-In Checkbox to user registration form.', 'LION' ),
			'author'            => 'ExchangeWP',
			'author_url'        => 'https://exchangewp.com/downloads/convertkit/',
			'icon'              => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/lib/images/convertkit50px.png' ),
			'file'              => dirname( __FILE__ ) . '/init.php',
			'category'          => 'email',
			'settings-callback' => 'it_exchange_convertkit_settings_callback',
		);
		it_exchange_register_addon( 'convertkit', $options );

	} else {

		add_action( 'admin_notices', 'it_exchange_add_convertkit_nag' );

	}

}
add_action( 'it_exchange_register_addons', 'it_exchange_register_convertkit_addon' );

/**
 * Adds the ConvertKit nag if not on the correct version of ExchangeWP
 *
 * @since 1.0.0
 * @return void
*/
function it_exchange_add_convertkit_nag() {
	?>
	<div id="it-exchange-convertkit-nag" class="it-exchange-nag">
		<?php
		printf( __( 'To use the ConvertKit add-on for ExchangeWP, you must be using ExchangeWP version 1.35.0 or higher. <a href="%s">Please update now</a>.', 'LION' ), admin_url( 'update-core.php' ) );
		?>
	</div>
    <?php
}

/**
 * Loads the translation data for WordPress
 *
 * @uses load_plugin_textdomain()
 * @since 1.0.0
 * @return void
*/
function it_exchange_convertkit_set_textdomain() {
	load_plugin_textdomain( 'LION', false, dirname( plugin_basename( __FILE__  ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'it_exchange_convertkit_set_textdomain' );

/**
 * Registers Plugin with iThemes updater class
 *
 * @since 1.0.0
 *
 * @param object $updater ithemes updater object
 * @return void
*/
function ithemes_exchange_addon_convertkit_updater_register( $updater ) {
	    $updater->register( 'exchange-addon-convertkit', __FILE__ );
}
add_action( 'ithemes_updater_register', 'ithemes_exchange_addon_convertkit_updater_register' );
// require( dirname( __FILE__ ) . '/lib/updater/load.php' );

function exchange_convertkit_plugin_updater() {

	$license_check = get_transient( 'exchangewp_license_check' );

	if ($license_check->license == 'valid' ) {
		$license_key = it_exchange_get_option( 'exchangewp_licenses' );
		$license = $license_key['exchange_license'];

		$edd_updater = new EDD_SL_Plugin_Updater( 'https://exchangewp.com', __FILE__, array(
				'version' 		=> '0.0.1', 				// current version number
				'license' 		=> $license,		 		// license key (used get_option above to retrieve from DB)
				'item_id' 		=> 325,					 	  // name of this plugin
				'author' 	  	=> 'ExchangeWP',    // author of this plugin
				'url'       	=> home_url(),
				'wp_override' => true,
				'beta'		  	=> false
			)
		);
	}

}

add_action( 'admin_init', 'exchange_convertkit_plugin_updater', 0 );
