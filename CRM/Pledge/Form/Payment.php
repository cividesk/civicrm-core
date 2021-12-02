<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 5                                                  |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2019                                |
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
 * @copyright CiviCRM LLC (c) 2004-2019
 */

/**
 * This class generates form components for processing a pledge payment.
 */
class CRM_Pledge_Form_Payment extends CRM_Core_Form {

  /**
   * The id of the pledge payment that we are proceessing.
   *
   * @var int
   */
  public $_id;

  /**
   * Explicitly declare the entity api name.
   */
  public function getDefaultEntity() {
    return 'PledgePayment';
  }

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
    // check for edit permission
    if (!CRM_Core_Permission::check('edit pledges')) {
      CRM_Core_Error::fatal(ts('You do not have permission to access this page.'));
    }

    $this->_id = CRM_Utils_Request::retrieve('ppId', 'Positive', $this);
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this);

    CRM_Utils_System::setTitle(ts('Edit Scheduled Pledge Payment'));

    if ($this->_action & CRM_Core_Action::ADD) {
      CRM_Utils_System::setTitle(ts('New Scheduled Pledge Payment'));
    }
  }

  /**
   * Set default values for the form.
   * the default values are retrieved from the database.
   */
  public function setDefaultValues() {
    $defaults = [];
    if ($this->_id) {
      $params['id'] = $this->_id;
      CRM_Pledge_BAO_PledgePayment::retrieve($params, $defaults);
      if (isset($defaults['contribution_id'])) {
        $this->assign('pledgePayment', TRUE);
      }
      $status = CRM_Core_PseudoConstant::getName('CRM_Pledge_BAO_Pledge', 'status_id', $defaults['status_id']);
      $this->assign('status', $status);
      $this->assign('scheduled_amount', $defaults['scheduled_amount']);
    }

    if ($this->_action & CRM_Core_Action::ADD) {
      //preset to one period after the last scheduled payment
      $pledgeParams = ['id' => $this->_pledgeID];
      CRM_Pledge_BAO_Pledge::retrieve($pledgeParams, $pledgeDefaults);

      // Add new payment after the last payment for the pledge
      $allPayments = CRM_Pledge_BAO_PledgePayment::getPledgePayments($this->_pledgeID);
      $lastPayment = array_pop($allPayments);

      $pledgeDefaults[scheduled_date] = $lastPayment[scheduled_date];
      $lastDate = CRM_Utils_Date::mysqlToIso(CRM_Pledge_BAO_PledgePayment::calculateNextScheduledDate($pledgeDefaults, $pledgeParams['installments'] +1));
      $defaults['pledge_payment_scheduled_date'] = $defaults['scheduled_date'] = $lastDate;
    }
    $defaults['option_type'] = 1;
    return $defaults;
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    // add various dates
    $this->addField('scheduled_date', [], TRUE, FALSE);

    $this->addMoney('scheduled_amount',
      ts('Scheduled Amount'), TRUE,
      ['readonly' => TRUE],
      TRUE,
      'currency',
      NULL,
      TRUE
    );

    $optionTypes = [
      '1' => ts('Adjust Pledge Payment Schedule?'),
      '2' => ts('Adjust Total Pledge Amount?'),
    ];
    $element = $this->addRadio('option_type',
      NULL,
      $optionTypes,
      [], '<br/>'
    );

    $this->_pledgeID = CRM_Core_DAO::getFieldValue('CRM_Pledge_DAO_PledgePayment', $this->_id, 'pledge_id');

    //check if there are any completed, live
    //contributions that are NOT associate to any
    //pledges for this contact
    $unlinkedContributions = CRM_Pledge_BAO_PledgePayment::getUnlinkedContributions($this->_contactId);
    if (!empty($unlinkedContributions)) {
      //show the contributions that fulfil criteria to link
      $this->add('select', 'contribution_id', ts('Link with Payment'), ['' => ts('- select -')] + $unlinkedContributions, FALSE);
    }

    if ($this->_action & CRM_Core_Action::UPDATE) {
      $buttons = [
        [
          'type' => 'next',
          'name' => ts('Delete'),
          'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
          'subName' => 'delete',
        ],
        [
          'type' => 'done',
          'name' => ts('Save'),
          'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
          'isDefault' => TRUE,
        ],
        [
          'type' => 'next',
          'name' => ts('Save and New'),
          'subName' => 'new',
        ],
        [
          'type' => 'cancel',
          'name' => ts('Cancel'),
        ],
      ];
    } else {
      $buttons = [
        [
          'type' => 'done',
          'name' => ts('Save'),
          'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
          'isDefault' => TRUE,
        ],
        [
          'type' => 'cancel',
          'name' => ts('Cancel'),
        ],
      ];
    }
    $this->addButtons($buttons);
  }

  /**
   * Process the form submission.
   */
  public function postProcess() {
    $formValues = $this->controller->exportValues($this->_name);
    $buttonName = $this->controller->getButtonName();

    if ($buttonName == '_qf_Payment_next_delete') {
      $formValues = $this->controller->exportValues($this->_name);

      //delete the scheduled pledge payment
      CRM_Pledge_BAO_PledgePayment::del($this->_id);

      //reduce the pledge amount (ie. civicrm_pledge.amount) by the corresponding amount
      // and reduce installment as well.
      CRM_Pledge_BAO_PledgePayment::updatePledgeAmount($this->_pledgeID, $formValues['scheduled_amount']);
      return;
    }

    $params = [
      'id' => $this->_id,
      'scheduled_date' => $formValues['scheduled_date'],
      'currency' => $formValues['currency'],
    ];

    if (CRM_Utils_Date::overdue($params['scheduled_date'])) {
      $params['status_id'] = CRM_Core_PseudoConstant::getKey('CRM_Pledge_BAO_Pledge', 'status_id', 'Overdue');
    }
    else {
      $params['status_id'] = CRM_Core_PseudoConstant::getKey('CRM_Pledge_BAO_Pledge', 'status_id', 'Pending');
    }

    if ($this->_action & CRM_Core_Action::ADD) {
      unset($params['id']);
      $params['pledge_id'] = $this->_pledgeID;
      $params['scheduled_amount'] = $formValues['scheduled_amount'];
    }

    if ($formValues['contribution_id']) {
      //if a contribution has been linked to the pledge payment,
      //update the status as well
      $params['contribution_id'] = $formValues['contribution_id'];
      $allStatus = CRM_Contribute_PseudoConstant::contributionStatus(NULL, 'name');
      $completedStatus = array_search('Completed', $allStatus);
      $params['status_id'] = $completedStatus;
    }

    CRM_Pledge_BAO_PledgePayment::add($params);

    if ($this->_action & CRM_Core_Action::ADD) {
      //increase the pledge amount (ie. civicrm_pledge.amount) by the corresponding amount
      //and update incremented installment as well
      CRM_Pledge_BAO_PledgePayment::updatePledgeAmount($this->_pledgeID, $formValues['scheduled_amount'], '+');
      return;
    }

    $adjustTotalAmount = (CRM_Utils_Array::value('option_type', $formValues) == 2);

    $pledgeScheduledAmount = CRM_Core_DAO::getFieldValue('CRM_Pledge_DAO_PledgePayment',
      $params['id'],
      'scheduled_amount',
      'id'
    );

    $oldestPaymentAmount = CRM_Pledge_BAO_PledgePayment::getOldestPledgePayment($this->_pledgeID, 2);
    if (($oldestPaymentAmount['count'] != 1) && ($oldestPaymentAmount['id'] == $params['id'])) {
      $oldestPaymentAmount = CRM_Pledge_BAO_PledgePayment::getOldestPledgePayment($this->_pledgeID);
    }
    if (($formValues['scheduled_amount'] - $pledgeScheduledAmount) >= $oldestPaymentAmount['amount']) {
      $adjustTotalAmount = TRUE;
    }
    // update pledge status
    CRM_Pledge_BAO_PledgePayment::updatePledgePaymentStatus($this->_pledgeID,
      [$params['id']],
      $params['status_id'],
      NULL,
      $formValues['scheduled_amount'],
      $adjustTotalAmount
    );

    $session = CRM_Core_Session::singleton();
    if ($buttonName == $this->getButtonName('next', 'new')) {
      $msg .= '<p>' . ts("Ready to add another.") . '</p>';
      $session->replaceUserContext(CRM_Utils_System::url('civicrm/pledge/payment',
        'reset=1&action=add&cid=' .  $this->_contactId. '&ppId=' . $this->_id
      ));
    }
    else {
      $statusMsg = ts('Pledge Payment Schedule has been updated.');
      CRM_Core_Session::setStatus($statusMsg, ts('Saved'), 'success');
    }
  }

}
