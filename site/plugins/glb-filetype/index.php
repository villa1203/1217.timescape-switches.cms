<?php

use Kirby\Filesystem\F;
use Kirby\Filesystem\Mime;

/**
 * Teach Kirby about .glb / .gltf 3D model files.
 *
 * Without this, Panel uploads fail with "Invalid file type" because Kirby
 * doesn't recognise the extension and the browser usually sends a .glb as
 * application/octet-stream. We register a new "model" file-type group and
 * map the extensions to their accepted MIME types (octet-stream included,
 * the same trick Kirby's own csv mapping uses).
 *
 * Portable: copy this whole plugin folder into another Kirby project to get
 * GLB upload support there too.
 */

// New file-type group so F::type('foo.glb') === 'model'
F::$types['model'] = ['glb', 'gltf'];

// Extension → accepted MIME types
Mime::$types['glb']  = ['model/gltf-binary', 'application/octet-stream'];
Mime::$types['gltf'] = ['model/gltf+json', 'application/json', 'application/octet-stream'];

Kirby::plugin('tms/glb-filetype', []);
