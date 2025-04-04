<?php

namespace App\ScrapeTypes;

use App\DataRepository;
use App\Tools\SaveTextTool;


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


// export type ScrapeContext = 'Desktop Web' | 'Mobile Web';



// export enum LoginDetails {
//     PleaseChoose = 'Please choose',
//     Failed = 'Failed',
//     GuestCheckout = 'Guest Checkout',
//     RequiresLogin = 'Requires Login',
//     RequiresMembership = 'Requires Membership',
// };

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

class WalletPayPalScrapeType implements ScrapeTypeInterface
{
    private readonly DataRepository $dataRepository;

    public function __construct(
        private readonly string $prompt
    ) {
        $this->dataRepository = new DataRepository();
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function getTools(): array
    {
        return [
            new SaveTextTool($this->dataRepository),
        ];
    }

    public function save(): void
    {
        // TODO Implement the save logic for WalletPayPal scrape type
        dump($this->dataRepository);
    }
}
