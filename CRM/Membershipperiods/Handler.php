<?php
/**
 * Class for FavrikMembershipperiods post handling.
 *
 * @author Favio Manriquez <favio@favrik.com>
 * @license AGPL-3.0
 */

class CRM_Membershipperiods_Handler {
  private $objectId = NULL;
  private $objectRef = NULL;
  private $method = NULL;

  public function __construct($method, $objectId, &$objectRef) {
    $this->method = $method;
    $this->objectId = $objectId;
    $this->objectRef = $objectRef;
  }

  /**
   * Method post
   *
   * @access public
   */
  public function post() {
    switch ($this->method) {
      case 'createMembership':
        return $this->onCreateMembership();
      case 'editMembership':
        return $this->onEditMembership();
      case 'createMembershipPayment':
        return $this->onCreateMembershipPayment();
    }
  }

  private function onCreateMembership() {
    CRM_Membershipperiods_BAO_FavrikMembershipPeriod::create(array(
      'membership_id' => $this->objectRef->id,
      'contact_id' => $this->objectRef->contact_id,
      'start_date' => $this->objectRef->start_date,
      'end_date' => $this->objectRef->end_date,
    ));
  }

  private function onEditMembership() {
    print_r($objectRef);

  }

  private function onCreateMembershipPayment() {

  }
}
