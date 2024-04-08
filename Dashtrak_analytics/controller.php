<?php
class MatomoController {
    private $pdo;
    private $idsite;

    public function __construct($pdo, $idsite) {
        $this->pdo = $pdo;
        $this->idsite = $idsite;
    }

    public function getTotalVisits() {
        // Validate input
        if (!is_numeric($this->idsite)) {
            return false;
        }

        // Prepare SQL statement
        $stmt = $this->pdo->prepare("SELECT total_visits FROM analytics WHERE idsite = ?");
        $stmt->execute([$this->idsite]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        return $result['total_visits'];
    }

    public function getTotalUniqueVisitors() {
        // Validate input
        if (!is_numeric($this->idsite)) {
            return false;
        }

        // Prepare SQL statement
        $stmt = $this->pdo->prepare("SELECT total_unique_visitors FROM analytics WHERE idsite = ?");
        $stmt->execute([$this->idsite]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        return $result['total_unique_visitors'];
    }

    public function getTopPages() {
        // Validate input
        if (!is_numeric($this->idsite)) {
            return false;
        }

        // Prepare SQL statement
        $stmt = $this->pdo->prepare("SELECT top_pages FROM analytics WHERE idsite = ?");
        $stmt->execute([$this->idsite]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        return json_decode($result['top_pages'], true);
    }

    public function getTopReferrers() {
        // Validate input
        if (!is_numeric($this->idsite)) {
            return false;
        }

        // Prepare SQL statement
        $stmt = $this->pdo->prepare("SELECT top_referrers FROM analytics WHERE idsite = ?");
        $stmt->execute([$this->idsite]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        return json_decode($result['top_referrers'], true);
    }
}

?>
