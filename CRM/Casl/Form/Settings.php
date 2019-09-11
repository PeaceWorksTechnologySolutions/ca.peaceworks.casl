<?php

use CRM_Casl_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Casl_Form_Settings extends CRM_Core_Form {
  public function buildQuickForm() {

    CRM_Utils_System::setTitle(E::ts('CASL Settings'));

    // add form elements
    $this->add(
      'select',
      'ignore_null',
      E::ts('Ignore unmarked contacts'),
      $this->getIgnoreOptions(),
      TRUE // required
    );
    $this->add(
      'select',
      'grant_consent',
      E::ts('Allow flag clearing'),
      $this->getGrantOptions(),
      TRUE // required
    );
    $this->add(
      'select',
      'autofill',
      E::ts('Autofill CASL fields'),
      $this->getAutofillOptions(),
      TRUE // required
    );
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));
    
    $defaults = array(
      'ignore_null'   => CRM_Core_BAO_Setting::getItem('casl', 'ignore_null'),
      'grant_consent' => CRM_Core_BAO_Setting::getItem('casl', 'grant_consent'),
      'autofill'      => CRM_Core_BAO_Setting::getItem('casl', 'autofill'),
    );
    $this->setDefaults($defaults);

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    CRM_Core_BAO_Setting::setItem($values['ignore_null'], 'casl', 'ignore_null');
    CRM_Core_BAO_Setting::setItem($values['grant_consent'], 'casl', 'grant_consent');
    CRM_Core_BAO_Setting::setItem($values['autofill'], 'casl', 'autofill');
    CRM_Core_Session::setStatus(E::ts('Your settings have been saved.'));
    parent::postProcess();
  }

  public function getIgnoreOptions() {
    $options = array(
      '' => E::ts('- select -'),
      '1' => E::ts('Process ALL contacts for CASL consent'),
      '2' => E::ts('Skip contacts who have "consent type" empty'),
    );
    return $options;
  }

  public function getGrantOptions() {
    $options = array(
      '' => E::ts('- select -'),
      '1' => E::ts('NEVER automatically clear the no-bulk-email condition'),
      '2' => E::ts('Allow no-bulk-email to be cleared, when appropriate'),
    );
    return $options;
  }

  public function getAutofillOptions() {
    $options = array(
      '' => E::ts('- select -'),
      '1' => E::ts('NEVER automatically fill CASL fields'),
      '2' => E::ts('Fill fields only on event registrations'),
      '3' => E::ts('Fill fields only on contributions'),
      '4' => E::ts('Fill fields on both events and contributions'),
    );
    return $options;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
