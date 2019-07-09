<?php

require_once 'casl.civix.php';
use CRM_Casl_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */
function casl_civicrm_config(&$config) {
  _casl_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function casl_civicrm_xmlMenu(&$files) {
  _casl_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function casl_civicrm_install() {
  _casl_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function casl_civicrm_postInstall() {
  _casl_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function casl_civicrm_uninstall() {
  _casl_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function casl_civicrm_enable() {
  _casl_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function casl_civicrm_disable() {
  _casl_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function casl_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _casl_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function casl_civicrm_managed(&$entities) {
  _casl_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function casl_civicrm_caseTypes(&$caseTypes) {
  _casl_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function casl_civicrm_angularModules(&$angularModules) {
  _casl_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function casl_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _casl_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function casl_civicrm_entityTypes(&$entityTypes) {
  _casl_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function casl_civicrm_themes(&$themes) {
  _casl_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function casl_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function casl_civicrm_navigationMenu(&$menu) {
  _casl_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _casl_civix_navigationMenu($menu);
} // */

function _casl_get_casl_group_id() {
    $result = civicrm_api3('CustomGroup', 'get', ['name' => 'casl']);
    if ($result['count'] != 1) return FALSE;
    return $result['id'];
}

function _casl_get_consent_type_id() {
    $result = civicrm_api3('CustomField', 'get', ['name' => "consent_type"]);
    if ($result['count'] != 1) return FALSE;
    return 'custom_' . $result['id'];
}

function _casl_get_consent_date_id() {
    $result = civicrm_api3('CustomField', 'get', ['name' => "consent_date"]);
    if ($result['count'] != 1) return FALSE;
    return 'custom_' . $result['id'];
}

function _casl_test_expiration($consent_date) {
    //Return expiration boolean based on whether the expiry time is before or after current time
    $consent_date = new DateTime($consent_date);
    if (date_modify($consent_date, '+2 years') < new DateTime()) {
        return TRUE;
    }
    return FALSE;
}

function _casl_set_do_not_email_flag($contact_ID, $do_not_email_flag) {
   $params = array (
        'id' => $contact_ID,
        'do_not_email' => $do_not_email_flag
   );
   $result = civicrm_api3('contact', 'create', $params);
   // TODO: create an activity on this.
}

/**
 * When a contact's custom fields are updated, uses CASL fields to determine the "Do Not Email" flag
 */
function casl_civicrm_custom($op, $groupid, $entityid, &$params) {
    $consent_group_id = _casl_get_casl_group_id();
    $consent_type_id  = _casl_get_consent_type_id();
    $consent_date_id  = _casl_get_consent_date_id();

    if (!$consent_group_id || !$consent_type_id || !$consent_date_id) {
        $message = ts('There was an error in looking up the CASL fields in your system. The CASL Support extension will not be able to check contacts for CASL consent.');
        CRM_Core_Session::setStatus($message, 'CASL Error', 'alert', ['expires': 0]);
        return;
    }

    if (($groupid==$consent_group_id) and ($op=='create' || $op=='edit')) {
        civicrm_initialize();
        $result = civicrm_api3('Contact', 'get', array(
            'contact_id' => $entityid,
            'return' => "contact_type,". $consent_type_id .",". $consent_date_id,
        ));
        $consent_type = $result['values'][$entityid][$consent_type_id];

        // Test if implicit consent has expired after 2 years
        if ($consent_type == "Implicit") {
            $consent_date = $result['values'][$entityid][$consent_date_id];
            if (_casl_test_expiration($consent_date)) {
                _casl_set_do_not_email_flag($entityid, TRUE);
            }
        } else if ($consent_type != "Explicit" && $consent_type != "Exempt") {
            // Set do-not-email flag in any scenario other than the protected cases
            _casl_set_do_not_email_flag($entityid, TRUE);
        }
    }
}

/**
 * Searches all contacts on cron to find any instances of implicit consent having expired
 * Turns on the Do Not Email flag if consent has expired
 */
function casl_civicrm_cron() {
    $consent_group_id = _casl_get_casl_group_id();
    $consent_type_id  = _casl_get_consent_type_id();
    $consent_date_id  = _casl_get_consent_date_id();

    if (!$consent_group_id || !$consent_type_id || !$consent_date_id) {
        // FIXME
        Civi::log()->error('During a cron run there was an error in looking up the CASL fields in your system. The CASL Support extension will not be able to check contacts for CASL consent.');
        return;
    }

    $result = civicrm_api3('Contact', 'get', array(
        $consent_type_id => "Implicit",
        'do_not_email' => 0,
        'options' => array('limit' => 0),
        'return' => "contact_type,". $consent_type_id .",". $consent_date_id,
    ));

    foreach ($result['values'] as $contact) {
        $consent_date = $contact[$consent_date_id];
        $contact_ID = $contact['id']; 
        if (_casl_test_expiration($consent_date)) {
            _casl_set_do_not_email_flag($contact_ID, TRUE);
        }
    }
}

/**
 * Implementation of hook_civicrm_check
 * Checks if custom fields exist
 */
function casl_civicrm_check(&$messages) {
    $consent_group_id = _casl_get_casl_group_id();
    $consent_type_id  = _casl_get_consent_type_id();
    $consent_date_id  = _casl_get_consent_date_id();

    if (!$consent_group_id || !$consent_type_id || !$consent_date_id) {
        $m = ts('There was an error in looking up the CASL fields in your system. The CASL Support extension will not be able to check contacts for CASL consent. To resolve this, ensure that the CASL custom group and fields still exist, are enabled, and have the correct names.');
        $messages[] = new CRM_Utils_Check_Message(
          'casl_missing',
          $m,
          ts('CASL Fields Missing'),
          \Psr\Log\LogLevel::CRITICAL,
          'fa-envelope'
        );
    }
}

