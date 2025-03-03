<?php

namespace ccxt;

// PLEASE DO NOT EDIT THIS FILE, IT IS GENERATED AND WILL BE OVERWRITTEN:
// https://github.com/ccxt/ccxt/blob/master/CONTRIBUTING.md#how-to-contribute-code

use Exception as Exception; // a common import

class bitmart extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'bitmart',
            'name' => 'BitMart',
            'countries' => array ( 'US', 'CN', 'HK', 'KR' ),
            'rateLimit' => 1000,
            'version' => 'v2',
            'has' => array (
                'CORS' => true,
                'fetchMarkets' => true,
                'fetchTicker' => true,
                'fetchTickers' => true,
                'fetchTime' => true,
                'fetchCurrencies' => true,
                'fetchOrderBook' => true,
                'fetchTrades' => true,
                'fetchMyTrades' => true,
                'fetchOHLCV' => true,
                'fetchBalance' => true,
                'createOrder' => true,
                'cancelOrder' => true,
                'cancelAllOrders' => true,
                'fetchOrders' => false,
                'fetchOrderTrades' => true,
                'fetchOpenOrders' => true,
                'fetchClosedOrders' => true,
                'fetchCanceledOrders' => true,
                'fetchOrder' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/61835713-a2662f80-ae85-11e9-9d00-6442919701fd.jpg',
                'api' => 'https://openapi.bitmart.com',
                'www' => 'https://www.bitmart.com/',
                'doc' => 'https://github.com/bitmartexchange/bitmart-official-api-docs',
                'referral' => 'http://www.bitmart.com/?r=rQCFLh',
            ),
            'requiredCredentials' => array (
                'apiKey' => true,
                'secret' => true,
                'uid' => true,
            ),
            'api' => array (
                'token' => array (
                    'post' => array (
                        'authentication',
                    ),
                ),
                'public' => array (
                    'get' => array (
                        'currencies',
                        'ping',
                        'steps',
                        'symbols',
                        'symbols_details',
                        'symbols/{symbol}/kline',
                        'symbols/{symbol}/orders',
                        'symbols/{symbol}/trades',
                        'ticker',
                        'time',
                    ),
                ),
                'private' => array (
                    'get' => array (
                        'orders',
                        'orders/{id}',
                        'trades',
                        'wallet',
                    ),
                    'post' => array (
                        'orders',
                    ),
                    'delete' => array (
                        'orders',
                        'orders/{id}',
                    ),
                ),
            ),
            'timeframes' => array (
                '1m' => 1,
                '3m' => 3,
                '5m' => 5,
                '15m' => 15,
                '30m' => 30,
                '45m' => 45,
                '1h' => 60,
                '2h' => 120,
                '3h' => 180,
                '4h' => 240,
                '1d' => 1440,
                '1w' => 10080,
                '1M' => 43200,
            ),
            'fees' => array (
                'trading' => array (
                    'tierBased' => true,
                    'percentage' => true,
                    'taker' => 0.002,
                    'maker' => 0.001,
                    'tiers' => array (
                        'taker' => [
                            [0, 0.20 / 100],
                            [10, 0.18 / 100],
                            [50, 0.16 / 100],
                            [250, 0.14 / 100],
                            [1000, 0.12 / 100],
                            [5000, 0.10 / 100],
                            [25000, 0.08 / 100],
                            [50000, 0.06 / 100],
                        ],
                        'maker' => [
                            [0, 0.1 / 100],
                            [10, 0.09 / 100],
                            [50, 0.08 / 100],
                            [250, 0.07 / 100],
                            [1000, 0.06 / 100],
                            [5000, 0.05 / 100],
                            [25000, 0.04 / 100],
                            [50000, 0.03 / 100],
                        ],
                    ),
                ),
            ),
            'exceptions' => array (
                'exact' => array (
                    'Place order error' => '\\ccxt\\InvalidOrder', // array("message":"Place order error")
                    'Not found' => '\\ccxt\\OrderNotFound', // array("message":"Not found")
                    'Visit too often, please try again later' => '\\ccxt\\DDoSProtection', // array("code":-30,"msg":"Visit too often, please try again later","subMsg":"","data":array())
                    'Unknown symbol' => '\\ccxt\\BadSymbol', // array("message":"Unknown symbol")
                ),
                'broad' => array (
                    'Maximum price is' => '\\ccxt\\InvalidOrder', // array("message":"Maximum price is 0.112695")
                    // array("message":"Required Integer parameter 'status' is not present")
                    // array("message":"Required String parameter 'symbol' is not present")
                    // array("message":"Required Integer parameter 'offset' is not present")
                    // array("message":"Required Integer parameter 'limit' is not present")
                    // array("message":"Required Long parameter 'from' is not present")
                    // array("message":"Required Long parameter 'to' is not present")
                    'is not present' => '\\ccxt\\BadRequest',
                ),
            ),
        ));
    }

    public function fetch_time ($params = array ()) {
        $response = $this->publicGetTime ($params);
        //
        //     {
        //         "server_time" => 1527777538000
        //     }
        //
        return $this->safe_integer($response, 'server_time');
    }

    public function sign_in ($params = array ()) {
        $message = $this->apiKey . ':' . $this->secret . ':' . $this->uid;
        $data = array (
            'grant_type' => 'client_credentials',
            'client_id' => $this->apiKey,
            'client_secret' => $this->hmac ($this->encode ($message), $this->encode ($this->secret), 'sha256'),
        );
        $response = $this->tokenPostAuthentication (array_merge ($data, $params));
        $accessToken = $this->safe_string($response, 'access_token');
        if (!$accessToken) {
            throw new AuthenticationError($this->id . ' signIn() failed to authenticate. Access token missing from $response->');
        }
        $expiresIn = $this->safe_integer($response, 'expires_in');
        $this->options['expires'] = $this->sum ($this->nonce (), $expiresIn * 1000);
        $this->options['accessToken'] = $accessToken;
        return $response;
    }

    public function fetch_markets ($params = array ()) {
        $markets = $this->publicGetSymbolsDetails ($params);
        //
        //     array (
        //         {
        //             "$id":"1SG_BTC",
        //             "base_currency":"1SG",
        //             "quote_currency":"BTC",
        //             "quote_increment":"0.1",
        //             "base_min_size":"0.1000000000",
        //             "base_max_size":"10000000.0000000000",
        //             "price_min_precision":4,
        //             "price_max_precision":6,
        //             "expiration":"NA"
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count ($markets); $i++) {
            $market = $markets[$i];
            $id = $this->safe_string($market, 'id');
            $baseId = $this->safe_string($market, 'base_currency');
            $quoteId = $this->safe_string($market, 'quote_currency');
            $base = $this->safe_currency_code($baseId);
            $quote = $this->safe_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            //
            // https://github.com/bitmartexchange/bitmart-official-api-docs/blob/master/rest/public/symbols_details.md#response-details
            // from the above API doc:
            // quote_increment Minimum order price as well as the price increment
            // price_min_precision Minimum price $precision (digit) used to query price and kline
            // price_max_precision Maximum price $precision (digit) used to query price and kline
            //
            // the docs are wrong => https://github.com/ccxt/ccxt/issues/5612
            //
            $quoteIncrement = $this->safe_string($market, 'quote_increment');
            $amountPrecision = $this->precision_from_string($quoteIncrement);
            $pricePrecision = $this->safe_integer($market, 'price_max_precision');
            $precision = array (
                'amount' => $amountPrecision,
                'price' => $pricePrecision,
            );
            $limits = array (
                'amount' => array (
                    'min' => $this->safe_float($market, 'base_min_size'),
                    'max' => $this->safe_float($market, 'base_max_size'),
                ),
                'price' => array (
                    'min' => null,
                    'max' => null,
                ),
                'cost' => array (
                    'min' => null,
                    'max' => null,
                ),
            );
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'precision' => $precision,
                'limits' => $limits,
                'info' => $market,
            );
        }
        return $result;
    }

    public function parse_ticker ($ticker, $market = null) {
        $timestamp = $this->milliseconds ();
        $marketId = $this->safe_string($ticker, 'symbol_id');
        $symbol = null;
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
                $symbol = $market['symbol'];
            } else if ($marketId !== null) {
                list($baseId, $quoteId) = explode('_', $marketId);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        $last = $this->safe_float($ticker, 'current_price');
        $percentage = $this->safe_float($ticker, 'fluctuation');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'highest_price'),
            'low' => $this->safe_float($ticker, 'lowest_price'),
            'bid' => $this->safe_float($ticker, 'bid_1'),
            'bidVolume' => $this->safe_float($ticker, 'bid_1_amount'),
            'ask' => $this->safe_float($ticker, 'ask_1'),
            'askVolume' => $this->safe_float($ticker, 'ask_1_amount'),
            'vwap' => null,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => $percentage * 100,
            'average' => null,
            'baseVolume' => $this->safe_float($ticker, 'volume'),
            'quoteVolume' => $this->safe_float($ticker, 'base_volume'),
            'info' => $ticker,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $request = array (
            'symbol' => $this->market_id($symbol),
        );
        $response = $this->publicGetTicker (array_merge ($request, $params));
        //
        //     {
        //         "volume":"97487.38",
        //         "ask_1":"0.00148668",
        //         "base_volume":"144.59",
        //         "lowest_price":"0.00144362",
        //         "bid_1":"0.00148017",
        //         "highest_price":"0.00151000",
        //         "ask_1_amount":"92.03",
        //         "current_price":"0.00148230",
        //         "fluctuation":"+0.0227",
        //         "symbol_id":"XRP_ETH",
        //         "url":"https://www.bitmart.com/trade?$symbol=XRP_ETH",
        //         "bid_1_amount":"134.78"
        //     }
        //
        return $this->parse_ticker($response);
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $tickers = $this->publicGetTicker ($params);
        $result = array();
        for ($i = 0; $i < count ($tickers); $i++) {
            $ticker = $this->parse_ticker($tickers[$i]);
            $symbol = $ticker['symbol'];
            $result[$symbol] = $ticker;
        }
        return $result;
    }

    public function fetch_currencies ($params = array ()) {
        $currencies = $this->publicGetCurrencies ($params);
        //
        //     array (
        //         {
        //             "$name":"CNY1",
        //             "withdraw_enabled":false,
        //             "id":"CNY1",
        //             "deposit_enabled":false
        //         }
        //     )
        //
        $result = array();
        for ($i = 0; $i < count ($currencies); $i++) {
            $currency = $currencies[$i];
            $currencyId = $this->safe_string($currency, 'id');
            $code = $this->safe_currency_code($currencyId);
            $name = $this->safe_string($currency, 'name');
            $withdrawEnabled = $this->safe_value($currency, 'withdraw_enabled');
            $depositEnabled = $this->safe_value($currency, 'deposit_enabled');
            $active = $withdrawEnabled && $depositEnabled;
            $result[$code] = array (
                'id' => $currencyId,
                'code' => $code,
                'name' => $name,
                'info' => $currency, // the original payload
                'active' => $active,
                'fee' => null,
                'precision' => null,
                'limits' => array (
                    'amount' => array( 'min' => null, 'max' => null ),
                    'price' => array( 'min' => null, 'max' => null ),
                    'cost' => array( 'min' => null, 'max' => null ),
                    'withdraw' => array( 'min' => null, 'max' => null ),
                ),
            );
        }
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'symbol' => $this->market_id($symbol),
            // 'precision' => 4, // optional price precision / depth level whose range is defined in $symbol details
        );
        $response = $this->publicGetSymbolsSymbolOrders (array_merge ($request, $params));
        return $this->parse_order_book($response, null, 'buys', 'sells', 'price', 'amount');
    }

    public function parse_trade ($trade, $market = null) {
        //
        // fetchTrades (public)
        //
        //     {
        //         "$amount":"2.29275119",
        //         "$price":"0.021858",
        //         "count":"104.8930",
        //         "order_time":1563997286061,
        //         "$type":"sell"
        //     }
        //
        // fetchMyTrades (private)
        //
        //     {
        //         "$symbol" => "BMX_ETH",
        //         "$amount" => "1.0",
        //         "fees" => "0.0005000000",
        //         "trade_id" => 2734956,
        //         "$price" => "0.00013737",
        //         "active" => true,
        //         "entrust_id" => 5576623,
        //         "$timestamp" => 1545292334000
        //     }
        //
        $id = $this->safe_string($trade, 'trade_id');
        $timestamp = $this->safe_integer_2($trade, 'timestamp', 'order_time');
        $type = null;
        $side = $this->safe_string_lower($trade, 'type');
        $price = $this->safe_float($trade, 'price');
        $amount = $this->safe_float($trade, 'amount');
        $cost = null;
        if ($price !== null) {
            if ($amount !== null) {
                $cost = $amount * $price;
            }
        }
        $orderId = $this->safe_integer($trade, 'entrust_id');
        $marketId = $this->safe_string($trade, 'symbol');
        $symbol = null;
        if ($marketId !== null) {
            if (is_array($this->markets_by_id) && array_key_exists($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
                $symbol = $market['symbol'];
            } else {
                list($baseId, $quoteId) = explode('_', $marketId);
                $base = $this->safe_currency_code($baseId);
                $quote = $this->safe_currency_code($quoteId);
                $symbol = $base . '/' . $quote;
            }
        }
        if ($symbol === null) {
            if ($market !== null) {
                $symbol = $market['symbol'];
            }
        }
        $feeCost = $this->safe_float($trade, 'fees');
        $fee = null;
        if ($feeCost !== null) {
            // is it always $quote, always $base, or $base-$quote depending on the $side?
            $feeCurrencyCode = null;
            $fee = array (
                'cost' => $feeCost,
                'currency' => $feeCurrencyCode,
            );
        }
        return array (
            'info' => $trade,
            'id' => $id,
            'order' => $orderId,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => $cost,
            'takerOrMaker' => null,
            'fee' => $fee,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
        );
        $response = $this->publicGetSymbolsSymbolTrades (array_merge ($request, $params));
        //
        //     array (
        //         {
        //             "amount":"2.29275119",
        //             "price":"0.021858",
        //             "count":"104.8930",
        //             "order_time":1563997286061,
        //             "type":"sell"
        //         }
        //     )
        //
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchMyTrades requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
            // 'offset' => 0, // current page, starts from 0
        );
        if ($limit !== null) {
            $request['limit'] = $limit; // default 500, max 1000
        }
        $response = $this->privateGetTrades (array_merge ($request, $params));
        //
        //     {
        //         "total_trades" => 216,
        //         "total_pages" => 22,
        //         "current_page" => 0,
        //         "$trades" => array (
        //             array (
        //                 "$symbol" => "BMX_ETH",
        //                 "amount" => "1.0",
        //                 "fees" => "0.0005000000",
        //                 "trade_id" => 2734956,
        //                 "price" => "0.00013737",
        //                 "active" => true,
        //                 "entrust_id" => 5576623,
        //                 "timestamp" => 1545292334000
        //             ),
        //         )
        //     }
        //
        $trades = $this->safe_value($response, 'trades', array());
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function fetch_order_trades ($id, $symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'entrust_id' => $id,
        );
        return $this->fetch_my_trades($symbol, $since, $limit, array_merge ($request, $params));
    }

    public function parse_ohlcv ($ohlcv, $market = null, $timeframe = '1m', $since = null, $limit = null) {
        return array (
            $this->safe_integer($ohlcv, 'timestamp'),
            $this->safe_float($ohlcv, 'open_price'),
            $this->safe_float($ohlcv, 'highest_price'),
            $this->safe_float($ohlcv, 'lowest_price'),
            $this->safe_float($ohlcv, 'current_price'),
            $this->safe_float($ohlcv, 'volume'),
        );
    }

    public function fetch_ohlcv ($symbol, $timeframe = '1m', $since = null, $limit = null, $params = array ()) {
        if ($since === null && $limit === null) {
            throw new ArgumentsRequired($this->id . ' fetchOHLCV requires either a `$since` argument or a `$limit` argument (or both)');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $periodInSeconds = $this->parse_timeframe($timeframe);
        $duration = $periodInSeconds * $limit * 1000;
        $to = $this->milliseconds ();
        if ($since === null) {
            $since = $to - $duration;
        } else {
            $to = $this->sum ($since, $duration);
        }
        $request = array (
            'symbol' => $market['id'],
            'from' => $since, // start time of k-line data (in milliseconds, required)
            'to' => $to, // end time of k-line data (in milliseconds, required)
            'step' => $this->timeframes[$timeframe], // steps of sampling (in minutes, default 1 minute, optional)
        );
        $response = $this->publicGetSymbolsSymbolKline (array_merge ($request, $params));
        //
        //     array (
        //         {
        //             "timestamp":1525761000000,
        //             "open_price":"0.010130",
        //             "highest_price":"0.010130",
        //             "lowest_price":"0.010130",
        //             "current_price":"0.010130",
        //             "volume":"0.000000"
        //         }
        //     )
        //
        return $this->parse_ohlcvs($response, $market, $timeframe, $since, $limit);
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $balances = $this->privateGetWallet ($params);
        //
        //     array (
        //         {
        //             "name":"Bitcoin",
        //             "available":"0.0000000000",
        //             "frozen":"0.0000000000",
        //             "id":"BTC"
        //         }
        //     )
        //
        $result = array( 'info' => $balances );
        for ($i = 0; $i < count ($balances); $i++) {
            $balance = $balances[$i];
            $currencyId = $this->safe_string($balance, 'id');
            $code = $this->safe_currency_code($currencyId);
            $account = $this->account ();
            $account['free'] = $this->safe_float($balance, 'available');
            $account['used'] = $this->safe_float($balance, 'frozen');
            $result[$code] = $account;
        }
        return $this->parse_balance($result);
    }

    public function parse_order ($order, $market = null) {
        //
        // createOrder
        //
        //     {
        //         "entrust_id":1223181
        //     }
        //
        // cancelOrder
        //
        //     array()
        //
        // fetchOrder, fetchOrdersByStatus, fetchOpenOrders, fetchClosedOrders
        //
        //     {
        //         "entrust_id":1223181,
        //         "$symbol":"BMX_ETH",
        //         "$timestamp":1528060666000,
        //         "$side":"buy",
        //         "$price":"1.000000",
        //         "fees":"0.1",
        //         "original_amount":"1",
        //         "executed_amount":"1",
        //         "remaining_amount":"0",
        //         "$status":3
        //     }
        //
        $id = $this->safe_string($order, 'entrust_id');
        $timestamp = $this->milliseconds ();
        $status = $this->parse_order_status($this->safe_string($order, 'status'));
        $symbol = $this->find_symbol($this->safe_string($order, 'symbol'), $market);
        $price = $this->safe_float($order, 'price');
        $amount = $this->safe_float($order, 'original_amount');
        $cost = null;
        $filled = $this->safe_float($order, 'executed_amount');
        $remaining = $this->safe_float($order, 'remaining_amount');
        if ($amount !== null) {
            if ($remaining !== null) {
                if ($filled === null) {
                    $filled = $amount - $remaining;
                }
            }
            if ($filled !== null) {
                if ($remaining === null) {
                    $remaining = $amount - $filled;
                }
                if ($cost === null) {
                    if ($price !== null) {
                        $cost = $price * $filled;
                    }
                }
            }
        }
        $side = $this->safe_string($order, 'side');
        $type = null;
        return array (
            'id' => $id,
            'info' => $order,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'amount' => $amount,
            'cost' => null,
            'average' => null,
            'filled' => $filled,
            'remaining' => $remaining,
            'status' => $status,
            'fee' => null,
            'trades' => null,
        );
    }

    public function parse_order_status ($status) {
        $statuses = array (
            '0' => 'all',
            '1' => 'open',
            '2' => 'open',
            '3' => 'closed',
            '4' => 'canceled',
            '5' => 'open',
            '6' => 'closed',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type !== 'limit') {
            throw new ExchangeError($this->id . ' allows limit orders only');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
            'side' => strtolower($side),
            'amount' => floatval ($this->amount_to_precision($symbol, $amount)),
            'price' => floatval ($this->price_to_precision($symbol, $price)),
        );
        $response = $this->privatePostOrders (array_merge ($request, $params));
        //
        //     {
        //         "entrust_id":1223181
        //     }
        //
        return $this->parse_order($response, $market);
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $intId = intval ($id);
        $request = array (
            'id' => $intId,
            'entrust_id' => $intId,
        );
        $response = $this->privateDeleteOrdersId (array_merge ($request, $params));
        //
        // responds with an empty object array()
        //
        return $this->parse_order($response);
    }

    public function cancel_all_orders ($symbol = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' cancelAllOrders requires a $symbol argument');
        }
        $side = $this->safe_string($params, 'side');
        if ($side === null) {
            throw new ArgumentsRequired($this->id . " cancelAllOrders requires a `$side` parameter ('buy' or 'sell')");
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'symbol' => $market['id'],
            'side' => $side, // 'buy' or 'sell'
        );
        $response = $this->privateDeleteOrders (array_merge ($request, $params));
        //
        // responds with an empty object array()
        //
        return $response;
    }

    public function fetch_orders_by_status ($status, $symbol = null, $since = null, $limit = null, $params = array ()) {
        if ($symbol === null) {
            throw new ArgumentsRequired($this->id . ' fetchOrdersByStatus requires a $symbol argument');
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        if ($limit === null) {
            $limit = 500; // default 500, max 1000
        }
        $request = array (
            'symbol' => $market['id'],
            'status' => $status,
            'offset' => 0, // current page, starts from 0
            'limit' => $limit,
        );
        $response = $this->privateGetOrders (array_merge ($request, $params));
        //
        //     {
        //         "$orders":array (
        //             {
        //                 "entrust_id":1223181,
        //                 "$symbol":"BMX_ETH",
        //                 "timestamp":1528060666000,
        //                 "side":"buy",
        //                 "price":"1.000000",
        //                 "fees":"0.1",
        //                 "original_amount":"1",
        //                 "executed_amount":"1",
        //                 "remaining_amount":"0",
        //                 "$status":3
        //             }
        //         ),
        //         "total_pages":1,
        //         "total_orders":1,
        //         "current_page":0,
        //     }
        //
        $orders = $this->safe_value($response, 'orders', array());
        return $this->parse_orders($orders, $market, $since, $limit);
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        // 5 = pending & partially filled orders
        return $this->fetch_orders_by_status (5, $symbol, $since, $limit, $params);
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        // 3 = closed orders
        return $this->fetch_orders_by_status (3, $symbol, $since, $limit, $params);
    }

    public function fetch_canceled_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        // 4 = canceled orders
        return $this->fetch_orders_by_status (4, $symbol, $since, $limit, $params);
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'id' => $id,
        );
        $response = $this->privateGetOrdersId (array_merge ($request, $params));
        //
        //     {
        //         "entrust_id":1223181,
        //         "$symbol":"BMX_ETH",
        //         "timestamp":1528060666000,
        //         "side":"buy",
        //         "price":"1.000000",
        //         "fees":"0.1",
        //         "original_amount":"1",
        //         "executed_amount":"1",
        //         "remaining_amount":"0",
        //         "status":3
        //     }
        //
        return $this->parse_order($response);
    }

    public function nonce () {
        return $this->milliseconds ();
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        if ($api === 'public') {
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
        } else if ($api === 'token') {
            $this->check_required_credentials();
            $body = $this->urlencode ($query);
            $headers = array (
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
        } else {
            $nonce = $this->nonce ();
            $this->check_required_credentials();
            $token = $this->safe_string($this->options, 'accessToken');
            if ($token === null) {
                throw new AuthenticationError($this->id . ' ' . $path . ' endpoint requires an accessToken option or a prior call to signIn() method');
            }
            $expires = $this->safe_integer($this->options, 'expires');
            if ($expires !== null) {
                if ($nonce >= $expires) {
                    throw new AuthenticationError($this->id . ' accessToken expired, supply a new accessToken or call the signIn() method');
                }
            }
            if ($query) {
                $url .= '?' . $this->urlencode ($query);
            }
            $headers = array (
                'Content-Type' => 'application/json',
                'X-BM-TIMESTAMP' => (string) $nonce,
                'X-BM-AUTHORIZATION' => 'Bearer ' . $token,
            );
            if ($method !== 'GET') {
                $query = $this->keysort ($query);
                $body = $this->json ($query);
                $message = $this->urlencode ($query);
                $headers['X-BM-SIGNATURE'] = $this->hmac ($this->encode ($message), $this->encode ($this->secret), 'sha256');
            }
        }
        return array( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body, $response, $requestHeaders, $requestBody) {
        if ($response === null) {
            return;
        }
        //
        //     array("$message":"Maximum price is 0.112695")
        //     array("$message":"Required Integer parameter 'status' is not present")
        //     array("$message":"Required String parameter 'symbol' is not present")
        //     array("$message":"Required Integer parameter 'offset' is not present")
        //     array("$message":"Required Integer parameter 'limit' is not present")
        //     array("$message":"Required Long parameter 'from' is not present")
        //     array("$message":"Required Long parameter 'to' is not present")
        //     array("$message":"Invalid status. status=6 not support any more, please use 3:deal_success orders, 4:cancelled orders")
        //     array("$message":"Not found")
        //     array("$message":"Place order error")
        //
        $feedback = $this->id . ' ' . $body;
        $message = $this->safe_string_2($response, 'message', 'msg');
        if ($message !== null) {
            $exact = $this->exceptions['exact'];
            if (is_array($exact) && array_key_exists($message, $exact)) {
                throw new $exact[$message]($feedback);
            }
            $broad = $this->exceptions['broad'];
            $broadKey = $this->findBroadlyMatchedKey ($broad, $message);
            if ($broadKey !== null) {
                throw new $broad[$broadKey]($feedback);
            }
            throw new ExchangeError($feedback); // unknown $message
        }
    }
}
