<?php
/**
 * ActivationEmail
 *
 * Copyright 2011-2022 Bob Ray
 *
 * @author Bob Ray
 * 1/30/11
 *
 * ActivationEmail is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * ActivationEmail is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ActivationEmail; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package activationemail
 */
/**
 * MODX ActivationEmail Plugin
 *
 * Description Sends an email to users on manual activation and
 * (optionally) deactivation.
 *
 * @package activationemail
 *
 *
 * Properties:
 */

/** @var $sendActivation boolean - Set to '1' to send the activation email; default '1'
 ** @var $sendDeactivation boolean - Set to '1' to send the deactivation email; default '0'
 ** @var $activationEmailTpl string - Tpl chunk for activation email;
 *     default: ActivationEmailTpl
 ** @var $deactivationEmailTpl string - Tpl chunk for deactivation email;
 *     default: DeactivationEmailTpl
 ** @var $activationURL string url to send activated users to;
 *     defaults to site_url System Setting
 ** @var $deactivationURL string url to send deactivated users to;
 *     defaults to site_url System Setting
 ** @var $sitename string site name to use in message; defaults
 *     to site_name System Setting
 ** @var $useFullname boolean - Use full name in msg instead of username.
 ** @var $replyToAddress string - reply-to address for emails;
 *     defaults to emailsender system setting.
 ** @var $activeSubject string - subject for activation emails;
 *     defaults to "Registration Approved".
 ** @var $activeSender string - Email Sender for activation emails;
 *     defaults to emailsender system setting.
 ** @var $activeFrom string - Email From for activation emails;
 *     defaults to emailsender system setting.
 ** @var $activeFromName string - Email From Name for activation emails;
 *     defaults to site_name system setting.
 ** @var $activeReplyTo string - Email Reply To for activation emails;
 *     defaults to emailsender system setting.
 *
 ** @var $deActiveSubject string - subject for deactivation emails;
 *     defaults to "Status Changed to Inactive".
 ** @var $deActiveSender string - Email Sender for deactivation emails;
 *     defaults to emailsender system setting.
 ** @var $deActiveFrom string - Email From for deactivation emails;
 *     defaults to emailsender system setting.
 ** @var $deActiveFromName string - Email From Name for deactivation emails;
 *     defaults to site_name system setting.
 ** @var $deActiveReplyTo string - Email Reply To for deactivation emails;
 *     defaults to emailsender system setting.
 *
 * Placeholders:
 *    [[+username]]
 *    [[+sitename]]
 *    [[+activationURL]]
 *    [[+deactivationURL]]
 *    [[+fromName]]
 *
 */

/* Connect to OnUserActivate and OnUserDeactivate */
/** @var $scriptProperties array */
/** @var $modx modX $sp */
/** @var $modx- >$xpdo xPDO */
/** @var $mode int */
/** @var $user modUser */
/** @var $UseFullName bool */

// $modx->log(modX::LOG_LEVEL_ERROR, "[ActivationEmail] - in plugin");

$sp = $scriptProperties;

$eventName = $modx->event->name;
$sendActivation = $modx->getOption('sendOnActivation', $sp, true);
$sendDeactivation = $modx->getOption('sendOnDeactivation', $sp, false);

/* get the emailsender system setting */
$emailSender = $modx->getOption('emailsender');

/* Get Properties */
/* This won't be used if activeReplyTo or deActiveReplyTo are set */
$replyTo = $modx->getOption('replyToAddress', $sp, null);
$replyTo = empty($replyTo) ? $emailSender : $replyTo;

/* This won't be used if activeFromName or deActiveFromName are set */
$fromName = $modx->getOption('fromName', $sp, null);
$fromName = empty($fromName) ? $modx->getOption('site_name') : $fromName;

/* If you hard-code these in the email templates, the settings of
 * these properties be ignored. Otherwise, the system settings will
 *  be used unless the properties are set.
 */

$siteName = $modx->getOption('sitename', $sp);
$siteName = empty($siteName) ? $modx->getOption('site_name') : $siteName;

$profile = $user->getOne('Profile');
$email = $profile->get('email');
$useFullName = $modx->getOption('useFullName', $sp, false);
$name = $useFullName ? $profile->get('fullname') : $user->get('username');

$fullName = $profile->get('fullname');
$send = false;
// $modx->log(modX::LOG_LEVEL_ERROR, "[ActivationEmail] - Event: " . $modx->event->name);
// $modx->log(modX::LOG_LEVEL_ERROR, "[ActivationEmail] - EventName: " . $eventName);

