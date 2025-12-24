<?php
require_once 'DatabaseConnection.php';

class TourModel {
    private $tour_id;
    private $name;
    private $description;
    private $price;
    private $duration;
    private $image_url;
    private $location;
    private $pax;
    private $db;

    public function __construct() {
        $dbConn = new DatabaseConnection();
        $this->db = $dbConn->connect();
    }

    public function getAllTours() {
        $sql = "SELECT * FROM tours";
        $result = $this->db->query($sql);
        $tours = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $tours[] = $row;
            }
        }
        return $tours;
    }
}
?>
