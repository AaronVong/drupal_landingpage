<?php
namespace Drupal\landing_page_form_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Implements a landing page form api
 */
class LandingPageContactForm extends FormBase{
  private $errorMessage = [
    "name_empty" => "Please let us know your name!",
    "email_empty" => "The email can be empty!",
    'subject_empty' => "Let us know what the subject is!",
    "message_empty" => "Send us a message!",
  ];

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form[] = [
      "name" => [
        "#type" => "textfield",
        "#required" => TRUE,
        "#required_error" => $this->errorMessage['name_empty'],
        "#weight" => '1',
        "#placeholder" => "Your name"
      ],
      "email" => [
        "#type" => "email",
        "#placeholder" => "Your email",
        "#required" => TRUE,
        "#required_error" => $this->errorMessage['email_empty'],
        "#weight" => '2',
      ],
      "subject" => [
        "#type" => "textfield",
        "#placeholder" => "Subject",
        "#required" => TRUE,
        "#required_error" => $this->errorMessage["subject_empty"],
        "#weight" =>'3',
      ],
      "message" => [
        "#type" => "textarea",
        "#placeholder" => "Your message",
        "#required" => TRUE,
        "#required_error" => $this->errorMessage["email_empty"],
        '#resizable' => FALSE,
        "#weight" => '4',
      ]
    ];
    $form["action"]["#type"] = "actions";
    $form["actions"]["submit"] = [
      "#type" => "submit",
      "#value" => "Submit",
      "#button_type" => "primary",
      '#prefix' => '<div class="submit-wrapper">',
      '#suffix' => '</div>',
      "#weight" => '4',
    ];
    return $form;
  }

  public function getFormId()
  {
    return "landing_page_contact_form";
  }

  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');
    $subject = $form_state->getValue('subject');
    $message = $form_state->getValue('message');

    if(strlen($name) <= 0){
      $form_state->setErrorByName("name", $this->t($this->errorMessage["name_empty"]));
    }

    if(strlen($email) <=0 ){
      $form_state->setErrorByName("email", $this->t($this->errorMessage["email_empty"]));
    }else if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
      $form_state->setErrorByName("email", $this->t("Email is not validate"));
    }

    if(strlen($subject) <= 0){
      $form_state->setErrorByName("subject", $this->t($this->errorMessage["subject_empty"]));
    }

    if(strlen($message) <= 0){
      $form_state->setErrorByName("message", $this->t($this->errorMessage["message_empty"]));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $this->messenger()->addStatus($this->t('Thank you for submit'));
  }
}
