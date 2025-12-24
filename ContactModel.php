<?php
class ContactModel {
    private $contact_id;
    private $name;
    private $email;
    private $message;

    // Getters
    public function getContactId() { return $this->contact_id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getMessage() { return $this->message; }
}
?>
