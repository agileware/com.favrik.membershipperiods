<?php

/**
 * FavrikMembershipPeriod.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_favrik_membership_period_create_spec(&$spec) {
  // $spec['some_parameter']['api.required'] = 1;
}

/**
 * FavrikMembershipPeriod.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_favrik_membership_period_create($params) {
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * FavrikMembershipPeriod.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_favrik_membership_period_get($params) {
  return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * Because this api doesn't follow the usual naming pattern we have to explicitly declare dao name.
 * @return string
 */
function _civicrm_api3_favrik_membership_period_DAO() {
  return 'CRM_Membershipperiods_DAO_FavrikMembershipPeriod';
}
