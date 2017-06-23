<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * Test FavrikMembershipPeriod hooks.
 *
 * @group headless
 */
class FavrikMembershipPeriodTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  /**
   * Contact values.
   *
   * @var array
   */
  private $contact;

  /**
   * Hardcoded financial type id from default database install.
   *
   * @var int
   */
  private $financialTypeId = 2; // Member dues.

  /**
   * Membership values.
   *
   * @var array
   */
  private $membership;

  public function setUpHeadless() {
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    parent::setUp();
    $this->createBaseTestData();
  }

  public function tearDown() {
    parent::tearDown();
  }

  /**
   * Populates the required entities to run the tests.
   *
   * When creating a membershipType, a financial type id from the actual
   * database (non-Test) seems to be required.
   */
  public function createBaseTestData() {
    $this->contact = civicrm_api3('Contact', 'create', array(
      'contact_type' => 'Individual',
      'first_name' => 'Test',
      'last_name' => 'Owner',
    ));

    $domain = civicrm_api3('Domain', 'create', array(
      'name' => "Super",
      'domain_version' => "4.7",
    ));

    $membershipType = civicrm_api3('MembershipType', 'create', array(
      'domain_id' => $domain['id'],
      'member_of_contact_id' => $this->contact['id'],
      'financial_type_id' => $this->financialTypeId,
      'duration_unit' => "month",
      'duration_interval' => 1,
      'period_type' => "rolling",
      'name' => "Test",
    ));

    $this->membership = civicrm_api3('Membership', 'create', array(
      'membership_type_id' => $membershipType['id'],
      'contact_id' => $this->contact['id'],
      'start_date' => "2017-05-05",
      'end_date' => "2017-06-05",
    ));

    $this->membership['values'] = array_shift($this->membership['values']);
  }

  /**
   * Test that the post hook create-Membership has been run on membership
   * creation, creating a membership period.
   */
  public function testCreateMembershipPeriodOnCreate() {
    $result = civicrm_api3('FavrikMembershipPeriod', 'getsingle',
      array('sequential' => 1, 'membership_id' => $this->membership['id']));

    // Membership BAO returns dates in this format.
    $periodStartDate = CRM_Utils_Date::customFormat($result['start_date'], '%Y%m%d');
    $periodEndDate = CRM_Utils_Date::customFormat($result['end_date'], '%Y%m%d');

    $this->assertEquals($this->membership['values']['start_date'], $periodStartDate);
    $this->assertEquals($this->membership['values']['end_date'], $periodEndDate);
  }

  /**
   * Test that the post hook edit-Membership has been run when the membership
   * is edited, and a new membership period is added.
   */
  public function testCreateMembershipPeriodOnEdit() {
    $this->membership = civicrm_api3('Membership', 'create', array(
      'id' => $this->membership['id'],
      'start_date' => "2017-05-05",
      'end_date' => "2017-07-05",
    ));

    $lastPeriod = civicrm_api3('FavrikMembershipPeriod', 'get', array(
      'sequential' => 1,
      'membership_id' => $this->membership['id'],
      'options' => array('sort' => 'end_date DESC'),
    ));

    $this->assertEquals(2, count($lastPeriod['values']));
    $this->assertEquals('2017-07-05', $lastPeriod['values'][0]['end_date']);
  }

  /**
   * Test that no membership periods are created if the membership edit did not
   * change any dates.
   */
  public function testMembershipPeriodUnchanged() {
   $this->membership = civicrm_api3('Membership', 'create', array(
      'id' => $this->membership['id'],
      'start_date' => "2017-05-05",
      'end_date' => "2017-06-05",
      'source' => 'self',
    ));

    $lastPeriod = civicrm_api3('FavrikMembershipPeriod', 'get', array(
      'sequential' => 1,
      'membership_id' => $this->membership['id'],
      'options' => array('sort' => 'end_date DESC'),
    ));

    $this->assertEquals(1, count($lastPeriod['values']));
    $this->assertEquals('2017-06-05', $lastPeriod['values'][0]['end_date']);
  }

  public function testContributionIsTracked() {
    $contribution = civicrm_api3('Contribution', 'create', array(
      'sequential' => 1,
      'financial_type_id' => $this->financialTypeId,
      'total_amount' => 100,
      'contact_id' => $this->contact['id'],
    ));

    civicrm_api3('MembershipPayment', 'create', array(
      'sequential' => 1,
      'membership_id' => $this->membership['id'],
      'contribution_id' => $contribution['id'],
    ));

    $period = civicrm_api3('FavrikMembershipPeriod', 'getsingle',
      array('sequential' => 1, 'membership_id' => $this->membership['id']));

    $this->assertEquals($contribution['id'], $period['contribution_id']);
  }

  /**
   * Test that the buildForm hook is called, and the form is populated
   * with custom data.
   */
  public function testCustomDataIsPopulated() {
    $form = $this
      ->getMockBuilder('CRM_Member_Form_MembershipView')
      ->disableOriginalConstructor()
      ->setMethods(array('get', 'get_template_vars', 'assign'))
      ->getMock();

    $form
      ->expects($this->once())
      ->method('get')
      ->with($this->equalTo('id'))
      ->will($this->returnValue($this->membership['id']));

    $form
      ->expects($this->once())
      ->method('get_template_vars')
      ->with($this->equalTo('viewCustomData'))
      ->will($this->returnValue(array()));

    $form
      ->expects($this->once())
      ->method('assign');

    $handler = new CRM_Membershipperiods_Hook_BuildForm($form);
    $handler->run();
  }

}
