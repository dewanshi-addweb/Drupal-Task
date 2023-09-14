<?php

namespace Drupal\shorten_url\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ShortenUrlForm extends FormBase {
  public function getFormId() {
    return 'shorten_url_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['long_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Long URL'),
    //   '#required' => true,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Shorten URL'),
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
   
      $short_url_identifier = $this->generateUniqueShortUrlIdentifier();
     
      $long_url = $form_state->getValue('long_url');

      $current_date = date('Y-m-d H:i:s'); 
        
      $query = \Drupal::database()->insert('shorte_url');
        
        $query->fields([
            'source_url',
            'short_url',
            'date',
            
        ])->values([
            $long_url,
            $short_url_identifier,
            $current_date,
            
        ])->execute();

     \Drupal::messenger()->addMessage($this->t('URL shortened successfully.'));
    
    
     $form_state->setRedirect('shorten_url.list');
  }

 public function generateUniqueShortUrlIdentifier() {
    
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $length = rand(5, 9);
    $short_url_identifier = '';
    for ($i = 0; $i < $length; $i++) {
      $short_url_identifier .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $short_url_identifier;
  }
  
}
?>
