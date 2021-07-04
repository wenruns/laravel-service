<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 15:46
 */

namespace WenRuns\Service;

class Button
{
    protected $text;

    protected $class = '';

    protected $size = 'sm';

    protected $type = 'default';

    protected $icon = '';

    protected $style = '';

    protected $attributes = [];

    protected $url = 'javascript:void(0)';


    const SIZE_SM = 'sm';
    const SIZE_XS = 'xs';

    const TYPE_DEFAULT = 'default';
    const TYPE_WARNGING = 'warning';
    const TYPE_DANGER = 'danger';
    const TYPE_SUCCESS = 'success';
    const TYPE_PRIMARY = 'primary';
    const TYPE_INFO = 'info';

    /**
     * Button constructor.
     * @param $text
     * @param array $options
     */
    public function __construct($text, $options = [])
    {
        $this->text = $text;
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }


    public function render()
    {

    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if (empty($arguments)) {
            return $this->$name;
        }
        dd($arguments);
        $this->$name = $arguments[0];
        return $this;
    }
}