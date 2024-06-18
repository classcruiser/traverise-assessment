<?php

namespace App\Services\Booking;

use App\Models\Payment;
use App\Models\PaymentStripe;
use Curl;
use Illuminate\Http\Request;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Payment as QBPayment;
use Illuminate\Support\Facades\DB;

class QuickbookService
{
	protected $dataService;
	protected $qb_access_token;
	protected $qb_customers;
	protected $qb_services;

    public function __construct()
    {
    	$this->qb_access_token = DB::table('config')->where('name', 'qb_access_token')->first();
    	$qb_refresh_token = DB::table('config')->where('name', 'qb_refresh_token')->first();

        $this->dataService = DataService::Configure([
        	'auth_mode' => 'oauth2',
        	'ClientID' => env('QB_CLIENT_ID'),
        	'ClientSecret' => env('QB_CLIENT_SECRET'),
        	'accessTokenKey' => $this->qb_access_token->value,
        	'refreshTokenKey' => $qb_refresh_token->value,
        	'QBORealmID' => env('QB_REALM_ID'),
        	'baseUrl' => (env('APP_ENV') == 'local' ? 'Development' : 'Production')
        ]);

        $this->dataService->setLogLocation(storage_path('qblog'));
        $this->dataService->throwExceptionOnError(env('APP_ENV') != 'production');

        if (env('APP_ENV') == 'local') {
        	$this->qb_customers = [
        		'hkd' => 67,
        		'usd' => 69,
        	];
        	$this->qb_services = [
        		'cpd' => 24,
        		'pf' => 25,
        		'cogs' => 26
        	];
        } else {
        	$this->qb_customers = [
        		'other' => 16, // Individual
        		'hkd' => 63, // Individual HKD
        		'usd' => 62, // Individual USD
        	];
        	$this->qb_services = [
        		'cpd' => 53,
        		'pf' => 55,
        		'cogs' => 57
        	];
        }
    }

    public function addInvoice($invoice)
    {
    	$booking = $invoice->booking;
    	$payment = $booking->payment;
    	$pi_id = $invoice->pi_id;
    	$stripe = PaymentStripe::where('intent', $pi_id)->first();

    	$customer = $this->addCustomer($booking->guest->details, $invoice->currency);

    	// 1. the real booking amount
    	$booking_amount = floatVal($stripe->amount);
    	$booking_amount_with_fee = floatVal($stripe->amount_paid);
    	$hkd_exchange_rate = floatVal($invoice->exchange_rate);

    	$booking_amount_hkd = floatVal($booking_amount * $hkd_exchange_rate);
    	$fee = floatVal(($booking_amount_with_fee - $booking_amount) * $hkd_exchange_rate);
    	$stripe_fee = -floatVal($invoice->fee);

    	$currency = $invoice->currency == 'hkd' ? 'HKD' : 'USD';

    	$rate = $invoice->currency == 'hkd' ? 1 : floatVal($this->getQuickbookExchangeRate('USD'));

    	$resource = Invoice::create([
    		"Line" => [
			    [
			    	"LineNum" => 1,
			        "Amount" => $booking_amount_hkd,
			        "DetailType" => "SalesItemLineDetail",
			        "Description" => 'Stay in '. $booking->location->name .' from '. $booking->check_in->format('d.m.Y') .' to '. $booking->check_out->format('d.m.Y'),
			        "SalesItemLineDetail" => [
			           "ItemRef" => [
			           		"value" => $this->qb_services['cpd'],
			           		"name" => "Customer Payable Deferral",
			           	]
			        ]
			    ],
			    [
			    	"LineNum" => 2,
			        "Amount" => $fee,
			        "DetailType" => "SalesItemLineDetail",
			        "Description" => 'Processing fee charged to customer',
			        "SalesItemLineDetail" => [
			           "ItemRef" => [
			           		"value" => $this->qb_services['pf'],
			           		"name" => "Processing fee (customer)",
			           	]
			        ]
			    ],
			    [
			    	"LineNum" => 3,
			        "Amount" => $stripe_fee,
			        "DetailType" => "SalesItemLineDetail",
			        "Description" => 'Actual processing fee',
			        "SalesItemLineDetail" => [
			           "ItemRef" => [
			           		"value" => $this->qb_services['cogs'],
			           		"name" => "Processing fee (paid by customer) - COGS",
			           	]
			        ]
			    ]
			],
			"ExchangeRate" => $rate,
			"CurrencyRef" => [
				"value" => $currency
			],
			"DocNumber" => $booking->ref,
		    "CustomerRef" => [
		    	"value" => $customer->Id
		    ],
		    "BillEmail" => [
		    	"Address" => $booking->guest->details->email
		    ],
    	]);

    	$result = $this->dataService->Add($resource);
    	$err = $this->dataService->getLastError();

    	if ($err) {
    		$invoice->increment('retry');
		    dd("The Response message is: " . $err->getResponseBody());
		}

		$invoice->update([
			'invoice_added' => 1,
			'qb_customer_id' => $customer->Id,
			'qb_invoice_id' => $result->Id,
			'qb_exchange_rate' => $rate
		]);

		return [
			'status' => 'success',
			'message' => 'Invoice added'
		];
    }

