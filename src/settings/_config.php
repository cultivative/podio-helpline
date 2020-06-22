<?php

// Example configuration file.
// Create a copy of this file and name it 'config.php' in the same folder (/src/settings/)
// Adjust the settings below with your project settings and field names before running the code
// When setting up the web service url, please make sure the Podio Workspace id is provided as shown below
// https://yourdomain.com/podio/helpline/request/?space_id=1234567

function initConfig($space_id) {
    switch ($space_id) {

        case 1234567: // Update the number here to reflect your Podio workspace id
            return array(

                // Your Workspace or Organization Name

                //Access configuration
                'client_id' => '',
                'client_secret' => '',
                'username' => '',
                'password' => '',

                // Workspace configuration
                'space_id' => $space_id,
                'alert_group' => '@[Call For Help](space:'.$space_id.')',

                // Request app configuration
                'request_app_id' => 12345678,
                'request_name' => 'name',
                'request_person' => 'person',
                'request_topic' => 'topic',
                'request_notes' => 'notes',
                'request_stage' => 'stage',
                'request_source' => 'source',
                'request_email_subject' => 'email-subject',
                'request_email_sender' => 'email-sender',

                // Person app configuration
                'person_app_id' => 12345678,
                'person_name' => 'name',
                'person_phones' => 'phones',
                'person_emails' => 'emails'

            );
            break;

        default:
            return null;
            break;
    }
}



