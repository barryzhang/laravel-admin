<?php

namespace Encore\Admin\Commands;

use Encore\Admin\Facades\Admin;
use Illuminate\Console\Command;

class MenuCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the admin menu.';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $menu = Admin::menu();

        echo json_encode($menu, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), "\r\n";
    }
}