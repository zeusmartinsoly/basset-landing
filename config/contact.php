<?php

return [

    'notification_recipients' => collect(explode(',', (string) env('CONTACT_NOTIFICATION_EMAIL', '')))
        ->map(fn (string $email): string => trim($email))
        ->filter()
        ->values()
        ->all(),

];
