<?php

return [
    'lead_time_minutes'   => env('APPT_LEAD_TIME_MINUTES', 120),
    'booking_window_days' => env('APPT_BOOKING_WINDOW_DAYS', 60),
    'slot_buffer_minutes' => env('APPT_SLOT_BUFFER_MINUTES', 0),
];

