<?php

/**
 * M1 – migrate the Work page list into case_study nodes + gallery media.
 * One-off: ddev drush scr scripts/m1-migrate-work.php
 * Copy stays as close as possible to the existing Work page (node 4).
 */

use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

$term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

function tag(string $name) {
  $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
  $existing = $storage->loadByProperties(['vid' => 'tags', 'name' => $name]);
  if ($existing) {
    return reset($existing);
  }
  $term = Term::create(['vid' => 'tags', 'name' => $name]);
  $term->save();
  echo "Created tag: $name\n";
  return $term;
}

$case_studies = [
  [
    'title' => 'IIED',
    'summary' => 'Most recently a Drupal 10 migration; led theming (custom Tailwind, reusable components); lightweight design system; IA improvements; GA4/GTM dashboards; related-content patterns and much more.',
    'role' => 'Theming lead',
    'tags' => ['Drupal', 'Tailwind', 'design systems', 'information architecture', 'analytics'],
    'link' => 'https://www.iied.org/',
    'weight' => 0,
  ],
  [
    'title' => 'Biocultural Heritage',
    'summary' => 'Audit and recommendations; site configuration; design; custom Tailwind theme and components.',
    'role' => 'Main developer',
    'tags' => ['Drupal', 'Tailwind', 'design'],
    'link' => 'https://biocultural.iied.org/',
    'weight' => 1,
  ],
  [
    'title' => 'Climate Resilience Finance',
    'summary' => 'Event site – IA, configuration, theming, performance checks and analytics/tracking; collaborated with in-house teams on content and deployment.',
    'role' => 'Main developer',
    'tags' => ['Drupal', 'information architecture', 'analytics', 'performance'],
    'link' => 'https://climateresilience.finance/',
    'weight' => 2,
  ],
  [
    'title' => 'El Gran Sueño',
    'summary' => 'Full multilingual Drupal build – IA, theming, workflows and editor support – and full branding.',
    'role' => 'Sole developer',
    'tags' => ['Drupal', 'multilingual', 'branding'],
    'link' => 'https://elgransueno.es/',
    'weight' => 3,
  ],
  [
    'title' => 'People Not Poaching',
    'summary' => 'Online learning platform – site development, theming and functions.',
    'role' => 'Developer',
    'tags' => ['Drupal', 'theming'],
    'link' => 'https://www.peoplenotpoaching.org/',
    'weight' => 4,
  ],
];

$node_storage = \Drupal::entityTypeManager()->getStorage('node');
foreach ($case_studies as $cs) {
  $existing = $node_storage->loadByProperties(['type' => 'case_study', 'title' => $cs['title']]);
  if ($existing) {
    echo "Skipping existing: {$cs['title']}\n";
    continue;
  }
  Node::create([
    'type' => 'case_study',
    'title' => $cs['title'],
    'field_description' => $cs['summary'],
    'field_role' => $cs['role'],
    'field_tags' => array_map(fn($t) => tag($t)->id(), $cs['tags']),
    'field_link' => ['uri' => $cs['link']],
    'field_weight' => $cs['weight'],
    'status' => 1,
  ])->save();
  echo "Created case study: {$cs['title']}\n";
}

$videos = [
  [
    'name' => 'Pastoralism MOOC',
    'url' => 'https://vimeo.com/793382389',
    'caption' => 'Pastoralism MOOC – animated video series (1 of 14); storyboarded and scripted with researchers, design, branding and After Effects animation',
    'link' => 'https://vimeo.com/793382389',
  ],
  [
    'name' => 'Climate change and conflict animation',
    'url' => 'https://www.youtube.com/watch?v=iiMoAI4PbBQ',
    'caption' => 'Climate change and conflict – shown at the Third International Conference on Environmental Peacebuilding, The Hague',
    'link' => 'https://www.iied.org/climate-change-conflict-reframing-debate',
  ],
];

$media_storage = \Drupal::entityTypeManager()->getStorage('media');
foreach ($videos as $v) {
  $existing = $media_storage->loadByProperties(['bundle' => 'remote_video', 'name' => $v['name']]);
  if ($existing) {
    echo "Skipping existing media: {$v['name']}\n";
    continue;
  }
  Media::create([
    'bundle' => 'remote_video',
    'name' => $v['name'],
    'field_media_oembed_video' => $v['url'],
    'field_in_gallery' => 1,
    'field_caption' => $v['caption'],
    'field_gallery_link' => ['uri' => $v['link']],
    'status' => 1,
  ])->save();
  echo "Created gallery video: {$v['name']}\n";
}

echo "Done.\n";
