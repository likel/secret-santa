<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('autoload.php');

use Likel\Santa\Object as SantaClause;
$ss = new SantaClause();

// Get the santa's DB id using the $_GET['santa'] variable
$santa = $ss->isSanta();

if(!empty($santa['id'])) {
    // Could have a CSS handler and cacher, but this program is not about that
    echo '<head><title>Secret Santa</title><link rel="stylesheet" type="text/css" href="css/page.css"></head>';

    // Assigns a giftee if not already assigned
    $ss->assignSanta($santa['id']);

    // Assigns a keyword if not already assigned
    $ss->assignKeyword($santa['id']);

    echo "<body><div class='container'><div class='saying'>";
    echo $santa['santa_name'] . ". You must give a gift to " . " " . " and the gift keyword is " . " " . ".";
    echo "</div></div></body>";
} else {
    // No santa found with that keyword
    die("You aren't a santa.");
}

/**
 * The index homepage;
 *
 *
 * @file       index.php (landing)
 * @site       Inbound
 * @copyright  Liam Julius Kelly
 * @version    v1.0.0
 * @link       http://www.inbound.com.au/index.php
 */

/*include "database.class.php";
$DB = new Database();

$link = $_GET['l'];

$DB->query("SELECT * FROM rand_users WHERE link = '$link'");
$isitaperson = $DB->result();

// got somebody
if($isitaperson){
		if($isitaperson["whohasthem"] != ""){
				echo "Hi ".$isitaperson["name"] . "! You have: " . $isitaperson["whohasthem"];
		} else {
				$DB->beginTransaction();
				$DB->query("SELECT * FROM rand_users WHERE link != '$link' AND taken = 0");
				$choices = $DB->results();

				$mychoice = array_rand($choices, 1);

				$DB->query("UPDATE rand_users SET whohasthem = '{$choices[$mychoice]["name"]}' WHERE link = '$link'");
				$DB->execute();

                $DB->query("UPDATE rand_users SET taken = 1 WHERE name = '{$choices[$mychoice]["name"]}'");
    			$DB->execute();

				$DB->endTransaction();

				echo "Hi ".$isitaperson["name"] . "! You have: " . $choices[$mychoice]["name"];
		}
} else {
		echo "I don't think so.";
}*/
