<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */
/*
 * Settings metadata file
 */

return array(
  'profile_double_optin' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'profile_double_optin',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.1',
    'title' => 'Enable Double Opt-in for Profile Group(s) field',
    'is_domain' => 1,
    'is_contact' => 0,
     'description' => 'When CiviMail is enabled, users who "subscribe" to a group from a profile Group(s) checkbox will receive a confirmation email. They must respond (opt-in) before they are added to the group.',
    'help_text' => null,
  ),
  'track_civimail_replies' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'track_civimail_replies',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.1',
    'title' => 'Enable Double Opt-in for Profile Group(s) field',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'When CiviMail is enabled, users who "subscribe" to a group from a profile Group(s) checkbox will receive a confirmation email. They must respond (opt-in) before they are added to the group.',
    'help_text' => null,
    'validate_callback' => 'CRM_Core_BAO_Setting::validateBoolSetting',
  ),
  'civimail_workflow' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'civimail_workflow',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.1',
    'title' => null,
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'When CiviMail is enabled, users who "subscribe" to a group from a profile Group(s) checkbox will receive a confirmation email. They must respond (opt-in) before they are added to the group.',
    'help_text' => null,
  ),
  'civimail_server_wide_lock' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'civimail_server_wide_lock',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.1',
    'title' => null,
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => null,
    'help_text' => null,
  ),
  'mailing_backend' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'mailing_backend',
    'type' => 'Array',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.1',
    'title' => null,
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => null,
    'help_text' => null,
  ),
  'profile_double_optin' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'profile_double_optin',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.1',
    'title' => 'Enable Double Opt-in for Profile Group(s) field',
    'is_domain' => 1,
    'is_contact' => 0,
     'description' => 'When CiviMail is enabled, users who "subscribe" to a group from a profile Group(s) checkbox will receive a confirmation email. They must respond (opt-in) before they are added to the group.',
    'help_text' => null,
  ),
  'profile_add_to_group_double_optin' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'profile_add_to_group_double_optin',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.1',
    'title' => 'Enable Double Opt-in for Profile Group(s) field',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'When CiviMail is enabled, users who "subscribe" to a group from a profile Group(s) checkbox will receive a confirmation email. They must respond (opt-in) before they are added to the group.',
    'help_text' => null,
  ),
  'disable_mandatory_tokens_check' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'disable_mandatory_tokens_check',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.4',
    'title' => 'Disable check for mandatory tokens',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Don\'t check for presence of mandatory tokens (domain address; unsubscribe/opt-out) before sending mailings. WARNING: Mandatory tokens are a safe-guard which facilitate compliance with the US CAN-SPAM Act. They should only be disabled if your organization adopts other mechanisms for compliance or if your organization is not subject to CAN-SPAM.',
    'help_text' => null,
  ),

  'verpSeparator' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'verpSeparator',
    'type' => 'Integer',
    'quick_form_type' => 'Element',
    'html_type' => 'Text',
    'default' => 0,
    'add' => '4.4',
    'title' => 'VERP Separator',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Separator character used when CiviMail generates VERP (variable envelope return path) Mail-From addresses.',
    'help_text' => null,
  ), 
  
  'mailerBatchLimit' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'mailerBatchLimit',
    'type' => 'Integer',
    'quick_form_type' => 'Element',    
    'html_type' => 'Text',
    'default' => 0,
    'add' => '4.4',
    'title' => 'Mailer Batch Limit',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Throttle email delivery by setting the maximum number of emails sent during each CiviMail run (0 = unlimited).',
    'help_text' => null,
  ),   
  'mailThrottleTime' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'mailThrottleTime',
    'type' => 'Integer',
    'quick_form_type' => 'Element',    
    'html_type' => 'Text',
    'default' => 0,
    'add' => '4.4',
    'title' => 'Mailer Throttle Time',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'The time to sleep in between each e-mail in micro seconds. Setting this above 0 allows you to control the rate at which e-mail messages are sent to the mail server, avoiding filling up the mail queue very quickly. Set to 0 to disable.',
    'help_text' => null,
  ),     
  'mailerJobSize' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'mailerJobSize',
    'type' => 'Integer',
    'quick_form_type' => 'Element',    
    'html_type' => 'Text',
    'default' => 0,
    'add' => '4.4',
    'title' => 'Mailer Job Size',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'If you want to utilize multi-threading enter the size you want your sub jobs to be split into. Recommended values are between 1,000 and 10,000. Use a lower value if your server has multiple cron jobs running simultaneously, but do not use values smaller than 1,000. Enter "0" to disable multi-threading and process mail as one single job - batch limits still apply.',
    'help_text' => null,
  ),     


  'mailerJobsMax' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'mailerJobsMax',
    'type' => 'Integer',
    'quick_form_type' => 'Element',    
    'html_type' => 'Text',
    'default' => 0,
    'add' => '4.4',
    'title' => 'Mailer CRON job limit',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'The maximum number of mailer delivery jobs executing simultaneously (0 = allow as many processes to execute as started by cron)',
    'help_text' => null,
  ),     


  'replyTo' => array(
    'group_name' => 'Mailing Preferences',
    'group' => 'mailing',
    'name' => 'replyTo',
    'type' => 'Integer',
    'quick_form_type' => 'Element',    
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.4',
    'title' => 'Enable Custom Reply-To',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Check to enable Reply To functionality for CiviMail.',
    'help_text' => null,
  ),     
    
  );