if ($eventName === 'OnUserActivate' && ($sendActivation)) {
//    $modx->log(modX::LOG_LEVEL_ERROR, "[ActivationEmail] - sending activation email");
    /* Activation */
    $activationEmailTpl = $modx->getOption('activationEmailTpl', $sp, 'ActivationEmailTpl');
    $activeURL = $modx->getOption('activationURL', $sp, null);
    $activeURL = empty($activeURL) ? $modx->getOption('site_url') : $activeURL;
    $_sender = $modx->getOption('activeSender', $sp, null, true);
    $_sender = empty($_sender) ? $emailSender : $_sender;
    $_reply = $modx->getOption('activeReplyTo', $sp, null, true);
    $_reply = empty($_reply) ? $replyTo : $_reply;
    $_from = $modx->getOption('activeFrom', $sp, null, true);
    $_from = empty($_from) ? $emailSender : $_from;
    $_fromName = $modx->getOption('activeFromName', $sp, null, true);
    $_fromName = empty($_fromName) ? $fromName : $_fromName;
    $fields['fromName'] = $_fromName;

    $subject = $modx->getOption('activeSubject', $sp, null, true);
    $_subject = empty($subject) ? 'Registration Approved' : $subject;
    $fields = array(
            'fullname' => $fullName,
            'username' => $name,
            'sitename' => $siteName,
            'activationURL' => $activeURL,
    );
    $_msg = $modx->getChunk($activationEmailTpl, $fields);
    $send = true;
    if (empty($_msg)) {
        $_msg = "<p>Dear " . $name . ",</p>
        <p>Thank you for registering at " . $siteName . '.</p>
        <p>Your registration has been approved and you may now access the Members area, please login <a href="' . $activeURL . '">here</a>.</p>';
        $_msg .= "<p>Kind Regards, <br />Site Administrator</p>";
    }
    $send = true;
} elseif ($eventName === 'OnUserDeactivate' && ($sendDeactivation)) {
//    $modx->log(modX::LOG_LEVEL_ERROR, "[ActivationEmail] - sending Deactivation email");
    /* deactivation */
    $deactivationEmailTpl = $modx->getOption('deactivationEmailTpl', $sp, 'DeactivationEmailTpl');
    $deActiveURL = $modx->getOption('deactivationURL', $sp, null);
    $deActiveURL = empty($deActiveURL) ? $modx->getOption('site_url') : $deActiveURL;

    $_sender = $modx->getOption('deActiveSender', $sp, null, true);
    $_sender = empty($_sender) ? $emailSender : $_sender;
    $_reply = $modx->getOption('deActiveReplyTo', $sp, null, true);
    $_reply = empty($_reply) ? $replyTo : $_reply;
    $_from = $modx->getOption('deActiveFrom', $sp, null, true);
    $_from = empty($from) ? $emailSender : $from;
    $_fromName = $modx->getOption('deActiveFromName', $sp, null, true);
    $_fromName = empty($_fromName) ? $fromName : $_fromName;
    $fields['fromName'] = $_fromName;

    $subject = $modx->getOption('deActiveSubject', $sp, null, true);
    $_subject = empty($subject) ? 'Status Changed to Inactive' : $subject;
    $fields = array(
            'fullname' => $fullName,
            'username' => $name,
            'sitename' => $siteName,
            'deactivationURL' => $deActiveURL,
    );
    $_msg = $modx->getChunk($deactivationEmailTpl, $fields);

    if (empty($_msg)) {
        $_msg = "<p>Dear " . $name . ',</p>
        <p>Your status at ' . $siteName . ' has been changed to "inactive." ' . 'If you believe this is an error please contact the site administrator at <a href="' . $deActiveURL . '">' . $deActiveURL . '</a>.</p>';
        $_msg .= "<p>Kind Regards, <br />Site Administrator</p>";
    }
    $send = true;
}


$fields = array(
        'fullname' => $fullName,
        'username' => $name,
        'sitename' => $siteName,
        'activationURL' => $activeURL,
        'deactivationURL' => $deActiveURL,
);

if ($send) {

    $modx->logManagerAction($eventName, 'modUser', $user->get('id'));
    $modx->getService('mail', 'mail.modPHPMailer');
    $modx->mail->set(modMail::MAIL_BODY, $_msg);
    $modx->mail->set(modMail::MAIL_FROM, $_from);
    $modx->mail->set(modMail::MAIL_FROM_NAME, $_fromName);
    $modx->mail->set(modMail::MAIL_SENDER, $_sender);
    $modx->mail->set(modMail::MAIL_SUBJECT, $_subject);
    $modx->mail->address('to', $email, $name);
    $modx->mail->address('reply-to', $_reply);
    $modx->mail->setHTML(true);
    $sent = $modx->mail->send();
    if ($sent) {
//        $modx->log(modX::LOG_LEVEL_ERROR, "[ActivationEmail] - send successful");
    } else {
//        $modx->log(modX::LOG_LEVEL_ERROR, "[ActivationEmail] - send failed");
    }
    $modx->mail->reset();
}

return '';