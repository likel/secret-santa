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

    /**
     * Returns if the get parameter is a santa
     *
     * @return bool
     */
    public function isSanta() {
        if(empty($_GET['santa'])) {
            return false;
        }

        $santa_object = $this->getSanta($_GET['santa']);

        return !empty($santa_object) ? $santa_object : false;
    }

    /**
     * Get the list of Santas from the DB
     *
     * @param bool $remaining Whether to return remaining santas or all
     * @return array
     */
    public function getSantas($remaining = false) {
        if(!$remaining) {
            $this->db->query("
                SELECT * FROM {$this->db->getTableName("santas")}
            ");
        } else {
            $this->db->query("
                SELECT a.* FROM {$this->db->getTableName("santas")} AS a
                LEFT JOIN {$this->db->getTableName("kids")} AS b ON a.id = b.gifter
                WHERE b.gifter IS NULL
            ");
        }

        // Execute and return results
        return $this->db->results();
    }

    /**
     * Get a single santa from the DB
     *
     * @param string $secret_phrase The phrase attached to the santa
     * @return array|false
     */
    public function getSanta($secret_phrase = '') {
        if(empty($secret_phrase)) {
            return false;
        }

        // Setup the query
        $this->db->query("
            SELECT * FROM {$this->db->getTableName("santas")}
            WHERE secret_phrase = :secret_phrase
        ");

        $this->db->bind(':secret_phrase', $secret_phrase);

        // Execute and return results
        return $this->db->result();
    }

    /**
     * Assigns a giftee to a santa if one isn't already set
     *
     * @param int $santa_id The santa's ID
     * @return void
     */
    public function assignSanta($santa_id) {
        if(empty($this->hasGiftee($santa_id))) {
            $santas_remaining = $this->getSantas(true);

            $santa_id_to_set = $santa_id;
            while($santa_id_to_set == $santa_id) {
                $random_santa = array_rand($santas_remaining);
                $santa_id_to_set = $santas_remaining[$random_santa]['id'];
            }

            // Setup the query
            $this->db->query("
                INSERT INTO {$this->db->getTableName("kids")} (gifter, gifted)
                VALUES (:gifter, :gifted)
            ");

            $this->db->bind(':gifter', $santa_id);
            $this->db->bind(':gifted', $santa_id_to_set);

            // Execute
            $this->db->execute();
        }
    }

    /**
     * Assigns a keyword to a santa if one isn't already set
     *
     * @param int $santa_id The santa's ID
     * @return void
     */
    public function assignKeyword($santa_id) {
        if(empty($this->hasKeyword($santa_id))) {
            $keywords_remaining = $this->getKeywords(true);

            $keyword_id = array_rand($keywords_remaining);
            $keyword = $keywords_remaining[$keyword_id]['santa_keyword'];

            // Setup the query
            $this->db->query("
                INSERT INTO {$this->db->getTableName("keywords")} (santa_id, keyword)
                VALUES (:santa_id, :keyword)
            ");

            $this->db->bind(':santa_id', $santa_id);
            $this->db->bind(':keyword', $keyword);

            // Execute
            $this->db->execute();
        }
    }

    /**
     * Get the list of Santa's keywords from the DB
     *
     * @param bool $remaining Whether to return remaining keywords or all
     * @return array
     */
    public function getKeywords($remaining = false) {
        if(!$remaining) {
            $this->db->query("
                SELECT * FROM {$this->db->getTableName("keywords")}
            ");
        } else {
            $this->db->query("
                SELECT a.* FROM {$this->db->getTableName("santas")} AS a
                LEFT JOIN {$this->db->getTableName("keywords")} AS b ON a.id = b.santa_id
                WHERE b.santa_id IS NULL
            ");
        }

        // Execute and return results
        return $this->db->results();
    }

    /**
     * Returns if the santa has a giftee already
     *
     * @param int $santa_id The santa's ID
     * @return int
     */
    public function hasGiftee($santa_id) {
        // Setup the query
        $this->db->query("
            SELECT b.* FROM {$this->db->getTableName("santas")} AS a
            JOIN {$this->db->getTableName("kids")} AS b ON a.id = b.gifter
            WHERE a.id = :santa_id
        ");

        $this->db->bind(':santa_id', $santa_id);

        // Execute and return results
        return $this->db->result();
    }

    /**
     * Returns if the santa has a keyword already
     *
     * @param int $santa_id The santa's ID
     * @return int
     */
    public function hasKeyword($santa_id) {
        // Setup the query
        $this->db->query("
            SELECT b.* FROM {$this->db->getTableName("santas")} AS a
            JOIN {$this->db->getTableName("keywords")} AS b ON a.id = b.santa_id
            WHERE a.id = :santa_id
        ");

        $this->db->bind(':santa_id', $santa_id);

        // Execute and return results
        return $this->db->result();
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
