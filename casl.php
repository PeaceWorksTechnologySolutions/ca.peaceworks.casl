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
    $group_check = civicrm_api3('CustomGroup', 'get', ['name' => "casl", 'sequential' => 1]);

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
    } else {
        $group_id = $group_check['values'][0]['id'];
    }

    // Check and create custom field for consent type
    $type_check = civicrm_api3('CustomField', 'get', ['name' => "consent_type"]);

    if ($type_check['count'] == 0) {
        $type_params = array(
            'custom_group_id' => $group_id,
            'name' => 'consent_type',
            'label' => 'Consent Type',
            'html_type' => 'Select',
            'data_type' => 'String',
            'help_pre' => 'Leave blank if no consent has been given. "Exempt" should be used rarely, and is for contacts such as staff.',
            'is_required' => 0,
            'is_searchable' => 1,
            'is_active' => 1,
            'text_length' => 31,
            'option_values' => array(
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
            'help_pre' => 'The most recent date where consent has been provided.',
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
            'help_pre' => 'Where the most recent consent was obtained from, such as a newsletter sign-up form.',
            'is_required' => 0,
            'is_searchable' => 1,
            'is_active' => 1,
            'text_length' => 510,
        );
        $method_result = civicrm_api3('CustomField', 'create', $method_params);
    }

    // Check and create custom field for tracking if no-bulk-email was checked by another way
    $method_check = civicrm_api3('CustomField', 'get', ['name' => 'flagged_already']);

    if ($method_check['count'] == 0) {
        $already_params = array(
            'custom_group_id' => $group_id,
            'name' => 'flagged_already',
            'label' => 'Flagged Otherwise',
            'html_type' => 'Radio',
            'data_type' => 'Boolean',
            'help_pre' => 'If yes, indicates that the no-bulk-email flag has been turned on for a different reason other than CASL (i.e. intentional opt-out).',
            'is_required' => 0,
            'is_searchable' => 0,
            'is_active' => 1,
            'is_view' => 1, // view only
            'text_length' => 255,
        );
        $already_result = civicrm_api3('CustomField', 'create', $already_params);
    }

    // Check and create activity_type field for casl
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
 */
function casl_civicrm_navigationMenu(&$menu) {
  _casl_civix_insert_navigation_menu($menu, 'Administer/Communications', array(
    'label' => E::ts('CASL Settings'),
    'name' => 'casl_support_settings',
    'url' => 'civicrm/admin/casl?reset=1',
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _casl_civix_navigationMenu($menu);
}

/**
 * Helper functions to look up API ids
 */
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
function _casl_get_consent_method_id() {
    $result = civicrm_api3('CustomField', 'get', ['name' => "consent_method"]);
    if ($result['count'] != 1) return FALSE;
    return 'custom_' . $result['id'];
}
function _casl_get_flagged_already_id() {
    $result = civicrm_api3('CustomField', 'get', ['name' => "flagged_already"]);
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
 * Helper function to set contact's latest consent date
 */
function _casl_set_consent_date($contact_id, $cdate, $notes=NULL) {
    $consent_date_id   = _casl_get_consent_date_id();
    $consent_method_id = _casl_get_consent_method_id();

    // First check if new date is later than existing record
    $result = civicrm_api3('Contact', 'get', ['contact_id' => $contact_id, 'return' => $consent_date_id]);
    if (!empty($result['values'][$contact_id][$consent_date_id]) and 
        (new DateTime($result['values'][$contact_id][$consent_date_id]) >= new DateTime($cdate))) {
        return;
    }
    
    // Set date
    $result = civicrm_api3('Contact', 'create', ['id' => $contact_id, $consent_date_id => $cdate]);
    // Set method
    if ($notes) {
        $result = civicrm_api3('Contact', 'create', ['id' => $contact_id, $consent_method_id => $notes]);
    }
  
    // Log on the contact as an activity
    $details = ts('The CASL Support extension has automatially set the consent date on this contact to '. $cdate .'.');
    if ($notes) {
        $details .= ' '. ts('Reason for this change') .': '. $notes;
    }
    $params = array (
        'source_contact_id' => 1,
        'activity_type_id' => 'casl',
        'activity_date_time' => date('Y/m/d H:i'),
        'subject' => ts('Consent date set by CASL'),
        'details' => $details,
        'status_id' => 'Completed',
        'api.ActivityContact.create' => ['contact_id' => $contact_id, 'record_type_id' => 3],
    );
    $result = civicrm_api3('Activity', 'create', $params);
}

/**
 * Helper function to set contact's no-bulk-email flag
 */
function _casl_set_no_bulk_email_flag($contact_id, $value=TRUE) {
    // Look up old contact for reference
    $old = civicrm_api3('Contact', 'get', ['id' => $contact_id, 'return' => 'is_opt_out']);

    // If no-bulk-email not set yet, set it
    if ($old['values'][$contact_id]['is_opt_out'] == '0') {
        // Set flag
        $result = civicrm_api3('Contact', 'create', ['id' => $contact_id, 'is_opt_out' => $value]);
      
        // Log on the contact as an activity
        $params = array (
            'source_contact_id' => 1,
            'activity_type_id' => 'casl',
            'activity_date_time' => date('Y/m/d H:i'),
            'subject' => ts('No-bulk-email set by CASL'),
            'details' => ts('The CASL Support extension has automatially set the "no-bulk-email" flag on this contact.'),
            'status_id' => 'Completed',
            'api.ActivityContact.create' => ['contact_id' => $contact_id, 'record_type_id' => 3],
        );
        $result = civicrm_api3('Activity', 'create', $params);

        // Unset flagged_already, since we're setting no-bulk-email
        $already_id = _casl_get_flagged_already_id();
        $result = civicrm_api3('Contact', 'create', ['id' => $contact_id, $already_id => 0]);
    }
}

/**
 * Implements hook_civicrm_custom
 * When a contact's custom fields are updated, uses CASL fields to determine the no-bulk-email flag
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
            _casl_set_no_bulk_email_flag($entityid);
        }
    }
}

/**
 * Implements hook_civicrm_cron
 * Searches all contacts on cron to find any instances of implicit consent having expired
 * Turns on the no-bulk-email flag if consent has expired
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
        'is_opt_out' => 0,
        'options' => array('limit' => 0),
        'return' => "contact_type,". $consent_type_id .",". $consent_date_id,
    ));

    foreach ($result['values'] as $contact) {
        $consent_date = $contact[$consent_date_id];
        $contact_id = $contact['id']; 
        if (_casl_test_expiration($consent_date)) {
            _casl_set_no_bulk_email_flag($contact_id);
        }
    }
}

/**
 * Implements hook_civicrm_check
 * Checks if custom fields exist
 */
function casl_civicrm_check(&$messages) {
    $consent_group_id  = _casl_get_casl_group_id();
    $consent_type_id   = _casl_get_consent_type_id();
    $consent_date_id   = _casl_get_consent_date_id();
    $consent_method_id = _casl_get_consent_method_id();
    $already_id        = _casl_get_flagges_already_id();

    if (!$consent_group_id || !$consent_type_id || !$consent_date_id || !$consent_method_id || !$already_id) {
        $m = ts('There was an error in looking up the CASL fields in your system. The CASL Support extension will not be able to check contacts for CASL consent. To resolve this, ensure that the CASL custom group and fields still exist, are enabled, and have the correct names. The following query on your CiviCRM database should return 4 results named "consent_type", "consent_date", "consent_method", and "flagged_already".');
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
            $message = ts('This contact does not have CASL consent for emails. Typically the no-bulk-email flag for them would have already been set. If so, they will not receive any of your bulk mailings in CiviMail.');
            CRM_Core_Session::setStatus($message, 'CASL Consent Absent');
        }
    }
}

/**
 * Implements hook_civicrm_post
 */
function casl_civicrm_post($op, $objectName, $objectId, &$objectRef) {
    // If new event reg made, update consent date
    if ($op=='create' && $objectName=='Participant') {
//drupal_set_message('<pre>'. print_r($objectRef,true) .'</pre>');        
        $autofill = CRM_Core_BAO_Setting::getItem('casl', 'autofill');
//drupal_set_message('<pre>'. print_r($autofill,true) .'</pre>');        
        if (($autofill == 2) or ($autofill == 4)) {
            $contact_id = $objectRef->contact_id;
            $d = $objectRef->register_date;
            $d = date('Y/m/d', strtotime($d));
            // Look up event data
            $event = civicrm_api3('Event', 'get', ['id' => $objectRef->event_id]);
            $name = $event['values'][$objectRef->event_id]['title'];
            // Set fields
            _casl_set_consent_date($contact_id, $d, ts('Registration in event') .': '. $name);
            // TODO: then check about unsetting no-bulk-email flag?
        }
    }

    // If new contribution made, update consent date
    if ($op=='create' && $objectName=='Contribution') {
        $autofill = CRM_Core_BAO_Setting::getItem('casl', 'autofill');
        if (($autofill == 3) or ($autofill == 4)) {
            $contact_id = $objectRef->contact_id;
            $d = $objectRef->receive_date;
            $d = date('Y/m/d', strtotime($d));
            // Set fields
            _casl_set_consent_date($contact_id, $d, ts('Contribution received for') .' $'. number_format($objectRef->total_amount, 2));
            // TODO: then check about unsetting no-bulk-email flag?
        }
    }
}

/**
 * Implements hook_civicrm_post
 */
function casl_civicrm_pre($op, $objectName, $id, &$params) {
    // If a contact's no-bulk-email is being set, log it so that we catch other sources
    $names = ['Contact', 'Individual', 'Household', 'Organization'];
    if (($op=='create' || $op=='edit') && in_array($objectName, $names)) {
        if (array_key_exists('is_opt_out', $params) and $params['is_opt_out']) {
            // Ignore if it's not changing
            if ($id) {
                $result = civicrm_api3('Contact', 'get', ['id'=>$id, 'return'=>'is_opt_out']);
                if ($result['values'][$id]['is_opt_out']) {
                    return;
                }
            }

            $already_id = _casl_get_flagged_already_id();
            $result = civicrm_api3('Contact', 'create', ['id' => $id, $already_id => 1]);
        }
    }
}

