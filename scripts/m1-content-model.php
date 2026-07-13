<?php

/**
 * M1 – case_study content model + gallery fields + views.
 * One-off build script: ddev drush scr scripts/m1-content-model.php
 * Deleted after the config it creates is exported to config/sync.
 */

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;
use Drupal\views\Entity\View;

$display_repo = \Drupal::service('entity_display.repository');

// --- 1. Content type -------------------------------------------------------

if (!NodeType::load('case_study')) {
  NodeType::create([
    'type' => 'case_study',
    'name' => 'Case study',
    'description' => 'A work project rendered in full on the work page – no standalone destination.',
    'new_revision' => TRUE,
    'preview_mode' => DRUPAL_OPTIONAL,
    'display_submitted' => FALSE,
  ])->save();
  echo "Created node type case_study\n";
}

// --- 2. New field storages --------------------------------------------------

$storages = [
  'field_role' => ['type' => 'string', 'cardinality' => 1, 'settings' => []],
  'field_screenshots' => [
    'type' => 'entity_reference',
    'cardinality' => -1,
    'settings' => ['target_type' => 'media'],
  ],
  'field_link' => ['type' => 'link', 'cardinality' => 1, 'settings' => []],
  'field_weight' => ['type' => 'integer', 'cardinality' => 1, 'settings' => []],
];
foreach ($storages as $name => $def) {
  if (!FieldStorageConfig::loadByName('node', $name)) {
    FieldStorageConfig::create([
      'field_name' => $name,
      'entity_type' => 'node',
      'type' => $def['type'],
      'cardinality' => $def['cardinality'],
      'settings' => $def['settings'],
    ])->save();
    echo "Created storage node.$name\n";
  }
}

// --- 3. Fields on case_study ------------------------------------------------

$fields = [
  'field_description' => [
    'label' => 'Summary',
    'description' => 'One or two sentences for the top of the card.',
    'required' => TRUE,
    'settings' => [],
  ],
  'field_content' => [
    'label' => 'Body',
    'description' => 'The full case-study content, shown inline on the card.',
    'required' => FALSE,
    'settings' => [],
  ],
  'field_role' => [
    'label' => 'Role',
    'description' => 'e.g. Lead front-end developer, Sole developer.',
    'required' => FALSE,
    'settings' => [],
  ],
  'field_tags' => [
    'label' => 'Tech / tags',
    'description' => '',
    'required' => FALSE,
    'settings' => [
      'handler' => 'default:taxonomy_term',
      'handler_settings' => [
        'target_bundles' => ['tags' => 'tags'],
        'auto_create' => TRUE,
      ],
    ],
  ],
  'field_screenshots' => [
    'label' => 'Screenshots',
    'description' => '',
    'required' => FALSE,
    'settings' => [
      'handler' => 'default:media',
      'handler_settings' => [
        'target_bundles' => ['image' => 'image'],
      ],
    ],
  ],
  'field_link' => [
    'label' => 'External link',
    'description' => 'The live site or project page.',
    'required' => FALSE,
    'settings' => ['link_type' => 16, 'title' => 1],
  ],
  'field_weight' => [
    'label' => 'Weight',
    'description' => 'Lower numbers appear first in the featured-work area.',
    'required' => FALSE,
    'settings' => [],
  ],
];
foreach ($fields as $name => $def) {
  if (!FieldConfig::loadByName('node', 'case_study', $name)) {
    FieldConfig::create([
      'field_name' => $name,
      'entity_type' => 'node',
      'bundle' => 'case_study',
      'label' => $def['label'],
      'description' => $def['description'],
      'required' => $def['required'],
      'settings' => $def['settings'],
    ])->save();
    echo "Created field case_study.$name\n";
  }
}

