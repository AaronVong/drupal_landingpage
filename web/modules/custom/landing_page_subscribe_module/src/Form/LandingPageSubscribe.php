<?php
namespace Drupal\landing_page_subscribe_module\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Exception;

class LandingPageSubscribe extends FormBase{
  private $validated = false;

  public function set_validated($value) {
    $this->validated = $value;
  }

  public function get_validated() : bool {
    return $this->validated;
  }
  public function buildForm(array $form, FormStateInterface $form_state): array
  {
    $form[] = [
      "email" => [
        "#type" => "email",
        "#placeholder" => "Your email",
        "#required" => TRUE,
        "#weight" => '0',
      ],
      "message" => [
        "#type" => 'markup',
        '#markup' => "<div class='ajax-message'></div>",
        "#weight" => '2',
      ]
    ];
    $form["action"]["#type"] = "actions";
    $form["actions"]["submit"] = [
      "#type" => "button",
      "#value" => "Subscribe",
      "#button_type" => "primary",
      '#prefix' => '<div class="submit-wrapper--subscribe">',
      '#suffix' => '</div>',
      "#weight" => '1',
      "#ajax" => [
        'callback' => '::handleSubscribe',
      ],
    ];
    return $form;
  }

  /**
   * Validate email field on change
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function validateEmail(array $form, FormStateInterface $form_state) : AjaxResponse {
    $response = new AjaxResponse();
    $email = $form_state->getValue('email');


    $this->set_validated(true);
    return $response;
  }

  /**
   * Find and check if email is already subscribed
   * @param $email
   * @return mixed  numberic character
   */
  public function isEmailSubscribed($email) : mixed
  {
    $connection = \Drupal::database();
    $foundEmail = $connection->select('subscribelist','sl')->condition('email',$email,'LIKE');
    $foundEmail->addField('sl', 'email');
    $result = $foundEmail->countQuery()->execute()->fetchField();
    return $result;
  }

  /**
   * Ajax handler for subscribe form
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   * @throws \Exception
   */
  public function handleSubscribe(array $form, FormStateInterface $form_state): AjaxResponse
  {
    $response = new AjaxResponse();
    $email = $form_state->getValue('email');

    try{
      # Validate email
      if(!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) <= 0){
        $response->setStatusCode(406);
        throw new \Exception('Invalid email');
      }

      # Check if email is subscribed, if so response fail
      if($this->isEmailSubscribed($email)) {
        $response->setStatusCode(406);
        throw new \Exception('The email already subscribed');
      }

      # Validation Passed
      # Get database connection
      $connection = \Drupal::database();
      # Insert success ?
      $result = $connection->insert('subscribelist')->fields([
        'email' => $email,
        'created' => time(),
      ])->execute();

      # Success : Response success
      $response->addCommand(
        new HtmlCommand(
          'div.ajax-message',
          '<div class="subscribe-message--success ajax-message-content">Thank you for subscribe</div>',
        )
      );
      return $response;
    } catch(Exception $error) {
      # Fail : Response Fail
      $response->addCommand(
        new HtmlCommand(
          'div.ajax-message',
          '<div class="subscribe-message--error ajax-message-content">'.$error->getMessage().'</div>'
        )
      );
      return $response;
    }
  }

  public function getFormId()
  {
    return "landing_page_subscribe";
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
//    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {}
}
