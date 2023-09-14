<?php

namespace Drupal\shorten_url\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Url;
use Drupal\Core\Link;

class ShortenUrlListController extends ControllerBase {
    
  public function content() {
    $query = \Drupal::database()->select('shorte_url', 'su');
    $query->fields('su', ['id', 'source_url', 'short_url', 'date']);
    $results = $query->execute()->fetchAll();

    $header = [
      'id' => $this->t('ID'),
      'source_url' => $this->t('Source URL'),
      'short_url' => $this->t('Short URL'),
      'date' => $this->t('Date'),
      'view_details' => $this->t('View Details'),
    ];

    $rows = [];
    foreach ($results as $row) {   
      $shortCode = $row->id; 
      $url = Url::fromRoute('shorten_url.view_details', ['id' => $shortCode]);
      $viewDetailsLink = Link::fromTextAndUrl($this->t('View Details'), $url)->toString();

      $rows[] = [
        'id' => $row->id,
        'source_url' => $row->source_url,
        'short_url' => $row->short_url,
        'date' => $row->date,
        'view_details' => $viewDetailsLink,
      ];
    }
    $table = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    return $table;
  }
}
?>
