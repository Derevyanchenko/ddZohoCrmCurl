<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

require 'ddWooAmoCrm/amo.php';

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}


/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

require 'vendor/autoload.php';
// use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
// use zcrmsdk\oauth\ZohoOAuth;
// use zcrmsdk\crm\bulkcrud\ZCRMBulkCallBack;
// use zcrmsdk\crm\bulkcrud\ZCRMBulkCriteria;
// use zcrmsdk\crm\bulkcrud\ZCRMBulkQuery;
// use zcrmsdk\crm\bulkcrud\ZCRMBulkRead;


/**
 * 
 * zoho
 * 
 */

if ( ! class_exists( 'ddZohoCrm' ) ) {

	class ddZohoCrm
	{
		public function __construct()
		{
			add_action( 'init', [$this, 'generateAccessToken'] );
		}

		/**
		 *  Generate refresh token 
		 **/ 
		public function generateRefreshToken()
		{

			$accounts_url = 'https://accounts.zoho.eu';
			$refresh_token_url = "{$accounts_url}/oauth/v2/token";

			$post = [
				'code' => '1000.97b057323af7d4848a111526e1945612.933ad08aeb697c773eead6211046c41a',
				'redirect_uri' => 'https://accounts.zoho.eu',
				'client_id' => '1000.6Z97A1GDP6YR7TWCU4X3LIQZ1HMNAZ',
				'client_secret' => '32fac628a3280bd8a98fd4a6a7e7e07debf5c38403',
				'grant_type' => 'authorization_code',
			];

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $refresh_token_url );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded'] );
			$response = curl_exec( $ch );
		}
		/**
		 *  Generate Access token 
		 **/ 
		public function generateAccessToken()
		{
			$accounts_url = 'https://accounts.zoho.eu';
			$access_token_url = "{$accounts_url}/oauth/v2/token";

			$post = [
				'refresh_token' => '1000.28fc0925911b81f6746f830637bdfe50.5aa9994ca31d59b824ee147dd913b0b2',
				'client_id' => '1000.6Z97A1GDP6YR7TWCU4X3LIQZ1HMNAZ',
				'client_secret' => '32fac628a3280bd8a98fd4a6a7e7e07debf5c38403',
				'grant_type' => 'refresh_token',
			];

			$response = wp_remote_post( $access_token_url, array(
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
				'body' => http_build_query( $post ),
			) );

			echo '<pre>';
			print_r( $response );
			echo '</pre>';

			// $ch = curl_init();
			// curl_setopt( $ch, CURLOPT_URL, $access_token_url );
			// curl_setopt( $ch, CURLOPT_POST, 1 );
			// curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
			// curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			// curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
			// curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded'] );

			// $response = curl_exec( $ch );
			// $response = json_decode( $response, true );

			// $access_token = $response['access_token'];
			// return $access_token;

			// var_dump( $access_token );
			// wp_die();
		}

		/**
		 * Test method (use for debug)
		 */ 
		public function test()
		{
			$this->insertLeads(
				'Test Company',
				'Danil',
				'Dere',
				'daniltest@gmail.com',
				'+36456456745'
			);
		}

		/**
		 * Insert Leads (records) 
		 **/ 
		public function insertLeads( $company, $firstName, $lastName, $email, $phone )
		{
			$api_domain = 'https://www.zohoapis.eu';
			$url = "{$api_domain}/crm/v2/Leads";
			$access_token = $this->generateAccessToken();

			$post_data = [
				'data' => [
					[
						"Company" => $company,
						"First_Name" => $firstName,
						"Last_Name" => $lastName,
						"Email" => $email,
						"Phone" => $phone,
					],
				],
				'trigger' => [
					"approval",
					"workflow",
					"blueprint"
				]
			];

			// $response = wp_remote_post($url, array(
			// 	"headers" => array(
			// 		'Authorization' => 'Zoho-oauthtoken ' . $access_token,
			// 		'Content-Type' => 'application/x-www-form-urlencoded',
			// 	),
			// 	"body" => json_encode( $post_data ),
			// ));

			// // проверка ошибки
			// if ( is_wp_error( $response ) ) {
			// 	$error_message = $response->get_error_message();
			// 	echo "Что-то пошло не так: $error_message";
			// } else {
			// 	echo 'Ответ: <pre>';
			// 	print_r( $response );
			// 	echo '</pre>';
			// }

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Zoho-oauthtoken ' . $access_token,
				'Content-Type: application/x-www-form-urlencoded'
			) );
			

			$response = curl_exec( $ch );
			$response = json_decode( $response, true );

			if ( $response['data'][0]['code'] == 'SUCCESS' ) {
				echo 'Lead was added successfully.';
			} else {
				echo 'Something went wrong.';
			}

		}

		/**
		 * Get all Leads Leads (records) 
		 **/ 
		public function getAllLeads()
		{
			$api_domain = 'https://www.zohoapis.eu';
			$url = "{$api_domain}/crm/v2/Leads";
			$access_token = $this->generateAccessToken();


			$response = wp_remote_get($url, array(
				'headers' => array(
					'Authorization' => 'Zoho-oauthtoken ' . $access_token,
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
			));


			if ( is_wp_error( $response ) ){
				echo $response->get_error_message();
			}
			if( wp_remote_retrieve_response_code( $response ) === 200 ){
				$body = wp_remote_retrieve_body( $response );
				$result = json_decode( $body, true )['data'];
				echo '<pre>';
				print_r( $result );
				echo '</pre>';
			} 

		}


	}

	$ddZohoCrm = new ddZohoCrm();

}