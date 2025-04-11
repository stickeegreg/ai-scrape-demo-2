<?php

namespace App\ScrapeTypes;

use App\Models\ScrapeRun;
use App\Tools\WalletPayPal\SaveErrorTool;
use App\Tools\WalletPayPal\SavePageTool;

// /**
//  * How a payment option appears on the page.
//  */
// export enum Presence {
//     Absent = '',
//     Present = 'Y',
//     BestPlacement = 'P',
//     Largest = 'L',
//     Default = 'D',
// };

// /**
//  * A single payment provider option.
//  */
// export class PaymentOption {
//     /**
//      * @property {string} name The payment option's name.
//      */
//     name = '';

//     /**
//      * @property {Presence} presence How the option appears on the page.
//      */
//     presence = Presence.Present;

//     /**
//      * @property {boolean} common If the payment provider is common or not.
//      */
//     common = false;
// };








// /**
//  * How a pay later appears on the page.
//  */
// export enum PayLaterPresence {
//     Absent = '',
//     Dynamic = 'dynamic',
//     Static = 'static',
// }

// /**
//  * A single payment provider option.
//  */
// export class PayLaterOption {
//     /**
//      * @property {string} name The payment option's name.
//      */
//     name = '';

//     /**
//      * @property {Presence} presence How the option appears on the page.
//      */
//     presence = PayLaterPresence.Absent;

//     /**
//      * @property {boolean} common If the payment provider is common or not.
//      */
//     common = false;
// };







// export class Screenshot
// {
//     /**
//      * @property {number} x The x position.
//      */
//     protected x: number = 0;

//     /**
//      * @property {number} y The y position.
//      */
//     protected y: number = 0;

//     /**
//      * @property {number} width The width.
//      */
//     protected width: number = 0;

//     /**
//      * @property {number} height The height.
//      */
//     protected height: number = 0;

//     /**
//      * @property {string} dataUrl The data URL.
//      */
//     protected dataUrl: string = '';

//     /**
//      * @property {number} pixelRatio The pixel ratio.
//      */
//     protected pixelRatio: number = 1;






// import type { PaymentOption } from './PaymentOption';
// import type { PayLaterOption } from './PayLaterOption';
// import type { Screenshot } from '@/models/Screenshot';

// export const pageTypes = [
//     'Product Page',
//     'Mini Cart',
//     'Shopping Cart',
//     'Payment / Checkout',
// ] as const;

// export type PageType = typeof pageTypes[number];

// /**
//  * Information about a single page within a scrape.
//  */
// export class Page {
//     /**
//      * @property {PageType} type The page being recorded.
//      */
//     type: PageType;

//     /**
//      * @property {PaymentOption[]} paymentOptions The payment options present on the page.
//      */
//     paymentOptions: PaymentOption[] = [];

//     /**
//      * @property {PayLaterOption[]} payLaterOptions The pay later options present on the page.
//      */
//     payLaterOptions: PayLaterOption[] = [];

//     /**
//      * @property {boolean} hasAddToCartButton Whether the page has an "Add to Cart" button.
//      */
//     addToCartButton = false;

//     /**
//      * @property {boolean} hasCheckoutButton Whether the page has a "Go to Checkout" button
//      */
//     checkoutButton = false;

//     /**
//      * @property {boolean} hasPayLaterButton Whether the page has a "Pay Later" button.
//      */
//     payLaterButton = false;

//     /**
//      * @property {Screenshot[]} screenshots Saved screenshots.
//      */
//     screenshots: Screenshot[] = [];

//     /**
//      * Create a new page record.
//      *
//      * @param {PageType} type The page being recorded.
//      */
//     constructor(type: PageType) {
//         if (pageTypes.indexOf(type) === -1) {
//             throw new Error('Invalid page type: ' + type);
//         }

//         this.type = type;
//     }
// }


// /**
//  * This class holds data collected about a single website.
//  */
// export class Scrape {
//     /**
//      * @property {number} id The ID of the Url record.
//      */
//     urlId = 0;

//     /**
//      * @property {string} canonicalUrl The URL provided by PayPal.
//      */
//     canonicalUrl = ''

//     /**
//      * @property {string} actualUrl The actual URL scraped.
//      */
//     actualUrl = '';

//     /**
//      * @property {ScrapeContext} context Desktop or mobile web.
//      */
//     context: ScrapeContext;

//     /**
//      * @property {string} country The country code of the URL.
//      */
//     country = '';

//     /**
//      * @property {boolean} first_time_signup If the user needs to sign up for an account.
//      */
//     first_time_signup: boolean;

//     /**
//      * @property {Page[]} pages Details about each page in the process.
//      */
//     pages: Page[] = [];

//     /**
//      * @property {string} loginDetails Whether login is required for purchase.
//      */
//     loginDetails = LoginDetails.PleaseChoose;

