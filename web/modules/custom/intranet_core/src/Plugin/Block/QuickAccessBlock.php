<?php

declare(strict_types=1);

namespace Drupal\intranet_core\Plugin\Block;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Provides the editable quick access links shown on the front page.
 */
#[Block(
  id: 'intranet_quick_access',
  admin_label: new TranslatableMarkup('Intranet MS: acessos rápidos'),
  category: new TranslatableMarkup('Intranet MS'),
)]
final class QuickAccessBlock extends BlockBase {

  /**
   * Maximum number of links supported by this block.
   */
  private const MAX_ITEMS = 8;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'items' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);
    $configured_items = $this->configuration['items'] ?? [];

    $form['items'] = [
      '#type' => 'details',
      '#title' => $this->t('Atalhos'),
      '#description' => $this->t('Preencha até oito atalhos. Linhas sem título ou endereço não serão exibidas.'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    for ($index = 0; $index < self::MAX_ITEMS; $index++) {
      $item = $configured_items[$index] ?? [];
      $form['items'][$index] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Atalho @number', ['@number' => $index + 1]),
      ];
      $form['items'][$index]['symbol'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Sigla'),
        '#default_value' => $item['symbol'] ?? '',
        '#maxlength' => 3,
        '#size' => 4,
        '#description' => $this->t('Até três caracteres exibidos no círculo do atalho.'),
      ];
      $form['items'][$index]['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Título'),
        '#default_value' => $item['title'] ?? '',
        '#maxlength' => 80,
      ];
      $form['items'][$index]['description'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Descrição'),
        '#default_value' => $item['description'] ?? '',
        '#maxlength' => 160,
      ];
      $form['items'][$index]['url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Endereço'),
        '#default_value' => $item['url'] ?? '',
        '#maxlength' => 2048,
        '#description' => $this->t('Use um caminho interno iniciado por / ou uma URL completa com HTTPS.'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state): void {
    parent::blockValidate($form, $form_state);

    foreach ($form_state->getValue('items', []) as $index => $item) {
      $title = trim((string) ($item['title'] ?? ''));
      $url = trim((string) ($item['url'] ?? ''));

      if (($title === '') !== ($url === '')) {
        $form_state->setErrorByName(
          "items][$index",
          $this->t('O atalho @number precisa ter título e endereço.', ['@number' => $index + 1]),
        );
        continue;
      }

      if ($url !== '' && !$this->isAllowedUrl($url)) {
        $form_state->setErrorByName(
          "items][$index][url",
          $this->t('Informe um caminho interno iniciado por / ou uma URL completa válida.'),
        );
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    parent::blockSubmit($form, $form_state);

    $items = [];
    foreach ($form_state->getValue('items', []) as $item) {
      $title = trim((string) ($item['title'] ?? ''));
      $url = trim((string) ($item['url'] ?? ''));
      if ($title === '' || $url === '') {
        continue;
      }

      $items[] = [
        'symbol' => trim((string) ($item['symbol'] ?? '')),
        'title' => $title,
        'description' => trim((string) ($item['description'] ?? '')),
        'url' => $url,
      ];
    }

    $this->configuration['items'] = $items;
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $items = [];

    foreach ($this->configuration['items'] ?? [] as $item) {
      $raw_url = trim((string) ($item['url'] ?? ''));
      if ($raw_url === '' || !$this->isAllowedUrl($raw_url)) {
        continue;
      }

      $url = str_starts_with($raw_url, '/')
        ? Url::fromUserInput($raw_url)
        : Url::fromUri($raw_url);

      $items[] = [
        'symbol' => trim((string) ($item['symbol'] ?? '')),
        'title' => trim((string) ($item['title'] ?? '')),
        'description' => trim((string) ($item['description'] ?? '')),
        'url' => $url->toString(),
      ];
    }

    return [
      '#theme' => 'intranet_quick_access',
      '#items' => $items,
    ];
  }

  /**
   * Checks whether a configured URL is safe for the quick access list.
   */
  private function isAllowedUrl(string $url): bool {
    if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
      return TRUE;
    }

    if (!UrlHelper::isValid($url, TRUE)) {
      return FALSE;
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], TRUE);
  }

}
