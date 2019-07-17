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
 * Helper function
 * Checks and creates needed custom fields
 */
function _casl_create_fields() {
    // Check and create custom group
    $group_check = civicrm_api3('CustomGroup', 'get', ['name' => "casl"]);

    if ($group_check['count'] == 0) {
        //Create the custom group
        $group_params = array(
            'title' => 'CASL',
            'name' => 'casl',
            'extends' => 'Contact',
            'help_pre' => ts("Fields to manage CASL consent"),
            'is_multiple' => 0,
            'collapse_adv_display' => 1,
            'is_reserved' => 1,
            'style' => 'Inline',
        );

        $group_result = civicrm_api3('CustomGroup', 'create', $group_params);
        $group_id = $group_result['id'];
    }

    // Check and create custom field for consent type
    $type_check = civicrm_api3('CustomField', 'get', ['name' => "consent_type"]);

    if ($type_check['count'] == 0) {
        //Create the custom fields in that custom group
        $type_params = array(
            'custom_group_id' => $group_id,
            'name' => 'consent_type',
            'label' => 'Consent Type',
            'html_type' => 'Select',
            'data_type' => 'String',
            'is_required' => 0,
            'is_searchable' => 1,
            'is_active' => 1,
            'text_length' => 31,
            'option_values' => array(
                'None' => 'None',
                'Implicit' => 'Implicit',
                'Explicit' => 'Explicit',
                'Exempt' => 'Exempt'
            ),
            'default_value' => 'None',
        );
        $type_result = civicrm_api3('CustomField', 'create', $type_params);
    }

    // Check and create custom field for consent date
    $date_check = civicrm_api3('CustomField', 'get', ['name' => "consent_date"]);

    if ($date_check['count'] == 0) {
        $date_params = array(
            'custom_group_id' => $group_id,
            'name' => 'consent_date',
            'label' => 'Date of Consent',
            'data_type' => 'Date',
            'html_type' => 'Select Date',
            'is_required' => 0,
            'is_searchable' => 1,
            'is_active' => 1,
            'start_date_years' => 10,
            'end_date_years' => 1,
            'date_format' => 'd M yy',
            'is_search_range' => 1,
        );
        $date_result = civicrm_api3('CustomField', 'create', $date_params);
    }

    // Check and create custom field for consent method
    $method_check = civicrm_api3('CustomField', 'get', ['name' => "consent_method"]);

    if ($method_check['count'] == 0) {
        $method_params = array(
            'custom_group_id' => $group_id,
            'name' => 'consent_method',
            'label' => 'Method of Consent',
            'html_type' => 'Text',
            'data_type' => 'String',
            'is_required' => 0,
            'is_searchable' => 1,
            'is_active' => 1,
            'text_length' => 510,
        );
        $method_result = civicrm_api3('CustomField', 'create', $method_params);
    }

    // Check and create custom field for consent method
    $method_check = civicrm_api3('OptionValue', 'get', ['name' => 'casl']);

    if ($method_check['count'] == 0) {
        $type_params = array(
            'option_group_id' => 'activity_type',
            'name' => 'casl',
            'label' => 'CASL Event',
            'icon' => 'fa-envelope',
            'is_active' => 1,
            'is_reserved' => 1,
        );
        $type_result = civicrm_api3('OptionValue', 'create', $type_params);
    }
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function casl_civicrm_install() {
  _casl_civix_civicrm_install();
  _casl_create_fields();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function casl_civicrm_postInstall() {
  _casl_civix_civicrm_postInstall();
  CRM_Core_BAO_Setting::setItem(1, 'casl', 'grant_consent');
  CRM_Core_BAO_Setting::setItem(1, 'casl', 'autofill');
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
  _casl_create_fields();
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

/**
 * Helper function to look up API id
 */
function _casl_get_casl_group_id() {
    $result = civicrm_api3('CustomGroup', 'get', ['name' => 'casl']);
    if ($result['count'] != 1) return FALSE;
    return $result['id'];
}

/**
 * Helper function to look up API id
 */
function _casl_get_consent_type_id() {
    $result = civicrm_api3('CustomField', 'get', ['name' => "consent_type"]);
    if ($result['count'] != 1) return FALSE;
    return 'custom_' . $result['id'];
}

/**
 * Helper function to look up API id
 */
function _casl_get_consent_date_id() {
    $result = civicrm_api3('CustomField', 'get', ['name' => "consent_date"]);
    if ($result['count'] != 1) return FALSE;
    return 'custom_' . $result['id'];
}

/**
 * Helper function to test consent date expiry
 */
function _casl_test_expiration($consent_date) {
    //Return expiration boolean based on whether the expiry time is before or after current time
    $consent_date = new DateTime($consent_date);
    if (date_modify($consent_date, '+2 years') < new DateTime()) {
        return TRUE;
    }
    return FALSE;
}

/**
 * Helper function to check consent for a contact
 */
function _casl_check_contact_has_consent($contact_id) {
    $consent_type_id  = _casl_get_consent_type_id();
    $consent_date_id  = _casl_get_consent_date_id();

    if (!$consent_type_id || !$consent_date_id) {
        $message = ts('There was an error in looking up the CASL fields in your system. The CASL Support extension will not be able to check contacts for CASL consent.');
        CRM_Core_Session::setStatus($message, 'CASL Error', 'alert', ['expires'=>0]);
        return true;
    }

    $result = civicrm_api3('Contact', 'get', array(
        'contact_id' => $contact_id,
        'return' => "contact_type,". $consent_type_id .",". $consent_date_id,
    ));
    $consent_type = $result['values'][$contact_id][$consent_type_id];

    if ($consent_type == "Implicit") {
        // Test if implicit consent has expired after 2 years
        $consent_date = $result['values'][$contact_id][$consent_date_id];
        if (_casl_test_expiration($consent_date)) {
            return false;
        }
    } else if ($consent_type != "Explicit" && $consent_type != "Exempt") {
        // Check if any other consent exists
        return false;
    }
    return true;
}

/**
 * Helper function to set contact's do-not-email flag
 */
function _casl_set_do_not_email_flag($contact_ID, $do_not_email_flag=TRUE) {
   $params = array (
       'id' => $contact_ID,
       'do_not_email' => $do_not_email_flag
   );
   $result = civicrm_api3('Contact', 'create', $params);

   // Log on the contact as an activity
   $params = array (
       'source_contact_id' => 1,
       'activity_type_id' => 'casl',
       'activity_date_time' => date('Y/m/d H:i'),
       'subject' => ts('Do-not-email set by CASL'),
       'details' => ts('The CASL Support extension has automatially set the "do-not-email" flag on this contact.'),
       'status_id' => 'Completed',
       'api.ActivityContact.create' => ['contact_id' => $contact_ID, 'record_type_id' => 3],
   );
   $result = civicrm_api3('Activity', 'create', $params);
}

/**
 * Implements hook_civicrm_custom
 * When a contact's custom fields are updated, uses CASL fields to determine the "Do Not Email" flag
 */
function casl_civicrm_custom($op, $groupid, $entityid, &$params) {
    $consent_group_id = _casl_get_casl_group_id();

    if (!$consent_group_id) {
        $message = ts('There was an error in looking up the CASL fields in your system. The CASL Support extension will not be able to check contacts for CASL consent.');
        CRM_Core_Session::setStatus($message, 'CASL Error', 'alert', ['expires'=>0]);
        return true;
    }

    if (($groupid==$consent_group_id) and ($op=='create' || $op=='edit')) {
        if (!_casl_check_contact_has_consent($entityid)) {
            _casl_set_do_not_email_flag($entityid);
        }
    }
}

/**
 * Implements hook_civicrm_cron
 * Searches all contacts on cron to find any instances of implicit consent having expired
 * Turns on the Do Not Email flag if consent has expired
 */
function casl_civicrm_cron() {
    $consent_type_id  = _casl_get_consent_type_id();
    $consent_date_id  = _casl_get_consent_date_id();

    if (!$consent_type_id || !$consent_date_id) {
        // FIXME: test this functionality
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
            _casl_set_do_not_email_flag($contact_ID);
        }
    }
}

/**
 * Implements hook_civicrm_check
 * Checks if custom fields exist
 */
function casl_civicrm_check(&$messages) {
    $consent_group_id = _casl_get_casl_group_id();
    $consent_type_id  = _casl_get_consent_type_id();
    $consent_date_id  = _casl_get_consent_date_id();

    if (!$consent_group_id || !$consent_type_id || !$consent_date_id) {
        $m = ts('There was an error in looking up the CASL fields in your system. The CASL Support extension will not be able to check contacts for CASL consent. To resolve this, ensure that the CASL custom group and fields still exist, are enabled, and have the correct names. The following query on your CiviCRM database should return 3 results named "consent_type", "consent_date", and "consent_method".');
        $m .= '<br/><pre>select cf.name,cf.label from civicrm_custom_field cf left join civicrm_custom_group cg on cg.id=cf.custom_group_id where cg.name=\'casl\'</pre>';
        $messages[] = new CRM_Utils_Check_Message(
          'casl_missing',
          $m,
          ts('CASL Fields Missing'),
          \Psr\Log\LogLevel::CRITICAL,
          'fa-envelope'
        );
    }
}

/**
 * Implements hook_civicrm_pageRun
 * Display warning when viewing contact
 */
function casl_civicrm_pageRun(&$page) {
    if ($page->getVar('_name') == 'CRM_Contact_Page_View_Summary') {
        if (!_casl_check_contact_has_consent($page->getVar('_contactId'))) {
            $message = ts('This contact does not have CASL consent for emails. Typically the do-not-email flag for them would have already been set. If so, they will not receive any of your bulk mailings in CiviMail.');
            CRM_Core_Session::setStatus($message, 'CASL Consent Absent');
        }
    }
}

