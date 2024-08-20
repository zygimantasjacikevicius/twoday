<?php
require_once 'Charity.php';
require_once 'Donation.php';
require_once 'CharityManager.php';
require_once 'DonationManager.php';
require_once 'Validator.php';
require_once 'Database.php';

// Initialize database tables
Database::initialize();

$charityManager = new CharityManager();
$donationManager = new DonationManager();

while (true) { // Keep showing the menu until the user decides to exit
    echo "Charity & Donation CLI\n";
    echo "1. Add Charity\n";
    echo "2. Edit Charity\n";
    echo "3. Delete Charity\n";
    echo "4. View Charities\n";
    echo "5. Add Donation\n";
    echo "6. View Donations\n";
    echo "7. Import Charities from CSV\n";
    echo "8. Exit\n";  // Add an exit option
    echo "Select an option: ";
    $choice = trim(fgets(STDIN));

    switch ($choice) {
        case 1:
            echo "Enter charity name: ";
            $name = trim(fgets(STDIN));
            echo "Enter representative email: ";
            $email = trim(fgets(STDIN));
            if (Validator::isValidEmail($email)) {
                $charityManager->addCharity($name, $email);
            } else {
                echo "Invalid email.\n";
            }
            break;

        case 2:
            $charityManager->editCharity();
            break;

        case 3:
            $charityManager->deleteCharity();  // Now shows available charities before asking for ID
            break;

        case 4:
            $charityManager->viewCharities();
            break;

        case 5:
            echo "Enter donor name: ";
            $donorName = trim(fgets(STDIN));
            echo "Enter donation amount: ";
            $amount = trim(fgets(STDIN));
            echo "Enter charity ID: ";
            $charityId = trim(fgets(STDIN));
            if (Validator::isValidAmount($amount)) {
                $donationManager->addDonation($donorName, $amount, $charityId);
            } else {
                echo "Invalid amount.\n";
            }
            break;

        case 6:
            $donationManager->viewDonations();
            break;

        case 7:
            echo "Enter CSV file path: ";
            $filePath = trim(fgets(STDIN));
            $charityManager->importFromCSV($filePath);
            break;

        case 8:
            echo "Exiting the application. Goodbye!\n";
            exit;

        default:
            echo "Invalid option.\n";
            break;
    }
}
