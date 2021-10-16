<?php
namespace Drupal\mymodule\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a block for rendering Taxonomy Terms in a view mode
 *
 * @Block(
 *   id = "taxonomy_term_page_rendered_entity",
 *   admin_label = @Translation("Taxonomy Term Page Rendered Entity"),
 *   category = @Translation("Custom"),
 * )
 */
class TaxonomyTermPageRenderedEntity extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    if ($term = \Drupal::routeMatch()->getParameter('taxonomy_term')) {
      $config = $this->getConfiguration();
      $build = \Drupal::entityTypeManager()->getViewBuilder('taxonomy_term')
                      ->view($term, $config['view_mode']);
      return $build;
    }
    return [
      '#markup' => $this->t('Not a Taxonomy Term page')
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $view_modes_opts = ['' => '- Select -'];
    $view_modes = \Drupal::entityQuery('entity_view_mode')
                         ->condition('targetEntityType', 'taxonomy_term')
                         ->execute();
                         
    foreach ($view_modes as $key => $value) {
      $val = str_replace("taxonomy_term.", "", $key);
      $view_modes_opts[$val] = $val;
    }

    $form['view_mode'] = [
      '#type' => 'select',
      '#options' => $view_modes_opts,
      '#required' => true,
      '#title' => $this->t('View Mode'),
      '#description' => $this->t('The view mode to render the Taxonomy Term'),
      '#default_value' => $config['view_mode'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['view_mode'] = $form_state->getValue('view_mode');
  }

}
