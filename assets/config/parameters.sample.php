<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 29.07.2014
 * Time: 19:46
 */
return array(
    'host' => 'http://evolveskateboards.ru',     // Used in various scripts, where $_SERVER is inaccessible, or where there is a risk thereof.
    'display_errors' => false,
    'use_perl_upload' => false,
    'use_external_dir' => false,
    'user_pictures_external_dir' => '/lib/init/rw',
    'user_pictures_path' => '/web/user_pictures/',
    'common_path' => dirname(dirname(__DIR__)) . '/assets/views/common/',
    'annotation_length' => 900,
    'thumb_variants' => [
        'small' => [
            'maxWidth' => 380,
            'maxHeight' => 192,
            'format' => 'jpg',
            'resizeUp' => false
        ],
        'tiny' => [
            'maxWidth' => 40,
            'maxHeight' => 30,
            'format' => 'jpg',
        ],
        'medium' => [
            'maxWidth' => 600,
            'maxHeight' => 400,
            'format' => 'jpg'
        ]
    ],
    'thumb_path' => __DIR__.'/../../web/cache/thumb/',
    'allow_install' => false,
    'use_ssl' => true,
    'admin_email' => [],//'admin1@gmail.com', 'admin2@example.com'],
    'robot_email' => 'robot@evolveskateboards.ru',
    'root_category_name' => 'Все',
    'social_links' => [
        'twitter' => 'https://twitter.com/EvolveSk8boards',
        'facebook' => 'https://www.facebook.com/EvolveSkateboardsUSA',
        'youtube' => 'https://www.youtube.com/user/evolveSkateboards',
    ],
    'wkhtmltopdf_path' => '/var/www/el/bin/wkhtmltopdf',
    'receipt' => [
        'bank_name' => 'ОАО "ВУЗ-БАНК" Г. ЕКАТЕРИНБУРГ',
        'bank_bic' => '046577781',
        'bank_account' => '30101810600000000781',
        'company_account' => '40702810500000101928',
        'company_name' => 'ООО "ИВОЛВ РУС"',
        'company_address' => '620144, Свердловская обл, Екатеринбург, Куйбышева, дом № 55, офис 509',
        'company_inn' => '6671468126',
        'company_kpp' => '667101001',
    ]
);
