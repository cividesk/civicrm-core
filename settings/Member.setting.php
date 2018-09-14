<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 5                                                  |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
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
 * @copyright CiviCRM LLC (c) 2004-2017
 * $Id$
 *
 */
/*
 * Settings metadata file
 */

return array(
  'default_renewal_contribution_page' => array(
    'group_name' => 'Member Preferences',
    'group' => 'member',
    'name' => 'default_renewal_contribution_page',
    'type' => 'Integer',
    'html_type' => 'Select',
    'default' => NULL,
    'pseudoconstant' => array(
      'name' => 'contributionPage',
    ),
    'add' => '4.1',
    'title' => 'Default online membership renewal page',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'If you select a default online contribution page for self-service membership renewals, a "renew" link pointing to that page will be displayed on the Contact Dashboard for memberships which were entered offline. You will need to ensure that the membership block for the selected online contribution page includes any currently available memberships.',
    'help_text' => NULL,
  ),
  'membership_reassignment' => array(
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
  ),
);
