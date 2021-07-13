<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 11:19
 */

namespace WenRuns\Laravel\Admin;


use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Database\Eloquent\Model;

class FormService
{
    /**
     * @var Form|null
     */
    protected $form = null;

    /**
     * @var null
     */
    protected $id = null;

    /**
     * @var array
     */
    protected $tools = [
        'closure' => null,
        'options' => [
            'list' => true,
            'view' => false,
            'delete' => false,
        ],
    ];

    /**
     * @var array
     */
    protected $footer = [
        'closure' => null,
        'options' => [
            'create' => false,
            'edit' => false,
            'view' => false,
            'submit' => true,
            'reset' => true
        ],
    ];

    /**
     * FormServer constructor.
     * @param $modelClass
     */
    public function __construct($modelClass)
    {
        if ($modelClass instanceof Model) {
            $model = $modelClass;
        } else {
            $model = new $modelClass();
        }
        $this->form = new Form($model);
    }

    public function ignore($columns = [])
    {
        $this->form->ignore($columns);
        return $this;
    }


    /**
     * @param \Closure $closure
     * @return $this
     */
    public function content(\Closure $closure)
    {
        $closure->call($this, $this->form);
        return $this;
    }

    /**
     * @param \Closure|null $closure
     * @param array $options
     * @return $this
     */
    public function tools(\Closure $closure = null, $options = ['list' => true, 'view' => false, 'delete' => false,])
    {
        $this->tools = [
            'closure' => $closure,
            'options' => $options,
        ];
        return $this;
    }

    /**
     * @param \Closure|null $closure
     * @param array $options
     * @return $this
     */
    public function footer(\Closure $closure = null, $options = ['create' => false, 'edit' => false, 'view' => false, 'submit' => true, 'reset' => true])
    {
        $this->footer = [
            'closure' => $closure,
            'options' => $options,
        ];
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function edit($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return $this
     */
    protected function checkInit()
    {
        $toolsConfig = $this->tools;
        $footerConfig = $this->footer;
        $this->form->tools(function (Form\Tools $tools) use ($toolsConfig) {
            $closure = $toolsConfig['closure'] ?? null;
            $options = $toolsConfig['options'] ?? [];
            $list = $options['list'] ?? true;
            $view = $options['view'] ?? false;
            $delete = $options['delete'] ?? false;
            $list || $tools->disableList();
            $view || $tools->disableView();
            $delete || $tools->disableDelete();
            if (is_callable($closure)) {
                $closure->call($this, $tools);
            }
        });
        $this->form->footer(function (Form\Footer $footer) use ($footerConfig) {
            $closure = $footerConfig['closure'] ?? null;
            $options = $footerConfig['options'] ?? [];
            $view = $options['view'] ?? false;
            $create = $options['create'] ?? false;
            $edit = $options['edit'] ?? false;
            $submit = $options['submit'] ?? true;
            $reset = $options['reset'] ?? true;
            $view || $footer->disableViewCheck();
            $create || $footer->disableCreatingCheck();
            $edit || $footer->disableEditingCheck();
            $submit || $footer->disableSubmit();
            $reset || $footer->disableReset();
            if (is_callable($closure)) {
                $closure->call($this, $footer);
            }
        });
        return $this;
    }

    public function title($text)
    {
        $this->form->setTitle($text);
        return $this;
    }

    /**
     * @param $actionUrl
     * @return $this
     */
    public function actionUrl($actionUrl)
    {
        $this->form->setAction($actionUrl);
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function saved(\Closure $closure)
    {
        $this->form->saved($closure);
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function saving(\Closure $closure)
    {
        $this->form->saving($closure);
        return $this;
    }

    /**
     * @param string|\Closure $script
     * @return $this
     */
    public function script($script)
    {
        if (is_callable($script)) {
            $script = $script->call($this);
        }
        Admin::script($script);
        return $this;
    }

    /**
     * @return Form|mixed|null
     */
    public function render()
    {
        $this->checkInit();
        if ($this->id) {
            $this->form->edit($this->id);
        }
        return $this->form;
    }


    /**
     * @param $modeClass
     * @return static
     */
    public static function instance($modeClass)
    {
        return new static($modeClass);
    }
}