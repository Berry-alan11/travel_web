<?php
class DestinationModel {
    private $id;
    private $name;
    private $slug;
    private $country;
    private $description;
    private $best_time_to_visit;
    private $image_url;

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getSlug() { return $this->slug; }
    public function getCountry() { return $this->country; }
    public function getDescription() { return $this->description; }
    public function getBestTimeToVisit() { return $this->best_time_to_visit; }
    public function getImageUrl() { return $this->image_url; }
}
?>
