<?php

namespace App\ScrapeTypes;

enum ScrapeType: string
{
    case Demo = 'Demo';
    case WalletPayPal = 'WalletPayPal';
}
