<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/18
 * Time: 10:56
 */

namespace WenRuns\Laravel\Admin\Form\Field;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Encore\Admin\Form\Field;
<<<<<<< HEAD
use WenRuns\Laravel\Laravel;
=======
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808

class MultiCheckbox extends Field
{

    /**
     * Other key for many-to-many relation.
     *
     * @var string
     */
    protected $otherKey;

    /**
     * @var string
     */
    protected $view = 'WenAdmin::form.multicheckbox';

    /**
     * @var string
     */
    protected $request_url = '';

    /**
     * @var array
     */
    protected $checked_values = [];

    /**
     * @var string
     * array: 提交一维数组
     * object: 提交多维数组（包含隶属关系）
     */
    protected $dataFormat = 'array';

    /**
     * @var array
     */
    protected $attach = [];

    /**
     * @var array
     */
    protected static $css = [
        '/vendor/laravel-admin/AdminLTE/plugins/select2/select2.min.css',
<<<<<<< HEAD
=======
        '/vendor/wenruns/laravel-service/form/multiCheckbox.css'
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
    ];

    /**
     * @var array
     */
    protected static $js = [
        '/vendor/laravel-admin/AdminLTE/plugins/select2/select2.full.min.js',
<<<<<<< HEAD
=======
        '/vendor/wenruns/laravel-service/form/multiCheckbox.js',
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
    ];

    /**
     * @var array
     */
    protected $config = [];


    public function __construct($column = '', array $arguments = [])
    {
        $this->unique_key = mt_rand(1000, 9999);
<<<<<<< HEAD
        Laravel::loadCss('form/multiCheckbox.js');
        Laravel::loadJs('form/multiCheckbox.css');
=======
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
        parent::__construct($column, $arguments);
    }


<<<<<<< HEAD
=======

>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
    /**
     * @param $attachData
     * @return $this
     */
    public function attach($attachData)
    {
        $this->attach = $attachData;
        return $this;
    }

    /**
     *
     * @param string $format
     *
     * @return $this
     */
    public function objectFormat($format = 'object')
    {
        $this->dataFormat = $format;
        return $this;
    }