    public function getQuickbookExchangeRate($target)
    {
    	$client = new \GuzzleHttp\Client();
    	$base_url = env('QB_BASE_URL');

    	$response = $client->request('GET', $base_url .'/company/'. env('QB_REALM_ID') .'/exchangerate?sourcecurrencycode='. $target, [
    		'headers' => [
    			'Accept' => 'application/json',
	    		'Authorization' => 'Bearer '. $this->qb_access_token->value
    		]
    	]);

    	$res = json_decode($response->getBody(), true);

    	return $res['ExchangeRate']['Rate'];
    }

    protected function addCustomer($guest, $currency)
    {
    	/*
    	$customer = $this->dataService->Query("Select * from Customer Where PrimaryEmailAddr = '". $guest->email ."'");

    	if (!$customer) {
    		// create customer
    		$data = [
    			'BillAddr' => [
    				'Line1' => $guest->address,
    				'City' => $guest->city,
    				'Country' => $guest->country,
    				'PostalCode' => $guest->zip
    			],
    			'Title' => $guest->title,
    			'GivenName' => $guest->fname,
    			'FamilyName' => $guest->lname,
    			'FullyQualifiedName' => $guest->full_name,
    			'CompanyName' => $guest->company,
    			'DisplayName' => $guest->full_name,
    			'PrimaryPhone' => [
    				'FreeFormNumber' => $guest->phone
    			],
    			'PrimaryEmailAddr' => [
    				'Address' => $guest->email
    			]
    		];

    		$customer = $this->dataService->Add(Customer::create($data));
    	}

    	$customer = $this->dataService->Query("Select * from Customer Where PrimaryEmailAddr = '". $guest->email ."'");
    	*/
    	$id = $this->qb_customers[$currency];
    	$customer = $this->dataService->Query("Select * from Customer where Id = '". $id ."'");

    	return $customer[0];
    }

    public function receivePayment($invoice)
    {
    	if (!$invoice->qb_customer_id || !$invoice->qb_invoice_id) {
    		return false;
    	}

    	$pi_id = $invoice->pi_id;
    	$stripe = PaymentStripe::where('intent', $pi_id)->first();

    	$qb_customer_id = $invoice->qb_customer_id;
    	$qb_invoice_id = $invoice->qb_invoice_id;
    	$qb_exchange_rate = $invoice->qb_exchange_rate;

    	$booking_amount = floatVal($stripe->amount);
    	$booking_amount_with_fee = floatVal($stripe->amount_paid);
    	$hkd_exchange_rate = floatVal($invoice->exchange_rate);

    	$booking_amount_hkd = floatVal($booking_amount * $hkd_exchange_rate);
    	$fee = floatVal(($booking_amount_with_fee - $booking_amount) * $hkd_exchange_rate);
    	$stripe_fee = -floatVal($invoice->fee);

    	$currency = $invoice->currency == 'hkd' ? 'HKD' : 'USD';

    	$request = QBPayment::create([
    		'CustomerRef' => [
    			'value' => $qb_customer_id
    		],
    		'TotalAmt' => floatVal($booking_amount_hkd + $fee + $stripe_fee),
    		"ExchangeRate" => $qb_exchange_rate,
			"CurrencyRef" => [
				"value" => $currency
			],
    		'Line' => [
    			[
    				'Amount' => floatVal($booking_amount_hkd + $fee + $stripe_fee),
    				'LinkedTxn' => [
    					[
    						'TxnId' => $qb_invoice_id,
    						'TxnType' => 'Invoice'
    					]
    				]
    			]
    		]
    	]);

    	$response = $this->dataService->Add($request);
    	$err = $this->dataService->getLastError();

    	if ($err) {
		    dd("The Response message is: " . $err->getResponseBody());
		}

		$invoice->update([
			'completed' => 1
		]);

		return;
    }

    public function refreshToken()
    {
    	$OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
    	$refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
    	$this->dataService->updateOAuth2Token($refreshedAccessTokenObj);

    	DB::table('config')->where('name', 'qb_access_token')->update([
    		'value' => $refreshedAccessTokenObj->getAccessToken()
    	]);

    	DB::table('config')->where('name', 'qb_refresh_token')->update([
    		'value' => $refreshedAccessTokenObj->getRefreshToken()
    	]);
    }
}
