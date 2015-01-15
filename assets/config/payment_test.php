<?php
return [
    'groups' => [
        'OPTION 2.M' => [
            'card_type' => 'mastercard',
            'tests' => [
                '21.1' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. Full 3DSecure',
                    'card' => 1,
                    'amount' => '21.10',
                    'comment' => 'блокировка и списание средств с карты в пользу ТСП c 3DSecure',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ]
                ]]],

                '21.2' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. Full 3DSecure',
                    'card' => 2,
                    'amount' => '21.20',
                    'comment' => '',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ]
                ]]],

                '21.3' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. No 3DSecure',
                    'card' => 3,
                    'amount' => '21.30',
                    'comment' => 'блокировка и списание средств с карты в пользу ТСП без 3DSecure',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ]
                ]]],

                '21.4' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. No 3DSecure',
                    'card' => 4,
                    'amount' => '21.40',
                    'comment' => '',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ]
                ]]],

                '21.5' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. Full 3DSecure',
                    'card' => 5,
                    'amount' => '21.50',
                    'comment' => '',
                    'expected' => [
                        'Host' => '01',
                        'RC' => '01',
                        'ACTION' => '2'
                    ]
                ]]],

                '21.6' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 6,
                    'amount' => '21.60',
                    'comment' => 'возможные коды ответа 04,33-38,41,43',
                    'expected' => [
                        'Host' => '04',
                        'RC' => '04',
                        'ACTION' => '2'
                    ]
                ]]],

                '21.7' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 7,
                    'amount' => '21.70',
                    'comment' => '',
                    'expected' => [
                        'Host' => '05',
                        'RC' => '05',
                        'ACTION' => '2'
                    ]
                ]]],

                '21.8' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 8,
                    'amount' => '21.80',
                    'comment' => '',
                    'expected' => [
                        'Host' => '51',
                        'RC' => '51',
                        'ACTION' => '2'
                    ]
                ]]],

                '21.9' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 9,
                    'amount' => '21.90',
                    'comment' => '',
                    'expected' => [
                        'Host' => '54',
                        'RC' => '54',
                        'ACTION' => '2'
                    ]
                ]]],

                '22.0' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 10,
                    'amount' => '22.00',
                    'comment' => 'возможные коды ответа 57,58,81,91,94',
                    'expected' => [
                        'Host' => '57',
                        'RC' => '57',
                        'ACTION' => '2'
                    ]
                ]]],

                '22.1' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. Full 3DSecure',
                    'card' => 2,
                    'amount' => '222.10',
                    'comment' => 'возможные коды ответа 61,62',
                    'expected' => [
                        'Host' => '61',
                        'RC' => '61',
                        'ACTION' => '2'
                    ]
                ]]],

                '22.2' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '22.20',
                        'comment' => 'блокировка и списание средств с карты в пользу ТСП c 3DSecure',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '22.20',
                        'comment' => 'отмена блокировки и возврат средств на карту',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ]
                ]],

                '22.3' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '22.30',
                        'comment' => 'EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '22.30',
                        'comment' => 'повтор первого EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '121',
                            'RC' => '00',
                            'ACTION' => '1'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '22.30',
                        'comment' => 'EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '22.30',
                        'comment' => 'повтор первого EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                ]],

                '22.4' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '22.40',
                        'comment' => 'EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Partial Reversal',
                        'card' => 1,
                        'amount' => '10.20',
                        'comment' => 'ORG_AMOUNT =22.40 EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU',
                            'ORG_AMOUNT' => '22.40'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Partial Reversal',
                        'card' => 1,
                        'amount' => '5.20',
                        'comment' => 'ORG_AMOUNT =22.40 EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU',
                            'ORG_AMOUNT' => '22.40'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Partial Reversal',
                        'card' => 1,
                        'amount' => '15.30',
                        'comment' => 'ORG_AMOUNT =22.40 EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '13',
                            'RC' => '13',
                            'ACTION' => '2'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU',
                            'ORG_AMOUNT' => '22.40'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Partial Reversal',
                        'card' => 1,
                        'amount' => '7.00',
                        'comment' => 'ORG_AMOUNT =22.40 EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU',
                            'ORG_AMOUNT' => '22.40'
                        ]
                    ],
                ]],

                '22.5' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '22.50',
                        'comment' => '',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '22.55',
                        'comment' => 'AMOUNT = 22.55',
                        'expected' => [
                            'Host' => '13',
                            'RC' => '13',
                            'ACTION' => '2'
                        ],
                        'data' => [
                            'AMOUNT' => '22.55'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '22.50',
                        'comment' => '',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                    ],
                ]],

                '22.6' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '22.60',
                    'comment' => 'EXP=1411',
                    'expected' => [
                        'Host' => '54',
                        'RC' => '54',
                        'ACTION' => '2'
                    ],
                    'data' => [
                        'EXP' => '1411'
                    ]
                ]]],

                '22.7' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '22.70',
                    'comment' => 'CVV2=123',
                    'expected' => [
                        'Host' => '05',
                        'RC' => '05',
                        'ACTION' => '2'
                    ],
                    'data' => [
                        'CVV2' => '123'
                    ]
                ]]],

                '22.8' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '22.80',
                    'comment' => 'CARD= добавить в конец 12',
                    'expected' => [
                        'Host' => '14',
                        'RC' => '14',
                        'ACTION' => '2'
                    ],
                    'data' => [
                        'card_add' => 12
                    ]
                ]]],

                '22.9' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '22.90',
                    'comment' => 'пароль 3DSecure=11111111 три раза',
                    'expected' => [
                        'Host' => 'NO',
                        'RC' => '-19',
                        'ACTION' => '3'
                    ],
                    'data' => [
                        '3DSecure' => '11111111'
                    ]
                ]]],

                '23.1' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '23.10',
                    'comment' => 'Отменить ввод 3DSecure',
                    'expected' => [
                        'Host' => 'NO',
                        'RC' => '-19',
                        'ACTION' => '3'
                    ],
                    'data' => [
                        'cancel_3DSecure' => true
                    ]
                ]]],
            ]
        ],

        'OPTION 2.V' => [
            'card_type' => 'visa',
            'tests' => [
                '26.1' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. Full 3DSecure',
                    'card' => 1,
                    'amount' => '26.10',
                    'comment' => 'блокировка и списание средств с карты в пользу ТСП c 3DSecure',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ]
                ]]],

                '26.2' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. Full 3DSecure',
                    'card' => 2,
                    'amount' => '26.20',
                    'comment' => '',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ]
                ]]],

                '26.3' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. No 3DSecure',
                    'card' => 3,
                    'amount' => '26.30',
                    'comment' => 'блокировка и списание средств с карты в пользу ТСП без 3DSecure',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ]
                ]]],

                '26.4' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. No 3DSecure',
                    'card' => 4,
                    'amount' => '26.40',
                    'comment' => '',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ]
                ]]],

                '26.5' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. Full 3DSecure',
                    'card' => 5,
                    'amount' => '26.50',
                    'comment' => '',
                    'expected' => [
                        'Host' => '01',
                        'RC' => '01',
                        'ACTION' => '2'
                    ]
                ]]],

                '26.6' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 6,
                    'amount' => '26.60',
                    'comment' => 'возможные коды ответа 04,33-38,41,43',
                    'expected' => [
                        'Host' => '04',
                        'RC' => '04',
                        'ACTION' => '2'
                    ]
                ]]],

                '26.7' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 7,
                    'amount' => '26.70',
                    'comment' => '',
                    'expected' => [
                        'Host' => '05',
                        'RC' => '05',
                        'ACTION' => '2'
                    ]
                ]]],

                '26.8' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 8,
                    'amount' => '26.80',
                    'comment' => '',
                    'expected' => [
                        'Host' => '51',
                        'RC' => '51',
                        'ACTION' => '2'
                    ]
                ]]],

                '26.9' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 9,
                    'amount' => '26.90',
                    'comment' => '',
                    'expected' => [
                        'Host' => '54',
                        'RC' => '54',
                        'ACTION' => '2'
                    ]
                ]]],

                '27.0' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Authorization and Sale',
                    'card' => 10,
                    'amount' => '27.00',
                    'comment' => 'возможные коды ответа 57,58,81,91,94',
                    'expected' => [
                        'Host' => '57',
                        'RC' => '57',
                        'ACTION' => '2'
                    ]
                ]]],

                '27.1' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale. Full 3DSecure',
                    'card' => 2,
                    'amount' => '227.10',
                    'comment' => 'возможные коды ответа 61,62',
                    'expected' => [
                        'Host' => '61',
                        'RC' => '61',
                        'ACTION' => '2'
                    ]
                ]]],

                '27.2' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '27.20',
                        'comment' => 'блокировка и списание средств с карты в пользу ТСП c 3DSecure',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '27.20',
                        'comment' => 'отмена блокировки и возврат средств на карту',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ]
                ]],

                '27.3' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '27.30',
                        'comment' => 'EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '27.30',
                        'comment' => 'повтор первого EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '121',
                            'RC' => '00',
                            'ACTION' => '1'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '27.30',
                        'comment' => 'EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '27.30',
                        'comment' => 'повтор первого EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '121',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                ]],

                '27.4' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '27.40',
                        'comment' => 'EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Partial Reversal',
                        'card' => 1,
                        'amount' => '10.20',
                        'comment' => 'ORG_AMOUNT =27.40 EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU',
                            'ORG_AMOUNT' => '27.40'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Partial Reversal',
                        'card' => 1,
                        'amount' => '5.20',
                        'comment' => 'ORG_AMOUNT =27.40 EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU',
                            'ORG_AMOUNT' => '27.40'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Partial Reversal',
                        'card' => 1,
                        'amount' => '15.30',
                        'comment' => 'ORG_AMOUNT =27.40 EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '13',
                            'RC' => '13',
                            'ACTION' => '2'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU',
                            'ORG_AMOUNT' => '27.40'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Partial Reversal',
                        'card' => 1,
                        'amount' => '12.00',
                        'comment' => 'ORG_AMOUNT =27.40 EMAIL = ECOMM_TEST@PRBB.RU',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'EMAIL' => 'ECOMM_TEST@PRBB.RU',
                            'ORG_AMOUNT' => '27.40'
                        ]
                    ],
                ]],

                '27.5' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth and Sale. Full 3DSecure',
                        'card' => 1,
                        'amount' => '27.50',
                        'comment' => '',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '27.55',
                        'comment' => 'AMOUNT = 27.55',
                        'expected' => [
                            'Host' => '13',
                            'RC' => '13',
                            'ACTION' => '2'
                        ],
                        'data' => [
                            'AMOUNT' => '27.55'
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => 1,
                        'amount' => '27.50',
                        'comment' => '',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                    ],
                ]],

                '27.6' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '27.60',
                    'comment' => 'EXP=1411',
                    'expected' => [
                        'Host' => '54',
                        'RC' => '54',
                        'ACTION' => '2'
                    ],
                    'data' => [
                        'EXP' => '1411'
                    ]
                ]]],

                '27.7' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '27.70',
                    'comment' => 'CVV2=123',
                    'expected' => [
                        'Host' => '05',
                        'RC' => '05',
                        'ACTION' => '2'
                    ],
                    'data' => [
                        'CVV2' => '123'
                    ]
                ]]],

                '27.8' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '27.80',
                    'comment' => 'CARD= добавить в конец 12',
                    'expected' => [
                        'Host' => '14',
                        'RC' => '14',
                        'ACTION' => '2'
                    ],
                    'data' => [
                        'card_add' => 12
                    ]
                ]]],

                '27.9' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '27.90',
                    'comment' => 'пароль 3DSecure=11111111 три раза',
                    'expected' => [
                        'Host' => 'NO',
                        'RC' => '-19',
                        'ACTION' => '3'
                    ],
                    'data' => [
                        '3DSecure' => '11111111'
                    ]
                ]]],

                '28.1' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth and Sale',
                    'card' => 2,
                    'amount' => '28.10',
                    'comment' => 'Отменить ввод 3DSecure',
                    'expected' => [
                        'Host' => 'NO',
                        'RC' => '-19',
                        'ACTION' => '3'
                    ],
                    'data' => [
                        'cancel_3DSecure' => true
                    ]
                ]]],
            ]
        ],

        'Макирование (Шифрование)' => [
            'card_type' => 'mastercard',

            'tests' => [
                '30.2' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth & Sale',
                    'card' => [1, 2, 3, 4],
                    'amount' => '30.20',
                    'comment' => 'шифрование только NONCE',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ],
                    'data' => [
                        'mac_fields' => ['NONCE']
                    ]
                ]]],

                '30.3' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth & Sale',
                    'card' => [1, 2, 3, 4],
                    'amount' => '30.30',
                    'comment' => 'исключить поле NONCE',
                    'expected' => [
                        'Host' => 'NO',
                        'RC' => '-17',
                        'ACTION' => '3'
                    ],
                    'data' => [
                        'mac_fields' => [],
                        'exclude_fields' => ['NONCE']
                    ]
                ]]],

                '30.5' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth & Sale',
                        'card' => [1, 2, 3, 4],
                        'amount' => '30.50',
                        'comment' => 'шифрование только NONCE',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'mac_fields' => ['NONCE']
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => [1, 2, 3, 4],
                        'amount' => '30.50',
                        'comment' => 'шифрование только NONCE',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'mac_fields' => ['NONCE']
                        ]
                    ]
                ]],

                '31.2' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth & Sale',
                    'card' => [1, 2, 3, 4],
                    'amount' => '31.20',
                    'comment' => 'NONCE, AMOUNT, ORDER, TIMESTAMP, TRTYPE, TERMINAL',
                    'expected' => [
                        'Host' => '00',
                        'RC' => '00',
                        'ACTION' => '0'
                    ],
                    'data' => [
                        'mac_fields' => ['NONCE', 'AMOUNT', 'ORDER', 'TIMESTAMP', 'TRTYPE', 'TERMINAL']
                    ]
                ]]],

                '31.3' => ['operations' => [[
                    'TRTYPE' => 1,
                    'description' => 'Auth & Sale',
                    'card' => [1, 2, 3, 4],
                    'amount' => '31.30',
                    'comment' => 'исключить поле NONCE',
                    'expected' => [
                        'Host' => 'NO',
                        'RC' => '-17',
                        'ACTION' => '3'
                    ],
                    'data' => [
                        'mac_fields' => ['AMOUNT', 'ORDER', 'TIMESTAMP', 'TRTYPE', 'TERMINAL'],
                        'exclude_fields' => ['NONCE']
                    ]
                ]]],

                '31.5' => ['operations' => [
                    [
                        'TRTYPE' => 1,
                        'description' => 'Auth & Sale',
                        'card' => [1, 2, 3, 4],
                        'amount' => '31.50',
                        'comment' => 'NONCE, AMOUNT, ORDER, TIMESTAMP, TRTYPE, TERMINAL',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'mac_fields' => ['NONCE', 'AMOUNT', 'ORDER', 'TIMESTAMP', 'TRTYPE', 'TERMINAL']
                        ]
                    ],
                    [
                        'TRTYPE' => 24,
                        'description' => 'Reversal',
                        'card' => [1, 2, 3, 4],
                        'amount' => '31.50',
                        'comment' => 'NONCE, AMOUNT, ORDER, TIMESTAMP, TRTYPE, TERMINAL',
                        'expected' => [
                            'Host' => '00',
                            'RC' => '00',
                            'ACTION' => '0'
                        ],
                        'data' => [
                            'mac_fields' => ['NONCE', 'AMOUNT', 'ORDER', 'TIMESTAMP', 'TRTYPE', 'TERMINAL']
                        ]
                    ]
                ]],
            ]
        ]
    ],
    
    'cards' => [
        'mastercard' => [
            '1' => ['card' => '5454210002686041', 'expires' => '1511', 'name' => 'ONE PRBB','cvv2' => '051', '3DSecure' => '12345678'],
            '2' => ['card' => '5454210002696495', 'expires' => '1511', 'name' => 'TWO PRBB','cvv2' => '797', '3DSecure' => '12345678'],
            '3' => ['card' => '5454210002640147', 'expires' => '1511', 'name' => 'THREE PRBB','cvv2' => '867'],
            '4' => ['card' => '5454210002551591', 'expires' => '1511', 'name' => 'FOUR PRBB','cvv2' => '418'],
            '5' => ['card' => '5454210002708563', 'expires' => '1511', 'name' => 'FIVE PRBB','cvv2' => '291'],
            '6' => ['card' => '5454210002596414', 'expires' => '1511', 'name' => 'SIX PRBB','cvv2' => '629'],
            '7' => ['card' => '5454210002809189', 'expires' => '1511', 'name' => 'SEVEN PRBB','cvv2' => '273'],
            '8' => ['card' => '5454210002281397', 'expires' => '1511', 'name' => 'EIGHT PRBB','cvv2' => '844'],
            '9' => ['card' => '5454210002382567', 'expires' => '1511', 'name' => 'NINE PRBB','cvv2' => '442'],
            '10' => ['card' => '5454210002225055', 'expires' => '1511', 'name' => 'TEN PRBB','cvv2' => '859'],
        ],
    
        'visa' => [
            '1' => ['card' => '4058444115434444', 'expires' => '1511', 'name' => 'ONE PRBBVISA','cvv2' => '396', '3DSecure' => '12345678'],
            '2' => ['card' => '4058444162615002', 'expires' => '1511', 'name' => 'TWO PRBBVISA','cvv2' => '132', '3DSecure' => '12345678'],
            '3' => ['card' => '4058444109795560', 'expires' => '1511', 'name' => 'THREE PRBBVISA','cvv2' => '423'],
            '4' => ['card' => '4058444156976121', 'expires' => '1511', 'name' => 'FOUR PRBBVISA','cvv2' => '414'],
            '5' => ['card' => '4058444104156685', 'expires' => '1511', 'name' => 'FIVE PRBBVISA','cvv2' => '448'],
            '6' => ['card' => '4058444151337246', 'expires' => '1511', 'name' => 'SIX PRBBVISA','cvv2' => '994'],
            '7' => ['card' => '4058444198517800', 'expires' => '1511', 'name' => 'SEVEN PRBBVISA','cvv2' => '548'],
            '8' => ['card' => '4058444145698364', 'expires' => '1511', 'name' => 'EIGHT PRBBVISA','cvv2' => '846'],
            '9' => ['card' => '4058444192878927', 'expires' => '1511', 'name' => 'NINE PRBBVISA','cvv2' => '782'],
            '10' => ['card' => '4058444168253881', 'expires' => '1511', 'name' => 'TEN PRBBVISA','cvv2' => '176'],
        ]
    ],

    'errors' => [
        'rc' => [
            '0' => 'Successfully completed успешно',
            '1' => 'Refer to card issuer позвонить эмитенту',
            '4' => 'PICK UP изъять',
            '5' => 'Do not Honour не обслуживать',
            '6' => 'Error ошибка',
            '7' => 'Pick-up card, special condition изъять, особые условия',
            '12' => 'Invalid transaction неправильная транзакция',
            '13' => 'Invalid amount неправильная сумма',
            '14' => 'No such card нет такой карты',
            '15' => 'No such issuer нет такого эмитента',
            '30' => 'Format error ошибка формата данных',
            '33' => 'Pick-up, expired card изъять, просроченная карта',
            '34' => 'Suspect Fraud подозрение в мошенничестве',
            '35' => 'Pick-up, card acceptor contact acquirer изъять, позвонить в банк эквайер',
            '36' => 'Pick up, card restricted изъять, ограничения по карте',
            '37' => 'Pick up, call acquirer security изъять, позвонить в банк эквайер',
            '38' => 'Pick up, Allowable PIN tries exceeded изъять, превышение попыток ввода ПИНа',
            '41' => 'Pick up, lost card изъять, утерянная карта',
            '43' => 'Pick up, stolen card изъять, украденная карта',
            '51' => 'Not sufficient funds недостаточно средств',
            '54' => 'Expired card / target срок действия истек',
            '57' => 'Transaction not permitted to cardholder операция не разрешена держателю карты',
            '58' => 'Transaction not permitted to terminal операция не разрешена терминалу',
            '59' => 'Suspected fraud подозрение в мошенничестве',
            '61' => 'Exceeds withdrawal amount limit превышение разрешенной суммы',
            '62' => 'Restricted card ограниченная карта',
            '65' => 'Exceeds withdrawal frequency limit превышение кол-ва операций',
            '82' => 'Time-out at issuer system / Bad CVV (VISA) время проведения операции истекло',
            '83' => 'Transaction failed операция завершилась с ошибкой',
            '91' => 'Issuer or switch is inoperative эмитент недоступен',
            '94' => 'Duplicate Transmission дублирование операции',
            '95' => 'Reconcile error / Auth Not found авторизация не найдена',
            '96' => 'System Malfunction ошибка системы',
            '-2' => 'Bad CGI Request Неправильный запрос',
            '-3' => 'No or Bar response received Ответ не получен или получен неправильный ответ',
            '-8' => 'Error in card number field Ошибка в поле номера карты',
            '-9' => 'Error in card expiration field Ошибка в поле срока действия карты',
            '-11' => 'Error in currency field Ошибка в поле валюты',
            '-12' => 'Error in merchant terminal field Ошибка в поле номера терминала',
            '-15' => 'Invalid Retrieval Reference Number Неправильный RRN или INT_REF',
            '-17' => 'Access denied В доступе отказано',
            '-18' => 'Error in CVC2 field description field Ошибка в поле описания CVC2',
            '-19' => 'Authentication failed Аутентификация неудачна',
            '-20' => 'Expired transaction Время обработки операции истекло',
        ],

        'action' => [
            '0' => 'Transaction successful completed Успешно завершено',
            '1' => 'Duplicate transaction detected Задвоенная операция',
            '2' => 'Transaction declined Операция отклонена',
            '3' => 'Transaction processing fault Ошибка процессировании операции'
        ]
    ]
];