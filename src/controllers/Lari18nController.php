<?php

namespace Nicolasbeauvais\Lari18n;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

/**
 * Class Lari18nController
 * @package Nicolasbeauvais\Lari18n
 */
class Lari18nController extends Controller
{

    /**
     * @var Lari18n
     */
    private $lari18n;

    public function __construct()
    {
        $this->lari18n = Lari18n::getInstance();
    }

    public function getToolbar()
    {
        $data = $this->lari18n->getToolbarData();

        return View::make('lari18n::toolbar', $data);
    }

}

