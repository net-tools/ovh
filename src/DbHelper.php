<?php

// namespace
namespace Nettools\Ovh;




// Helper class to dump OVH database
class DbHelper{
	
	/** 
	 * Dump a database
	 *
	 * @param \Ovh\Api $api
	 * @param string $service OVH service (usually, the web domain)
	 * @param string $db database name (xxx.mysql.db)
	 * @return string Returns a task id or NULL if an error occurs
	 */
	static function databaseDump(\Ovh\Api $api, $service, $db)
	{
		// ask for a "now" dump
		$task = $api->post("/hosting/web/$service/database/$db/dump", 
							[
								'date' => 'now'
							]);

		// dump to be created 
		if ( is_array($task) && array_key_exists('objectId', $task) )
			return $task['objectId'];
		else
			return NULL;
	}
	
	
	
	
	/** 
	 * Check a database dump status ; if available, download it and halt script
	 *
	 * @param \Ovh\Api $api
	 * @param string $service OVH service (usually, the web domain)
	 * @param string $db database name (xxx.mysql.db)
	 * @param string $id Task id returned by `databaseDump`
	 * @return string Returns a status string if dump is not ready, or redirects to download url
	 */
	static function databaseDumpStatus(\Ovh\Api $api, $service, $db, $id)
	{
		$task = $api->get("/hosting/web/$service/database/$db/dump/$id");

		if ( is_array($task) && array_key_exists('status', $task) )
			if ( $task['status'] == 'created' )
			{
				header('Location: ' . $task['url']);
				die();
			}
			else
				return $task['status'];
		else
			return 'unknown';
	}
	
		
	
	/** 
	 * Remove a database dump
	 *
	 * @param \Ovh\Api $api
	 * @param string $service OVH service (usually, the web domain)
	 * @param string $db database name (xxx.mysql.db)
	 * @param string $id Task id returned by `databaseDump`
	 */
	static function databaseDumpDelete(\Ovh\Api $api, $service, $db, $id)
	{
		$api->delete("/hosting/web/$service/database/$db/dump/$id");
	}
}

?>