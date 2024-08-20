<?php
class Donation {
    private $id;
    private $donorName;
    private $amount;
    private $charityId;
    private $dateTime;

    public function __construct($id, $donorName, $amount, $charityId) {
        $this->id = $id;
        $this->donorName = $donorName;
        $this->setAmount($amount);
        $this->charityId = $charityId;
        $this->dateTime = new DateTime();
    }

    public function getId() {
        return $this->id;
    }

    public function getDonorName() {
        return $this->donorName;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCharityId() {
        return $this->charityId;
    }

    public function getDateTime() {
        return $this->dateTime->format('Y-m-d H:i:s');
    }

    public function setAmount($amount) {
        if (!is_numeric($amount) || $amount <= 0) {
            throw new InvalidArgumentException("Donation amount must be a positive number.");
        }
        $this->amount = $amount;
    }
}
