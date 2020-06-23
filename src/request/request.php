<?php

require_once '../../vendor/autoload.php';
require_once '../../config/podio-settings.php';

class request {
    private $config;
    private $id;
    private $cid;
    private $name;
    private $topic;
    private $person;
    private $notes;
    private $stage;
    private $source;
    private $email_subject;
    private $email_sender;
    private $email_domain;


    function __construct($request_id, $config)
    {
        $this->config = $config;
        $this->id = $request_id;

        Podio::setup($config['client_id'], $config['client_secret']);
        Podio::authenticate_with_password($config['username'], $config['password']);

        $item = PodioItem::get($request_id);

        $this->cid = $item->app_item_id_formatted;
        $this->name = $this->cid . ": Unknown";
        $this->topic = $item->fields[$config['request_topic']]->values[0]['text'];
        $this->person = $item->fields[$config['request_person']]->values;
        $this->notes = $item->fields[$config['request_notes']]->values;
        error_log(json_encode($this->notes), 0);

        if ($item->fields[$config['request_stage']]->values[0]['text']) {
            $this->stage = $item->fields[$config['request_stage']]->values[0]['text'];
        } else {
            $this->stage = "New";
        }

        $this->email_subject = $item->fields[$config['request_email_subject']]->values;

        $email = strstr($item->fields[$config['request_email_sender']]->values[0]['value'], '<');
        $this->email_sender = str_replace(array('<', '>'), '',$email);

        $this->email_domain = strstr($this->email_sender, '@');

        if ( $item->fields[$config['request_source']]->values[0]['text'] ) {
            $this->source = $item->fields[$config['request_source']]->values[0]['text'];
        } else {
            switch ($this->email_domain) {
                case '@google.com':
                    $this->source = "Phone";
                    break;

                case '@txt.voice.google.com':
                    $this->source = "Text";
                    break;

                default:
                    $this->source = "Email";
                    break;
            }
        }
    }

    function getSource() {
        return $this->source;
    }

    function setSource($source) {
        $this->source = $source;
    }

    function getEmail() {
        return $this->email_sender;
    }

    function getSubject() {
        return $this->email_subject;
    }

    function getPhone() {
        $phone = str_replace('New voicemail from ', '', $this->email_subject);
        $phone = str_replace('New text message from ', '', $phone);
        $phone = str_replace('New missed call from ', '', $phone);
        $phone = str_replace('.', '', $phone);

        if ($phone == "Unknown Caller") {
            return null;
        } else {
            return $phone;
        }
    }

    function cleanseVoicemail() {
        $voice_message = strstr($this->notes, '<br />');
        $voice_message = strstr($voice_message, 'YOUR ACCOUNT', true);
        $voice_message = str_replace('play message', '<br />Play Voice Message:', $voice_message);

        if ($voice_message) {
            $this->notes = $voice_message;
        }
    }

    function setPerson($person) {
        $this->person = $person;
    }

    function setStage($stage) {
        $this->stage = $stage;
    }

    function setTopic($topic) {
        $this->topic = $topic;
        $this->name = $this->cid . ": " . $topic;
    }

    function setNotes($notes) {
        $this->notes = $notes;
    }

    function updatePodio() {

        if ($this->person) {
            $person_id = $this->person->getId();
        } else {
            $person_id = null;
        }

        PodioItem::update($this->id, array('fields'=>array(
            $this->config['request_name']=>$this->name,
            $this->config['request_topic']=>$this->topic,
            $this->config['request_person']=>$person_id,
            $this->config['request_notes']=>$this->notes,
            $this->config['request_stage']=>$this->stage,
            $this->config['request_source']=>$this->source
        )));
    }

    function alertGroup() {
        if ($this->email_subject == "New missed call from Unknown Caller.") {
            $alert = "No caller id and no message. Nothing that can be done here. Moving this to done";
        } else {
            $alert = $this->config['alert_group'] . PHP_EOL;
            switch ($this->source) {
                case "Phone":
                    $alert = $alert . "We just received a new request via voicemail!" . PHP_EOL . PHP_EOL;
                    $alert = $alert . "Phone: " . $this->getPhone() . PHP_EOL . PHP_EOL;
                    break;

                case "Text":
                    $alert = $alert . "We just received a new request via text message!" . PHP_EOL . PHP_EOL;
                    $alert = $alert . "Phone: " . $this->getPhone() . PHP_EOL . PHP_EOL;
                    break;

                case "Email":
                    $alert = $alert . "We just received a new request via email!" . PHP_EOL . PHP_EOL;
                    $alert = $alert . "Email Subject: " . $this->email_subject . PHP_EOL . "Email Sender: " . $this->email_sender  . PHP_EOL . PHP_EOL;
                    break;
            }
            $alert = $alert . "========================================" . PHP_EOL . PHP_EOL;
            $alert = $alert . strip_tags($this->notes) . PHP_EOL . PHP_EOL;
            $alert = $alert . "========================================" . PHP_EOL . PHP_EOL;
            $alert = $alert . "Important first step:" . PHP_EOL . "Please take a look at the person first (by clicking on their name next to Person on the left) to see whether we already have an open request with them (listed at the bottom of the person's permanent record in our People Database. If yes, please let the manager of the open request know about the new call / email and once confirmed that this is not a new request, change the 'Topic' on this one to 'Duplicate' and move its 'Stage' to 'Done'." . PHP_EOL . PHP_EOL;
            $alert = $alert . "Also, once you have identified what this request is about, please choose the corresponding 'Topic' from the drop down on the request (This will auto re-generate the Request name for better visibility)." . PHP_EOL . PHP_EOL;;
            $alert = $alert . "If you are ready to take this one on, please assign this request to you as its request manager and follow up with the requester later today.";
        }
        PodioComment::create('item', $this->id, array('value'=>$alert));
    }
}