//     /**
//      * @property {string} error The reason for an unsuccessful scrape.
//      */
//     error = '';

//     /**
//      * @property {string} errorType The reason for an unsuccessful scrape.
//      */
//     errorType = '';

//     /**
//      * @property {boolean} errorFailAllVariants If a failure, should we fail all variants.
//      */
//     errorFailAllVariants = false;

//     /**
//      * @property {Screenshot[]} errorScreenshots Screenshots taken when an error occurred.
//      */
//     errorScreenshots: Screenshot[] = [];

//     /**
//      * @property {string} comments Additional comments.
//      */
//     comments = '';

// };


enum ScrapeContext: string {
    case DesktopWeb = 'Desktop Web';
    case MobileWeb = 'Mobile Web';
}

enum LoginDetails: string {
    case PleaseChoose = 'Please choose';
    case Failed = 'Failed';
    case GuestCheckout = 'Guest Checkout';
    case RequiresLogin = 'Requires Login';
    case RequiresMembership = 'Requires Membership';
}

// TODO these should be dynamically fetched from WalletPayPal
enum ErrorType: string {
    case SITE_SERVER_UNAVAILABLE = 'Site/Server Unavailable';
    case PAGE_NOT_FOUND = '404 Page Not Found';
    case INSECURE_WEBSITE = 'Insecure Website';
    case REQUIRES_IDENTIFICATION = 'Requires Identification';
    case URL_ENRICHMENT = 'URL Enrichment';
    case TECHNICAL_ERROR = 'Technical Error';
    case REDIRECTS_TO_DIFFERENT_SITE = 'Redirects to a different site';
    case QUOTATION_REQUIRED_BEFORE_PAYMENT = 'Quotation Required Before Payment';
    case PAYMENTS_MANAGED_VIA_EXTERNAL_SITE = 'Payments managed via external site';
    case HARD_BLOCK = 'Hard Block';
    case NO_ECOMMERCE_PAYMENTS_ON_SITE = 'No Ecommerce/Payments On Site';
    case BUSINESS_IS_CLOSING_HOLDING_PAGE = 'Business is closing holding page';
    case OTHER = 'Other';
}

class WalletPayPalScrapeType implements ScrapeTypeInterface
{
    /**
     * @property {string} actualUrl The actual URL scraped.
     */
    private string $actualUrl = '';

    /**
     * @property {string} loginDetails Whether login is required for purchase.
     */
    private LoginDetails $loginDetails = LoginDetails::PleaseChoose;

    /**
     * @property {boolean} errorFailAllVariants If a failure, should we fail all variants.
     */
    private bool $errorFailAllVariants = false; // TODO

    /**
     * @property {Screenshot[]} errorScreenshots Screenshots taken when an error occurred.
     */
    private array $errorScreenshots = [];

    /**
     * @property {string} comments Additional comments.
     */
    private string $comments = '';

    private SavePageTool $savePageTool;
    private SaveErrorTool $saveErrorTool;

    /**
     * Constructor for the WalletPayPalScrapeType class.
     *
     * @param string $prompt The prompt to be used for scraping.
     * @param int $urlId The ID of the URL record.
     * @param string $context The context of the scrape (e.g., Desktop Web, Mobile Web).
     * @param string $canonicalUrl The URL provided by PayPal.
     * @param bool $firstTimeSignup Indicates if the user needs to sign up for an account.
     * @param string $country The country code of the URL.
     */
    public function __construct(
        private readonly string $prompt,
        private readonly ScrapeRun $scrapeRun,

        // TODO: these should be on the ScrapeRun object?
        private int $urlId,
        private ScrapeContext $context, // TODO: use to determine if we are scraping mobile or desktop
        private string $canonicalUrl,
        private bool $firstTimeSignup, // TODO: if this is true we must sign up for an account
        private string $country
    ) {
        $this->savePageTool = new SavePageTool();
        $this->saveErrorTool = new SaveErrorTool();
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function getTools(): array
    {
        return [
            $this->savePageTool,
            $this->saveErrorTool,
        ];
    }

    public function save(): void
    {
        // TODO Implement the save logic for WalletPayPal scrape type
        $scrapeData = [
            'urlId' => $this->urlId,
            'actualUrl' => $this->actualUrl,
            'loginDetails' => $this->loginDetails,
            'error' => $this->saveErrorTool->getError(),
            'errorType' => $this->saveErrorTool->getErrorType(),
            'errorFailAllVariants' => $this->errorFailAllVariants,
            'errorScreenshots' => $this->saveErrorTool->getErrorScreenshots(),
            'comments' => $this->comments,
            'pages' => $this->savePageTool->getPages(),
        ];

        dump($scrapeData);

        $scrapeRun = $this->scrapeRun->fresh();

        $data = $scrapeRun->data;
        $data['result'] = $scrapeData;
        $scrapeRun->data = $data;

        $scrapeRun->save();
    }
}
