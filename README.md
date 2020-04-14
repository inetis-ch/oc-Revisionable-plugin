# Revisionable
> This plugin add `RevisionableController` a Controller Behavior for display revisions history on the backend

## Setup for a model
1. Ensure your model have the [Revisionable Trait](https://octobercms.com/docs/database/traits#revisionable)
2. Add behavior `\Inetis\Revisionable\Behaviors\RevisionableController::class` to your Controller  
   This will make available the list of model changes at this URL `vendor/plugin/model/history/$id`
3. Add "Show history" button on Preview (`preview.htm`) file of your controller
    ```html
    <a href="<?= Backend::url('vendor/plugin/model/history/' . $formModel->id) ?>"
       class="btn btn-default oc-icon-history">
        <?= e(trans('inetis.revisionable::lang.btn_show_history')) ?>
    </a>
    ``` 
 
 ### Showing who made the change 
 If your revisions also store the user id ([see documentation](https://octobercms.com/docs/database/traits#revisionable)) you can register the relation 
 in your `Plugin.php` file for that the name of the user be displayed in history table.
 
```php
use October\Rain\Database\Models\Revision;

class Plugin extends PluginBase
{
    // [ ... ]

    public function boot()
    {
        // [ ... ]

        Revision::extend(function (Revision $revision) {

            // If you use Backend user
            $revision->belongsTo['user'] = [\Backend\Models\User::class];

            // or if you use Frontend user
            $revision->belongsTo['user'] = [\RainLab\User\Models\User::class];

        });
    }
}
```

### Customize the rendering
You can override the default rendering of the history page (`plugins/inetis/revisionable/behaviors/revisionablecontroller/partials/_container.htm`) 
by creating a `_revisionable_container.htm` file in the views directory of your controller.

### Use custom timezone
By default the Backend user timezone is used but you can override that by creating a file in `/config/inetis/revisionable/config.php` with you desired timezone (see below)
```php
<?php

return [

    /**
     * Timezone used for display modification date 
     * by default use Backend timezone : \Backend\Models\Preference::get('timezone')     
     * but you can override it to a regional timezone e.g.: 'Europe/Zurich'
     */
    'timezone' => 'Europe/Zurich',

];
```
