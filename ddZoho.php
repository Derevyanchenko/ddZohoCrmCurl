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
			add_action( 'init', [$this, 'getAllLeads'] );
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

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $access_token_url );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded'] );

			$response = curl_exec( $ch );
			$response = json_decode( $response, true );

			$access_token = $response['access_token'];
			return $access_token;
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

			$post_data = json_encode( $post_data );

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


			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Zoho-oauthtoken ' . $access_token,
				'Content-Type: application/x-www-form-urlencoded'
			) );

			$response = curl_exec( $ch );
			$response = json_decode( $response, true );

			echo '<pre>';
			print_r($response);
			echo '</pre>';
		}


	}

	$ddZohoCrm = new ddZohoCrm();

}