// Form display.
$form = $display_repo->getFormDisplay('node', 'case_study', 'default');
$form->setComponent('title', ['type' => 'string_textfield', 'weight' => 0]);
$form->setComponent('field_description', ['type' => 'string_textarea', 'weight' => 1, 'settings' => ['rows' => 3]]);
$form->setComponent('field_content', ['type' => 'text_textarea', 'weight' => 2]);
$form->setComponent('field_role', ['type' => 'string_textfield', 'weight' => 3]);
$form->setComponent('field_tags', ['type' => 'entity_reference_autocomplete_tags', 'weight' => 4]);
$form->setComponent('field_screenshots', ['type' => 'media_library_widget', 'weight' => 5]);
$form->setComponent('field_link', ['type' => 'link_default', 'weight' => 6]);
$form->setComponent('field_weight', ['type' => 'number', 'weight' => 7]);
$form->save();
echo "Saved case_study form display\n";

// View display (default = what the featured-work cards render).
$view = $display_repo->getViewDisplay('node', 'case_study', 'default');
$view->setComponent('field_description', ['type' => 'basic_string', 'label' => 'hidden', 'weight' => 0]);
$view->setComponent('field_role', ['type' => 'string', 'label' => 'inline', 'weight' => 1]);
$view->setComponent('field_content', ['type' => 'text_default', 'label' => 'hidden', 'weight' => 2]);
$view->setComponent('field_screenshots', ['type' => 'entity_reference_entity_view', 'label' => 'hidden', 'weight' => 3]);
$view->setComponent('field_tags', ['type' => 'entity_reference_label', 'label' => 'inline', 'weight' => 4, 'settings' => ['link' => FALSE]]);
$view->setComponent('field_link', ['type' => 'link', 'label' => 'hidden', 'weight' => 5]);
$view->removeComponent('field_weight');
$view->removeComponent('links');
$view->save();
echo "Saved case_study view display\n";

// --- 4. Gallery fields on media types ---------------------------------------

$media_storages = [
  'field_in_gallery' => ['type' => 'boolean', 'settings' => []],
  'field_caption' => ['type' => 'string', 'settings' => []],
  'field_gallery_link' => ['type' => 'link', 'settings' => []],
];
foreach ($media_storages as $name => $def) {
  if (!FieldStorageConfig::loadByName('media', $name)) {
    FieldStorageConfig::create([
      'field_name' => $name,
      'entity_type' => 'media',
      'type' => $def['type'],
      'cardinality' => 1,
      'settings' => $def['settings'],
    ])->save();
    echo "Created storage media.$name\n";
  }
}

$gallery_bundles = ['image', 'remote_video', 'svg_image'];
$media_fields = [
  'field_in_gallery' => ['label' => 'Show in gallery', 'settings' => ['on_label' => 'In gallery', 'off_label' => 'Not in gallery']],
  'field_caption' => ['label' => 'Caption', 'settings' => []],
  'field_gallery_link' => ['label' => 'Link', 'settings' => ['link_type' => 16, 'title' => 1]],
];
foreach ($gallery_bundles as $bundle) {
  foreach ($media_fields as $name => $def) {
    if (!FieldConfig::loadByName('media', $bundle, $name)) {
      FieldConfig::create([
        'field_name' => $name,
        'entity_type' => 'media',
        'bundle' => $bundle,
        'label' => $def['label'],
        'required' => FALSE,
        'settings' => $def['settings'],
      ])->save();
      echo "Created field media.$bundle.$name\n";
    }
  }
  $form = $display_repo->getFormDisplay('media', $bundle, 'default');
  $form->setComponent('field_in_gallery', ['type' => 'boolean_checkbox', 'weight' => 20]);
  $form->setComponent('field_caption', ['type' => 'string_textfield', 'weight' => 21]);
  $form->setComponent('field_gallery_link', ['type' => 'link_default', 'weight' => 22]);
  $form->save();
  $view = $display_repo->getViewDisplay('media', $bundle, 'default');
  $view->setComponent('field_caption', ['type' => 'string', 'label' => 'hidden', 'weight' => 10]);
  $view->setComponent('field_gallery_link', ['type' => 'link', 'label' => 'hidden', 'weight' => 11]);
  $view->removeComponent('field_in_gallery');
  $view->save();
  echo "Saved media.$bundle displays\n";
}

// --- 5. Views ----------------------------------------------------------------

