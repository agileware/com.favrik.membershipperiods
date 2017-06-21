<?php

require_once 'membershipperiods.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function membershipperiods_civicrm_config(&$config) {
  _membershipperiods_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function membershipperiods_civicrm_xmlMenu(&$files) {
  _membershipperiods_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function membershipperiods_civicrm_install() {
  _membershipperiods_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function membershipperiods_civicrm_postInstall() {
  _membershipperiods_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function membershipperiods_civicrm_uninstall() {
  _membershipperiods_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function membershipperiods_civicrm_enable() {
  _membershipperiods_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function membershipperiods_civicrm_disable() {
  _membershipperiods_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function membershipperiods_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _membershipperiods_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function membershipperiods_civicrm_managed(&$entities) {
  _membershipperiods_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function membershipperiods_civicrm_caseTypes(&$caseTypes) {
  _membershipperiods_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function membershipperiods_civicrm_angularModules(&$angularModules) {
  _membershipperiods_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function membershipperiods_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _membershipperiods_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 */
function membershipperiods_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_Membershipperiods_DAO_FavrikMembershipPeriod'] = array(
    'name' => 'MembershipPeriod',
    'class' => 'CRM_Membershipperiods_DAO_FavrikMembershipPeriod',
    'table' => 'civicrm_favrikmembershipperiods',
  );
}

/**
 * Implements hook_civicrm_post().
 */
function membershipperiods_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  $method = $op . $objectName;
  $validMethods = array(
    'createMembership',
    'editMembership',
    'createMembershipPayment',
  );

  if (in_array($method, $validMethods)) {
    $handler = new CRM_Membershipperiods_Hook_Post($method, $objectRef);
    $handler->run();
  }
}

/**
 * Implements hook_civicrm_buildForm().
 */
function membershipperiods_civicrm_buildForm($formName, &$form) {
  if (
       $formName === 'CRM_Member_Form_MembershipView'
    && $form->getAction() === CRM_Core_Action::VIEW
  ) {

    $handler = new CRM_Membershipperiods_Hook_BuildForm($form);
    $handler->run();
  }
}
