<?php

namespace App\Http\Controllers;

use App\Gateway\Payment;
use App\Models\TransactionLog;
use App\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Mockery\Exception;

class TransactionController extends Controller
{
    public function index( Transaction $transaction ){
        try{
            return response()->ok($transaction->toArray());
        }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }

    public function  checkout( Transaction $transaction ){
        try{
            return response()->ok($transaction
                                    ->checkout()
                                    ->redirect());
        }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }

    public function  finish( Request $request ){

            $MerchantOrderID = $request->input('MerchantOrderID');
            $Status = $request->input('Status');
            $Hash = $request->input('Hash');

            $transaction = Transaction::find( $MerchantOrderID );

            $log = [
                'transaction_id' => $transaction->id,
                'op' => 'callback',
                'arguments' => json_encode($request->toArray()),
            ];

            try{

                if( $transaction->status != Transaction::$process ){
                    throw new Exception('TRANSACTION_ALREADY_PROCESED', Payment::$error_dublicated_request);
                }

                /*
                 * @ If Such transaction not found reverse money
                 *
                 * */

                if( empty( $transaction ) )
                    Payment::autoReversal('TRANSACTION_NOT_FOUND_WHIT_THIS_ID');

                /*
                 * @ Set language from transaction
                 *
                 * */
                App::setLocale( $transaction->lang );

                /*
                 * @ If payment is success but tickets
                 * @ Otherwise delete transaction
                 *
                 * */

                if( $Status == Payment::$success ){

                    $transaction->success();

                    $statuses = [];
                    foreach ( $transaction->tickets as $ticket ){
                        $statuses[] = $ticket->buy();
                    }

                    /*
                     * @ If all tickets canceled return money to user
                     * @ Otherwise return 200 OK
                     *
                     * */

                    $statuses = array_unique( $statuses );

                    if( count( $statuses ) == 1 && $statuses[0] == Ticket::$cancel )
                        Payment::autoReversal( $ticket->reason );

                    $transaction->notify();
                }else{
                    $transaction->cancel();
                }

                $log['text'] = json_encode($transaction->toArray());

                TransactionLog::create($log);

                return response()->ok();
        }catch( Exception $e ){

            $code = 500;

            if( $e->getCode() > 0 ){
                $code = $e->getCode();
            }

            $log['text'] = json_encode([
                'code' => $code,
                'message' => $e->getMessage(),
            ]);

            TransactionLog::create($log);

            return response()->error( $e->getMessage(), $code, [
                'error' => strtolower($e->getMessage())
            ] );
        }
    }

    public function alta( $search ){

        $site = 'http://altaok.ge/';
        $items = 50;
        $page = 1;
        $gateway = $site.'index.php?subcats=Y&status=A&pshort=Y&pfull=Y&pname=Y&pkeywords=Y&search_performed=Y&q='.urlencode($search).'&dispatch=products.search&items_per_page='.$items.'&page='.$page.'result_ids=pagination_contents';

        $c = \file_get_contents($gateway);

        $dom = new \DOMDocument();
        libxml_use_internal_errors(TRUE);
        $dom->loadHTML($c);
        libxml_clear_errors();

        $finder = new \DomXPath($dom);

        $mainbox_title = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' mainbox-title ')]");

        foreach ($mainbox_title as $value ){
            $strong = $value->getElementsByTagName('strong');
            foreach ( $strong as $s ){
                $total = $s->textContent;
            }
        }

        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' cm-required-form ')]");
        $results = [];

        foreach ($nodes as $node){
            $data = [];

            $images = $node->getElementsByTagName('img');

            if( !empty( $images ) ){
                foreach ( $images as $img ){
                    $data['image'] = $site.$img->getAttribute('src');
                }
            }

            $links = $node->getElementsByTagName('a');

            if( !empty( $links ) ){
                foreach ( $links as $link ){
                    foreach ( $link->attributes as $attribute ){

                        if( $attribute->name == 'href' ){
                            $href = $site.$attribute->value;
                        }

                        if(  in_array( $attribute->value, [
                            'product-title'
                        ] ) ){
                            $data['link']['name'] = $link->nodeValue;
                            $data['link']['href'] = $href;
                        }
                    }
                }
            }

            $spans = $node->getElementsByTagName('span');

            if( !empty( $spans ) ){
                foreach ( $spans as $span ){
                    foreach ( $span->attributes as $attribute ){
                        if(  in_array( $attribute->value, [
                            'price-num'
                        ] ) ){
                            $data['spans'][] = $span->nodeValue;
                        }
                    }
                }
            }

            $results[] = $data;
        }

        $i = 1;

        echo '<h3 align="center">მონაცემები altaok.ge</h3>';

        if(!empty($results)){

            echo '<h3 align="center">სულ მოიძებნა '.$total.' პროდუქტი გაჩვენებ პირველ '.$items.' ცალს</h3>';

            foreach ( $results as $result ){
                ob_start();
                ?>

                <table border="0"  align="center" width="100%">
                    <tr style="background: lightgrey;">
                        <td align="center" width="5%"><?=$i?></td>
                        <td width="5%"><img src="<?=$result['image']?>"> </td>
                        <td style="padding: 10px;" width="95%"><a target="_blank" href="<?=$result['link']['href']?>">
                                <?=$result['link']['name']?></a>
                            <p>შესადარებელი ფასი ალტაში: <h4><?=$result['spans'][0]?> <?=$result['spans'][1]?></h4> </p>
                        </td>
                    </tr>
                </table>
                <?php
                echo ob_get_clean();

                $i++;
            }
        }else{
            echo '<p align="center">მოდელი არ მოიძებნა</p>';
        }
    }
}
