<?php

namespace Encore\Admin\Filter;

use Encore\Admin\Admin;
use Illuminate\Support\Arr;

class Between extends AbstractFilter
{
    protected $view = null;

    /**
     * Format id.
     *
     * @param string $column
     * @return array|string
     */
    public function formatId($column)
    {
        $id = str_replace('.', '_', $column);

        return ['start' => "{$id}_start", 'end' => "{$id}_end"];
    }

    /**
     * Format two field names of this filter.
     *
     * @param string $column
     * @return array
     */
    protected function formatName($column)
    {
        $columns = explode('.', $column);

        if(count($columns) == 1) {
            $name = $columns[0];
        } else {

            $name = array_shift($columns);

            foreach($columns as $column) {
                $name .= "[$column]";
            }
        }

        return ['start' => "{$name}[start]", 'end' => "{$name}[end]"];
    }

    public function condition($inputs)
    {
        if(! Arr::has($inputs, $this->column)) {
            return null;
        }

        $this->value = Arr::get($inputs, $this->column);

        $value = array_filter($this->value, function($val) {
            return $val !== '';
        });

        if(empty($value)) return null;

        if(! isset($value['start'])) {
            return $this->buildCondition($this->column, '<=', $value['end']);
        }

        if(! isset($value['end'])) {
            return $this->buildCondition($this->column, '>=', $value['start']);
        }

        $this->query = 'whereBetween';

        return $this->buildCondition($this->column, $this->value);
    }

    public function datetime()
    {
        $this->view = 'admin::filter.betweenDatetime';

        $this->prepareForDatetime();
    }

    protected function prepareForDatetime()
    {
        $css = [
            'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css'
        ];

        $js = [
            'moment/min/moment.min.js',
            'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'
        ];

        $options['format'] = 'YYYY-MM-DD HH:mm:ss';
        $options['locale'] = 'zh-cn';

        $startOptions = json_encode($options);
        $endOptions = json_encode($options + ['useCurrent' =>false]);

        $script = <<<EOT
            $('#{$this->id['start']}').datetimepicker($startOptions);
            $('#{$this->id['end']}').datetimepicker($endOptions);
            $("#{$this->id['start']}").on("dp.change", function (e) {
                $('#{$this->id['end']}').data("DateTimePicker").minDate(e.date);
            });
            $("#{$this->id['end']}").on("dp.change", function (e) {
                $('#{$this->id['start']}').data("DateTimePicker").maxDate(e.date);
            });
EOT;

        $js[] = "moment/locale/{$options['locale']}.js";

        Admin::js($js);
        Admin::css($css);
        Admin::script($script);
    }

    public function render()
    {
        if(isset($this->view)) {
            return view($this->view, $this->variables());
        }

        return parent::render();
    }
}