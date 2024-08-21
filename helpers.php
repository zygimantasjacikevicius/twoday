<?php
function waitForKeypress($message = "Press any key to go back to the main menu.") {
    echo $message . "\n";
    fgets(STDIN);
}

