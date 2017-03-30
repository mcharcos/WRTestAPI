<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/lib/API.class.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/lib/APIKey.class.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/lib/Category.class.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/lib/Books.class.php';
class WRAPI extends API
{
    protected $User;

    // Constructor verifies user key since lack of client context in server for api
    public function __construct($request, $origin) {
        parent::__construct($request);
        
        // Abstracted out for example
        $APIKey = new APIKey();

        if (!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
            throw new Exception('Invalid API Key');
        } else if (array_key_exists('token', $this->request) && !$APIKey->verifyUser($this->request['token'])) {
            throw new Exception('Invalid User Token');
        }

        $this->User = $APIKey->getUser();
    }

    // Category API
     protected function category() {
        if ($this->method == 'GET' || $this->method == 'POST') {
            $category = new Category();
            
            switch($this->verb) {
                case "list":
                    $result = $category->get_list();
                    break;
                case "details":
                    $result = $category->get_details($this->args[0]);
                    break;
                case "booksbyid":
                    $result = $category->get_books_byid($this->args[0]);
                    break;
                case "books":
                    $result = $category->get_books_byname($this->args[0]);
                    break;
                default:
                    return "Action ".$this->verb." for category does not exist. Available actions are list, details or books.";
                    break;
            }
            return json_encode($result);
        } else {
            return "Only accepts GET or POST requests";
        }
     }
     
    // Books API
     protected function books() {
        if ($this->method == 'GET' || $this->method == 'POST') {
            
            $book = new Books();
            switch($this->verb) {
                case "list":
                    $result = $book->get_list();
                    break;
                case "details":
                    $result = $book->get_details($this->args[0], false);
                    break;
                case "detailsbyuuid":
                    $result = $book->get_details($this->args[0], true);
                    break;
                default:
                    return "Action ".$this->verb." for books does not exist. Available actions are list or details.";
                    break;
            }
            
            
            return json_encode($result);
        } else {
            return "Only accepts GET or POST requests";
        }
     }
 }
 
 ?>