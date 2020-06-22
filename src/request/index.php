<?php

require_once '../vendor/autoload.php';
require_once '../settings/config.php';
require_once '../person/person.php';
require_once 'request.php';

error_log("Podio hook (Request): " . json_encode($_POST),0);

if ( $config = initConfig($_POST['space_id']) ) {

    switch ($_POST['type']) {

        case 'item.create':
            $request = new request($_POST['item_id'], $config);
            if ($request->getSource() == "Email") {
                $person = new person(null, $request->getEmail(), null, $config);
                $request->setPerson($person);
            } else {
                if ($request->getSubject() == "New missed call from Unknown Caller.") {
                    $request->setTopic("No message left");
                    $request->setNotes("No message left");
                    $request->setStage("Done");
                } else {
                    if ($request->getPhone()) {
                        $person = new person(null, null, $request->getPhone(), $config);
                        $request->setPerson($person);
                    }
                }
            }
            if ($request->getSource() == "Phone") {
                $request->cleanseVoicemail();
            }
            $request->updatePodio();
            $request->alertGroup();
            break;

        case 'hook.verify':
            Podio::setup($config['client_id'], $config['client_secret']);
            Podio::authenticate_with_password($config['username'], $config['password']);
            PodioHook::validate($_POST['hook_id'], array('code' => $_POST['code']));
            break;

    }
    $message = "Processed request: " . $_POST['item_id'];

} else {

    $message = "No matching configuration found for space_id: " . $_POST['space_id'];

}

header('Content-type:application/json;charset=utf-8');
echo json_encode(['message' => $message]);
error_log( $message, 0);