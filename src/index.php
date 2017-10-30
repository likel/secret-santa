<?php
/**
 * An example and the page that output's the secret santas
 *
 * @package     secret-santa
 * @author      Liam Kelly <https://github.com/likel>
 * @copyright   2017 Liam Kelly
 * @license     GPL-3.0 License <https://github.com/likel/secret-santa/blob/master/LICENSE>
 * @link        https://github.com/likel/secret-santa
 * @version     1.0.0
 */
require_once('autoload.php');

use Likel\Santa\Object as SantaClause;
$ss = new SantaClause();

$ss->truncate();

// Get the santa's DB id using the $_GET['santa'] variable
$santa = $ss->isSanta();

if(!empty($santa['id'])) {
    // Could have a CSS handler and cacher, but this program is not about that
    echo '<head><title>Secret Santa</title><link rel="stylesheet" type="text/css" href="css/page.css"></head>';

    // Assigns a giftee if not already assigned
    $giftee = $ss->assignSanta($santa['id']);

    // Assigns a keyword if not already assigned
    $keyword = $ss->assignKeyword($santa['id']);

    echo "<body><div class='container'><div class='saying'>";
    echo $santa['santa_name'] . ". You must give a gift to <strong>" . $giftee . "</strong> and the inspiration is <strong>" . $keyword . "</strong>.";
    echo "</div></div></body>";
} else {
    // No santa found with that keyword
    die("You aren't a santa.");
}
