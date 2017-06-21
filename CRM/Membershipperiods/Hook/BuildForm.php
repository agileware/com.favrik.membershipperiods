<?php

/**
 * Class for FavrikMembershipperiods buildForm hook handling.
 *
 * @author Favio Manriquez <favio@favrik.com>
 * @license AGPL-3.0
 */

class CRM_Membershipperiods_Hook_BuildForm {
  private $form;
  private $periodsData = array();

  public function __construct(&$form) {
    $this->form = $form;
  }

  /**
   * Retrieves membership periods and assigns them to the viewCustomData
   * template variable.
   */
  public function run() {
    $this->prepareFormData();
    $this->assignViewCustomData();
  }

  private function prepareFormData() {
    foreach ($this->getMembershipPeriods() as $index => $period) {
      $value = $this->formatDate($period['start_date'])
             . ' - ' . $this->formatDate($period['end_date'])
             . $this->contributionLink($period);

      $this->periodsData[] = array(
        'field_title' => ts('Period %1', array(1 => $index + 1)),
        'field_value' => $value,
      );
    }

    $this->periodsData[count($this->periodsData) - 1]['field_value'] .= <<<JS
      <script>
      function favrikClickContribution(contributionId, contactId) {
        var link = jQuery('a[href*="contribution?reset=1&id=' + contributionId + '&cid=' + contactId +'&action=view&context=membership&selectedChild=contribute&compId"]');
        link[0].click();
      }
      </script>
JS;
  }

  private function getMembershipPeriods() {
    $periods = civicrm_api3('FavrikMembershipPeriod', 'get', array(
      'membership_id' => $this->form->get('id'),
      'sequential' => 1,
      'options' => array('sort' => 'start_date ASC'),
    ));

    return $periods['values'];
  }

  private function formatDate($date) {
    return CRM_Utils_Date::customFormat($date, '%e %b %Y');
  }

  private function contributionLink($period) {
    if (!isset($period['contribution_id'])) {
      return '';
    }

    $link = <<<JS
      <a href="#" onclick="favrikClickContribution({$period['contribution_id']}, {$period['contact_id']});return false;">Contribution</a>
JS;
    return ' &nbsp;&nbsp; ' . $link;
  }

  private function assignViewCustomData() {
    if (empty($this->periodsData)) {
      return;
    }

    $viewCustomData = $this->form->get_template_vars('viewCustomData');
    $viewCustomData[] = array(
      array(
        'title' => ts('Membership Periods'),
        'fields' => $this->periodsData,
      ),
    );

    $this->form->assign('viewCustomData', $viewCustomData);
  }

}
