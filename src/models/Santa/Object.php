<?php
/**
 * The database object which helps to abstract database functions
 *
 * Can be instantiated like so:
 *
 *      use Likel\Santa\Object as SantaClause;
 *      $ss = new SantaClause();
 *
 * @package     secret-santa
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     GPL-3.0 License <https://github.com/likel/secret-santa/blob/master/LICENSE>
 * @link        https://github.com/likel/secret-santa
 * @version     1.0.0
 */
namespace Likel\Santa;

class Object
{
    private $db; // Store the database connection

    /**
     * Construct the Secret Santa object and initialise the DB
     *
     * @param array $parameters An assoc. array that holds the santa parameters
     * @return void
     */
    function __construct($parameters = array())
    {
        if(!is_array($parameters)) {
            $parameters = array();
        }

        // Defaults
        $parameters["credentials_location"] = empty($parameters["credentials_location"]) ? __DIR__ . '/../../ini/credentials.ini' : $parameters["credentials_location"];

        // Attempt to load the database from the credentials.ini file
        try {
            $this->loadDatabase($parameters["credentials_location"]);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function getDB() {
        return $this->db;
    }

    /**
     * Attempt to retrieve the credentials from the credentials file
     * and initialise the Database object
     *
     * @param array $credentials_location The credentials.ini file location
     * @return void
     * @throws \Exception If credentials are empty or not found
     */
    private function loadDatabase($credentials_location)
    {
        if(file_exists($credentials_location)) {
            $db_credentials = parse_ini_file($credentials_location, true);
            $credentials = $db_credentials["likel_db"];

            if(!empty($credentials)){
                $this->db = new \Likel\DB($credentials_location);
            } else {
                throw new \Exception('The \'likel_db\' parameter in the credentials file is empty');
            }
        } else {
            throw new \Exception('The credentials file could not be located at ' . $credentials_location);
        }
    }
}
