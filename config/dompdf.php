<?php

// ==========================================
// 1. Configuration DomPDF - config/dompdf.php
// ==========================================

return [
    /**
     * Paramètres DomPDF
     */
    'show_warnings' => false,   // Masquer les avertissements
    'public_path' => public_path(),
    'convert_entities' => true,
    'options' => [
        /**
         * Format de page par défaut
         */
        'default_paper_size' => 'a4',
        'default_font' => 'DejaVu Sans',
        
        /**
         * Chemin des polices
         */
        'font_dir' => storage_path('fonts/'),
        'font_cache' => storage_path('fonts/'),
        'temp_dir' => sys_get_temp_dir(),
        
        /**
         * Paramètres de rendu
         */
        'chroot' => public_path(),
        'enable_font_subsetting' => false,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_orientation' => 'portrait',
        'default_paper_size' => 'a4',
        
        /**
         * Sécurité et performance
         */
        'is_font_subsetting_enabled' => false,
        'is_remote_enabled' => true,
        'is_javascript_enabled' => false,
        'is_html5_parser_enabled' => true,
    ],
];