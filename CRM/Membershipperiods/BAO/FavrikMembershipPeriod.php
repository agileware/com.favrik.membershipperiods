<?php

class CRM_Membershipperiods_BAO_FavrikMembershipPeriod
  extends CRM_Membershipperiods_DAO_FavrikMembershipPeriod {
  /**
   * Create a new FavrikMembershipPeriod based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Membershipperiods_DAO_FavrikMembershipPeriod|NULL
   */
  public static function create(&$params) {
    $period = new CRM_Membershipperiods_DAO_FavrikMembershipPeriod();

    CRM_Utils_Hook::pre(
      'create',
      'FavrikMembershipPeriod',
      CRM_Utils_Array::value('id', $params),
      $params
    );

    $period->copyValues($params);
    $period->save();

    CRM_Utils_Hook::post(
      'create',
      'FavrikMembershipPeriod',
      $period->id,
      $period
    );

    return $period;
  }

  public static function retrieve(&$params, $defaults) {
    $period = new CRM_Membershipperiods_DAO_FavrikMembershipPeriod();
    $period->copyValues($params);

    if ($period->find(TRUE)) {
      CRM_Core_DAO::storeValues($period, $defaults);
      return $item;
    }

    return NULL;
  }

  public static function retrieveByMembership(&$params) {

  }
}
