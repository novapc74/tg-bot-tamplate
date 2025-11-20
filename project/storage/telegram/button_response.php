<?php

$response = $update = [
    'update_id' => 458558486,
    'callback_query' => [
        'id' => '2773516237998786277',
        'from' => [
            'id' => 645759570,
            'is_bot' => false,  // В выводе пусто, что обычно означает false
            'first_name' => 'Вячеслав',
            'last_name' => 'Макаров',
            'username' => 'novapc',
            'language_code' => 'ru',
        ],
        'message' => [
            'message_id' => 483,
            'from' => [
                'id' => 8566674584,
                'is_bot' => 1,  // true
                'first_name' => 'novapc_bot',
                'username' => 'novapc_bot',
            ],
            'chat' => [
                'id' => 645759570,
                'first_name' => 'Вячеслав',
                'last_name' => 'Макаров',
                'username' => 'novapc',
                'type' => 'private',
            ],
            'date' => 1762856313,
            'text' => 'Выберите опцию:',
            'reply_markup' => [
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'HELP',
                            'callback_data' => '/help',
                        ],
                        [
                            'text' => 'IMAGE',
                            'callback_data' => 'image_action',
                        ],
                        [
                            'text' => 'FORM',
                            'callback_data' => 'form_action',
                        ],
                        [
                            'text' => 'D',
                            'callback_data' => 'action4',
                        ],
                    ],
                    [
                        [
                            'text' => 'Google',
                            'url' => 'https://google.com/',
                        ],
                        [
                            'text' => 'HH',
                            'url' => 'https://hh.ru/',
                        ],
                    ],
                ],
            ],
        ],
        'chat_instance' => '-65079636872030603',
        'data' => '/help',
    ],
];
