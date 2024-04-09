<?php
// PDO connections to two different databases
$dsn1 = 'mysql:host=localhost;dbname=database1';
$username1 = 'username1';
$password1 = 'password1';
$options1 = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$dsn2 = 'mysql:host=localhost;dbname=database2';
$username2 = 'username2';
$password2 = 'password2';
$options2 = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// PDO connections
try {
    $pdo1 = new PDO($dsn1, $username1, $password1, $options1);
    $pdo2 = new PDO($dsn2, $username2, $password2, $options2);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

class AnalyticsController {
    private $pdo1; // PDO connection for the first database
    private $pdo2; // PDO connection for the second database

    public function __construct($pdo1, $pdo2) {
        $this->pdo1 = $pdo1;
        $this->pdo2 = $pdo2;
    }

    // Function to fetch top 10 visits and actions
    public function getTopVisitsAndActions() {
        $analyticsId = $this->getAnalyticsIdFromSession();
        $sql = "SELECT * FROM matomo_log_visit 
                LEFT JOIN matomo_log_link_visit_action ON matomo_log_visit.idvisit = matomo_log_link_visit_action.idvisit 
                LEFT JOIN matomo_log_action ON matomo_log_action.idaction = matomo_log_link_visit_action.idaction_url 
                LEFT JOIN matomo_log_conversion ON matomo_log_visit.idvisit = matomo_log_conversion.idvisit 
                LEFT JOIN matomo_log_conversion_item ON matomo_log_visit.idvisit = matomo_log_conversion_item.idvisit
                WHERE idsite = ?
                LIMIT 10";
        $stmt = $this->pdo1->prepare($sql);
        $stmt->execute([$analyticsId]);
        return $stmt->fetchAll();
    }

    // Function to fetch all visits and actions
    public function getAllVisitsAndActions() {
        $analyticsId = $this->getAnalyticsIdFromSession();
        $sql = "SELECT * FROM matomo_log_visit 
                LEFT JOIN matomo_log_link_visit_action ON matomo_log_visit.idvisit = matomo_log_link_visit_action.idvisit 
                LEFT JOIN matomo_log_action ON matomo_log_action.idaction = matomo_log_link_visit_action.idaction_url 
                LEFT JOIN matomo_log_conversion ON matomo_log_visit.idvisit = matomo_log_conversion.idvisit 
                LEFT JOIN matomo_log_conversion_item ON matomo_log_visit.idvisit = matomo_log_conversion_item.idvisit
                WHERE idsite = ?";
        $stmt = $this->pdo1->prepare($sql);
        $stmt->execute([$analyticsId]);
        return $stmt->fetchAll();
    }

    // Function to fetch count of unique visitors for a specific period and website
    public function getCountUniqueVisitors() {
        $analyticsId = $this->getAnalyticsIdFromSession();
        $sql = "SELECT COUNT(DISTINCT(idvisitor)) AS unique_visitor_ids
                FROM matomo_log_visit
                WHERE idsite = ?";
        $stmt = $this->pdo1->prepare($sql);
        $stmt->execute([$analyticsId]);
        return $stmt->fetchColumn();
    }

    // Function to fetch count of unique pageviews for a given ID Site
    public function getCountUniquePageviews() {
        $analyticsId = $this->getAnalyticsIdFromSession();
        $sql = "SELECT COUNT(DISTINCT(matomo_log_visit.idvisit)) as UNIQUE_PAGEVIEWS
                FROM matomo_log_visit
                LEFT JOIN matomo_log_link_visit_action ON matomo_log_visit.idvisit = matomo_log_link_visit_action.idvisit
                LEFT JOIN matomo_log_action ON matomo_log_action.idaction = matomo_log_link_visit_action.idaction_url
                WHERE matomo_log_visit.idsite = ?
                AND type = 1";
        $stmt = $this->pdo1->prepare($sql);
        $stmt->execute([$analyticsId]);
        return $stmt->fetchColumn();
    }

    // Function to fetch all page URL pageviews
    public function getAllPageURLPageviews() {
        $analyticsId = $this->getAnalyticsIdFromSession();
        $sql = "SELECT *
                FROM matomo_log_visit
                LEFT JOIN matomo_log_link_visit_action ON matomo_log_visit.idvisit = matomo_log_link_visit_action.idvisit
                LEFT JOIN matomo_log_action ON matomo_log_action.idaction = matomo_log_link_visit_action.idaction_url
                WHERE matomo_log_visit.idsite = ?
                AND type = 1";
        $stmt = $this->pdo1->prepare($sql);
        $stmt->execute([$analyticsId]);
        return $stmt->fetchAll();
    }

    // Function to fetch total number of pageviews for each Page URL
    public function getTotalPageviewsPerPage() {
        $analyticsId = $this->getAnalyticsIdFromSession();
        $sql = "SELECT name as page_url, COUNT(*) as hits
                FROM matomo_log_visit
                LEFT JOIN matomo_log_link_visit_action ON matomo_log_visit.idvisit = matomo_log_link_visit_action.idvisit
                LEFT JOIN matomo_log_action ON matomo_log_action.idaction = matomo_log_link_visit_action.idaction_url
                WHERE matomo_log_visit.idsite = ?
                AND type = 1
                GROUP BY page_url
                ORDER BY hits DESC";
        $stmt = $this->pdo1->prepare($sql);
        $stmt->execute([$analyticsId]);
        return $stmt->fetchAll();
    }

    // Function to fetch count and select all Outlinks from specific Page URLs
    public function getCountAndSelectOutlinks() {
        $analyticsId = $this->getAnalyticsIdFromSession();
        $sql = "SELECT lan.name, COUNT(*) 
                FROM (
                    SELECT lva.idpageview AS idpageview, lva.idvisit AS idvisit
                    FROM matomo_log_link_visit_action lva
                    LEFT JOIN matomo_log_action ON matomo_log_action.idaction = lva.idaction_url
                    WHERE idsite = ?
                    AND matomo_log_action.type = 1
                    AND matomo_log_action.name LIKE '%example.org/homepage'
                ) AS pages 
                LEFT JOIN matomo_log_link_visit_action lvn ON lvn.idpageview = pages.idpageview 
                    AND lvn.idvisit = pages.idvisit 
                LEFT JOIN matomo_log_action lan ON lan.idaction = lvn.idaction_url 
                WHERE idsite = ?
                AND lan.type = 2
                GROUP BY lan.name 
                ORDER BY COUNT(*) DESC";
        $stmt = $this->pdo1->prepare($sql);
        $stmt->execute([$analyticsId, $analyticsId]);
        return $stmt->fetchAll();
    }

    // Function to get analytics ID from session
    private function getAnalyticsIdFromSession() {
        // Implement your session logic here to get clientid and projectid
        if (!isset($_SESSION['clientid']) || !isset($_SESSION['projectid'])) {
            throw new Exception("Client ID and Project ID not found in session.");
        }
        $clientId = $_SESSION['clientid'];
        $projectId = $_SESSION['projectid'];

        // Fetch analyticsid from ClientProject table
        $sql = "SELECT AnalyticsID FROM ClientProject WHERE ClientID = ? AND ProjectID = ?";
        $stmt = $this->pdo2->prepare($sql);
        $stmt->execute([$clientId, $projectId]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new Exception("Analytics ID not found for Client ID: $clientId and Project ID: $projectId");
        }
        return $row['AnalyticsID'];
    }

    // Function to render HTML page showing top 10 results
    public function showTopResultsPage() {
        $results = $this->getTopVisitsAndActions();
        // Include HTML code to display top 10 results
        // Add a button to redirect to the page showing all results
    }

    // Function to render HTML page showing all results
    public function showAllResultsPage() {
        $results = $this->getAllVisitsAndActions();
        // Include HTML code to display all results
        // Add a button to redirect back to the page showing top 10 results
    }
}

// Example usage
session_start();
$controller = new AnalyticsController($pdo1, $pdo2);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action == 'showAll') {
        $controller->showAllResultsPage();
    } else {
        $controller->showTopResultsPage();
    }
} else {
    // Default action: show top results
    $controller->showTopResultsPage();
}
?>
