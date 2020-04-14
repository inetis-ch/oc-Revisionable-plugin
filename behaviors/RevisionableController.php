<?php namespace Inetis\Revisionable\Behaviors;

use Backend\Behaviors\FormController;
use Backend\Classes\ControllerBehavior;
use Config;
use Lang;
use October\Rain\Exception\ApplicationException;

class RevisionableController extends ControllerBehavior
{
    /** @var string */
    public $timezone;

    /** @var array */
    private $fieldsTranslations;

    /**
     * @param $controller
     *
     * @throws \ApplicationException
     */
    public function __construct($controller)
    {
        parent::__construct($controller);

        $this->timezone = Config::get('inetis.revisionable::timezone', 'UTC');
    }

    /**
     * @param int $recordId
     *
     * @return string
     * @throws \SystemException
     * @throws ApplicationException
     */
    public function history($recordId)
    {
        if (!$this->controller->isClassExtendedWith(FormController::class)) {
            throw new ApplicationException('Your Controller should implement Backend\Behaviors\FormController');
        }

        $model = $this->controller->formFindModelObject($recordId);

        if (!method_exists($model, 'getRevisionHistoryName')) {
            throw new ApplicationException('Your model should use Revisionable trait');
        }

        $this->prepareFieldsTranslations();

        $name = $this->getFormController()->getConfig('name') ?: class_basename($this->controller);

        $this->controller->pageTitle = Lang::get('inetis.revisionable::lang.page_title', ['name' => $name]);
        $this->controller->bodyClass = 'slim-container';

        return $this->revisionableMakePartial('container', [
            'model' => $model,
            'modelName' => $name,
            'revisions' => $this->getRevisions($model),
            'timezone' => $this->timezone,
            'translateField' => function ($field) {
                return $this->translateField($field);
            },
            'url' => [
                'list' => $this->controller->actionUrl(''),
                'preview' => $this->controller->actionUrl('preview', $recordId),
                'update' => $this->controller->actionUrl('update', $recordId),
            ],
        ]);
    }

    public function translateField($field)
    {
        if ($value = $this->fieldsTranslations[$field] ?? null) {
            return trans($value);
        }

        return $field;
    }

    /**
     * @param string $partial
     * @param array  $params
     *
     * @return string Partial contents
     * @throws \SystemException
     */
    private function revisionableMakePartial($partial, $params = [])
    {
        $contents = $this->controller->makePartial('revisionable_' . $partial, $params, false);

        if (!$contents) {
            $contents = $this->makePartial($partial, $params);
        }

        return $contents;
    }

    private function prepareFieldsTranslations()
    {
        $formFieldsConfigPath = $this->getFormController()->getConfig('form');

        try {
            $config = $this->getFormController()->makeConfig($formFieldsConfigPath);
        } catch (\Exception $e) {
            return;
        }

        $fields = collect($config->fields ?? [])->merge($config->tabs['fields'] ?? []);

        $this->fieldsTranslations = $fields->map(function ($item) {
            return $item['label'] ?? null;
        });
    }

    /** @return  FormController $formController */
    private function getFormController()
    {
        return $this->controller->asExtension('FormController');
    }

    /**
     * @param $model
     *
     * @return mixed
     */
    private function getRevisions($model)
    {
        return $model->{$model->getRevisionHistoryName()}()
            ->orderByDesc('id')
            ->get()
            ->groupBy(function ($revision) {
                return (string)$revision->created_at;
            });
    }
}
