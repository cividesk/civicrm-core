<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 5                                                  |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2018                                |
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
 * @copyright CiviCRM LLC (c) 2004-2018
 */
class CRM_Report_Form_Mailing_Summary extends CRM_Report_Form {

  protected $_summary = NULL;

  protected $_customGroupExtends = array();

  protected $_add2groupSupported = FALSE;

  public $_drilldownReport = array('mailing/detail' => 'Link to Detail Report');

  protected $_charts = array(
    '' => 'Tabular',
    'bar_3dChart' => 'Bar Chart',
  );

  public $campaignEnabled = FALSE;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->_columns = array();

    $this->_columns['civicrm_mailing'] = array(
      'dao' => 'CRM_Mailing_DAO_Mailing',
      'fields' => array(
        'id' => array(
          'name' => 'id',
          'title' => ts('Mailing ID'),
          'required' => TRUE,
          'no_display' => TRUE,
        ),
        'name' => array(
          'title' => ts('Mailing Name'),
          'required' => TRUE,
        ),
        'created_date' => array(
          'title' => ts('Date Created'),
        ),
        'subject' => array(
          'title' => ts('Subject'),
        ),
      ),
      'filters' => array(
        'is_completed' => array(
          'title' => ts('Mailing Status'),
          'operatorType' => CRM_Report_Form::OP_SELECT,
          'type' => CRM_Utils_Type::T_INT,
          'options' => array(
            0 => 'Incomplete',
            1 => 'Complete',
          ),
          //'operator' => 'like',
          'default' => 1,
        ),
        'mailing_id' => array(
          'name' => 'id',
          'title' => ts('Mailing Name'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'type' => CRM_Utils_Type::T_INT,
          'options' => CRM_Mailing_BAO_Mailing::getMailingsList(),
          'operator' => 'like',
        ),
        'mailing_subject' => array(
          'name' => 'subject',
          'title' => ts('Mailing Subject'),
          'type' => CRM_Utils_Type::T_STRING,
          'operator' => 'like',
        ),
      ),
      'order_bys' => array(
        'mailing_name' => array(
          'name' => 'name',
          'title' => ts('Mailing Name'),
        ),
        'mailing_subject' => array(
          'name' => 'subject',
          'title' => ts('Mailing Subject'),
        ),
      ),
    );

    $this->_columns['civicrm_mailing_job'] = array(
      'dao' => 'CRM_Mailing_DAO_MailingJob',
      'fields' => array(
        'start_date' => array(
          'title' => ts('Start Date'),
          'dbAlias' => 'MIN(mailing_job_civireport.start_date)',
        ),
        'end_date' => array(
          'title' => ts('End Date'),
          'dbAlias' => 'MAX(mailing_job_civireport.end_date)',
        ),
      ),
      'filters' => array(
        'status' => array(
          'type' => CRM_Utils_Type::T_STRING,
          'default' => 'Complete',
          'no_display' => TRUE,
        ),
        'is_test' => array(
          'type' => CRM_Utils_Type::T_INT,
          'default' => 0,
          'no_display' => TRUE,
        ),
        'start_date' => array(
          'title' => ts('Start Date'),
          'default' => 'this.year',
          'operatorType' => CRM_Report_Form::OP_DATE,
          'type' => CRM_Utils_Type::T_DATE,
        ),
        'end_date' => array(
          'title' => ts('End Date'),
          'default' => 'this.year',
          'operatorType' => CRM_Report_Form::OP_DATE,
          'type' => CRM_Utils_Type::T_DATE,
        ),
      ),
      'order_bys' => array(
        'start_date' => array(
          'title' => ts('Start Date'),
          'dbAlias' => 'MIN(mailing_job_civireport.start_date)',
        ),
        'end_date' => array(
          'title' => ts('End Date'),
          'default_weight' => '1',
          'default_order' => 'DESC',
          'dbAlias' => 'MAX(mailing_job_civireport.end_date)',
        ),
      ),
      'grouping' => 'mailing-fields',
    );

    $this->_columns['civicrm_mailing_event_queue'] = array(
      'dao' => 'CRM_Mailing_DAO_Mailing',
      'fields' => array(
        'queue_count' => array(
          'name' => 'id',
          'title' => ts('Intended Recipients'),
        ),
      ),
    );

    $this->_columns['civicrm_mailing_event_delivered'] = array(
      'dao' => 'CRM_Mailing_DAO_Mailing',
      'fields' => array(
        'delivered_count' => array(
          'name' => 'event_queue_id',
          'title' => ts('Successful Deliveries'),
        ),
        'accepted_rate' => array(
          'title' => ts('Successful Delivery Rate'),
          'statistics' => array(
            'calc' => 'PERCENTAGE',
            'top' => 'civicrm_mailing_event_delivered.delivered_count',
            'base' => 'civicrm_mailing_event_queue.queue_count',
          ),
        ),
      ),
    );

    $this->_columns['civicrm_mailing_event_bounce'] = array(
      'dao' => 'CRM_Mailing_DAO_Mailing',
      'fields' => array(
        'bounce_count' => array(
          'name' => 'event_queue_id',
          'title' => ts('Bounces'),
        ),
        'bounce_rate' => array(
          'title' => ts('Bounce Rate'),
          'statistics' => array(
            'calc' => 'PERCENTAGE',
            'top' => 'civicrm_mailing_event_bounce.bounce_count',
            'base' => 'civicrm_mailing_event_queue.queue_count',
          ),
        ),
      ),
    );

    $this->_columns['civicrm_mailing_event_opened'] = array(
      'dao' => 'CRM_Mailing_DAO_Mailing',
      'fields' => array(
        'unique_open_count' => array(
          'name' => 'id',
          'alias' => 'mailing_event_opened_civireport',
          'dbAlias' => 'mailing_event_opened_civireport.event_queue_id',
          'title' => ts('Unique Opens'),
        ),
        'unique_open_rate' => array(
          'title' => ts('Unique Open Rate'),
          'statistics' => array(
            'calc' => 'PERCENTAGE',
            'top' => 'civicrm_mailing_event_opened.unique_open_count',
            'base' => 'civicrm_mailing_event_delivered.delivered_count',
          ),
        ),
        'open_count' => array(
          'name' => 'event_queue_id',
          'title' => ts('Total Opens'),
        ),
        'open_rate' => array(
          'title' => ts('Total Open Rate'),
          'statistics' => array(
            'calc' => 'PERCENTAGE',
            'top' => 'civicrm_mailing_event_opened.open_count',
            'base' => 'civicrm_mailing_event_delivered.delivered_count',
          ),
        ),
      ),
    );

    $this->_columns['civicrm_mailing_event_trackable_url_open'] = array(
      'dao' => 'CRM_Mailing_DAO_Mailing',
      'fields' => array(
        'click_count' => array(
          'name' => 'event_queue_id',
          'title' => ts('Unique Clicks'),
        ),
        'CTR' => array(
          'title' => ts('Click-through Rate'),
          'default' => 0,
          'statistics' => array(
            'calc' => 'PERCENTAGE',
            'top' => 'civicrm_mailing_event_trackable_url_open.click_count',
            'base' => 'civicrm_mailing_event_delivered.delivered_count',
          ),
        ),
        'CTO' => array(
          'title' => ts('Click-through to Open Rate'),
          'default' => 0,
          'statistics' => array(
            'calc' => 'PERCENTAGE',
            'top' => 'civicrm_mailing_event_trackable_url_open.click_count',
            'base' => 'civicrm_mailing_event_opened.open_count',
          ),
        ),
      ),
    );

    $this->_columns['civicrm_mailing_event_unsubscribe'] = array(
      'dao' => 'CRM_Mailing_DAO_Mailing',
      'fields' => array(
        'unsubscribe_count' => array(
          'name' => 'id',
          'title' => ts('Unsubscribe Requests'),
          'alias' => 'mailing_event_unsubscribe_civireport',
          'dbAlias' => 'mailing_event_unsubscribe_civireport.event_queue_id',
        ),
        'optout_count' => array(
          'name' => 'id',
          'title' => ts('Opt-out Requests'),
          'alias' => 'mailing_event_optout_civireport',
          'dbAlias' => 'mailing_event_optout_civireport.event_queue_id',
        ),
      ),
    );
    $this->_columns['civicrm_mailing_group'] = array(
      'dao' => 'CRM_Mailing_DAO_MailingGroup',
      'filters' => array(
        'entity_id' => array(
          'title' => ts('Groups Included in Mailing'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'type' => CRM_Utils_Type::T_INT,
          'options' => CRM_Core_PseudoConstant::group(),
        ),
      ),
    );
    $config = CRM_Core_Config::singleton();
    $this->campaignEnabled = in_array("CiviCampaign", $config->enableComponents);
    if ($this->campaignEnabled) {
      $this->_columns['civicrm_campaign'] = array(
        'dao' => 'CRM_Campaign_DAO_Campaign',
        'fields' => array(
          'title' => array(
            'title' => ts('Campaign Name'),
          ),
        ),
        'filters' => array(
          'title' => array(
            'type' => CRM_Utils_Type::T_STRING,
          ),
        ),
      );
    }
    parent::__construct();
  }

  public function preProcess() {
    $this->assign('chartSupported', TRUE);
    parent::preProcess();
  }

  /**
   * manipulate the select function to query count functions.
   */
  public function select() {
    $this->_columnHeaders = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (!empty($field['required']) || !empty($this->_params['fields'][$fieldName])) {
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
          }
        }
      }
    }
  }

  public function from() {

  }

  public function where() {
    $clauses = array();
    //to avoid the sms listings
    $clauses[] = "{$this->_aliases['civicrm_mailing']}.sms_provider_id IS NULL";

    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('filters', $table)) {
        foreach ($table['filters'] as $fieldName => $field) {
          $clause = NULL;
          if (CRM_Utils_Array::value('type', $field) & CRM_Utils_Type::T_DATE) {
            $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
            $from = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
            $to = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);

            $clause = $this->dateClause($this->_aliases[$tableName] . '.' . $field['name'], $relative, $from, $to, $field['type']);
          }
          else {
            $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);

            if ($op) {
              if ($fieldName == 'relationship_type_id') {
                $clause = "{$this->_aliases['civicrm_relationship']}.relationship_type_id=" . $this->relationshipId;
              }
              else {
                $clause = $this->whereClause($field,
                  $op,
                  CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
                  CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
                  CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
                );
              }
            }
          }

          if (!empty($clause)) {
            $clauses[] = $clause;
          }
        }
      }
    }

    if (empty($clauses)) {
      $this->_where = "WHERE ( 1 )";
    }
    else {
      $this->_where = "WHERE " . implode(' AND ', $clauses);
    }
  }


  public function orderBy() {
    parent::orderBy();
    CRM_Contact_BAO_Query::getGroupByFromOrderBy($this->_groupBy, $this->_orderByArray);
  }

  public function postProcess() {

    $this->beginPostProcess();

    // get the acl clauses built before we assemble the query
    $this->buildACLClause(CRM_Utils_Array::value('civicrm_contact', $this->_aliases));

    $sql = $this->buildQuery(TRUE);

    $rows = $graphRows = array();
    $this->buildRows($sql, $rows);

    $this->formatDisplay($rows);
    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }

  /**
   * Build the report query.
   *
   * @param bool $applyLimit
   *
   * @return string
   */
  public function buildQuery($applyLimit = TRUE) {
    $this->buildGroupTempTable();
    $this->select();
    $this->buildPermissionClause();
    $this->where();
    $this->orderBy();

    if ($applyLimit && empty($this->_params['charts'])) {
      $this->limit();
    }
    // Custom Query
    $additionalForm = '';
    $additionalSelect = '';
    if ($this->isTableSelected('civicrm_mailing_group')) {
      $additionalForm .= "
        LEFT JOIN civicrm_mailing_group {$this->_aliases['civicrm_mailing_group']}
    ON {$this->_aliases['civicrm_mailing_group']}.mailing_id = {$this->_aliases['civicrm_mailing']}.id";
    }

    if ($this->campaignEnabled && $this->isTableSelected('civicrm_campaign')) {
      $additionalSelect = ' , campaign_civireport.title as civicrm_campaign_title ';
      $additionalForm .= "
        LEFT JOIN civicrm_campaign {$this->_aliases['civicrm_campaign']}
        ON {$this->_aliases['civicrm_campaign']}.id = {$this->_aliases['civicrm_mailing']}.campaign_id";
    }

    $tmp_where_partial = $this->_where;
    $sql = "SELECT SQL_CALC_FOUND_ROWS 
      mailing_civireport.id as civicrm_mailing_id, 
      mailing_civireport.created_date as civicrm_mailing_created_date,
      mailing_civireport.subject as civicrm_mailing_subject,
      mailing_job_civireport.start_date as civicrm_mailing_job_start_date,
      mailing_civireport.name as civicrm_mailing_name, 
      mailing_job_civireport.end_date as civicrm_mailing_job_end_date,
      if (a1.civicrm_mailing_event_queue_queue_count,               a1.civicrm_mailing_event_queue_queue_count, 0 )              as civicrm_mailing_event_queue_queue_count,
      if (a2.civicrm_mailing_event_delivered_delivered_count,       a2.civicrm_mailing_event_delivered_delivered_count, 0 )      as civicrm_mailing_event_delivered_delivered_count,
      if (a3.civicrm_mailing_event_bounce_bounce_count  ,           a3.civicrm_mailing_event_bounce_bounce_count, 0 )            as civicrm_mailing_event_bounce_bounce_count,
      if (a4.civicrm_mailing_event_opened_unique_open_count,        a4.civicrm_mailing_event_opened_unique_open_count, 0 )       as civicrm_mailing_event_opened_unique_open_count,
      if (a5.civicrm_mailing_event_opened_open_count,               a5.civicrm_mailing_event_opened_open_count, 0 )              as civicrm_mailing_event_opened_open_count,
      if (a6.civicrm_mailing_event_trackable_url_open_click_count,  a6.civicrm_mailing_event_trackable_url_open_click_count, 0 ) as civicrm_mailing_event_trackable_url_open_click_count,
      if (a7.civicrm_mailing_event_unsubscribe_unsubscribe_count,   a7.civicrm_mailing_event_unsubscribe_unsubscribe_count, 0 )  as civicrm_mailing_event_unsubscribe_unsubscribe_count,
      if (a8.civicrm_mailing_event_unsubscribe_optout_count,        a8.civicrm_mailing_event_unsubscribe_optout_count, 0 )       as civicrm_mailing_event_unsubscribe_optout_count,
      
      CONCAT(round(civicrm_mailing_event_opened_unique_open_count / civicrm_mailing_event_delivered_delivered_count * 100, 2 ),'%') as civicrm_mailing_event_opened_unique_open_rate,
      CONCAT(round(civicrm_mailing_event_opened_open_count        / civicrm_mailing_event_delivered_delivered_count * 100, 2), '%') as civicrm_mailing_event_opened_open_rate,
      CONCAT(round(civicrm_mailing_event_trackable_url_open_click_count /civicrm_mailing_event_delivered_delivered_count * 100,2 ),
      '%') as civicrm_mailing_event_trackable_url_open_CTR,
      CONCAT(round(civicrm_mailing_event_trackable_url_open_click_count / civicrm_mailing_event_opened_unique_open_count * 100, 2 ),
      '%') as civicrm_mailing_event_trackable_url_open_CTO,
      CONCAT(round(civicrm_mailing_event_delivered_delivered_count / civicrm_mailing_event_queue_queue_count* 100, 2), '%') as civicrm_mailing_event_delivered_accepted_rate,
      CONCAT(round(civicrm_mailing_event_bounce_bounce_count / civicrm_mailing_event_queue_queue_count * 100, 2), '%')      as civicrm_mailing_event_bounce_bounce_rate 
      {$additionalSelect}
    FROM civicrm_mailing mailing_civireport
      
      LEFT JOIN civicrm_mailing_job mailing_job_civireport
        ON mailing_civireport.id = mailing_job_civireport.mailing_id
            
      LEFT JOIN civicrm_mailing_event_queue mailing_event_queue_civireport
            ON mailing_event_queue_civireport.job_id = mailing_job_civireport.id  
      {$additionalForm} 
      LEFT JOIN (
        SELECT mailing_civireport.id,  COUNT(*) as civicrm_mailing_event_queue_queue_count
        FROM civicrm_mailing_event_queue
        INNER JOIN civicrm_mailing_job mailing_job_civireport ON (mailing_job_civireport.id = civicrm_mailing_event_queue.job_id)
        INNER JOIN civicrm_mailing mailing_civireport ON (mailing_job_civireport.mailing_id = mailing_civireport.id)
        {$additionalForm} 
        {$tmp_where_partial}
        GROUP BY mailing_civireport.id
      ) as a1 ON (mailing_civireport.id = a1.id)

      LEFT JOIN (
        select  mailing_civireport.id, count(DISTINCT mailing_event_delivered_civireport.event_queue_id) as civicrm_mailing_event_delivered_delivered_count 
        from civicrm_mailing_event_queue mailing_event_queue_civireport
        LEFT JOIN civicrm_mailing_event_bounce mailing_event_bounce_civireport
        ON mailing_event_bounce_civireport.event_queue_id = mailing_event_queue_civireport.id
        INNER JOIN  civicrm_mailing_event_delivered mailing_event_delivered_civireport  
        ON (mailing_event_delivered_civireport.event_queue_id = mailing_event_queue_civireport.id  AND mailing_event_bounce_civireport.id IS null)
        INNER JOIN civicrm_mailing_job mailing_job_civireport ON (mailing_job_civireport.id = mailing_event_queue_civireport.job_id)
        INNER JOIN civicrm_mailing mailing_civireport ON (mailing_job_civireport.mailing_id = mailing_civireport.id)
        {$additionalForm} 
        {$tmp_where_partial} 
        GROUP BY mailing_civireport.id  
      ) as a2  ON (mailing_civireport.id = a2.id)

      LEFT JOIN (
        select mailing_civireport.id, count(DISTINCT mailing_event_bounce_civireport.event_queue_id) as civicrm_mailing_event_bounce_bounce_count
        from civicrm_mailing_event_queue mailing_event_queue_civireport
        INNER JOIN  civicrm_mailing_event_bounce mailing_event_bounce_civireport  
        ON (mailing_event_bounce_civireport.event_queue_id = mailing_event_queue_civireport.id )
        INNER JOIN civicrm_mailing_job mailing_job_civireport ON (mailing_job_civireport.id = mailing_event_queue_civireport.job_id)   
        INNER JOIN civicrm_mailing mailing_civireport ON (mailing_job_civireport.mailing_id = mailing_civireport.id)
        {$additionalForm} 
        {$tmp_where_partial} 
        GROUP BY mailing_civireport.id  
      ) as a3  ON (mailing_civireport.id  = a3.id)
      

      LEFT JOIN (
        select mailing_civireport.id, count(DISTINCT mailing_event_opened_civireport.event_queue_id) as civicrm_mailing_event_opened_unique_open_count
        from civicrm_mailing_event_queue mailing_event_queue_civireport
        INNER JOIN  civicrm_mailing_event_opened mailing_event_opened_civireport  
        ON (mailing_event_opened_civireport.event_queue_id = mailing_event_queue_civireport.id )
        INNER JOIN civicrm_mailing_job mailing_job_civireport ON (mailing_job_civireport.id = mailing_event_queue_civireport.job_id)   
        INNER JOIN civicrm_mailing mailing_civireport ON (mailing_job_civireport.mailing_id = mailing_civireport.id)
        {$additionalForm} 
        {$tmp_where_partial}
        GROUP BY mailing_civireport.id  
      ) as a4  ON (mailing_civireport.id  = a4.id)
      
      LEFT JOIN (
        select mailing_civireport.id, count(mailing_event_opened_civireport.event_queue_id) as civicrm_mailing_event_opened_open_count
        from civicrm_mailing_event_queue mailing_event_queue_civireport
        INNER JOIN  civicrm_mailing_event_opened mailing_event_opened_civireport  
        ON (mailing_event_opened_civireport.event_queue_id = mailing_event_queue_civireport.id )
        INNER JOIN civicrm_mailing_job mailing_job_civireport ON (mailing_job_civireport.id = mailing_event_queue_civireport.job_id)   
        INNER JOIN civicrm_mailing mailing_civireport ON (mailing_job_civireport.mailing_id = mailing_civireport.id)
        {$additionalForm} 
        {$tmp_where_partial} 
        GROUP BY mailing_civireport.id   
      ) as a5  ON (mailing_civireport.id  = a5.id)
      
      LEFT JOIN (
        select  mailing_civireport.id, count( mailing_event_trackable_url_open_civireport.event_queue_id) as civicrm_mailing_event_trackable_url_open_click_count
        from civicrm_mailing_event_queue mailing_event_queue_civireport
        INNER JOIN  civicrm_mailing_event_trackable_url_open mailing_event_trackable_url_open_civireport  
        ON (mailing_event_trackable_url_open_civireport.event_queue_id = mailing_event_queue_civireport.id )
        INNER JOIN civicrm_mailing_job mailing_job_civireport ON (mailing_job_civireport.id = mailing_event_queue_civireport.job_id)   
        INNER JOIN civicrm_mailing mailing_civireport ON (mailing_job_civireport.mailing_id = mailing_civireport.id)
        {$additionalForm} 
        {$tmp_where_partial}
        GROUP BY mailing_civireport.id   
      ) as a6  ON (mailing_civireport.id  = a6.id)
      
      LEFT JOIN (
        select  mailing_civireport.id, count( DISTINCT mailing_event_unsubscribe_civireport.event_queue_id) as civicrm_mailing_event_unsubscribe_unsubscribe_count
        from civicrm_mailing_event_queue mailing_event_queue_civireport
        INNER JOIN  civicrm_mailing_event_unsubscribe mailing_event_unsubscribe_civireport  
        ON (mailing_event_unsubscribe_civireport.event_queue_id = mailing_event_queue_civireport.id AND  mailing_event_unsubscribe_civireport.org_unsubscribe = 0 )
        INNER JOIN civicrm_mailing_job mailing_job_civireport ON (mailing_job_civireport.id = mailing_event_queue_civireport.job_id)   
        INNER JOIN civicrm_mailing mailing_civireport ON (mailing_job_civireport.mailing_id = mailing_civireport.id)
        {$additionalForm} 
        {$tmp_where_partial} 
        GROUP BY mailing_civireport.id
       ) as a7  ON (mailing_civireport.id  = a7.id)
      LEFT JOIN (
        select mailing_civireport.id, count( DISTINCT mailing_event_optout_civireport.event_queue_id) as civicrm_mailing_event_unsubscribe_optout_count
        from civicrm_mailing_event_queue mailing_event_queue_civireport
        INNER JOIN  civicrm_mailing_event_unsubscribe mailing_event_optout_civireport  
        ON (mailing_event_optout_civireport.event_queue_id = mailing_event_queue_civireport.id AND mailing_event_optout_civireport.org_unsubscribe = 1)
        INNER JOIN civicrm_mailing_job mailing_job_civireport ON (mailing_job_civireport.id = mailing_event_queue_civireport.job_id)   
        INNER JOIN civicrm_mailing mailing_civireport ON (mailing_job_civireport.mailing_id = mailing_civireport.id)
        {$additionalForm} 
        {$tmp_where_partial} 
        GROUP BY mailing_civireport.id
       ) as a8  ON (mailing_civireport.id  = a8.id) 
     {$tmp_where_partial}
     GROUP BY mailing_civireport.id  
     {$this->_orderBy}  
     {$this->_limit}";
    CRM_Utils_Hook::alterReportVar('sql', $this, $this);
    $this->addToDeveloperTab($sql);
    return $sql;
  }
  /**
   * @return array
   */
  public static function getChartCriteria() {
    return array(
      'count' => array(
        'civicrm_mailing_event_delivered_delivered_count' => ts('Successful Deliveries'),
        'civicrm_mailing_event_bounce_bounce_count' => ts('Bounces'),
        'civicrm_mailing_event_opened_open_count' => ts('Total Opens'),
        'civicrm_mailing_event_opened_unique_open_count' => ts('Unique Opens'),
        'civicrm_mailing_event_trackable_url_open_click_count' => ts('Unique Clicks'),
        'civicrm_mailing_event_unsubscribe_unsubscribe_count' => ts('Unsubscribe'),
      ),
      'rate' => array(
        'civicrm_mailing_event_delivered_accepted_rate' => ts('Successful Delivery Rate'),
        'civicrm_mailing_event_bounce_bounce_rate' => ts('Bounce Rate'),
        'civicrm_mailing_event_opened_open_rate' => ts('Total Open Rate'),
        'civicrm_mailing_event_opened_unique_open_rate' => ts('Unique Open Rate'),
        'civicrm_mailing_event_trackable_url_open_CTR' => ts('Click-through Rate'),
        'civicrm_mailing_event_trackable_url_open_CTO' => ts('Click-through to Open Rate'),
      ),
    );
  }

  /**
   * @param $fields
   * @param $files
   * @param $self
   *
   * @return array
   */
  public static function formRule($fields, $files, $self) {
    $errors = array();

    if (empty($fields['charts'])) {
      return $errors;
    }

    $criteria = self::getChartCriteria();
    $isError = TRUE;
    foreach ($fields['fields'] as $fld => $isActive) {
      if (in_array($fld, array(
        'delivered_count',
        'bounce_count',
        'open_count',
        'click_count',
        'unsubscribe_count',
        'accepted_rate',
        'bounce_rate',
        'open_rate',
        'CTR',
        'CTO',
        'unique_open_rate',
        'unique_open_count',
      ))) {
        $isError = FALSE;
      }
    }

    if ($isError) {
      $errors['_qf_default'] = ts('For Chart view, please select at least one field from %1 OR %2.', array(
        1 => implode(', ', $criteria['count']),
        2 => implode(', ', $criteria['rate']),
      ));
    }

    return $errors;
  }

  /**
   * @param $rows
   */
  public function buildChart(&$rows) {
    if (empty($rows)) {
      return;
    }

    $criteria = self::getChartCriteria();

    $chartInfo = array(
      'legend' => ts('Mail Summary'),
      'xname' => ts('Mailing'),
      'yname' => ts('Statistics'),
      'xLabelAngle' => 20,
      'tip' => array(),
    );

    $plotRate = $plotCount = TRUE;
    foreach ($rows as $row) {
      $chartInfo['values'][$row['civicrm_mailing_name']] = array();
      if ($plotCount) {
        foreach ($criteria['count'] as $criteriaName => $label) {
          if (isset($row[$criteriaName])) {
            $chartInfo['values'][$row['civicrm_mailing_name']][$label] = $row[$criteriaName];
            $chartInfo['tip'][$label] = "{$label} #val#";
            $plotRate = FALSE;
          }
          elseif (isset($criteria['count'][$criteriaName])) {
            unset($criteria['count'][$criteriaName]);
          }
        }
      }
      if ($plotRate) {
        foreach ($criteria['rate'] as $criteriaName => $label) {
          if (isset($row[$criteria])) {
            $chartInfo['values'][$row['civicrm_mailing_name']][$label] = $row[$criteriaName];
            $chartInfo['tip'][$label] = "{$label} #val#";
            $plotCount = FALSE;
          }
          elseif (isset($criteria['rate'][$criteriaName])) {
            unset($criteria['rate'][$criteriaName]);
          }
        }
      }
    }

    if ($plotCount) {
      $criteria = $criteria['count'];
    }
    else {
      $criteria = $criteria['rate'];
    }

    $chartInfo['criteria'] = array_values($criteria);

    // dynamically set the graph size
    $chartInfo['xSize'] = ((count($rows) * 125) + (count($rows) * count($criteria) * 40));

    // build the chart.
    CRM_Utils_OpenFlashChart::buildChart($chartInfo, $this->_params['charts']);
    $this->assign('chartType', $this->_params['charts']);
  }

  /**
   * Alter display of rows.
   *
   * Iterate through the rows retrieved via SQL and make changes for display purposes,
   * such as rendering contacts as links.
   *
   * @param array $rows
   *   Rows generated by SQL, with an array for each row.
   */
  public function alterDisplay(&$rows) {
    $entryFound = FALSE;
    foreach ($rows as $rowNum => $row) {
      // CRM-16506
      if (array_key_exists('civicrm_mailing_id', $row)) {
        if (array_key_exists('civicrm_mailing_name', $row)) {
          $rows[$rowNum]['civicrm_mailing_name_link'] = CRM_Report_Utils_Report::getNextUrl('mailing/detail',
            'reset=1&force=1&mailing_id_op=eq&mailing_id_value=' . $row['civicrm_mailing_id'],
            $this->_absoluteUrl, $this->_id, $this->_drilldownReport
          );
          $rows[$rowNum]['civicrm_mailing_name_hover'] = ts('View Mailing details for this mailing');
          $entryFound = TRUE;
        }
        if (array_key_exists('civicrm_mailing_event_opened_open_count', $row)) {
          $rows[$rowNum]['civicrm_mailing_event_opened_open_count'] = CRM_Mailing_Event_BAO_Opened::getTotalCount($row['civicrm_mailing_id']);
          $entryFound = TRUE;
        }
      }
      // skip looking further in rows, if first row itself doesn't
      // have the column we need
      if (!$entryFound) {
        break;
      }
    }
  }

}

