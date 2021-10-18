<?php

return [

    /**
     * Timezone used for display modification date
     * by default use Backend user timezone : \Backend\Models\Preference::get('timezone')
     * but you can override it to a regional timezone e.g.: 'Europe/Zurich'
     */
    'timezone' => BackendAuth::getUser() ? \Backend\Models\Preference::get('timezone') : 'UTC',

];
