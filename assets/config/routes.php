<?php
return array(
    'rest' => array('/api(/<controller>(/<id>(/<property>)))',
        array(
            'controller' => 'Default',
            'action' => 'get'
        )
    ),
    'error' => array(
        '/error/<id>',
        array(
            'controller' => 'error',
            'action' => 'view'
        ),
    ),
    'faq' => array('/pages/frequently-asked-questions',
        array(
            'controller' => 'Faq',
            'action' => 'index'
        )
    ),
    'pages_page' => array('/pages/<alias>',
        array(
            'controller' => 'Pages',
            'action' => 'show'
        )
    ),
    'news' => array('/blogs/news(/)',
        array(
            'controller' => 'news',
            'action' => 'index'
        )
    ),
    'news_item' => array('/blogs/news(/<alias>)',
        array(
            'controller' => 'news',
            'action' => 'show'
        )
    ),
    'admin_error' => array(
        '/admin/error/<id>',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'error',
            'action' => 'view'
        ),
    ),
    'wishlist_add_product' => array('/wishlist/add-product/<id>',
        array(
            'controller' => 'Wishlist',
            'action' => 'add_product'
        ),
        'POST'
    ),
    'wishlist_delete_product' => array('/wishlist/remove-product/<id>',
        array(
            'controller' => 'Wishlist',
            'action' => 'delete_product'
        ),
        'POST'
    ),

    'wishlist_new' => array('/wishlist/new',
        array(
            'controller' => 'Wishlist',
            'action' => 'new'
        ),
        'POST'
    ),
    'search' => array('/search(/page)', array(
        'controller' => 'Search',
        'action' => 'index',
        'page'   =>  1
        ),
        'GET'
    ),
    'profile_edit' => ['/account/profile/edit',
        array(
            'controller' => 'account',
            'action' => 'edit_profile'
        )
    ],

    'admin_option_value' => array('/admin/option-value(/<action>(/<id>))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'OptionValue',
            'action' => 'index',
            'force_hyphens' => true
        )
    ),

    'admin_newsletter_signup' => array('/admin/newsletter-signup(/<action>(/<id>))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'NewsletterSignup',
            'action' => 'index',
            'force_hyphens' => true
        )
    ),

    'admin_newsletter_template' => array('/admin/newsletter-template(/<action>(/<id>))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'NewsletterTemplate',
            'action' => 'index',
            'force_hyphens' => true
        )
    ),

    'admin_newsletter' => array('/admin/newsletter(/<action>(/<id>))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'Newsletter',
            'action' => 'index',
            'force_hyphens' => true
        )
    ),

    'admin_product_option_value' => array('/admin/product-option-value(/<action>(/<id>))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'ProductOptionValue',
            'action' => 'index',
            'force_hyphens' => true
        )
    ),

    'admin_product_image' => array('/admin/product-image(/<action>(/<id>))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'ProductImage',
            'action' => 'index',
            'force_hyphens' => true
        )
    ),

    'admin_entity_action' => array(
        array(
            '/admin/<controller>/<id>/<action>',
            array(
                'id' => '\d+'
            ),
        ),
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'home',

            'force_hyphens' => true
        )
    ),

    'admin' => array('/admin(/<controller>(/<action>(/<id>)))',
        array(
            'namespace' => 'App\\Admin\\',
            'controller' => 'home',
            'action' => 'index',
            'force_hyphens' => true
        )
    ),

    'collection_item' => array('/collections/<category>/products/<alias>',
        array(
            'controller' => 'product',
            'action' => 'view'
        )
    ),
    'product_item' => array('/products/<alias>',
        array(
            'controller' => 'product',
            'action' => 'view'
        )
    ),

    'collection' => array('/collections/<category>',
        array(
            'controller' => 'category',
            'action' => 'view',
            'category' => 'all'
        )
    ),

    'install' => array('/install(/<id>)',
        array(
            'controller' => 'install',
            'action' => 'index'
        )
    ),
	'default' => array('(/<controller>(/<action>(/<id>)))',
        array(
            'controller' => 'home',
            'action' => 'index'
        )
    ),
);
