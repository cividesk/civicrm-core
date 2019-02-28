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

/**
 * This class generates form components for component preferences.
 */
class CRM_Admin_Form_Preferences_Member extends CRM_Admin_Form_Preferences {

  protected $_settings = [
    'default_renewal_contribution_page' => CRM_Core_BAO_Setting::MEMBER_PREFERENCES_NAME,
  ];

  public function preProcess() {
    CRM_Utils_System::setTitle(ts('CiviMember Component Settings'));
    $this->_varNames = array(
      CRM_Core_BAO_Setting::MEMBER_PREFERENCES_NAME => array(
        'default_renewal_contribution_page' => array(
          'html_type' => 'select',
          'title' => ts('Default online membership renewal page for <strong>offline</strong> memberships'),
          'option_values' => array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::contributionPage(),
          'weight' => 1,
          'description' => ts('If you select a default online contribution page for self-service membership renewals, a "renew" link pointing to that page will be displayed on the Contact Dashboard for memberships which were entered offline. You will need to ensure that the membership block for the selected online contribution page includes any currently available memberships.'),
        ),
        'membership_reassignment' => array(
          'html_type' => 'YesNo',
          'title' => ts('Re-assignment of related membership'),
          'weight' => 2,
          'description' => ts("If enabled, Cancel Related Membership link will be available (Membership status set to Cancelled with Today date as end date) and while creating new related membership Join and Start date set as Today's date, delete link will not be available."),

        ),
        'online_renewal_contribution_page' => array(
          'html_type' => 'select',
          'title' => ts('Default online membership renewal page for <strong> online</strong> memberships'),
          'option_values' => array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::contributionPage(),
          'weight' => 1,
          'description' => ts('If you select a default online contribution page for self-service membership renewals, a "renew" link pointing to that page will be displayed on the Contact Dashboard for memberships which were entered online. You will need to ensure that the membership block for the selected online contribution page includes any currently available memberships.'),
        ),
      ),
    );
  }
}
