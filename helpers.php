<?php
function waitForKeypress() {
    echo "Press any key to go back to the main menu.\n";
    fgets(STDIN); // Wait for user input
}
