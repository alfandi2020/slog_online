<?php

namespace App\Http\Controllers\Api;

use App\Entities\Receipts\Receipt;
use App\Entities\Receipts\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceiptsController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'receipt_numbers' => 'required|string',
        ]);

        $receiptNumbers = $this->sanitizetoArray($request->get('receipt_numbers'));

        $receipts = Receipt::where(function ($query) use ($receiptNumbers) {
            $query->whereIn('number', $receiptNumbers);
            $query->orWhereIn('reference_no', $receiptNumbers);
        })
        ->get();

        return fractal()->collection($receipts, function ($receipt) {

            $statusCode = $receipt->proof ? $receipt->proof->status_code : $receipt->status_code;
            $receiptStatus = Status::getNameById($statusCode);

            if ($receipt->status_code == 'mn') {
                $destinationName = $receipt->lastProgress->manifest->destinationNetwork->origin->name;
                $receiptStatus .= ' to '.$destinationName;
            }

            if ($receipt->status_code == 'rd') {
                $destinationName = $receipt->lastProgress->manifest->destinationNetwork->origin->name;
                $receiptStatus = 'Received on '.$destinationName;
            }

            return [
                'number'       => $receipt->number,
                'reference_no' => $receipt->reference_no,
                'pcs_count'    => $receipt->pcs_count.' Koli',
                'items_count'  => $receipt->items_count.' Dus',
                'weight'       => $receipt->weight.' Kg',
                'service'      => $receipt->service(),
                'consignor'    => $receipt->consignor['name'],
                'consignee'    => $receipt->consignee['name'],
                'origin'       => $receipt->origin->name,
                'destination'  => $receipt->destination->name,
                'last_update'  => $receipt->updated_at->format('d-m-Y H:i'),
                'status'       => $receiptStatus,
                'recipient'    => $receipt->consignee['recipient'] ?? '',
            ];
        });
    }

    public function show(Receipt $receipt)
    {
        return fractal()->item($receipt, function ($receipt) {

            $progressList = array_filter($receipt->progressList(), function ($progress) {
                return array_key_exists($progress['status_code'], Status::publicList());
            });

            return [
                'number'       => $receipt->number,
                'reference_no' => $receipt->reference_no,
                'service'      => $receipt->service(),
                'pickup_time'  => $receipt->pickup_time->format('d-m-Y H:i'),
                'origin'       => $receipt->origin->name,
                'destination'  => $receipt->destination->name,
                'consignor'    => $receipt->consignor['name'],
                'consignee'    => $receipt->consignee['name'],
                'pcs_count'    => $receipt->pcs_count.' Koli',
                'items_count'  => $receipt->items_count.' Dus',
                'weight'       => $receipt->weight.' Kg',
                'last_update'  => $receipt->updated_at->format('d-m-Y H:i'),
                'status'       => Status::getNameById($receipt->status_code),
                'recipient'    => $receipt->consignee['recipient'] ?? '',
                'is_delivered' => $receipt->isDelivered(),
                'progress'     => $progressList,
            ];
        });
    }

    private function sanitizetoArray($receiptNumbers)
    {
        return explode(',', $receiptNumbers);
    }
}
