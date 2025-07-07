<?php

return [
    'site_name' => 'Digital Portfolio',
    'uploads_dir' => 'storage/portfolio_uploads',
    'max_upload_size' => 10 * 1024 * 1024, // 10 MB
    'allowed_file_types' => ['jpg', 'jpeg', 'png', 'pdf', 'docx'],
    'default_language' => 'en',
    'timezone' => 'UTC',
    'features' => [
        'enable_comments' => true,
        'enable_ratings' => false,
        'show_contact_form' => true,
    ],
];

?>