<?php

return [
    'languages' => [
        'ca',
        'es',
        'en'
    ],
    'route' => [
        'prefix' => 'editora',
    ],
    'front_url' => '',
    'uploads-storage' => 'editora_public',
    'private-uploads-storage' => 'editora_private',
    'url-storage-relative' => true,
    'curl-refresh-command' => '',
    'special_classes' => [],
    // Only for sites whose front is the legacy Editora one: it reads omp_instances_cache before
    // omp_values, and this admin never rebuilds that cache, so saving or deleting an instance
    // must drop its rows for the front to show the change.
    'invalidate-legacy-instance-cache' => false,
];
