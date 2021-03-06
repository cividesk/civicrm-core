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
 * System.check API has many special test cases, so they have their own class.
 *
 * We presume that in a test environment, checkDefaultMailbox and
 * checkDomainNameEmail always fail with a warning, and checkLastCron fails with
 * an error.
 *
 * @package CiviCRM_APIv3
 * @group headless
 */
class api_v3_SystemCheckTest extends CiviUnitTestCase {
  protected $_contactID;
  protected $_locationType;
  protected $_params;

  public function setUp() {
    parent::setUp();
    $this->useTransaction(TRUE);
  }

  /**
   * Ensure that without any StatusPreference set, checkDefaultMailbox shows up.
   * @param int $version
   * @dataProvider versionThreeAndFour
   */
  public function testSystemCheckBasic($version) {
    $this->_apiversion = $version;
    $result = $this->callAPISuccess('System', 'check', []);
    foreach ($result['values'] as $check) {
      if ($check['name'] == 'checkDefaultMailbox') {
        $testedCheck = $check;
        break;
      }
    }
    $this->assertEquals($testedCheck['severity_id'], '3', ' in line ' . __LINE__);
  }

  /**
   * Permanently hushed items should never show up.
   * @param int $version
   * @dataProvider versionThreeAndFour
   */
  public function testSystemCheckHushForever($version) {
    $this->_apiversion = $version;
    $this->_params = [
      'name' => 'checkDefaultMailbox',
      'ignore_severity' => 7,
    ];
    $statusPreference = $this->callAPISuccess('StatusPreference', 'create', $this->_params);
    $result = $this->callAPISuccess('System', 'check', []);
    foreach ($result['values'] as $check) {
      if ($check['name'] == 'checkDefaultMailbox') {
        $testedCheck = $check;
        break;
      }
      else {
        $testedCheck = [];
      }
    }
    $this->assertEquals($testedCheck['is_visible'], '0', 'in line ' . __LINE__);
  }

  /**
   * Disabled items should never show up.
   *
   * @param int $version
   *
   * @dataProvider versionThreeAndFour
   */
  public function testIsInactive($version) {
    $this->_apiversion = $version;
    $this->callAPISuccess('StatusPreference', 'create', [
      'name' => 'checkDefaultMailbox',
      'is_active' => 0,
    ]);
    $result = $this->callAPISuccess('System', 'check', [])['values'];
    foreach ($result as $check) {
      if ($check['name'] === 'checkDefaultMailbox') {
        $this->fail('Check should have been skipped');
      }
    }
  }

  /**
   * Items hushed through tomorrow shouldn't show up.
   *
   * @param int $version
   *
   * @dataProvider versionThreeAndFour
   * @throws \Exception
   */
  public function testSystemCheckHushFuture($version) {
    $this->_apiversion = $version;
    $tomorrow = new DateTime('tomorrow');
    $this->_params = [
      'name' => 'checkDefaultMailbox',
      'ignore_severity' => 7,
      'hush_until' => $tomorrow->format('Y-m-d'),
    ];
    $statusPreference = $this->callAPISuccess('StatusPreference', 'create', $this->_params);
    $result = $this->callAPISuccess('System', 'check', []);
    foreach ($result['values'] as $check) {
      if ($check['name'] === 'checkDefaultMailbox') {
        $testedCheck = $check;
        break;
      }
      else {
        $testedCheck = [];
      }
    }
    $this->assertEquals($testedCheck['is_visible'], '0', 'in line ' . __LINE__);;
  }

  /**
   * Items hushed through today should show up.
   * @param int $version
   * @dataProvider versionThreeAndFour
   */
  public function testSystemCheckHushToday($version) {
    $this->_apiversion = $version;
    $today = new DateTime('today');
    $this->_params = [
      'name' => 'checkDefaultMailbox',
      'ignore_severity' => 7,
      'hush_until' => $today->format('Y-m-d'),
    ];
    $statusPreference = $this->callAPISuccess('StatusPreference', 'create', $this->_params);
    $result = $this->callAPISuccess('System', 'check', []);
    foreach ($result['values'] as $check) {
      if ($check['name'] == 'checkDefaultMailbox') {
        $testedCheck = $check;
        break;
      }
      else {
        $testedCheck = [];
      }
    }
    $this->assertEquals($testedCheck['is_visible'], '1', 'in line ' . __LINE__);
  }

  /**
   * Items hushed through yesterday should show up.
   * @param int $version
   * @dataProvider versionThreeAndFour
   */
  public function testSystemCheckHushYesterday($version) {
    $this->_apiversion = $version;
    $yesterday = new DateTime('yesterday');
    $this->_params = [
      'name' => 'checkDefaultMailbox',
      'ignore_severity' => 7,
      'hush_until' => $yesterday->format('Y-m-d'),
    ];
    $statusPreference = $this->callAPISuccess('StatusPreference', 'create', $this->_params);
    $result = $this->callAPISuccess('System', 'check', []);
    foreach ($result['values'] as $check) {
      if ($check['name'] == 'checkDefaultMailbox') {
        $testedCheck = $check;
        break;
      }
      else {
        $testedCheck = [];
      }
    }
    $this->assertEquals($testedCheck['is_visible'], '1', 'in line ' . __LINE__);
  }

  /**
   * Items hushed above current severity should be hidden.
   * @param int $version
   * @dataProvider versionThreeAndFour
   */
  public function testSystemCheckHushAboveSeverity($version) {
    $this->_apiversion = $version;
    $this->_params = [
      'name' => 'checkDefaultMailbox',
      'ignore_severity' => 4,
    ];
    $statusPreference = $this->callAPISuccess('StatusPreference', 'create', $this->_params);
    $result = $this->callAPISuccess('System', 'check', []);
    foreach ($result['values'] as $check) {
      if ($check['name'] == 'checkDefaultMailbox') {
        $testedCheck = $check;
        break;
      }
      else {
        $testedCheck = [];
      }
    }
    $this->assertEquals($testedCheck['is_visible'], '0', 'in line ' . __LINE__);
  }

  /**
   * Items hushed at current severity should be hidden.
   * @param int $version
   * @dataProvider versionThreeAndFour
   */
  public function testSystemCheckHushAtSeverity($version) {
    $this->_apiversion = $version;
    $this->_params = [
      'name' => 'checkDefaultMailbox',
      'ignore_severity' => 3,
    ];
    $this->callAPISuccess('StatusPreference', 'create', $this->_params);
    $result = $this->callAPISuccess('System', 'check');
    foreach ($result['values'] as $check) {
      if ($check['name'] == 'checkDefaultMailbox') {
        $testedCheck = $check;
        break;
      }
      else {
        $testedCheck = [];
      }
    }
    $this->assertEquals($testedCheck['is_visible'], '0', 'in line ' . __LINE__);
  }

  /**
   * Items hushed below current severity should be shown.
   * @param int $version
   * @dataProvider versionThreeAndFour
   */
  public function testSystemCheckHushBelowSeverity($version) {
    $this->_apiversion = $version;
    $this->_params = [
      'name' => 'checkDefaultMailbox',
      'ignore_severity' => 2,
    ];
    $statusPreference = $this->callAPISuccess('StatusPreference', 'create', $this->_params);
    $result = $this->callAPISuccess('System', 'check', []);
    foreach ($result['values'] as $check) {
      if ($check['name'] == 'checkDefaultMailbox') {
        $testedCheck = $check;
        break;
      }
      else {
        $testedCheck = [];
      }
    }
    $this->assertEquals($testedCheck['is_visible'], '1', 'in line ' . __LINE__);
  }

}
