<?php

namespace Drupal\trustarc\Form;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class TrustarcSettingsForm.
 *
 * Provides a settings form for TrustArc module.
 */
class TrustarcSettingsForm extends ConfigFormBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a TrustarcSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LoggerInterface $logger) {
    $this->configFactory = $config_factory;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('logger.factory')->get('trustarc')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'trustarc_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['trustarc.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('trustarc.settings');

    // CMP Version
    $form['cmp_version'] = [
      '#type' => 'radios',
      '#title' => $this->t('CMP Version'),
      '#options' => [
        'advanced' => $this->t('Advanced'),
        'pro' => $this->t('Pro'),
      ],
      '#default_value' => $config->get('cmp_version') ?: 'advanced',
    ];

    // CMP Script ID
    $form['cmp_script'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CMP Script ID'),
      '#description' => $this->t('Your unique TrustArc CMP ID.'), // Tooltip text as description
      '#default_value' => $config->get('cmp_script'),
      '#required' => TRUE,
    ];

    // CMP Script Params
    $form['cmp_script_params'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CMP Script Params'),
      '#description' => $this->t('Additional parameters for customizing the CMP script.'),
      '#default_value' => $config->get('cmp_script_params'),
      '#required' => TRUE,
    ];

    // Banner Container
    $form['cmp_banner'] = [
      '#type' => 'textfield',  // Use textarea for flexibility
      '#title' => $this->t('Banner Container'),
      '#description' => $this->t('The ID of the HTML element where the CMP banner will be displayed, or the complete HTML code.'),
      '#default_value' => $config->get('cmp_banner') ?: 'consent_blackbar',
      '#markup' => $config->get('cmp_banner') ?: 'consent_blackbar',
    ];

    // Example for Checkbox (cmp_preferences):
    $form['cmp_preferences'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display Cookie Preferences Link'),
      '#description' => $this->t('Enable to display a link that opens the cookie preferences modal.'),
      '#default_value' => $config->get('cmp_preferences'),
    ];

