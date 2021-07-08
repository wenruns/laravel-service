<?php


namespace WenRuns\Laravel\Admin\Grid\Tools;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Selector extends \Encore\Admin\Grid\Tools\Selector
{
    protected $column;

    protected function addSelector($column, $label, $options = [], $query = null, $type = 'many')
    {
        if (is_array($label)) {
            if ($options instanceof \Closure) {
                $query = $options;
            }

            $options = $label;
            $label = __(Str::title($column));
        }

        $this->column = $column;
        $this->selectors[$column] = compact('label', 'options', 'type', 'query');

        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function addQuery($params = [])
    {
        list($label, $options, $type, $query) = array_values($this->selectors[$this->column]);
        $this->selectors[$this->column] = compact('label', 'options', 'type', 'query', 'params');
        return $this;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function render()
    {
        return view('laravel-service::grid.selector', [
            'selectors' => $this->selectors,
            'selected' => static::parseSelected(),
        ]);
    }

    /**
     * @param string $column
     * @param null $value
     * @param false $add
     * @param array $selector
     * @return string
     */
    public static function url($column, $value = null, $add = false, $selector = [])
    {
        $query = request()->query();
        $selected = static::parseSelected();

        $params = $selector['params'] ?? [];

        $options = Arr::get($selected, $column, []);

        if (is_null($value)) {
            Arr::forget($query, "_selector.{$column}");

            return request()->fullUrlWithQuery($query + $params);
        }

        if (in_array($value, $options)) {
            array_delete($options, $value);
        } else {
            if ($add) {
                $options = [];
            }

            array_push($options, $value);
        }

        if (!empty($options)) {
            Arr::set($query, "_selector.{$column}", implode(',', $options));
        } else {
            Arr::forget($query, "_selector.{$column}");
        }

        return request()->fullUrlWithQuery($query + $params);
    }
}
