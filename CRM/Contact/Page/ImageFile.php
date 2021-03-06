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
class CRM_Contact_Page_ImageFile extends CRM_Core_Page {
  /**
   * Time to live (seconds).
   *
   * @var int
   *
   * 12 hours: 12 * 60 * 60 = 43200
   */
  private $ttl = 43200;

  /**
   * Run page.
   *
   * @throws \Exception
   */
  public function run() {
    $photo = CRM_Utils_Request::retrieve('photo', 'String', CRM_Core_DAO::$_nullObject);
    if (!preg_match('/^[^\/]+\.(jpg|jpeg|png|gif)$/i', $photo)) {
      throw new CRM_Core_Exception(ts('Malformed photo name'));
    }

    // FIXME Optimize performance of image_url query
    $sql = "SELECT id FROM civicrm_contact WHERE image_url like %1;";
    $params = [
      1 => ["%" . $photo, 'String'],
    ];

    $dao = CRM_Core_DAO::executeQuery($sql, $params);
    $cid = NULL;
    while ($dao->fetch()) {
      $cid = $dao->id;
    }
    if ($cid) {
      $config = CRM_Core_Config::singleton();
      $fileName = pathinfo($photo, PATHINFO_FILENAME);
      $fileExtension = pathinfo($photo, PATHINFO_EXTENSION);
      // Functionality to resize image by passing width and height in url along with photo name,
      // this will create imange with width and height as suffix to image name
      // e.g img_112112133313.png will be img_112112133313_150_150.png
      $width  = CRM_Utils_Request::retrieve('width', 'Positive', CRM_Core_DAO::$_nullObject);
      $height = CRM_Utils_Request::retrieve('height', 'Positive', CRM_Core_DAO::$_nullObject);
      $thisFileName = $config->customFileUploadDir . $photo;
      if ($width && $height) {
        $suffix = '_w' . $width . '_h' . $height;
        try {
          $thisFileName = CRM_Utils_File::resizeImage($thisFileName, $width, $height, $suffix, TRUE, 'cache', TRUE);
        } catch (CRM_Core_Exception $e) {
          // processing error
        }
      }

      $this->download(
        $thisFileName,
        'image/' . (strtolower($fileExtension) == 'jpg' ? 'jpeg' : $fileExtension),
        $this->ttl
      );
      CRM_Utils_System::civiExit();
    }
    else {
      throw new CRM_Core_Exception(ts('Photo does not exist'));
    }
  }

  /**
   * Download image.
   *
   * @param string $file
   *   Local file path.
   * @param string $mimeType
   * @param int $ttl
   *   Time to live (seconds).
   */
  protected function download($file, $mimeType, $ttl) {
    if (!file_exists($file)) {
      header("HTTP/1.0 404 Not Found");
      return;
    }
    elseif (!is_readable($file)) {
      header('HTTP/1.0 403 Forbidden');
      return;
    }
    CRM_Utils_System::setHttpHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', CRM_Utils_Time::getTimeRaw() + $ttl));
    CRM_Utils_System::setHttpHeader("Content-Type", $mimeType);
    CRM_Utils_System::setHttpHeader("Content-Disposition", "inline; filename=\"" . basename($file) . "\"");
    CRM_Utils_System::setHttpHeader("Cache-Control", "max-age=$ttl, public");
    CRM_Utils_System::setHttpHeader('Pragma', 'public');
    readfile($file);
  }

}
