<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 15:48
 */

use WenRuns\Service\Button;


if (!function_exists('buttons')) {
    function buttons(array $buttons, \Closure $clusre, $toString = false)
    {
        $html = $toString ? '' : [];
        foreach ($buttons as $key => $buttonText) {
            $button = new Button($buttonText);
            if (is_callable($clusre)) {
                call_user_func($clusre, $button);
            }
            if ($toString) {
                $html .= $button->render();
            } else {
                $html[$key] = $button;
            }
        }
        return $html;
    }
}