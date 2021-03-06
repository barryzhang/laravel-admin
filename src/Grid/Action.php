<?php

namespace Encore\Admin\Grid;

use Encore\Admin\Admin;

class Action {

    const SHOW      = 'show';
    const EDIT      = 'edit';
    const DELETE    = 'delete';

    protected $actions = [
        self::SHOW,
        self::EDIT,
        self::DELETE
    ];

    protected $actionViews = [
        self::SHOW      => '<a href="/{path}/{id}"><i class="fa fa-eye"></i></a> ',
        self::EDIT      => '<a href="/{path}/{id}/edit"><i class="fa fa-edit"></i></a> ',
        self::DELETE    => '<a href="javascript:void(0);" data-id="{id}" class="_delete"><i class="fa fa-trash"></i></a> ',
    ];

    protected $path = '';

    public function __construct($actions = 'show|edit|delete')
    {
        $actions = explode('|', $actions);

        $this->actions = array_intersect($actions, $this->actions);

        $this->initScript();
    }

    public function initScript()
    {
        $this->path = app('router')->current()->getPath();

        $script = <<<SCRIPT
            $('._delete').click(function() {
                var id = $(this).data('id');
                if(confirm("确认删除！")) {
                    $.post('/{$this->path}/' + id, {_method:'delete'}, function(data){
                        console.log(data);
                        //location.reload(true);
                    });
                }
            });
SCRIPT;

        Admin::script($script);

    }

    public function render($id)
    {
        $html = '';

        foreach($this->actions as $action) {
            $html .= str_replace(['{path}', '{id}'], [$this->path, $id], $this->actionViews[$action]);
        }

        return $html;
    }

}