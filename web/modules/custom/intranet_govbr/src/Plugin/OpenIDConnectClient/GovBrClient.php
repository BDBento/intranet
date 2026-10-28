<?php

declare(strict_types=1);

namespace Drupal\intranet_govbr\Plugin\OpenIDConnectClient;

use Drupal\Core\Form\FormStateInterface;
use Drupal\openid_connect\Plugin\OpenIDConnectClient\OpenIDConnectGenericClient;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gov.br OpenID Connect client backed by environment-only credentials.
 *
 * @OpenIDConnectClient(
 *   id = "govbr",
 *   label = @Translation("Gov.br")
 * )
 */
final class GovBrClient extends OpenIDConnectGenericClient {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return $this->runtimeConfiguration() + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    parent::setConfiguration($configuration);

    // Runtime values always win over exported configuration. This prevents a
    // stale endpoint or a credential stored in configuration from being used.
    $this->configuration = array_replace(
      $this->configuration,
      $this->runtimeConfiguration(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration(): array {
    $configuration = parent::getConfiguration();

    // Never persist credentials when the client entity is edited or exported.
    $configuration['client_id'] = '';
    $configuration['client_secret'] = '';

    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $environment = $this->environment();
    $credentials_configured = $this->credentialsConfigured();

    $form['runtime_status'] = [
      '#type' => 'item',
      '#title' => $this->t('Configuração por ambiente'),
      '#markup' => $this->t(
        'Ambiente: <strong>@environment</strong>. Credenciais: <strong>@status</strong>. A ativação também exige GOVBR_ENABLED=true.',
        [
          '@environment' => $environment,
          '@status' => $credentials_configured ? $this->t('configuradas') : $this->t('ausentes'),
        ],
      ),
    ];
    $form['redirect_url'] = [
      '#type' => 'item',
      '#title' => $this->t('URL de retorno'),
      '#markup' => $this->getRedirectUrl()->toString(),
    ];
    $form['prompt'] = [
      '#title' => $this->t('Prompt'),
      '#type' => 'checkboxes',
      '#options' => [
        'login' => $this->t('Solicitar autenticação'),
        'consent' => $this->t('Solicitar consentimento'),
      ],
      '#default_value' => $this->configuration['prompt'] ?? ['login'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $prompt = array_filter((array) $form_state->getValue('prompt', []));
    if ($prompt === []) {
      $form_state->setErrorByName('prompt', $this->t('Selecione ao menos uma opção de prompt.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['prompt'] = array_values(array_filter(
      (array) $form_state->getValue('prompt', []),
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function authorize(string $scope = 'openid profile email', array $additional_params = []): Response {
    if (!$this->runtimeEnabled() || !$this->credentialsConfigured()) {
      return new Response(
        (string) $this->t('A autenticação Gov.br ainda não foi habilitada neste ambiente.'),
        Response::HTTP_SERVICE_UNAVAILABLE,
        ['Content-Type' => 'text/plain; charset=UTF-8'],
      );
    }

    return parent::authorize($scope, $additional_params);
  }

  /**
   * {@inheritdoc}
   */
  public function retrieveTokens(string $authorization_code): ?array {
    if (!$this->runtimeEnabled() || !$this->credentialsConfigured()) {
      return NULL;
    }

    $tokens = parent::retrieveTokens($authorization_code);
    if ($tokens === NULL || empty($tokens['access_token'])) {
      return NULL;
    }

    // openid_connect 3.0.0-alpha9 decodes ID-token claims without validating
    // its signature. Do not allow those claims into account provisioning. The
    // identity is obtained from Gov.br's TLS-protected UserInfo endpoint using
    // the access token returned by the trusted token endpoint.
    unset($tokens['id_token']);

    return $tokens;
  }

  /**
   * Returns fixed public endpoints and credentials from the environment.
   */
  private function runtimeConfiguration(): array {
    $base_url = $this->environment() === 'production'
      ? 'https://sso.acesso.gov.br'
      : 'https://sso.staging.acesso.gov.br';

    return [
      'client_id' => (string) (getenv('GOVBR_CLIENT_ID') ?: ''),
      'client_secret' => (string) (getenv('GOVBR_CLIENT_SECRET') ?: ''),
      'provider_slug' => 'govbr',
      'iss_allowed_domains' => '',
      'prompt' => $this->configuration['prompt'] ?? ['login'],
      'issuer_url' => $base_url . '/',
      'authorization_endpoint' => $base_url . '/authorize',
      'token_endpoint' => $base_url . '/token',
      'userinfo_endpoint' => $base_url . '/userinfo',
      'end_session_endpoint' => '',
      'scopes' => ['openid', 'profile', 'email'],
    ];
  }

  /**
   * Returns the selected Gov.br environment.
   */
  private function environment(): string {
    return strtolower((string) getenv('GOVBR_ENVIRONMENT')) === 'production'
      ? 'production'
      : 'staging';
  }

  /**
   * Checks the explicit runtime feature flag.
   */
  private function runtimeEnabled(): bool {
    return in_array(
      strtolower((string) getenv('GOVBR_ENABLED')),
      ['1', 'true', 'yes', 'on'],
      TRUE,
    );
  }

  /**
   * Checks whether both credentials exist without exposing their values.
   */
  private function credentialsConfigured(): bool {
    return getenv('GOVBR_CLIENT_ID') !== FALSE
      && trim((string) getenv('GOVBR_CLIENT_ID')) !== ''
      && getenv('GOVBR_CLIENT_SECRET') !== FALSE
      && trim((string) getenv('GOVBR_CLIENT_SECRET')) !== '';
  }

}
