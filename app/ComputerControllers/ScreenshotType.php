<?php

namespace App\ComputerControllers;

enum ScreenshotType: string
{
    case VIEWPORT = 'viewport';
    case PAGE = 'page';
    case SCREEN = 'screen';
}
