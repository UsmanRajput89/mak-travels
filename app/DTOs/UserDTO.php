<?php 
namespace App\DTOs;

class UserDTO
{
    public $username;
    public $email;
    public $password;
    public $first_name = null; 
    public $last_name = null;   

    public function __construct($username, $email, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }
}