    /**
     * Set options.
     *
     * @param array|callable|string $options
     *
     * @return $this|mixed
     */
    public function options($options = [])
    {
        // remote options
        if (is_string($options)) {
            // reload selected
            if (class_exists($options) && in_array(Model::class, class_parents($options))) {
                return $this->model(...func_get_args());
            }

            return $this->loadRemoteOptions(...func_get_args());
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        if (is_callable($options)) {
            $this->options = $options;
        } else {
            $this->options = (array)$options;
        }

        return $this;
    }

    /**
     * Load options from current selected resource(s).
     *
     * @param string $model
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function model($model, $idField = 'id', $textField = 'name')
    {
        if (!class_exists($model)
            || !in_array(Model::class, class_parents($model))
        ) {
            throw new \InvalidArgumentException("[$model] must be a valid model class");
        }

        $this->options = function ($value) use ($model, $idField, $textField) {
            if (empty($value)) {
                return [];
            }

            $resources = [];

            if (is_array($value)) {
                if (Arr::isAssoc($value)) {
                    $resources[] = Arr::get($value, $idField);
                } else {
                    $resources = array_column($value, $idField);
                }
            } else {
                $resources[] = $value;
            }

            return $model::find($resources)->pluck($textField, $idField)->toArray();
        };

        return $this;
    }

    /**
     * Load options from remote.
     *
     * @param string $url
     * @param array $parameters
     * @param array $options
     *
     * @return $this
     */
    protected function loadRemoteOptions($url, $parameters = [], $options = [])
    {
        $ajaxOptions = [
            'url' => $url . '?' . http_build_query($parameters),
        ];
        $configs = array_merge([
<<<<<<< HEAD
            'allowClear' => true,
            'placeholder' => [
                'id' => '',
=======
            'allowClear'  => true,
            'placeholder' => [
                'id'   => '',
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
                'text' => trans('admin.choose'),
            ],
        ], $this->config);

        $configs = json_encode($configs);
        $configs = substr($configs, 1, strlen($configs) - 2);

        $ajaxOptions = json_encode(array_merge($ajaxOptions, $options));

        $this->script = <<<EOT

$.ajax($ajaxOptions).done(function(data) {

  $("{$this->getElementClassSelector()}").each(function(index, element) {
      $(element).select2({
        data: data,
        $configs
      });
      var value = $(element).data('value') + '';
      if (value) {
        value = value.split(',');
        $(element).select2('val', value);
      }
  });
});

EOT;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function readOnly()
    {
        //移除特定字段名称,增加MultipleSelect的修订
        //没有特定字段名可以使多个readonly的JS代码片段被Admin::script的array_unique精简代码
        $script = <<<'EOT'
$("form select").on("select2:opening", function (e) {
    if($(this).attr('readonly') || $(this).is(':hidden')){
    e.preventDefault();
    }
});
$(document).ready(function(){
    $('select').each(function(){
        if($(this).is('[readonly]')){
            $(this).closest('.form-group').find('span.select2-selection__choice__remove').first().remove();
            $(this).closest('.form-group').find('li.select2-search').first().remove();
            $(this).closest('.form-group').find('span.select2-selection__clear').first().remove();
        }
    });
});
EOT;
        Admin::script($script);

        return parent::readOnly();
    }

    /**
     * Get other key for this many-to-many relation.
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function getOtherKey()
    {
        if ($this->otherKey) {
            return $this->otherKey;
        }

        if (is_callable([$this->form->model(), $this->column]) &&
            ($relation = $this->form->model()->{$this->column}()) instanceof BelongsToMany
        ) {
            /* @var BelongsToMany $relation */
            $fullKey = $relation->getQualifiedRelatedPivotKeyName();
            $fullKeyArray = explode('.', $fullKey);

            return $this->otherKey = end($fullKeyArray);
        }

        throw new \Exception('Column of this field must be a `BelongsToMany` relation.');
    }

    /**
     * {@inheritdoc}
     */
    public function fill($data)
    {
        if ($this->form && $this->form->shouldSnakeAttributes()) {
            $key = Str::snake($this->column);
        } else {
            $key = $this->column;
        }

        $relations = Arr::get($data, $key);

        if (is_string($relations)) {
            $this->value = explode(',', $relations);
        }

        if (!is_array($relations)) {
            return;
        }

        $first = current($relations);

        if (is_null($first)) {
            $this->value = null;

            // MultipleSelect value store as an ont-to-many relationship.
        } elseif (is_array($first)) {
            foreach ($relations as $relation) {
                $this->value[] = Arr::get($relation, "pivot.{$this->getOtherKey()}");
            }

            // MultipleSelect value store as a column.
        } else {
            $this->value = $relations;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginal($data)
    {
        $relations = Arr::get($data, $this->column);

        if (is_string($relations)) {
            $this->original = explode(',', $relations);
        }

        if (!is_array($relations)) {
            return;
        }

        $first = current($relations);

        if (is_null($first)) {
            $this->original = null;

            // MultipleSelect value store as an ont-to-many relationship.
        } elseif (is_array($first)) {
            foreach ($relations as $relation) {
                $this->original[] = Arr::get($relation, "pivot.{$this->getOtherKey()}");
            }
            // MultipleSelect value store as a column.
        } else {
            $this->original = $relations;
        }
    }

    public function prepare($value)
    {
        $value = (array)$value;

        return array_filter($value, 'strlen');
    }

    public function requestUrl($url)
    {
        $this->request_url = $url;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function render()
    {
        $configs = array_merge([
<<<<<<< HEAD
            'allowClear' => true,
            'placeholder' => [
                'id' => '',
=======
            'allowClear'  => true,
            'placeholder' => [
                'id'   => '',
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
                'text' => $this->label,
            ],
        ], $this->config);

        $configs = json_encode($configs);

        if (empty($this->script)) {
            $this->script = "$(\"{$this->getElementClassSelector()}\").select2($configs);";
        }

        if ($this->options instanceof \Closure) {
            if ($this->form) {
                $this->options = $this->options->bindTo($this->form->model());
            }

            $this->options(call_user_func($this->options, $this->value, $this));
        }

        $this->options = array_filter($this->options, 'strlen');

        $this->addVariables([
<<<<<<< HEAD
            'options' => $this->options,
            'unique_key' => $this->unique_key,
            'request_url' => $this->request_url,
            'checked_values' => $this->defaultCheckedValue(),
            'format' => $this->dataFormat,
            'attach' => is_array($this->attach) ? json_encode($this->attach) : $this->attach,
=======
            'options'        => $this->options,
            'unique_key'     => $this->unique_key,
            'request_url'    => $this->request_url,
            'checked_values' => $this->defaultCheckedValue(),
            'format'         => $this->dataFormat,
            'attach'         => is_array($this->attach) ? json_encode($this->attach) : $this->attach,
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
        ]);
        $this->attribute('data-value', implode(',', $this->checked_values));
        return parent::render();
    }


    /**
     * 填充默认值
     * @return false|string
     */
    protected function defaultCheckedValue()
    {
        $data = $this->getData($this->column);
        $values = $this->value();
        if ($this->dataFormat == 'object') {
            $this->getCheckedValue($data, $values);
        } else {
            $this->checked_values = array_merge(
                is_array($values) ? $values : explode(',', $values),
                is_array($data) ? $data : explode(',', $data)
            );
        }
        return json_encode($this->checked_values);
    }

    /**
     *
     * @param $data
     * @param $value
     * @return $this|MultiCheckbox
     */
    protected function getCheckedValue($data, $value)
    {
        if (!empty($data)) {
            $data = $this->mergeValues($data);
        }

        if (!empty($value)) {
            $value = $this->mergeValues($value);
        }

        if (empty($value) && empty($data)) {
            return $this;
        }

        return $this->getCheckedValue($data, $value);
    }

    /**
     * @param $data
     * @return array|mixed
     */
    protected function mergeValues($data)
    {
        if (is_string($data)) {
            try {
                $data = json_decode($data, true);
            } catch (\Exception $e) {
                $data = explode(',', $data);
            }
        }
        if (isset($data['value'])) {
            $this->checked_values = array_merge($this->checked_values, $data['value']);
            $data = $data['sub'] ?? [];
        } else {
            $this->checked_values = array_merge($this->checked_values, $data);
            $data = [];
        }
        return $data;
    }

    /**
     * @param $index
     * @return array
     */
    protected function getData($index)
    {
        $index = explode('.', $index);
        $data = $this->form->model()->toArray();
        if (empty($data)) {
            return [];
        }
        foreach ($index as $dex) {
            $data = $data[$dex] ?? [];
        }
        return $data;
    }

}
