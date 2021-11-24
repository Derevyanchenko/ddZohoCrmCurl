<?php

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
			add_action( 'init', [$this, 'test'] );
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

			$response = wp_remote_post($refresh_token_url, array(
				'sslverify' => false,
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
				'body' => http_build_query( $post ),
			));

			if (  is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Something went wrong: {$error_message}";
			} else {
				$body = wp_remote_retrieve_body( $response );
				print_r( $body );
			}

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
				'sslverify' => false,
				'body' => http_build_query( $post ),
			) );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Something went wrong: {$error_message}";
			} 
			if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
				$body = wp_remote_retrieve_body( $response );
				$body_json = json_decode( $body );
				$access_token = $body_json->access_token;
				return $access_token;
			}
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

			$response = wp_remote_post($url, array(
				"headers" => array(
					'Authorization' => 'Zoho-oauthtoken ' . $access_token,
					'Content-Type' => 'application/x-www-form-urlencoded',
					'sslverify' => false,
				),
				"body" => json_encode( $post_data ),
			));

			// проверка ошибки
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Something went wrong: $error_message";
			} 

			if ( wp_remote_retrieve_response_code( $response ) === 201 ) {
				echo 'Lead was successful addded.';
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