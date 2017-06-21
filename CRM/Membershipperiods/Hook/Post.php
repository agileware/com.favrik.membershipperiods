<?php
/**
 * Class for FavrikMembershipperiods post hook handling.
 *
 * @author Favio Manriquez <favio@favrik.com>
 * @license AGPL-3.0
 */

class CRM_Membershipperiods_Hook_Post {
  private $objectRef = NULL;
  private $method = NULL;

  /**
   * @param string $method - The concatenation of $op and $objectName.
   * @param object $objectRef - The Membership or MembershipPayment entity object.
   */
  public function __construct($method, &$objectRef) {
    $this->method = ucfirst($method);
    $this->objectRef = $objectRef;
  }

  /**
   * Skips membership operations when receiving a lifetime membership.
   *
   * @see onCreateMembership
   * @see onEditMembership
   * @see onCreateMembershipPayment
   */
  public function run() {
    if ($this->isMembershipEntityHook() && $this->isLifetimeMembership()) {
      return;
    }

    $methodName = 'on' . $this->method;
    $this->$methodName();
  }

  /**
   * @return bool - True if hook belongs to Membership entity, false otherwise.
   */
  private function isMembershipEntityHook() {
    return in_array($this->method, array('CreateMembership', 'EditMembership'));
  }

  /**
   * @return bool - True if this is a lifetime membership, false otherwise.
   */
  private function isLifetimeMembership() {
    if ($this->objectRef->end_date === 'null' || is_null($this->objectRef->end_date)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Create a membership period on new membership.
   */
  private function onCreateMembership() {
    $this->createMembershipPeriod(
      $this->objectRef->start_date, $this->objectRef->end_date
    );
  }

  private function createMembershipPeriod($startDate, $endDate) {
    civicrm_api3('FavrikMembershipPeriod', 'create', array(
      'membership_id' => $this->objectRef->id,
      'contact_id' => $this->objectRef->contact_id,
      'start_date' => $startDate,
      'end_date' => $endDate,
    ));
  }

  /**
   * Checks if a new membership period needs to be created; if so, creates it.
   */
  private function onEditMembership() {
    $lastPeriod = $this->getLastMembershipPeriod($this->objectRef->id);
    $lastPeriodEndDate = new DateTime($lastPeriod['end_date']);
    $lastPeriodEndDate->setTime(0, 0);

    $membershipEndDate = new DateTime($this->objectRef->end_date);
    $membershipEndDate->setTime(0, 0);

    if ($lastPeriodEndDate == $membershipEndDate) {
      return;
    }

    $nextPeriod = $this->calculateNextPeriod(
      $lastPeriodEndDate, $membershipEndDate
    );

    if ($nextPeriod) {
      $this->createMembershipPeriod(
        $nextPeriod['startDate'], $nextPeriod['endDate']
      );
    }
  }

  /**
   * Calculates next membership period according to last period end_date.
   */
  private function calculateNextPeriod($lastPeriodEndDate, $membershipEndDate) {
    $membershipType = civicrm_api3('MembershipType', 'getsingle', array(
      'id' => $this->objectRef->membership_type_id,
    ));

    // Sanity checks.
    if ($lastPeriodEndDate > $membershipEndDate
      || $membershipType['duration_unit'] === 'lifetime'
    ) {
      return NULL; // Unhandled cases.
    }

    $lastPeriodEndDate->add(new DateInterval('P1D'));

    return array(
      'startDate' => $lastPeriodEndDate->format('Y-m-d'),
      'endDate' => $this->objectRef->end_date,
    );
  }

  /**
   * Assigns a contribution id to the most recent membership period.
   */
  private function onCreateMembershipPayment() {
    $lastPeriod = $this->getLastMembershipPeriod($this->objectRef->membership_id);

    civicrm_api3('FavrikMembershipPeriod', 'create', array(
      'id' => $lastPeriod['id'],
      'contribution_id' => $this->objectRef->contribution_id,
    ));
  }

  private function getLastMembershipPeriod($membershipId) {
    $lastPeriod = civicrm_api3('FavrikMembershipPeriod', 'get', array(
      'sequential' => 1,
      'membership_id' => $membershipId,
      'options' => array('sort' => 'end_date DESC', 'limit' => 1),
    ));

    return $lastPeriod['values'][0];
  }

}
