<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 15:05
 */

namespace WenRuns\Service;

use Illuminate\Support\Arr;

/**
 * Class Button
 * @package WenRuns\Service
 */
class ButtonService
{
    /**
     * @var array
     */
    protected $buttons;

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
     * @param array $buttons
     */
    public function __construct(array $buttons)
    {
        $this->buttons = $buttons;
    }

    protected function make($button)
    {
        if (Arr::get($button, 'hide', false)) {
            return '';
        }
        $type = Arr::get($button, 'type', self::TYPE_DEFAULT);
        $size = Arr::get($button, 'size', self::SIZE_SM);
        $classs = Arr::get($button, 'class', '');
        $text = Arr::get($button, 'text', '');
        $url = Arr::get($button, 'url', 'javascript:void(0)');
        $icon = $this->icon(Arr::get($button, 'icon'));
        $style = $this->style(Arr::get($button, 'style'));
        $attributes = $this->attribute(Arr::get($button, 'attributes'));
        return <<<HTML
<a href="{$url}" class="btn btn-{$type} btn-{$size} {$classs}" title="{$text}" $style="margin:2px;{$style}" {$attributes}>{$icon}{$text}</a>
HTML;
    }

    protected function style($styles)
    {
        if (is_array($styles)) {
            dd(json_encode($styles));
        }
        return $styles;
    }

    protected function attribute($attributes)
    {

    }

    protected function icon($icon)
    {
        if ($icon) {
            if (is_array($icon)) {
                $icon = implode(' ', $icon);
            }
            return <<<HTML
<i class="fa {$icon}"></i>&nbsp;&nbsp;&nbsp;&nbsp;
HTML;
        }
        return '';
    }

    protected function makeButtons($buttons)
    {
        if (isset($buttons[0])) {
            $html = '';
            foreach ($buttons as $button) {
                $html .= $this->make($button);
            }
            return $html;
        }
        return false;
    }

    /**
     * @return string
     */
    public function render()
    {
        if ($html = $this->makeButtons($this->buttons)) {
            return $html;
        }
        return $this->make($this->buttons);
    }


    public static function create(array $buttons)
    {
        return (new static($buttons))->render();
    }

}