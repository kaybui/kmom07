<?php

require __DIR__.'/config_with_app.php';

$app->theme->addStylesheet('status_message.css');
$status = new Anax\Flashingmessage\CStatusMessage($di);
$status->addInfoMessage("Info");
$status->addErrorMessage("Error");
$status->addWarningMessage("Warning");
$status->addSuccessMessage("Success");
$status->retrieveMessages();

$app->theme->setVariable('title', "Test page for CStatusMessage")
->setVariable('main', "
<h1>Test page for CStatusMessage</h1>

" . $status->messagesHtml());

$app->theme->addStylesheet('../webroot/css/flash.css');
$app->theme->render();
