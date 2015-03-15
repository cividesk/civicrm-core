<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2015                                |
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
 * @copyright CiviCRM LLC (c) 2004-2015
 * $Id$
 *
 */
class CRM_Utils_Hook_Standalone extends CRM_Utils_Hook {

  /**
   *
   * @see CRM_Utils_Hook::invoke()
   *
   * @param integer $numParams Number of parameters to pass to the hook
   * @param unknown $arg1 parameter to be passed to the hook
   * @param unknown $arg2 parameter to be passed to the hook
   * @param unknown $arg3 parameter to be passed to the hook
   * @param unknown $arg4 parameter to be passed to the hook
   * @param unknown $arg5 parameter to be passed to the hook
   * @param mixed $arg6
   * @param string $fnSuffix function suffix, this is effectively the hook name
   *
   * @return Ambigous <boolean, multitype:>
   */
  function invoke($numParams,
    &$arg1, &$arg2, &$arg3, &$arg4, &$arg5, &$arg6,
    $fnSuffix) {

    return $this->commonInvoke($numParams,
      $arg1, $arg2, $arg3, $arg4, $arg5, $arg6,
      $fnSuffix, 'standalone'
    );
  }
}