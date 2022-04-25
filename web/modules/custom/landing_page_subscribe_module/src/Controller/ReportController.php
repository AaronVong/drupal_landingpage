<?php
/**
 *
 * @file
 * Contains \Drupal\landing_page_subscribe_module\Controller\ReportController
 */

namespace Drupal\landing_page_subscribe_module\Controller;
use \Drupal\Core\Controller\ControllerBase;
use \Drupal\Core\Database\Database;

/**
 * Controller for landing_page_subscribe_module list
 */

class ReportController extends ControllerBase{
  /**
   * Gets all Subscribed client
   * @return array
   */
  protected function load() :array {
    $select = Database::getConnection()->select('subscribelist', 'subscribe_list')->fields('subscribe_list',['id', 'email', 'created']);
    $entries = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);
    return $entries;
  }

  /**
   * Create Subscribed list page
   * @return array
   */

  public function report() : array{
    $content = [];

    # Helper text
    $content['message'] = [
      '#markup' => $this->t('Below is a list of all subscribed clients')
    ];

    # Header for table
    $header = [
      $this->t('Id'),
      $this->t('email'),
      $this->t('created')
    ];

    # Table rows content
    $rows = [];
    foreach ($entries = $this->load() as $entry) {
      $rows[] = array_map("\Drupal\Component\Utility\HTML::escape", $entry);
    }

    # Set data for table
    $content['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No entries were found'),
    ];

    # Set cache
    $content['#cache']['max-age'] = 0;

    return $content;
  }
}
