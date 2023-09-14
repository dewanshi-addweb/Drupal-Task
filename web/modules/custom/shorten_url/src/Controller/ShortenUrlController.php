<?php

namespace Drupal\shorten_url\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Routing\TrustedRedirectResponse;

class ShortenUrlController extends ControllerBase {

  public function viewDetailsPage($id) {
    $query = \Drupal::database()->select('shorte_url', 's');
    $query->fields('s', ['id', 'source_url', 'short_url', 'visited_count']);
    $query->condition('s.id', $id);
    $result = $query->execute()->fetchObject();

   
    if (!$result) {
      
      return [
        '#markup' => $this->t('Record not found.'),
      ];
    }

    
    $shortcode = $result->short_url;
    $shortLink = Link::fromTextAndUrl($shortcode, Url::fromRoute('shorten_url.short_link_redirect', ['shortcode' => $shortcode]));

    
    $output = [
      'id' => [
        '#markup' => '<div>' . $this->t('ID') . ': ' . $result->id . '</div>',
      ],
      'source_url' => [
        '#markup' => '<div>' . $this->t('Source URL') . ': ' . $result->source_url . '</div>',
      ],
      'short_url' => [
        '#markup' => '<div>' . $this->t('Short URL') . ': ' . $shortLink->toString() . '</div>',
      ],
      'visited_count' => [
        '#markup' => '<div>' . $this->t('Visited Count') . ': ' . $result->visited_count . '</div>',
      ],
      '#cache' => array(
        'max-age' => 0,
      ),
    ];




    return $output;
}


public function shortLinkRedirect($shortcode) {
   $result = $this->getShortLinkInfo($shortcode);

  if ($result) {
    if (!empty($result->source_url) && filter_var($result->source_url, FILTER_VALIDATE_URL)) {
      $this->updateVisitedCount($shortcode);
      return new TrustedRedirectResponse($result->source_url, 302);
    } else {
      return [
        '#markup' => $this->t('Invalid source URL.'),
      ];
    }
  } else {  
    return [
      '#markup' => $this->t('Shortcode not found.'),
    ];
  }
}

  private function getShortLinkInfo($shortcode) {
    $result = \Drupal::database()->select('shorte_url', 's')
      ->fields('s', ['source_url', 'visited_count'])
      ->condition('short_url', $shortcode)
      ->execute()
      ->fetchObject();

    return $result;
  }

  private function updateVisitedCount($shortcode) {
    
    \Drupal::database()->update('shorte_url')
      ->condition('short_url', $shortcode)
      ->expression('visited_count', 'visited_count + 1')
      ->execute();

      
  }

}
?>
