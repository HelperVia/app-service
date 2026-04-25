<?php


return [

    'default_department_name' => 'General',
    'default_agent_chat_limit' => 5,
    'prechatform_enabled' => true,
    'postchatform_enabled' => true,
    'prechatform' => [
        'en' => [
            'fields' => [
                [
                    'id' => 'default_information',
                    'name' => 'information',
                    'type' => 'information',
                    'label' => '',
                    'order' => 1,
                    'required' => false,
                    'deletable' => true,
                    'content' => '<b>Welcome!</b> Please fill out the form below to start the chat.',
                ],
                [
                    'id' => 'default_name',
                    'name' => 'name',
                    'type' => 'name',
                    'label' => 'Full Name',
                    'order' => 2,
                    'required' => true,
                    'deletable' => true,
                ],
                [
                    'id' => 'default_email',
                    'name' => 'email',
                    'type' => 'email',
                    'label' => 'Email Address',
                    'order' => 3,
                    'required' => true,
                    'deletable' => true,
                ],
            ],
        ],
        'tr' => [
            'fields' => [
                [
                    'id' => 'default_information',
                    'name' => 'information',
                    'type' => 'information',
                    'label' => '',
                    'order' => 1,
                    'required' => false,
                    'deletable' => true,
                    'content' => '<b>Hoş geldiniz!</b> Sohbeti başlatmak için lütfen aşağıdaki formu doldurun.',
                ],
                [
                    'id' => 'default_name',
                    'name' => 'name',
                    'type' => 'name',
                    'label' => 'Ad Soyad',
                    'order' => 2,
                    'required' => true,
                    'deletable' => true,
                ],
                [
                    'id' => 'default_email',
                    'name' => 'email',
                    'type' => 'email',
                    'label' => 'E-posta Adresi',
                    'order' => 3,
                    'required' => true,
                    'deletable' => true,
                ],
            ],
        ]
    ],
    'postchatform' => [
        'en' => [
            'fields' => [
                [
                    'label' => null,
                    'name' => 'thank_u_message',
                    'order' => 0,
                    'required' => true,
                    'deletable' => true,
                    'type' => 'thank_u_message',
                    'content' => 'Thank you for the chat. Feel free to leave us any additional feedback.',
                    '_id' => 'z72ob_thank_u_message',
                ],
                [
                    'label' => 'Is this the first time you have chatted with us about this case?',
                    'name' => 'choice_list',
                    'order' => 1,
                    'required' => true,
                    'deletable' => true,
                    'type' => 'choice_list',
                    'options' => [
                        ['value' => 'Yes', '_id' => 'pww0j'],
                        ['value' => 'No', '_id' => 'wlajs'],
                    ],
                    '_id' => '6tf3v_choice_list',
                ],
                [
                    'label' => 'How would you rate this chat?',
                    'name' => 'chat_rating',
                    'order' => 2,
                    'required' => true,
                    'deletable' => true,
                    'type' => 'chat_rating',
                    '_id' => 'ldatf_chat_rating',
                ],
            ],
        ],
        'tr' => [
            'fields' => [
                [
                    'label' => null,
                    'name' => 'thank_u_message',
                    'order' => 0,
                    'required' => true,
                    'deletable' => true,
                    'type' => 'thank_u_message',
                    'content' => 'Sohbet için teşekkürler. Eklemek istediğiniz bir geri bildirim varsa lütfen paylaşın.',
                    '_id' => 'z72ob_thank_u_message',
                ],
                [
                    'label' => 'Bu konu hakkında bizimle ilk kez mi iletişime geçiyorsunuz?',
                    'name' => 'choice_list',
                    'order' => 1,
                    'required' => true,
                    'deletable' => true,
                    'type' => 'choice_list',
                    'options' => [
                        ['value' => 'Evet', '_id' => 'pww0j'],
                        ['value' => 'Hayır', '_id' => 'wlajs'],
                    ],
                    '_id' => '6tf3v_choice_list',
                ],
                [
                    'label' => 'Bu sohbeti nasıl değerlendirirsiniz?',
                    'name' => 'chat_rating',
                    'order' => 2,
                    'required' => true,
                    'deletable' => true,
                    'type' => 'chat_rating',
                    '_id' => 'ldatf_chat_rating',
                ],
            ],
        ]
    ],
    'widget_default_languages' => [
        'en' => [
            'label' => 'English',
            'translations' => [
                'welcome_message' => 'Welcome 👋 How can we assist you today?',
                'customer_name' => 'Customer',
                'message_placeholder' => 'Type your message here...',
                'offline_info' => 'We’re currently offline. Please leave a message and our team will get back to you shortly via email.',
                'queued_customer_info' => 'You are currently number %number% in line. Estimated wait time is approximately %minutes% minute(s). Thank you for your patience.'
            ]
        ],

        'tr' => [
            'label' => 'Türkçe',
            'translations' => [
                'welcome_message' => 'Hoş geldiniz 👋 Size  nasıl yardımcı olabiliriz?',
                'customer_name' => 'Müşteri',
                'message_placeholder' => 'Mesajınızı buraya yazın...',
                'offline_info' => 'Şu anda çevrimdışıyız. Lütfen bir mesaj bırakın, ekibimiz en kısa sürede e-posta ile size dönüş yapacaktır.',
                'queued_customer_info' => 'Şu anda sırada %number%. sıradasınız. Tahmini bekleme süresi yaklaşık %minutes% dakika. Anlayışınız için teşekkür ederiz.'
            ]
        ]
    ],
    'widget' => [
        'customization' => [
            "appearance" => [
                "initial" => "maximized",
                "window" => "bubble",
                "theme" => "light",
                "themeColor" => "null",
                "themeDetail" => [
                    "minimized" => [
                        "widget_bg" => "#90DB27",
                        "widget_text" => "#FFFFFF",
                        "widget_icon_color" => "#FFFFFF",
                        "gradient" => true
                    ],
                    "maximized" => [
                        "chat_bg" => "#F6F6F7",
                        "primary_color" => "#90DB27",
                        "customer_bubble" => "#90DB27",
                        "customer_bubble_text" => "#FFFFFF",
                        "agent_bubble" => "#FFFFFF",
                        "agent_bubble_text" => "#212121",
                        "system_messages" => "#9E9E9E",
                        "gradient" => true
                    ]
                ]
            ],
            "position" => [
                "align" => "right",
                "side_spacing" => 1,
                "bottom_spacing" => 29
            ],
            "visibility" => "hidden",
            "advanced" => [
                "show_logo" => true,
                "show_agent_photo" => true,
                "show_rate_agent" => true,
                "show_powered_by" => true,
                "show_typing_indicator" => true,
                "sound_enabled" => true
            ]
        ]
    ]


];