<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 15:46
 */

namespace WenRuns\Service;

/**
 * Class Button
 * @method Button text($text = null)
 * @method Button class($class = null)
 * @method Button size($size = null)
 * @method Button type($type = null)
 * @method Button icon($icon = null)
 * @method Button style($style = null)
 * @method Button attributes($attributes = null)
 * @method Button url($url = null)
 * @package WenRuns\Service
 */
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
    const TYPE_WARNING = 'warning';
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
        $this->$name = $arguments[0] ?? null;
        return $this;
    }
}