<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/16
 * Time: 9:51
 */

namespace WenRuns\Laravel\Admin\Form\Field;


use Encore\Admin\Form\Field;

class InputLoan extends Field\Text
{

    protected $view = 'WenAdmin::form.inputloan';


    protected $apiUri = '';

    protected $extraData = '';

    protected $method = 'POST';

    protected $javascriptCallback = '';

    protected $_disableDetail = false;

    public function __construct(string $column = '', array $arguments = [])
    {
        parent::__construct($column, $arguments);
    }

    /**
     * @param $url
     * @param string $extraData
     * @param string $method
     * @return $this
     */
    public function config($url, $extraData = '', $method = 'POST')
    {
        $this->apiUri = $url;
        $this->extraData = $extraData;
        $this->method = $method;
        return $this;
    }

    public function disableDetail($disable = true)
    {
        $this->_disableDetail = $disable;
        return $this;
    }

    /**
     * @param $javascriptFunc
     * @return $this
     */
    public function javascriptCallback($javascriptFunc)
    {
        $this->javascriptCallback = preg_replace("/(\r|\n)+/", '', $javascriptFunc);
        return $this;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->addVariables([
            'api'                => $this->apiUri,
            'extraData'          => json_encode(['data' => $this->extraData]),
            'method'             => $this->method,
            'javascriptCallback' => $this->javascriptCallback,
            'disableDetail'      => $this->_disableDetail ? 1 : 0,
        ]);
        return parent::render();
    }



}
