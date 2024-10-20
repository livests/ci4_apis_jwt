<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use App\Models\TokenBlacklisted;

class AuthorController extends ResourceController
{
    protected $modelName = "App\Models\Phase3\AuthorModel";
    protected $format = "json";

    // Register Method
    // [POST] -> name, email, password, phone_no
    public function registerAuthor(){

        $validationRules = array(
            "name" => array(
                "rules" => "required|min_length[3]",
                "errors" => array(
                    "required" => "Name fields is required",
                    "min_length" => "Author Name should have atleast 3 characters"
                 )
            ),
            "email" => array(
                "rules" => "required|min_length[4]|is_unique[authors.email]",
                "errors" => array(
                    "required" => "Author Email is required",
                    "min_length" => "Email has alteast 4 characters in length",
                    "is_unique" => "Email Already Exists"
                )
            ),
            "password" => array( 
                "rules" => "required|min_length[5]",
                "errors" => array( 
                    "required" => "Password is required",
                    "min_length" => "Password will be atleast of 5 character"
                 )
             )
        );

        if(!$this->validate($validationRules)){

           return $this->respond(array(
            "status" => false,
            "message" => "Form submission failed",
            "errors" => $this->validator->getErrors()
           ));

        }

        $AuthorData = [
            "name" => $this->request->getPost("name"),
            "email" => $this->request->getPost("email"),
            "password" => password_hash($this->request->getPost("password"), PASSWORD_DEFAULT)
        ];

        //if($this->model->insert())
        if($this->model->save($AuthorData)){
            return $this->respond([
                "status" => true,
                "message" => "Author Registered Successfully"
            ]);
        } else{
            return $this->respond([
                "status" => false,
                "message" => "Failed to register Author"
            ]);

        }
    }

    // Login Method
    // [POST] -> email, password
    public function loginAuthor(){
        // Validate input
        $validationRules = [
            "email" => [
                "rules" => "required"
            ],
            "password" => [
                "rules" => "required"
            ]
        ];
    
        if (!$this->validate($validationRules)) {
            return $this->respond([
                "status" => false,
                "message" => "Fields are required",
                "errors" => $this->validator->getErrors()
            ]);
        }
    
        // Check if author exists by email
        $authorData = $this->model->where('email', $this->request->getVar("email"))->first();
    
        if ($authorData) {
            // Author exists, verify password
            if (password_verify($this->request->getVar("password"), $authorData["password"])) {
                // Password matches, generate JWT token
                $key = getenv("JWT_KEY");
                $payloadData = [
                    "iss" => "localhost",
                    "aud" => "localhost",
                    "iat" => time(),
                    "exp" => time() + 3600, // Token expires in 1 hour
                    "user" => [
                        "id" => $authorData['id'],
                        "email" => $authorData['email']
                    ]
                ];
    
                // Generate the token
                $token = JWT::encode($payloadData, $key, "HS256");
    
                // Respond with the token
                return $this->respond([
                    "status" => true,
                    "message" => "User logged in successfully",
                    "token" => $token
                ]);
            } else {
                // Password is incorrect
                return $this->respond([
                    "status" => false,
                    "message" => "Login failed due to incorrect password"
                ]);
            }
        } else {
            // Email not found
            return $this->respond([
                "status" => false,
                "message" => "Login failed due to incorrect email address"
            ]);
        }
    }
    // Profile Method
    // [GET] -> Protected Method -> Valid Token in Request Header
    public function authorProfile(){
        
        return $this->respond([
            "status" => true,
            "message" => "Author Profile Information",
            "data" => $this->request->userData
        ]);
    }

    // Logout Method
    // [GET] -> Protected Method -> Valid Token in Request Header
    public function logoutAuthor(){
        
        $token = $this->request->jwtToken;

        $tokenBlackListedObject = new TokenBlacklisted();

        if($tokenBlackListedObject->insert([
            "token" => $token
        ])){

            return $this->respond([
                "status" => true,
                "message" => "Author Successfully Logged Out"
            ]);
        }else{

            return $this->respond([
                "status" => false,
                "message" => "Logout failed"
            ]);
        }
    }
}