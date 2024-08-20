<?php
require_once 'Database.php';

class CharityManager {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function charityExistsByName($name) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM charities WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetchColumn() > 0;  // Returns true if the charity name exists
    }

    public function addCharity($name, $email) {
        // Check for duplicate charity names
        if ($this->charityExistsByName($name)) {
            echo "Charity with the name '$name' already exists. Cannot add duplicate.\n";
            return;
        }
    
        // Proceed with adding if no duplicates
        $stmt = $this->pdo->prepare("INSERT INTO charities (name, representative_email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);
        echo "Charity added successfully with ID: " . $this->pdo->lastInsertId() . "\n";
        echo "Press any key to go back to the main menu.\n";
        fgets(STDIN); // Wait for user input to return
    }    

    public function editCharity() {
        // Show all charities
        $stmt = $this->pdo->query("SELECT id, name, representative_email FROM charities");
        $charities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (count($charities) == 0) {
            echo "No charities available to edit.\n";
            echo "Press any key to go back to the main menu.\n";
            fgets(STDIN); // Wait for user input to return
        }
    
        // Display the list of charities with IDs
        echo "Available charities:\n";
        foreach ($charities as $charity) {
            echo "ID: {$charity['id']}, Name: {$charity['name']}, Email: {$charity['representative_email']}\n";
        }
    
        // Ask the user to select a charity ID to edit
        echo "Enter the ID of the charity you want to edit: ";
        $id = trim(fgets(STDIN));
    
        // Check if the ID exists
        $stmt = $this->pdo->prepare("SELECT id, name, representative_email FROM charities WHERE id = ?");
        $stmt->execute([$id]);
        $charity = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$charity) {
            echo "Charity with ID $id not found.\n";
            return;
        }
    
        // Ask what to edit: name, email, both, or go back
        echo "What would you like to edit?\n";
        echo "1. Name\n";
        echo "2. Email\n";
        echo "3. Both\n";
        echo "4. Go back\n";
        echo "Enter your choice: ";
        $choice = trim(fgets(STDIN));
    
        switch ($choice) {
            case '1': // Edit name
                echo "Enter the new charity name: ";
                $newName = trim(fgets(STDIN));
                $this->updateCharityName($id, $newName);
                break;
    
            case '2': // Edit email
                echo "Enter the new representative email: ";
                $newEmail = trim(fgets(STDIN));
                if (Validator::isValidEmail($newEmail)) {
                    $this->updateCharityEmail($id, $newEmail);
                } else {
                    echo "Invalid email format.\n";
                }
                break;
    
            case '3': // Edit both name and email
                echo "Enter the new charity name: ";
                $newName = trim(fgets(STDIN));
                echo "Enter the new representative email: ";
                $newEmail = trim(fgets(STDIN));
                if (Validator::isValidEmail($newEmail)) {
                    $this->updateCharity($id, $newName, $newEmail);
                } else {
                    echo "Invalid email format.\n";
                }
                break;
    
            case '4': // Go back
                echo "Going back.\n";
                return;
    
            default:
                echo "Invalid choice. Going back.\n";
                return;
        }
    
        echo "Charity updated successfully.\n";
        echo "Press any key to go back to the main menu.\n";
        fgets(STDIN); // Wait for user input to return
    }
    
    private function updateCharityName($id, $name) {
        $stmt = $this->pdo->prepare("UPDATE charities SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
    }
    
    private function updateCharityEmail($id, $email) {
        $stmt = $this->pdo->prepare("UPDATE charities SET representative_email = ? WHERE id = ?");
        $stmt->execute([$email, $id]);
    }
    
    private function updateCharity($id, $name, $email) {
        $stmt = $this->pdo->prepare("UPDATE charities SET name = ?, representative_email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $id]);
    }
    
    public function deleteCharity() {
        // Show all charities
        $stmt = $this->pdo->query("SELECT id, name, representative_email FROM charities");
        $charities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (count($charities) == 0) {
            echo "There are no charities to delete.\n";
            echo "Press any key to go back to the main menu.\n";
            fgets(STDIN); // Wait for user input to return
        }
    
        // Display the list of charities with IDs
        echo "Charities:\n";
        foreach ($charities as $charity) {
            echo "ID: {$charity['id']}, Name: {$charity['name']}, Email: {$charity['representative_email']}\n";
        }
    
        // Ask the user to select a charity ID to delete
        echo "Enter the ID of the charity you want to delete: ";
        $id = trim(fgets(STDIN));
    
        // Check if the ID exists
        $stmt = $this->pdo->prepare("SELECT id FROM charities WHERE id = ?");
        $stmt->execute([$id]);
        $charity = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$charity) {
            echo "Charity with ID $id not found.\n";
            echo "Press any key to go back to the main menu.\n";
            fgets(STDIN); // Wait for user input to return
        }
    
        // Proceed with deletion
        $stmt = $this->pdo->prepare("DELETE FROM charities WHERE id = ?");
        $stmt->execute([$id]);
        echo "Charity deleted successfully.\n";
        echo "Press any key to go back to the main menu.\n";
        fgets(STDIN); // Wait for user input to return
    }
    
    public function viewCharities() {
        $stmt = $this->pdo->query("SELECT id, name, representative_email FROM charities");
        $charities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (count($charities) == 0) {
            echo "There are no charities yet.\n";
            echo "Press any key to go back to the main menu.\n";
            fgets(STDIN); // Wait for user input to return
            return;
        }
    
        // Display the list of charities with IDs
        echo "Charities:\n";
        foreach ($charities as $charity) {
            echo "ID: {$charity['id']}, Name: {$charity['name']}, Email: {$charity['representative_email']}\n";
        }
    
        // Ask the user to press any key to go back
        echo "Press any key to go back to the main menu.\n";
        fgets(STDIN); // Wait for user input to return
    }    

    public function importFromCSV($filePath) {
        if (!file_exists($filePath)) {
            echo "File not found: $filePath\n";
            return;
        }
    
        // Open the file for reading with the correct delimiter
        $file = fopen($filePath, 'r');
        
        if ($file === false) {
            echo "Unable to open file: $filePath\n";
            return;
        }
    
        // Read the first line to detect the delimiter
        $firstLine = fgets($file);
        rewind($file); // Reset file pointer to the beginning after reading the first line
    
        // Detect delimiter by checking for the presence of a comma or semicolon in the first line
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
    
        // Read the CSV line by line using the detected delimiter
        $lineNumber = 0;
        while (($line = fgetcsv($file, 0, $delimiter)) !== FALSE) {
            $lineNumber++;
    
            // Trim any whitespace from the fields
            $line = array_map('trim', $line);
    
            // Each line in the CSV should contain: Charity Name, Representative Email
            if (count($line) < 2 || empty($line[0]) || empty($line[1])) {
                echo "Invalid format in CSV file on line $lineNumber: less than 2 fields or empty fields\n";
                continue;
            }
    
            list($name, $email) = $line;
    
            // Validate the email before adding to the database
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Invalid email format for charity '$name' on line $lineNumber: $email\n";
                continue;
            }
    
            // Check for duplicate charity names
            if ($this->charityExistsByName($name)) {
                echo "Duplicate charity name on line $lineNumber: '$name'. Skipping.\n";
                continue;
            }
    
            // Add the charity to the database
            $this->addCharity($name, $email);
        }
    
        // Close the file after reading
        fclose($file);
        echo "CSV import completed.\n";
        echo "Press any key to go back to the main menu.\n";
        fgets(STDIN); // Wait for user input to return
    }
        
}
