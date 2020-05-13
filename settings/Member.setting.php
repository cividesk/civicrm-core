<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */
/*
 * Settings metadata file
 */

return [
  'default_renewal_contribution_page' => [
    'group_name' => 'Member Preferences',
    'group' => 'member',
    'name' => 'default_renewal_contribution_page',
    'type' => 'Integer',
    'html_type' => 'select',
    'default' => NULL,
    'pseudoconstant' => [
      // @todo - handle table style pseudoconstants for settings & avoid deprecated function.
      'callback' => 'CRM_Contribute_PseudoConstant::contributionPage',
    ],
    'add' => '4.1',
    'title' => ts('Default online membership renewal page'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('If you select a default online contribution page for self-service membership renewals, a "renew" link pointing to that page will be displayed on the Contact Dashboard for memberships which were entered <strong>offline</strong>. You will need to ensure that the membership block for the selected online contribution page includes any currently available memberships.'),
    'help_text' => NULL,
  ],
  'membership_reassignment' => [
    'group_name' => 'Member Preferences',
    'group' => 'member',
    'name' => 'membership_reassignment',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => FALSE,
    'add' => '4.7',
    'title' => 'Re-assignment of related membership',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => "If enabled, Cancel Related Membership link will be available (Membership status set to Cancelled with Today date as end date) and while creating new related membership Join and Start date set as Today's date, delete link will not be available.",
    'help_text' => NULL,
  ],
  'online_renewal_contribution_page' => [
    'group_name' => 'Member Preferences',
    'group' => 'member',
    'name' => 'online_renewal_contribution_page',
    'type' => 'Integer',
    'html_type' => 'select',
    'default' => NULL,
    'pseudoconstant' => [
      // @todo - handle table style pseudoconstants for settings & avoid deprecated function.
      'callback' => 'CRM_Contribute_PseudoConstant::contributionPage',
    ],
    'title' => 'Default online membership renewal page for <strong> online</strong> memberships',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('If you select a default online contribution page for self-service membership renewals, a "renew" link pointing to that page will be displayed on the Contact Dashboard for memberships which were entered <strong>online</strong>. You will need to ensure that the membership block for the selected online contribution page includes any currently available memberships.'),
    'help_text' => NULL,
  ],
];
