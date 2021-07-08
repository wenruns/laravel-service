<?php


namespace WenRuns\Laravel\Admin\Grid\Tools;


use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

class Header extends \Encore\Admin\Grid\Tools\Header
{
    public function render()
    {
        $header = $this->grid->header();
        if (is_array($header)) {
            $content = '';
            foreach ($header as $item) {
                $itemContent = call_user_func($item, $this->queryBuilder());
                if (empty($itemContent)) {
                    continue;
                }
                if ($itemContent instanceof Renderable) {
                    $itemContent = $itemContent->render();
                }

                if ($itemContent instanceof Htmlable) {
                    $itemContent = $itemContent->toHtml();
                }
                $content .= $itemContent;
            }

            if (empty($content)) {
                return '';
            }
        } else {
            $content = call_user_func($header, $this->queryBuilder());
            if (empty($content)) {
                return '';
            }
            if ($content instanceof Renderable) {
                $content = $content->render();
            }

            if ($content instanceof Htmlable) {
                $content = $content->toHtml();
            }
        }
        return <<<HTML
    <div class="box-header with-border clearfix">
        {$content}
    </div>
HTML;
    }
}
