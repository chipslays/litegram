<?php

namespace Litegram\Debug;

class Update
{
    public const MESSAGE = '{
        "update_id": 31348350,
        "message": {
            "message_id": 44414,
            "from": {
                "id": 436432850,
                "is_bot": false,
                "first_name": "чипсы",
                "last_name": "лейс",
                "username": "chipslays",
                "language_code": "ru"
            },
            "chat": {
                "id": 436432850,
                "first_name": "чипсы",
                "last_name": "лейс",
                "username": "chipslays",
                "type": "private"
            },
            "date": 1605891721,
            "text": "Test"
        }
    }';

    public const COMMAND = '{
        "update_id": 31348350,
        "message": {
            "message_id": 44414,
            "from": {
                "id": 436432850,
                "is_bot": false,
                "first_name": "чипсы",
                "last_name": "лейс",
                "username": "chipslays",
                "language_code": "en"
            },
            "chat": {
                "id": 436432850,
                "first_name": "чипсы",
                "last_name": "лейс",
                "username": "chipslays",
                "type": "private"
            },
            "date": 1605891721,
            "text": "/version"
        }
    }';

    public const CALLBACK_QUERY = '{
        "update_id": 163399206,
        "callback_query": {
            "id": "1874464818486990916",
            "from": {
                "id": 436432850,
                "is_bot": false,
                "first_name": "чипсы лейс",
                "username": "chipslays",
                "language_code": "ru"
            },
            "message": {
                "message_id": 1054,
                "from": {
                    "id": 1453712331235,
                    "is_bot": true,
                    "first_name": "Тесты",
                    "username": "TestBot"
                },
                "chat": {
                    "id": 436432850,
                    "first_name": "чипсы лейс",
                    "username": "chipslays",
                    "type": "private"
                },
                "date": 1611138080,
                "edit_date": 1611138145,
                "text": "Список всех доступных тестов (1/2):",
                "entities": [
                    {
                        "offset": 0,
                        "length": 35,
                        "type": "bold"
                    }
                ],
                "reply_markup": {
                    "inline_keyboard": [
                        [
                            {
                                "text": "1111?",
                                "callback_data": "K0ktLokvzsgvj08sTqlKq6oAAA=="
                            }
                        ],
                        [
                            {
                                "text": "2222?",
                                "callback_data": "K0ktLokvzsgvj08sTitOTAMA"
                            }
                        ],
                        [
                            {
                                "text": "3333?",
                                "callback_data": "K0ktLokvzsgvjy9PLUwtBwA="
                            }
                        ],
                        [
                            {
                                "text": "4444?",
                                "callback_data": "K0ktLokvzsgvj6+oKquqACIA"
                            }
                        ],
                        [
                            {
                                "text": "👈 Сюда",
                                "callback_data": "K0ktLonPyQQSBvEGAA=="
                            },
                            {
                                "text": "Туда 👉",
                                "callback_data": "K0ktLonPyQQSBvGGAA=="
                            }
                        ]
                    ]
                }
            },
            "chat_instance": "56656092177350901239",
            "data": "K0ktLokvzsgvj6+oKquqACIA"
        }
    }';
}