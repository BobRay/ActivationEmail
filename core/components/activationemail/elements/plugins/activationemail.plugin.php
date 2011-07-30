<?php
/**
 * ActivationEmail
 *
 * Copyright 2011 Bob Ray
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
 * MODx ActivationEmail Snippet
 *
 * Description Sends an email to users on manual activation and
 * (optionally) deactivation.
  *
 * @package activationemail
 * @version 1.0.4
 *
 * Properties:
 *
 * @property sendOnActivation (boolean) - Sset to '1' to send the activation email; default '1'
 * @property sendOnDeactivation (boolean) - Set to '1' to send the deactivation email; default '0'
 * @property activationEmailTpl (string) - Tpl chunk for activation email;
 *     default: ActivationEmailTpl
 * @property deactivationEmailTpl (string) - Tpl chunk for deactivation email;
 *     default: DeactivationEmailTpl
 * @property activationURL (string) url to send activated users to;
 *     defaults to site_url System Setting
 * @property deactivationURL (string) url to send deactivated users to;
 *     defaults to site_url System Setting
 * @property sitename (string) site name to use in message; defaults
 *     to site_name System Setting
 * @property useFullname (boolean) - Use full name in msg instead of username.
 * @property replyToAddress (string) - reply-to address for emails;
 *     defaults to emailsender system setting.
 * @property activeSubject (string) - subject for activation emails;
 *     defaults to "Registration Approved".
 * @property activeSender(string) - Email Sender for activation emails;
 *     defaults to emailsender system setting.
 * @property activeFrom (string) - Email From for activation emails;
 *     defaults to emailsender system setting.
 * @property activeFromName(string) - Email From Name for activation emails;
 *     defaults to site_name system setting.
 * @property activeReplyTo(string) - Email Reply To for activation emails;
 *     defaults to emailsender system setting.
 *
 * @property deActiveSubject (string) - subject for deactivation emails;
 *     defaults to "Status Changed to Inactive".
 * @property deActiveSender(string) - Email Sender for deactivation emails;
 *     defaults to emailsender system setting.
 * @property deActiveFrom (string) - Email From for deactivation emails;
 *     defaults to emailsender system setting.
 * @property deActiveFromName(string) - Email From Name for deactivation emails;
 *     defaults to site_name system setting.
 * @property deActiveReplyTo(string) - Email Reply To for deactivation emails;
 *     defaults to emailsender system setting.
 *
 * Placeholders:
 *    [[+username]]
 *    [[+sitename]]
 *    [[+activationURL]]
 *    [[+deactivationURL]]
 *
 */

/* Connect to OnUserBeforeSave (Note: *not* OnBeforeUserFormSave) */

/* return dirname(__FILE__); */

if ($mode != modSystemEvent::MODE_UPD ) {
    return;
}

$sendActivation = $modx->getOption('sendOnActivation',$scriptProperties,true) ? true : false;
$sendDeactivation = $modx->getOption('sendOnDeactivation',$scriptProperties,false) ? true : false;
$activationEmailTpl = $modx->getOption('activationEmailTpl',$scriptProperties,'ActivationEmailTpl');
$deactivationEmailTpl = $modx->getOption('deactivationEmailTpl',$scriptProperties,'DeactivationEmailTpl');

/* get the system setting */
$emailSender = $modx->getOption('emailsender');

/* This won't be used if activeReplyTo or deActiveReplyTo are set */ 
$replyTo = $modx->getOption('replyToAddress',$scriptProperties, null);
$replyTo = empty($replyTo) ? $emailSender : $replyTo;

/* This won't be used if activeFromName or deActiveFromName are set */
$fromName = empty($scriptProperties['fromName']) ? $modx->getOption('site_name'): $scriptProperties['fromName'];

/* If you hard-code these in the email templates, the settings of
 * these properties be ignored. Otherwise, the system settings will
 *  be used unless the properties are set.
 */

