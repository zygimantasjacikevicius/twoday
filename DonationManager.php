<?php
require_once 'Database.php';

class DonationManager {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function addDonation($donorName, $amount, $charityId) {
        // First, check if the charity ID exists in the charities table
        $stmt = $this->pdo->prepare("SELECT id FROM charities WHERE id = ?");
        $stmt->execute([$charityId]);
        $charity = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$charity) {
            echo "Error: Charity ID $charityId does not exist. Cannot add donation.\n";
            echo "Press any key to go back to the main menu.\n";
            fgets(STDIN); // Wait for user input to return
            return;
        }

        // If the charity exists, proceed with the donation insertion
        $stmt = $this->pdo->prepare("INSERT INTO donations (donor_name, amount, charity_id, date_time) VALUES (?, ?, ?, ?)");
        $dateTime = (new DateTime())->format('Y-m-d H:i:s');
        $stmt->execute([$donorName, $amount, $charityId, $dateTime]);
        echo "Donation added successfully with ID: " . $this->pdo->lastInsertId() . "\n";
        echo "Press any key to go back to the main menu.\n";
        fgets(STDIN); // Wait for user input to return
    }

    public function viewDonations() {
        $stmt = $this->pdo->query("SELECT * FROM donations");
        $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (count($donations) == 0) {
            echo "There are no donations yet.\n";
            echo "Press any key to go back to the main menu.\n";
            fgets(STDIN); // Wait for user input to return
            return;
        }
    
        // Display the list of donations
        echo "Donations:\n";
        foreach ($donations as $donation) {
            echo "ID: {$donation['id']}, Donor: {$donation['donor_name']}, Amount: {$donation['amount']}, Charity ID: {$donation['charity_id']}, Date: {$donation['date_time']}\n";
        }
    
        echo "Press any key to go back to the main menu.\n";
        fgets(STDIN);
    }    
}
