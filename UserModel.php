<?php
class UserModel {
    private $user_id;
    private $name;
    private $email;
    private $password_hash;
    private $phone;
    private $id_card;
    private $user_role;

    // Getters
    public function getUserId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPhone() { return $this->phone; }
    public function getIdCard() { return $this->id_card; }
    public function getUserRole() { return $this->user_role; }

    // Setters
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPasswordHash($password_hash) { $this->password_hash = $password_hash; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setIdCard($id_card) { $this->id_card = $id_card; }
    public function setUserRole($user_role) { $this->user_role = $user_role; }
}
?>
