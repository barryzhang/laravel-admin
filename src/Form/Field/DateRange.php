<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class DateRange extends Field
{
    protected $format = 'YYYY-MM-DD';

    protected $css = [
        'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css'
    ];

    protected $js = [
        'moment/min/moment.min.js',
        'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'
    ];

    public function __construct($column, $arguments)
    {
        $this->column['start']  = $column;
        $this->column['end']    = $arguments[0];

        $this->label  = $this->formatLabel($arguments);
        $this->id     = $this->formatId($this->column);

        $this->options(['format' => $this->format]);
    }

    public function render()
    {
        $this->options['locale'] = 'zh-cn';

        $startOptions = json_encode($this->options);
        $endOptions = json_encode($this->options + ['useCurrent' =>false]);

        $this->script = <<<EOT
            $('#{$this->id['start']}').datetimepicker($startOptions);
            $('#{$this->id['end']}').datetimepicker($endOptions);
            $("#{$this->id['start']}").on("dp.change", function (e) {
                $('#{$this->id['end']}').data("DateTimePicker").minDate(e.date);
            });
            $("#{$this->id['end']}").on("dp.change", function (e) {
                $('#{$this->id['start']}').data("DateTimePicker").maxDate(e.date);
            });
EOT;

        $this->js[] = "moment/locale/{$this->options['locale']}.js";

        return parent::render();
    }
}