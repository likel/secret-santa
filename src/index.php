<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
