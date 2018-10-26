<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
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
 */
class CRM_Member_Form_BlockEdit extends CRM_Core_Form {

  /**
   * The id of the membership.
   *
   * @var int
   */
  protected $_id;

  /**
   * The variable which holds the information of a Membership
   *
   * @var array
   */
  protected $_values;

  /**
   * Explicitly declare the form context.
   */
  public function getDefaultContext() {
    return 'create';
  }
  /**
   * Set variables up before form is built.
   */
  public function preProcess() {
    $this->_action = CRM_Core_Action::UPDATE;
    parent::preProcess();
    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive', $this);
    $this->assign('id', $this->_id);
    $this->_memberID = CRM_Utils_Request::retrieve('membership_id', 'Positive', $this);

    $this->_values = civicrm_api3('Membership', 'getsingle', array('id' => $this->_id));
  }

  /**
   * Set default values.
   *
   * @return array
   */
  public function setDefaultValues() {
    return $this->_values;
  }

  /**
   * Build quickForm.
   */
  public function buildQuickForm() {
    CRM_Utils_System::setTitle(ts('Update Membership details'));

    $membershipFields = $this->getMembershipFields();
    $this->assign('membershipFields', $membershipFields);
    foreach ($membershipFields as $name => $membershipField) {
      $this->add($membershipField['htmlType'],
        $name,
        $membershipField['title'],
        $membershipField['attributes'],
        $membershipField['is_required']
      );
    }

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Update'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ),
    ));
  }

  /**
   * Process the form submission.
   */
  public function postProcess() {
    $params = array(
      'id' => $this->_id,
      'contact_id' => $this->_values['contact_id'],
      'membership_type_id' => $this->_values['membership_type_id'],
      'join_date' => CRM_Utils_Array::value('join_date', $this->_submitValues),
      'start_date' => CRM_Utils_Array::value('start_date', $this->_submitValues),
    );

    $this->submit($params);

    CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url(CRM_Utils_System::currentPath()));
  }

  /**
   * Wrapper function to process form submission
   *
   * @param array $submittedValues
   *
   */
  protected function submit($submittedValues) {
    // simply update the Membership
    civicrm_api3('Membership', 'create', $submittedValues);
  }

  /**
   * Get Membership fields
   */
  public function getMembershipFields() {
    $membershipFields = array(
      'join_date' => array(
        'htmlType' => 'datepicker',
        'name' => 'join_date',
        'title' => ts('Join Date'),
        'is_required' => TRUE,
        'attributes' => array(
          'date' => 'yyyy-mm-dd',
        ),
      ),
      'start_date' => array(
        'htmlType' => 'datepicker',
        'name' => 'start_date',
        'title' => ts('Start Date'),
        'is_required' => TRUE,
        'attributes' => array(
          'date' => 'yyyy-mm-dd',
        ),
      ),
    );
    return $membershipFields;
  }

}
