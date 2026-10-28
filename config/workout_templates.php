<?php

return [

    'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],

    'default_days' => [
        1 => ['Senin'],
        2 => ['Senin', 'Kamis'],
        3 => ['Senin', 'Rabu', 'Jumat'],
        4 => ['Senin', 'Selasa', 'Kamis', 'Jumat'],
        5 => ['Senin', 'Selasa', 'Rabu', 'Jumat', 'Sabtu'],
        6 => ['Senin', 'Selasa', 'Rabu', 'Jumat', 'Sabtu', 'Minggu'],
        7 => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
    ],

    'cardio' => [
        'fokus' => 'Kardio zona 2',
        'exercises' => [
            ['nama' => 'Jalan cepat, sepeda santai, atau elliptical', 'set' => 1, 'rep' => '30–40 menit', 'rpe' => '4–5'],
            ['nama' => 'Pemanasan 5 menit + pendinginan 5 menit', 'set' => 1, 'rep' => '10 menit', 'rpe' => '3–4'],
        ],
    ],

    'rest' => [
        'fokus' => 'Istirahat',
        'exercises' => [],
    ],

    'splits' => [

        'full_body' => [
            'label' => 'Full Body',
            'days' => [
                'full_a' => [
                    'fokus' => 'Full Body A',
                    'otot' => ['kaki', 'dada', 'punggung', 'bahu', 'core'],
                    'exercises' => [
                        ['nama' => 'Goblet squat', 'set' => 3, 'rep' => '8–12', 'rpe' => '6'],
                        ['nama' => 'Bench press atau push-up', 'set' => 3, 'rep' => '8–12', 'rpe' => '6'],
                        ['nama' => 'Seated row', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Romanian deadlift', 'set' => 2, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Plank', 'set' => 2, 'rep' => '30–45 detik', 'rpe' => '5'],
                    ],
                ],
                'full_b' => [
                    'fokus' => 'Full Body B',
                    'otot' => ['kaki', 'punggung', 'dada', 'bahu', 'core'],
                    'exercises' => [
                        ['nama' => 'Leg press', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Lat pulldown', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Shoulder press', 'set' => 3, 'rep' => '8–12', 'rpe' => '6'],
                        ['nama' => 'Hip thrust', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Dead bug', 'set' => 2, 'rep' => '10 per sisi', 'rpe' => '5'],
                    ],
                ],
            ],
        ],

        'upper_lower' => [
            'label' => 'Upper/Lower',
            'days' => [
                'upper_a' => [
                    'fokus' => 'Upper A',
                    'otot' => ['dada', 'punggung', 'bahu', 'lengan'],
                    'exercises' => [
                        ['nama' => 'Bench press', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Barbell row', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Shoulder press', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Lat pulldown', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Triceps pushdown', 'set' => 2, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Biceps curl', 'set' => 2, 'rep' => '12–15', 'rpe' => '6'],
                    ],
                ],
                'lower_a' => [
                    'fokus' => 'Lower A',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Squat', 'set' => 3, 'rep' => '6–10', 'rpe' => '7'],
                        ['nama' => 'Romanian deadlift', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Leg press', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Leg curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Calf raise', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Plank', 'set' => 2, 'rep' => '45 detik', 'rpe' => '5'],
                    ],
                ],
                'upper_b' => [
                    'fokus' => 'Upper B',
                    'otot' => ['dada', 'punggung', 'bahu', 'lengan'],
                    'exercises' => [
                        ['nama' => 'Incline dumbbell press', 'set' => 3, 'rep' => '8–12', 'rpe' => '7'],
                        ['nama' => 'Pull-up atau assisted pull-up', 'set' => 3, 'rep' => '6–10', 'rpe' => '7'],
                        ['nama' => 'Lateral raise', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Cable row', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Face pull', 'set' => 2, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Hammer curl', 'set' => 2, 'rep' => '12–15', 'rpe' => '6'],
                    ],
                ],
                'lower_b' => [
                    'fokus' => 'Lower B',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Deadlift', 'set' => 3, 'rep' => '5–8', 'rpe' => '7'],
                        ['nama' => 'Bulgarian split squat', 'set' => 3, 'rep' => '8–10 per sisi', 'rpe' => '7'],
                        ['nama' => 'Hip thrust', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Leg extension', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Calf raise', 'set' => 3, 'rep' => '15–20', 'rpe' => '6'],
                        ['nama' => 'Hanging knee raise', 'set' => 2, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
            ],
        ],

        'push_pull_legs' => [
            'label' => 'Push–Pull–Legs',
            'days' => [
                'push_a' => [
                    'fokus' => 'Push A',
                    'otot' => ['dada', 'bahu', 'triceps'],
                    'exercises' => [
                        ['nama' => 'Bench press', 'set' => 4, 'rep' => '6–8', 'rpe' => '7'],
                        ['nama' => 'Incline dumbbell press', 'set' => 3, 'rep' => '8–12', 'rpe' => '7'],
                        ['nama' => 'Overhead press', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Lateral raise', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Triceps pushdown', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'pull_a' => [
                    'fokus' => 'Pull A',
                    'otot' => ['punggung', 'biceps', 'bahu belakang'],
                    'exercises' => [
                        ['nama' => 'Deadlift', 'set' => 3, 'rep' => '5–8', 'rpe' => '7'],
                        ['nama' => 'Pull-up', 'set' => 3, 'rep' => '6–10', 'rpe' => '7'],
                        ['nama' => 'Barbell row', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Face pull', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Biceps curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'legs_a' => [
                    'fokus' => 'Legs A',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Squat', 'set' => 4, 'rep' => '6–8', 'rpe' => '7'],
                        ['nama' => 'Romanian deadlift', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Leg press', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Leg curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Calf raise', 'set' => 4, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Plank', 'set' => 3, 'rep' => '45 detik', 'rpe' => '5'],
                    ],
                ],
                'push_b' => [
                    'fokus' => 'Push B',
                    'otot' => ['bahu', 'dada', 'triceps'],
                    'exercises' => [
                        ['nama' => 'Overhead press', 'set' => 4, 'rep' => '6–8', 'rpe' => '7'],
                        ['nama' => 'Incline barbell press', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Cable fly', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Lateral raise', 'set' => 3, 'rep' => '15–20', 'rpe' => '6'],
                        ['nama' => 'Skull crusher', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'pull_b' => [
                    'fokus' => 'Pull B',
                    'otot' => ['punggung', 'biceps', 'bahu belakang'],
                    'exercises' => [
                        ['nama' => 'Pull-up', 'set' => 4, 'rep' => '6–8', 'rpe' => '7'],
                        ['nama' => 'T-bar row', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Seated cable row', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Rear delt fly', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Hammer curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'legs_b' => [
                    'fokus' => 'Legs B',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Front squat', 'set' => 4, 'rep' => '6–8', 'rpe' => '7'],
                        ['nama' => 'Hip thrust', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Walking lunge', 'set' => 3, 'rep' => '10–12 per sisi', 'rpe' => '7'],
                        ['nama' => 'Leg extension', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Seated calf raise', 'set' => 4, 'rep' => '15–20', 'rpe' => '6'],
                        ['nama' => 'Hanging knee raise', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
            ],
        ],

        'ppl_upper_lower' => [
            'label' => 'PPL + Upper/Lower',
            'days' => [
                'push_a' => [
                    'fokus' => 'Push A',
                    'otot' => ['dada', 'bahu', 'triceps'],
                    'exercises' => [
                        ['nama' => 'Bench press', 'set' => 4, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Incline dumbbell press', 'set' => 3, 'rep' => '8–12', 'rpe' => '7'],
                        ['nama' => 'Overhead press', 'set' => 3, 'rep' => '6–10', 'rpe' => '7'],
                        ['nama' => 'Lateral raise', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Triceps pushdown', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'pull_a' => [
                    'fokus' => 'Pull A',
                    'otot' => ['punggung', 'biceps', 'bahu belakang'],
                    'exercises' => [
                        ['nama' => 'Deadlift', 'set' => 3, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Pull-up', 'set' => 3, 'rep' => '6–10', 'rpe' => '7'],
                        ['nama' => 'Barbell row', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Face pull', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Biceps curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'legs_a' => [
                    'fokus' => 'Legs A',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Squat', 'set' => 4, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Romanian deadlift', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Leg press', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Leg curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Calf raise', 'set' => 4, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Plank', 'set' => 3, 'rep' => '45 detik', 'rpe' => '5'],
                    ],
                ],
                'upper_a' => [
                    'fokus' => 'Upper A',
                    'otot' => ['dada', 'punggung', 'bahu', 'lengan'],
                    'exercises' => [
                        ['nama' => 'Incline barbell press', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Seated cable row', 'set' => 3, 'rep' => '8–12', 'rpe' => '7'],
                        ['nama' => 'Lateral raise', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Cable fly', 'set' => 2, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Hammer curl', 'set' => 2, 'rep' => '12–15', 'rpe' => '6'],
                    ],
                ],
                'lower_a' => [
                    'fokus' => 'Lower A',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Front squat', 'set' => 3, 'rep' => '6–8', 'rpe' => '7'],
                        ['nama' => 'Hip thrust', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Walking lunge', 'set' => 3, 'rep' => '10–12 per sisi', 'rpe' => '7'],
                        ['nama' => 'Leg extension', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Hanging knee raise', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
            ],
        ],

        'ppl_twice' => [
            'label' => 'PPL dua kali',
            'days' => [
                'push_a' => [
                    'fokus' => 'Push A',
                    'otot' => ['dada', 'bahu', 'triceps'],
                    'exercises' => [
                        ['nama' => 'Bench press', 'set' => 4, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Incline dumbbell press', 'set' => 3, 'rep' => '8–12', 'rpe' => '7'],
                        ['nama' => 'Overhead press', 'set' => 3, 'rep' => '6–10', 'rpe' => '7'],
                        ['nama' => 'Lateral raise', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Triceps pushdown', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'pull_a' => [
                    'fokus' => 'Pull A',
                    'otot' => ['punggung', 'biceps', 'bahu belakang'],
                    'exercises' => [
                        ['nama' => 'Deadlift', 'set' => 3, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Pull-up', 'set' => 3, 'rep' => '6–10', 'rpe' => '7'],
                        ['nama' => 'Barbell row', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Face pull', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Biceps curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'legs_a' => [
                    'fokus' => 'Legs A',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Squat', 'set' => 4, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Romanian deadlift', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Leg press', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Leg curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Calf raise', 'set' => 4, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Plank', 'set' => 3, 'rep' => '45 detik', 'rpe' => '5'],
                    ],
                ],
                'push_b' => [
                    'fokus' => 'Push B',
                    'otot' => ['bahu', 'dada', 'triceps'],
                    'exercises' => [
                        ['nama' => 'Overhead press', 'set' => 4, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Incline barbell press', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Cable fly', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Lateral raise', 'set' => 3, 'rep' => '15–20', 'rpe' => '6'],
                        ['nama' => 'Skull crusher', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'pull_b' => [
                    'fokus' => 'Pull B',
                    'otot' => ['punggung', 'biceps', 'bahu belakang'],
                    'exercises' => [
                        ['nama' => 'Pull-up', 'set' => 4, 'rep' => '6–8', 'rpe' => '7'],
                        ['nama' => 'T-bar row', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Seated cable row', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Rear delt fly', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Hammer curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'legs_b' => [
                    'fokus' => 'Legs B',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Front squat', 'set' => 4, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Hip thrust', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Walking lunge', 'set' => 3, 'rep' => '10–12 per sisi', 'rpe' => '7'],
                        ['nama' => 'Leg extension', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Seated calf raise', 'set' => 4, 'rep' => '15–20', 'rpe' => '6'],
                        ['nama' => 'Hanging knee raise', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
            ],
        ],

        'upper_lower_ppl' => [
            'label' => 'Upper/Lower + PPL',
            'days' => [
                'upper_a' => [
                    'fokus' => 'Upper A',
                    'otot' => ['dada', 'punggung', 'bahu', 'lengan'],
                    'exercises' => [
                        ['nama' => 'Bench press', 'set' => 4, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Barbell row', 'set' => 4, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Shoulder press', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Lat pulldown', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Triceps pushdown', 'set' => 2, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Biceps curl', 'set' => 2, 'rep' => '12–15', 'rpe' => '6'],
                    ],
                ],
                'lower_a' => [
                    'fokus' => 'Lower A',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Squat', 'set' => 4, 'rep' => '5–8', 'rpe' => '7–8'],
                        ['nama' => 'Romanian deadlift', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Leg press', 'set' => 3, 'rep' => '10–12', 'rpe' => '7'],
                        ['nama' => 'Leg curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                        ['nama' => 'Calf raise', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Plank', 'set' => 2, 'rep' => '45 detik', 'rpe' => '5'],
                    ],
                ],
                'push_a' => [
                    'fokus' => 'Push A',
                    'otot' => ['dada', 'bahu', 'triceps'],
                    'exercises' => [
                        ['nama' => 'Incline dumbbell press', 'set' => 3, 'rep' => '8–12', 'rpe' => '7'],
                        ['nama' => 'Overhead press', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Cable fly', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Lateral raise', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Triceps pushdown', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'pull_a' => [
                    'fokus' => 'Pull A',
                    'otot' => ['punggung', 'biceps', 'bahu belakang'],
                    'exercises' => [
                        ['nama' => 'Pull-up', 'set' => 3, 'rep' => '6–10', 'rpe' => '7'],
                        ['nama' => 'Seated cable row', 'set' => 3, 'rep' => '8–12', 'rpe' => '7'],
                        ['nama' => 'Face pull', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Rear delt fly', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Biceps curl', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
                'legs_a' => [
                    'fokus' => 'Legs A',
                    'otot' => ['kaki', 'glutes', 'core'],
                    'exercises' => [
                        ['nama' => 'Front squat', 'set' => 3, 'rep' => '6–8', 'rpe' => '7'],
                        ['nama' => 'Hip thrust', 'set' => 3, 'rep' => '8–10', 'rpe' => '7'],
                        ['nama' => 'Walking lunge', 'set' => 3, 'rep' => '10–12 per sisi', 'rpe' => '7'],
                        ['nama' => 'Leg extension', 'set' => 3, 'rep' => '12–15', 'rpe' => '6'],
                        ['nama' => 'Hanging knee raise', 'set' => 3, 'rep' => '10–12', 'rpe' => '6'],
                    ],
                ],
            ],
        ],

    ],

];
