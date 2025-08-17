<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slide = new App\Models\HeroSlide();
$slide->title = ['en' => 'Test Slide'];
$slide->subtitle = ['en' => 'This is a test slide'];
$slide->description = ['en' => '<p>This is a description of the test slide.</p>'];
$slide->additional_details = ['en' => '<p>These are additional details that will be shown in the modal.</p><p>You can add more information here.</p>'];
$slide->button_text = ['en' => 'Learn More'];
$slide->button_link = '#';
$slide->image = '/images/placeholder.jpg';
$slide->is_active = true;
$slide->sort_order = 1;
$slide->save();

echo "Slide created successfully\n";