$siteName = $modx->getOption('sitename',$scriptProperties);
$siteName = empty($siteName)? $modx->getOption('site_name') : $siteName;
$activeURL = $modx->getOption('activationURL', $scriptProperties,null);
$activeURL = empty($activeURL)? $modx->getOption('site_url') : $activeURL ;
$deActiveURL = $modx->getOption('deactivationURL', $scriptProperties,null);
$deActiveURL = empty($deActiveURL)? $modx->getOption('site_url') : $deActiveURL ;

$profile = $user->getOne('Profile');
$email = $profile->get('email');
$name = $scriptProperties['useFullname'] ? $profile->get('fullname') : $user->get('username');
$id = $user->get('id');
$dbUser = $modx->getObject('modUser',$id);
$before = $dbUser->get('active');
$after = $user->get('active');

$fields = array(
    'username' => $name,
    'sitename' => $siteName,
    'activationURL' => $activeURL,
    'deactivationURL' => $deActiveURL,
);


$send = false;

if ($sendActivation && (empty($before) && $after)) {
    /* activation */
    $_sender = empty($scriptProperties['activeSender'])? $emailSender : $scriptProperties['activeSender'];
    $_reply = empty($scriptProperties['activeReplyTo'])? $replyTo : $scriptProperties['activeReplyTo'];
    $_from = empty($scriptProperties['activeFrom'])? $emailSender : $scriptProperties['activeFrom'];
    $_fromName = empty($scriptProperties['activeFromName'])? $fromName : $scriptProperties['activeFromName'];
    
    $subject = $modx->getOption('activeSubject', $scriptProperties,null);
    $_subject = empty($subject)? 'Registration Approved' : $subject ;
    $_msg = $modx->getChunk($activationEmailTpl,$fields);
    $send = true;
    $eventName = 'activate_user';
    if (empty($_msg)) {
        $_msg = "<p>Dear " . $name . ",</p>
        <p>Thank you for registering at " . $siteName . '.</p>
        <p>Your registration has been approved and you may now access the Members area, please login <a href="' . $activeURL . '">here</a>.</p>';
        $_msg .= "<p>Kind Regards, <br />Site Administrator</p>";
    }
}

if ($sendDeactivation && (empty($after) && $before)) {
    /* deactivation */
    $_sender = empty($scriptProperties['deActiveSender'])? $emailSender : $scriptProperties['deActiveSender'];
    $_reply = empty($scriptProperties['deActiveReplyTo'])? $replyTo : $scriptProperties['deActiveReplyTo'];
    $from = empty($scriptProperties['deActiveFrom'])? $emailSender : $scriptProperties['deActiveFrom'];
    $_fromName = empty($scriptProperties['deActiveFromName'])? $fromName : $scriptProperties['deActiveFromName'];
    
    $subject = $modx->getOption('deActiveSubject', $scriptProperties,null);
    $_subject = empty($subject)? 'Status Changed to Inactive' : $subject ;
    $_msg = $modx->getChunk($deactivationEmailTpl,$fields);
    $send = true;
    $eventName = 'deactivate_user';
    if (empty($_msg)) {
        $_msg = "<p>Dear " . $name . ',</p>
        <p>Your status at ' .  $siteName . ' has been changed to "inactive." '. 'If you believe this is an error please contact the site administrator at <a href="' . $deActiveURL  .'">' . $deActiveURL . '</a>.</p>';
    $_msg .= "<p>Kind Regards, <br />Site Administrator</p>";
    }
}

if ($send ) {
        
        $modx->logManagerAction($eventName,'modUser',$user->get('id'));
        $modx->getService('mail', 'mail.modPHPMailer');
        $modx->mail->set(modMail::MAIL_BODY, $_msg);
        $modx->mail->set(modMail::MAIL_FROM, $_from);
        $modx->mail->set(modMail::MAIL_FROM_NAME, $_fromName);
        $modx->mail->set(modMail::MAIL_SENDER, $_sender);
        $modx->mail->set(modMail::MAIL_SUBJECT, $_subject);
        $modx->mail->address('to', $email, $name);
        $modx->mail->address('reply-to',$_reply);
        $modx->mail->setHTML(true);
        $sent = $modx->mail->send();
        $modx->mail->reset();
 }