if (!View::load('featured_work')) {
  View::create([
    'id' => 'featured_work',
    'label' => 'Featured work',
    'description' => 'Case studies rendered in full as cards on the work page.',
    'base_table' => 'node_field_data',
    'base_field' => 'nid',
    'display' => [
      'default' => [
        'display_plugin' => 'default',
        'id' => 'default',
        'display_title' => 'Default',
        'position' => 0,
        'display_options' => [
          'title' => 'Selected work',
          'row' => ['type' => 'entity:node', 'options' => ['view_mode' => 'full']],
          'pager' => ['type' => 'none', 'options' => ['offset' => 0]],
          'access' => ['type' => 'perm', 'options' => ['perm' => 'access content']],
          'cache' => ['type' => 'tag', 'options' => []],
          'style' => ['type' => 'default', 'options' => []],
          'filters' => [
            'status' => [
              'id' => 'status',
              'table' => 'node_field_data',
              'field' => 'status',
              'entity_type' => 'node',
              'entity_field' => 'status',
              'value' => '1',
              'plugin_id' => 'boolean',
            ],
            'type' => [
              'id' => 'type',
              'table' => 'node_field_data',
              'field' => 'type',
              'entity_type' => 'node',
              'entity_field' => 'type',
              'value' => ['case_study' => 'case_study'],
              'plugin_id' => 'bundle',
            ],
          ],
          'sorts' => [
            'field_weight_value' => [
              'id' => 'field_weight_value',
              'table' => 'node__field_weight',
              'field' => 'field_weight_value',
              'order' => 'ASC',
              'plugin_id' => 'standard',
            ],
            'created' => [
              'id' => 'created',
              'table' => 'node_field_data',
              'field' => 'created',
              'entity_type' => 'node',
              'entity_field' => 'created',
              'order' => 'DESC',
              'plugin_id' => 'date',
            ],
          ],
        ],
      ],
      'block_1' => [
        'display_plugin' => 'block',
        'id' => 'block_1',
        'display_title' => 'Block',
        'position' => 1,
        'display_options' => ['display_extenders' => []],
      ],
    ],
  ])->save();
  echo "Created view featured_work\n";
}

if (!View::load('gallery')) {
  View::create([
    'id' => 'gallery',
    'label' => 'Gallery',
    'description' => 'Media flagged for the work-page gallery – newest first.',
    'base_table' => 'media_field_data',
    'base_field' => 'mid',
    'display' => [
      'default' => [
        'display_plugin' => 'default',
        'id' => 'default',
        'display_title' => 'Default',
        'position' => 0,
        'display_options' => [
          'title' => 'Gallery',
          'row' => ['type' => 'entity:media', 'options' => ['view_mode' => 'full']],
          'pager' => ['type' => 'none', 'options' => ['offset' => 0]],
          'access' => ['type' => 'perm', 'options' => ['perm' => 'view media']],
          'cache' => ['type' => 'tag', 'options' => []],
          'style' => ['type' => 'default', 'options' => []],
          'filters' => [
            'status' => [
              'id' => 'status',
              'table' => 'media_field_data',
              'field' => 'status',
              'entity_type' => 'media',
              'entity_field' => 'status',
              'value' => '1',
              'plugin_id' => 'boolean',
            ],
            'field_in_gallery_value' => [
              'id' => 'field_in_gallery_value',
              'table' => 'media__field_in_gallery',
              'field' => 'field_in_gallery_value',
              'value' => '1',
              'plugin_id' => 'boolean',
            ],
          ],
          'sorts' => [
            'created' => [
              'id' => 'created',
              'table' => 'media_field_data',
              'field' => 'created',
              'entity_type' => 'media',
              'entity_field' => 'created',
              'order' => 'DESC',
              'plugin_id' => 'date',
            ],
          ],
        ],
      ],
      'block_1' => [
        'display_plugin' => 'block',
        'id' => 'block_1',
        'display_title' => 'Block',
        'position' => 1,
        'display_options' => ['display_extenders' => []],
      ],
    ],
  ])->save();
  echo "Created view gallery\n";
}

echo "Done.\n";
