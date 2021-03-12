<?php

// namespace
namespace Nettools\Ovh;




// helper class for OVH api authentication
class AuthHelper{
	
	/**
	 * Get a consumer key and a validation url ; ovh account should be link to consumer key by visiting the url
	 *
	 * @param string $appKey
	 * @param string $appSecret
	 * @param string $redirection Url of application to redirect to after successful ovh account linking
	 * @param string[] $verbs HTTP verbs array (GET, POST, etc.)
	 * @param string $endpoint 
	 *
	 * For information, this verbs array is later transformed to the following rights structure
	 *	[
	 * 		{
	 * 			"method": "GET",
	 *			"path": "/*"
	 *		}
	 *	]
	 */
	static function authorize($appKey, $appSecret, $redirection, array $verbs, $endpoint = 'ovh-eu')
	{
		session_start();
		$_SESSION['consumer_key'] = NULL;
		
		
		// prepare rights
		$rights = array();
		foreach ( $verbs as $grant )
			$rights[] = (object) [
								'method'    => $grant,
								'path'      => "/*"
							];
		
				
		// Get credentials
		$conn = new \Ovh\Api($appKey, $appSecret, $endpoint);
		$credentials = $conn->requestCredentials($rights, $redirection);
		
		// store consumer key in session, to be later retrieved back from ovh url
		$_SESSION['consumer_key'] = $credentials["consumerKey"];
		return $credentials["validationUrl"];
	}
	
	
	
	/**
	 * Get the consumer key after successfull authentication and ovh account linking
	 *
	 * @return string
	 */
	static function authorized()
	{
		session_start();
		return $_SESSION['consumer_key'];
	}
}

?>