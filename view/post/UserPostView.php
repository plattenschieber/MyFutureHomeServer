<?php

/*
 * Autor Bertram Buchardt
 * Alle Rechte vorbehalten
 * 
 */
require_once ROOT . "/view/ParentView.php";

/**
 * Description of HompePage
 *
 * @author Bertram
 */
class UserPostView extends ParentView {

    private $user;

    /**
     * Erzeugt einen Homepage View
     * @param stdClass $_content Beinhaltet die Inhaltsfelder
     */
    public function process() {
        $this->handleRequest();
        echo $this->genResponse();
    }

    /*     * @Override
     * Verarbeite POST/GET Eingaben
     * @return void
     */

    protected function handleRequest() {
        parent::handleRequest();
        $this->user = $_SESSION[obj]->getUser();
        if (!is_a($this->user, "User")) {
            $this->setState(State::ERROR);
            $this->addError(ERROR_NOUSER_EXCEPTION . "@" . filter_input(INPUT_SERVER, 'PHP_SELF'));
            return;
        }
        $this->handlePost();
    }

    private function handlePost() {
        $name = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);
        $firstName = filter_input(INPUT_POST, 'firstName', FILTER_DEFAULT);
        $sex = filter_input(INPUT_POST, 'sex', FILTER_DEFAULT);
        $job = filter_input(INPUT_POST, 'job', FILTER_DEFAULT);
        $birthdate = filter_input(INPUT_POST, 'birthdate', FILTER_DEFAULT);
        
        $postalCode = filter_input(INPUT_POST, 'postalCode', FILTER_DEFAULT);
        $children = filter_input(INPUT_POST, 'children', FILTER_SANITIZE_NUMBER_INT);
        $city = filter_input(INPUT_POST, 'city', FILTER_DEFAULT);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
       
        
        $this->user->setName($name);
        $this->user->setFirstName($firstName);
        $this->user->setSex($sex);
        $this->user->setJob($job);
        $this->user->setBirthdate($birthdate);
        $this->user->setPostalCode($postalCode);
        $this->user->setCity($city);
        $this->user->setEmail($email);
        $this->user->setChildren($children);
        
        $location = Func::getLocation($postalCode . " " . $city);
        $this->user->setLat($location->lat);
        $this->user->setLng($location->lng);
      
        if ($this->user->saveToDB()) {
            $this->setState(State::SUCCESS);
            $this->res->phoneId = $this->user->getPhoneId();
        } else {
            $this->setState(State::ERROR);
            $this->res->phoneId = $this->user->getPhoneId();
            $this->addError(ERROR_NO_VALID_USER_EXCEPTION . "@" . filter_input(INPUT_SERVER, 'PHP_SELF'));
        }

        $this->res->warnings = $this->user->getWarnings();
    }

}
