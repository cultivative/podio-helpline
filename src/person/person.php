<?php

require_once '../../vendor/autoload.php';
require_once '../../config/podio-settings.php';

class person {

    private $config;
    private $id;
    private $name;


    function __construct($person_id, $email, $phone, $config) {

        $this->config = $config;
        $this->id = $person_id;
        $item = null;

        Podio::setup($config['client_id'], $config['client_secret']);
        Podio::authenticate_with_password($config['username'], $config['password']);

        if ($phone) {
            $item = PodioSearchResult::app($config['person_app_id'], array(
                'counts' => true,
                'highlights' => false,
                'limit' => 1,
                'offset' => 0,
                'query' => $phone,
                'ref_type' => "item"
            ));
            if ($item[0]->id) {
                $this->id = $item[0]->id;
                error_log("Found by Phone: " . $this->id, 0);
            }
        }

        if ($email) {
            $item = PodioSearchResult::app($config['person_app_id'], array(
                'counts' => true,
                'highlights' => false,
                'limit' => 1,
                'offset' => 0,
                'query' => $email,
                'ref_type' => "item"
            ));
            if ($item[0]->id) {
                $this->id = $item[0]->id;
                error_log("Found by Email: " . $this->id, 0);
            }
        }

        if ($person_id) {
            $this->id = $person_id;
            error_log("Person ID provided: " . $this->id, 0);
        }

        if ($this->id) {
            $item = PodioItem::get($this->id);
        } else {
            if ($phone AND $email) {
                error_log("Creating new person with phone & email", 0);
                $item = PodioItem::create($config['person_app_id'], array('fields'=>array(
                    $config['person_name'] =>'Unknown',
                    $config['person_emails'] => array('type' => 'other', 'value' => $email),
                    $config['person_phones'] => array('type' => 'other', 'value' => $phone)
                )));
                $this->id = $item->id;

            } elseif ($phone) {
                error_log("Creating new person with phone only", 0);
                $item = PodioItem::create($config['person_app_id'], array('fields'=>array(
                    $config['person_name'] =>'Unknown',
                    $config['person_phones'] => array('type' => 'other', 'value' => $phone)
                )));
                $this->id = $item->id;

            } elseif ($email) {
                error_log("Creating new person with email only", 0);
                $item = PodioItem::create($config['person_app_id'], array('fields'=>array(
                    $config['person_name'] =>'Unknown',
                    $config['person_emails'] => array('type' => 'other', 'value' => $email)
                )));
                $this->id = $item->id;

            } else {
                error_log("No data to create new person", 0);
            }
        }

        if ($item) {
            $this->name = $item->fields[$config['person_name']]->values;
        }
    }

    function getId() {
        return $this->id;
    }

}