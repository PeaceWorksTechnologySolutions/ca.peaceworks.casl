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
            'help_pre' => ts('Fields to manage CASL consent.'),
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
            'help_pre' => ts('Ideally this field should ALWAYS be filled out.'),
            'help_post' => ts('"Exempt" should be used rarely. It\'s meant for contacts who don\'t need to give consent, such as staff.'),
            'weight' => 1,
            'is_required' => 0,
            'is_searchable' => 1,
            'is_active' => 1,
            'text_length' => 31,
            'option_values' => array(
                'None' => 'No consent',
                'Implicit' => 'Implicit',
                'Explicit' => 'Explicit',
                'Exempt' => 'Exempt'
            ),
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
            'help_post' => ts('The most recent date when consent has been provided.'),
            'weight' => 2,
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

    // Check and create custom field for expiry date
    $expiry_check = civicrm_api3('CustomField', 'get', ['name' => "expiry_date"]);

    if ($expiry_check['count'] == 0) {
        $expiry_params = array(
            'custom_group_id' => $group_id,
            'name' => 'expiry_date',
            'label' => 'Date of Consent Expiry',
            'data_type' => 'Date',
            'html_type' => 'Select Date',
            'help_post' => ts('When the implicit CASL consent expires, 2 years after the consent date.'),
            'weight' => 2,
            'is_view' => 1, // view only
            'is_required' => 0,
            'is_searchable' => 1,
            'is_active' => 1,
            'start_date_years' => 10,
            'end_date_years' => 1,
            'date_format' => 'd M yy',
            'is_search_range' => 1,
        );
        $expiry_result = civicrm_api3('CustomField', 'create', $expiry_params);
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
            'help_post' => ts('Where the most recent consent was obtained from, such as a newsletter sign-up form.'),
            'weight' => 3,
            'is_required' => 0,
            'is_searchable' => 1,
            'is_active' => 1,
            'text_length' => 510,
        );
        $method_result = civicrm_api3('CustomField', 'create', $method_params);
    }

    // Check and create custom field for tracking if no-bulk-email was checked by another way
    $already_check = civicrm_api3('CustomField', 'get', ['name' => 'flagged_already']);

    if ($already_check['count'] == 0) {
        $already_params = array(
            'custom_group_id' => $group_id,
            'name' => 'flagged_already',
            'label' => 'Flagged Otherwise',
            'html_type' => 'Radio',
            'data_type' => 'Boolean',
            'help_pre' => ts('Used for tracking data changes.'),
            'help_post' => ts('This field is used to track if the no-bulk-email flag has been turned on for a different reason other than CASL (i.e. intentional opt-out). When that situation is detected the field is automatically set to "yes". This then limits some functionality the CASL calculations provide, to prevent accidentally turning off the no-bulk-email flag when someone has intentionally wanted it to be on.'),
            'weight' => 4,
            'is_required' => 0,
            'is_searchable' => 0,
            'is_active' => 1,
            'is_view' => 1, // view only
            'text_length' => 255,
        );
        $already_result = civicrm_api3('CustomField', 'create', $already_params);
    }

    // Check and create activity_type field for casl
    $option_check = civicrm_api3('OptionValue', 'get', ['name' => 'casl']);

    if ($option_check['count'] == 0) {
        $option_params = array(
            'option_group_id' => 'activity_type',
            'name' => 'casl',
            'label' => 'CASL Event',
            'icon' => 'fa-envelope',
            'is_active' => 1,
            'is_reserved' => 1,
        );
        $option_result = civicrm_api3('OptionValue', 'create', $option_params);
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
  CRM_Core_BAO_Setting::setItem(1, 'casl', 'ignore_null');
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
function _casl_get_expiry_date_id() {
    $result = civicrm_api3('CustomField', 'get', ['name' => "expiry_date"]);
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
 * Helper functions to log CASL action as a civi activity
 */
function _casl_log_activity($contact_id, $subject, $details) {
    $params = array (
        'source_contact_id' => 1,
        'activity_type_id' => 'casl',
        'activity_date_time' => date('Y/m/d H:i'),
        'subject' => $subject,
        'details' => $details,
        'status_id' => 'Completed',
        'api.ActivityContact.create' => ['contact_id' => $contact_id, 'record_type_id' => 3],
    );
    $result = civicrm_api3('Activity', 'create', $params);
}

/**
 * Helper function to update expiry_date and test consent date expiry
 */
function _casl_update_and_test_expiration($consent_date, $contact_id) {
    ///check that consent date is valid. If not, empty expiry date as well.
    if (empty($consent_date)) {
        $expiry_date = '';
    }
    else {
        //If consent date is valid, update the expired date field
        $consent_date = new DateTime($consent_date);
        $expiry_date = date_modify($consent_date, '+2 years')->format('Y/m/d');
    }
    $expiry_field = _casl_get_expiry_date_id();
    $update_contact = civicrm_api3('Contact', 'create', [
      'id' => $contact_id,
      $expiry_field => $expiry_date,
    ]);

    //Return expiration boolean based on whether the expiry time is before or after current time
    if ($expiry_date < new DateTime()) {
        return TRUE;
    }
    return FALSE;
}

/**
 * Helper function to check consent for a contact
 * Returns:
 *  0 -- no consent
 *  1 -- null consent value (and null is allowed)
 *  2 -- consent
 */
function _casl_check_contact_has_consent($contact_id) {
    $consent_type_id  = _casl_get_consent_type_id();
    $consent_date_id  = _casl_get_consent_date_id();

    if (!$consent_type_id || !$consent_date_id) {
        $message = ts('There was an error in looking up the CASL fields in your system. The CASL Support extension will not be able to check contacts for CASL consent.');
        CRM_Core_Session::setStatus($message, 'CASL Error', 'alert', ['expires'=>0]);
        return 2;
    }

    $result = civicrm_api3('Contact', 'get', array(
        'contact_id' => $contact_id,
        'return' => "contact_type,". $consent_type_id .",". $consent_date_id,
    ));
    $consent_type = $result['values'][$contact_id][$consent_type_id];

    $ignore_null = CRM_Core_BAO_Setting::getItem('casl', 'ignore_null');
    if (($ignore_null == 2) and ($consent_type == NULL)) {
        // Consent field empty, and global setting set to allow this
        return 1;
    } else if ($consent_type == "Implicit") {
        // Check that implicit consent isn't expired
        $consent_date = $result['values'][$contact_id][$consent_date_id];
        if (!_casl_update_and_test_expiration($consent_date, $contact_id)) {
            return 2;
        }
    } else if ($consent_type == "Explicit") {
        return 2;
    } else if ($consent_type == "Exempt") {
        return 2;
    }
    return 0;
}

/**
 * Helper function to set contact's latest consent date, and give implicit consent
 */
function _casl_set_consent_date($contact_id, $cdate, $notes=NULL) {
    $consent_type_id   = _casl_get_consent_type_id();
    $consent_date_id   = _casl_get_consent_date_id();
    $consent_method_id = _casl_get_consent_method_id();

    // First check if new date is later than existing record
    $result = civicrm_api3('Contact', 'get', ['contact_id' => $contact_id, 'return' => $consent_type_id .','. $consent_date_id]);
    $ct = $result['values'][$contact_id][$consent_type_id];
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
  
    // If no consent yet, give implicit consent
    if (($ct != 'Explicit') and ($ct != 'Exempt')) {
        $result = civicrm_api3('Contact', 'create', ['id' => $contact_id, $consent_type_id => 'Implicit']);
    }

    // Log on the contact as an activity
    $details = ts('The CASL Support extension has automatically set the consent date on this contact to '. $cdate .'.');
    if ($notes) {
        $details .= ' '. ts('Reason for this change') .': '. $notes;
    }
    _casl_log_activity($contact_id, ts('Consent date set by CASL'), $details);
}

/**
 * Helper function to set contact's no-bulk-email flag
 */
function _casl_set_no_bulk_email_flag($contact_id) {
    // Look up old contact for reference
    $old = civicrm_api3('Contact', 'get', ['id' => $contact_id, 'return' => 'is_opt_out']);

    // If no-bulk-email not set yet, set it
    if ($old['values'][$contact_id]['is_opt_out'] == '0') {
        // Set flag
        $result = civicrm_api3('Contact', 'create', ['id' => $contact_id, 'is_opt_out' => 1]);
        _casl_log_activity($contact_id, ts('No-bulk-email set by CASL'),
            ts('The CASL Support extension has automatically set the "no-bulk-email" flag on this contact.'));

        // Unset flagged_already, since we're setting no-bulk-email
        $already_id = _casl_get_flagged_already_id();
        $result = civicrm_api3('Contact', 'create', ['id' => $contact_id, $already_id => 0]);
    }
}

/**
 * Helper function to examine contact and unset no-bulk-email flag if appropriate
 */
function _casl_check_unset_no_bulk_email_flag($contact_id) {
    // Never unset if global setting doesn't allow
    if (CRM_Core_BAO_Setting::getItem('casl', 'grant_consent') != 2) {
        return;
    }

    // Don't unset if don't currently have consent
    if (_casl_check_contact_has_consent($contact_id) != 2) {
        return;
    }

    // Nothing to do if it's already unset
    $already_id = _casl_get_flagged_already_id();
    $c = civicrm_api3('Contact', 'get', ['id' => $contact_id, 'return' => $already_id .',is_opt_out']);
    if ($c['values'][$contact_id]['is_opt_out'] != 1) {
        return;
    }
    // To be safe, don't unset if it's been manually set
    if ($c['values'][$contact_id][$already_id] == 1) {
        return;
    }

    // Otherwise, we can clear the no-bulk-mail flag now
    $result = civicrm_api3('Contact', 'create', ['id' => $contact_id, 'is_opt_out' => 0]);
    _casl_log_activity($contact_id, ts('No-bulk-email cleared by CASL'),
        ts('The CASL Support extension has automatically cleard the "no-bulk-email" flag on this contact, since consent appears to be in place.'));

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
        // Only proceed if consent type or consent date were changed
        $go = false;
        $change_ids = [
            substr(_casl_get_consent_type_id(), 7), 
            substr(_casl_get_consent_date_id(), 7),
        ];
        foreach ($params as $p) {
            if (in_array($p['custom_field_id'], $change_ids)) {
                $go = true;
            }
        }
        if ($go) {
            //Get the consent date, to use in updating the expiry date
            $get_consent = civicrm_api3('Contact', 'get', [
                'sequential' => 1,
                'return' => _casl_get_consent_date_id(),
                'id' => $entityid,
            ]);
            if ($get_consent['count'] > 0) {
                $consent_date = $get_consent['values'][0][_casl_get_consent_date_id()];
                _casl_update_and_test_expiration($consent_date, $entityid);
            }

            if (_casl_check_contact_has_consent($entityid) == 0) {
                _casl_set_no_bulk_email_flag($entityid);
            } else {
                _casl_check_unset_no_bulk_email_flag($entityid);
            }
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
        if (_casl_update_and_test_expiration($consent_date, $contact_id)) {
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
    $already_id        = _casl_get_flagged_already_id();

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
        $c = _casl_check_contact_has_consent($page->getVar('_contactId'));
        if ($c == 0) {
            $message = ts('This contact does not have CASL consent for emails (for example it might be missing or expired). Typically the no-bulk-email flag for them would have already been set (depending on some other conditions). If so, they will not receive any of your bulk mailings in CiviMail.');
            CRM_Core_Session::setStatus($message, 'CASL Consent Absent');
        } else if ($c == 1) {
            $message = ts('This contact does not have any CASL consent information filled in. The system is set to allow them to still receive bulk emails (unless you see that the no-bulk-email flag has otherwise been set). However it\'s much preferred to update their information in the CASL section below!');
            CRM_Core_Session::setStatus($message, 'CASL Consent Missing');
        }
    }
}

/**
 * Implements hook_civicrm_post
 */
function casl_civicrm_post($op, $objectName, $objectId, &$objectRef) {
    // If new event reg made, update consent date
    if ($op=='create' && $objectName=='Participant') {
        $autofill = CRM_Core_BAO_Setting::getItem('casl', 'autofill');
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
            // To help proptect against infinite loop, only set if it's not set yet
            $result = civicrm_api3('Contact', 'get', ['id'=>$id, 'return'=>$already_id]);
            if ($result['values'][$id][$already_id] != 1) {
                // To prevent infinite loop, only set if it's not set yet
                $result = civicrm_api3('Contact', 'create', ['id'=>$id, $already_id=>1]);
            }
        }
    }
}

