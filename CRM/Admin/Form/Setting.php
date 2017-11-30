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
 * This class generates form components generic to CiviCRM settings.
 */
class CRM_Admin_Form_Setting extends CRM_Core_Form {

  use CRM_Admin_Form_SettingTrait;

  protected $_settings = [];

  protected $includesReadOnlyFields;

  /**
   * Set default values for the form.
   *
   * Default values are retrieved from the database.
   */
  public function setDefaultValues() {
    if (!$this->_defaults) {
      $this->_defaults = [];
      $this->setDefaultsForMetadataDefinedFields();

      // @todo these should be retrievable from the above function.
      $this->_defaults['enableSSL'] = Civi::settings()->get('enableSSL');
      $this->_defaults['verifySSL'] = Civi::settings()->get('verifySSL');
      $this->_defaults['environment'] = CRM_Core_Config::environment();
      $this->_defaults['enableComponents'] = Civi::settings()->get('enable_components');
    }

    return $this->_defaults;
  }

  /**
   * Build the form object.
   *
   * @throws \CRM_Core_Exception
   */
  public function buildQuickForm() {
    CRM_Core_Session::singleton()->pushUserContext(CRM_Utils_System::url('civicrm/admin', 'reset=1'));
    $this->addButtons([
      [
        'type' => 'next',
        'name' => ts('Save'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ],
    ]);

    $this->addFieldsDefinedInSettingsMetadata();

    if ($this->includesReadOnlyFields) {
      CRM_Core_Session::setStatus(ts("Some fields are loaded as 'readonly' as they have been set (overridden) in civicrm.settings.php."), '', 'info', ['expires' => 0]);
      $descriptions = array();
      $settingMetaData = $this->getSettingsMetaData();
      global  $civicrm_setting;
      foreach ($settingMetaData as $setting => $props) {
	if (isset($props['quick_form_type'])) {
	  if (isset($props['pseudoconstant'])) {
            $options = civicrm_api3('Setting', 'getoptions', array(
              'field' => $setting,
            ));
	  }
	  else {
	    $options = NULL;
	  }
	  $add = 'add' . $props['quick_form_type'];
	  if ($add == 'addElement') {
            $this->$add(
              $props['html_type'],
              $setting,
              ts($props['title']),
              ($options !== NULL) ? $options['values'] : CRM_Utils_Array::value('html_attributes', $props, array()),
              ($options !== NULL) ? CRM_Utils_Array::value('html_attributes', $props, array()) : NULL
            );
          }
          elseif ($add == 'addSelect') {
            $this->addElement('select', $setting, ts($props['title']), $options['values'], CRM_Utils_Array::value('html_attributes', $props));
          }
          elseif ($add == 'addCheckBox') {
            $this->addCheckBox($setting, ts($props['title']), $options['values'], NULL, CRM_Utils_Array::value('html_attributes', $props), NULL, NULL, array('&nbsp;&nbsp;'));
          }
          elseif ($add == 'addChainSelect') {
            $this->addChainSelect($setting, array(
              'label' => ts($props['title']),
            ));
          }
          elseif ($add == 'addMonthDay') {
            $this->add('date', $setting, ts($props['title']), CRM_Core_SelectValues::date(NULL, 'M d'));
          }
          else {
            $this->$add($setting, ts($props['title']));
          }
	  // Migrate to using an array as easier in smart...
	  $descriptions[$setting] = ts($props['description']);
	  $this->assign("{$setting}_description", ts($props['description']));
	  if ($setting == 'max_attachments') {
	    //temp hack @todo fix to get from metadata
	    $this->addRule('max_attachments', ts('Value should be a positive number'), 'positiveInteger');
	  }
	  if ($setting == 'maxFileSize') {
	    //temp hack
	    $this->addRule('maxFileSize', ts('Value should be a positive number'), 'positiveInteger');
	  }
	}

	// CRM-21495 (Respect settings override in civicrm.setting.php)
	if (isset($civicrm_setting[$props][$setting])) {
	  $this->getElement($setting)->freeze();
	}
      }
    }
  }

  /**
   * Process the form submission.
   */
  public function postProcess() {
    // store the submitted values in an array
    $params = $this->controller->exportValues($this->_name);

    self::commonProcess($params);
  }

  /**
   * Common Process.
   *
   * @todo Document what I do.
   *
   * @param array $params
   * @throws \CRM_Core_Exception
   */
  public function commonProcess(&$params) {

    foreach (['verifySSL', 'enableSSL'] as $name) {
      if (isset($params[$name])) {
        Civi::settings()->set($name, $params[$name]);
        unset($params[$name]);
      }
    }
    try {
      $this->saveMetadataDefinedSettings($params);
    }
    catch (CiviCRM_API3_Exception $e) {
      CRM_Core_Session::setStatus($e->getMessage(), ts('Save Failed'), 'error');
    }

    $this->filterParamsSetByMetadata($params);

    $params = CRM_Core_BAO_ConfigSetting::filterSkipVars($params);
    if (!empty($params)) {
      throw new CRM_Core_Exception('Unrecognized setting. This may be a config field which has not been properly migrated to a setting. (' . implode(', ', array_keys($params)) . ')');
    }

    CRM_Core_Config::clearDBCache();
    // This doesn't make a lot of sense to me, but it maintains pre-existing behavior.
    Civi::cache('session')->clear();
    CRM_Utils_System::flushCache();
    CRM_Core_Resources::singleton()->resetCacheCode();

    CRM_Core_Session::setStatus(" ", ts('Changes Saved'), "success");
  }

  public function rebuildMenu() {
    // ensure config is set with new values
    $config = CRM_Core_Config::singleton(TRUE, TRUE);

    // rebuild menu items
    CRM_Core_Menu::store();
  }

}