    // Query Selector for Preferences Link
    $form['cmp_preferences_selector'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Query Selector'),
      '#description' => $this->t('A CSS selector to specify where the cookie preferences link should be placed. For example, \'#footer-links\' will place the link inside the element with the ID \'footer-links\'. If left blank, the link will be appended to the &lt;body&gt; tag.'),
      '#default_value' => $config->get('cmp_preferences_selector'),
      '#states' => [
        'visible' => [
          ':input[name="cmp_preferences"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Fire Custom Events for GTM
    $form['cmp_standard_event_listener'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Fire Custom Events for GTM'),
      '#description' => $this->t('Enable this option to fire custom events according to the categories in the CMP. This allows for more granular tracking and event handling based on user consent choices. This will also push events to the dataLayer for further use in your GTM. For example, \'GDPR Pref Allows 1\', \'GDPR Pref Allows 2\', etc.'),
      '#default_value' => $config->get('cmp_standard_event_listener'),
    ];

    // Enable Google Consent Mode
    $form['cmp_google_consent_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Google Consent Mode'),
      '#description' => $this->t('Enable Google Consent Mode v2 to dynamically adjust the behavior of Google tags (like Google Analytics and Google Ads) based on the user\'s consent choices.'),
      '#default_value' => $config->get('cmp_google_consent_mode'),
    ];

    // GA Measurement ID (Conditional)
    $form['cmp_ga_measurement_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('GA Measurement ID'),
      '#description' => $this->t('Your Google Analytics Measurement ID (e.g., \'G-XXXXXXXXXX\'). This is required to use Google Consent Mode with Google Analytics.'),
      '#default_value' => $config->get('cmp_ga_measurement_id'),
      '#states' => [
        'visible' => [
          ':input[name="cmp_google_consent_mode"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Ads Data Redaction (Conditional)
    $form['cmp_ads_data_redaction'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Data Redaction'),
      '#description' => $this->t('When enabled, Google Analytics will redact user data when consent for analytics is not granted. This helps protect user privacy.'),
      '#default_value' => $config->get('cmp_ads_data_redaction'),
      '#states' => [
        'visible' => [
          ':input[name="cmp_google_consent_mode"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Enable URL Passthrough (Conditional)
    $form['cmp_url_passthrough'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable URL Passthrough'),
      '#description' => $this->t('When enabled, URL parameters will be passed through to Google Analytics even when consent is not granted. This can be useful for campaign tracking.'),
      '#default_value' => $config->get('cmp_url_passthrough'),
      '#states' => [
        'visible' => [
          ':input[name="cmp_google_consent_mode"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['google_consent_mode_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Google Consent Mode Settings'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    // Consent Type Mapping (Table) - STATIC
    $form['google_consent_mode_settings']['cmp_consent_type_mapping'] = [
      '#type' => 'table',
      '#title' => $this->t('Consent Type Mapping'),
      '#description' => $this->t('Map TrustArc consent categories to Google Consent Mode v2 consent types.'),
      '#header' => [
        $this->t('Google Consent Type'),
        $this->t('TrustArc Category ID'),
      ],
      '#rows' => [],
      '#table_id' => 'consent-type-mapping-table',
      '#prefix' => '<div id="consent-type-mapping-container">',
      '#suffix' => '</div>',
      '#states' => [  // Correctly added #states
        'visible' => [
          ':input[name="cmp_google_consent_mode"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $consent_types = [ // Google Consent Mode v2 consent types
      'ad_storage' => 'Ad Storage',
      'analytics_storage' => 'Analytics Storage',
      'ad_user_data' => 'Ad User Data',
      'ad_personalization' => 'Ad Personalization',
      'functionality_storage' => 'Functionality Storage',
      'personalization_storage' => 'Personalization Storage',
      'security_storage' => 'Security Storage',
      'wait_for_update' => 'Wait for Update',
    ];

    foreach ($consent_types as $key => $label) {
      $form['google_consent_mode_settings']['cmp_consent_type_mapping'][$key] = [
        'google_consent_type' => [
          '#markup' => $label,
        ],
        'trustarc_category_id' => [
          '#type' => 'number',
          '#attributes' => ['class' => ['trustarc-category-id']],
          '#default_value' => $config->get('cmp_consent_type_mapping')[$key] ?? '',
        ],
        '#states' => [
          'required' => [
            ':input[name="cmp_google_consent_mode"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    $form['#attached']['library'][] = 'trustarc/trustarc_admin'; // Attach the library

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $values = $form_state->getValues();
      $this->configFactory->getEditable('trustarc.settings')
        ->set('cmp_version', $values['cmp_version'])
        ->set('cmp_script', $values['cmp_script'])
        ->set('cmp_script_params', $values['cmp_script_params'])
        ->set('cmp_banner', $values['cmp_banner'])
        ->set('cmp_preferences', $values['cmp_preferences'])
        ->set('cmp_preferences_selector', $values['cmp_preferences_selector'])
        ->set('cmp_standard_event_listener', $values['cmp_standard_event_listener'])
        ->set('cmp_google_consent_mode', $values['cmp_google_consent_mode'])
        ->set('cmp_ga_measurement_id', $values['cmp_ga_measurement_id'])
        ->set('cmp_ads_data_redaction', $values['cmp_ads_data_redaction'])
        ->set('cmp_url_passthrough', $values['cmp_url_passthrough'])
        ->set('cmp_consent_type_mapping', $values['cmp_consent_type_mapping'])
        ->save();

      // Log the form submission values
      $this->logger->info('TrustArc settings form submitted with values: @values', ['@values' => json_encode($values)]);

      parent::submitForm($form, $form_state);
    } catch (\Exception $e) {
      // Log the error
      $this->logger->error('Error submitting TrustArc settings form: @message', ['@message' => $e->getMessage()]);
      \Drupal::messenger()->addError($this->t('An error occurred while saving the settings: @message', ['@message' => $e->getMessage()]));
    }
  }
}