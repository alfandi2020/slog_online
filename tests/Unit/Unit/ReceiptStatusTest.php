<?php

namespace Tests\Unit\Unit;

use Tests\TestCase;
use App\Entities\Receipts\Status;

class ReceiptStatusTest extends TestCase
{
    /** @test */
    public function retrieve_all_receipt_statuses_list()
    {
        $receiptStatus = new Status;

        $this->assertEquals([
            // Delivery
            'de' => trans('receipt_status.de'),
            'mw' => trans('receipt_status.mw'),
            'rw' => trans('receipt_status.rw'),
            'mn' => trans('receipt_status.mn'),
            'ot' => trans('receipt_status.ot'),
            'rd' => trans('receipt_status.rd'),
            'od' => trans('receipt_status.od'),
            'no' => trans('receipt_status.no'),
            'pr' => trans('receipt_status.pr'),
            'pd' => trans('receipt_status.pd'),

            // Prove of Delivery
            'dl' => trans('receipt_status.dl'),
            'bd' => trans('receipt_status.bd'),
            'au' => trans('receipt_status.au'),
            'mr' => trans('receipt_status.mr'),
            'o1' => trans('receipt_status.o1'),
            'o2' => trans('receipt_status.o2'),
            'o3' => trans('receipt_status.o3'),
            'o4' => trans('receipt_status.o4'),
            'o5' => trans('receipt_status.o5'),
            'o6' => trans('receipt_status.o6'),
            'o7' => trans('receipt_status.o7'),
            'o8' => trans('receipt_status.o8'),
            'o9' => trans('receipt_status.o9'),
            'o0' => trans('receipt_status.o0'),

            // After Delivery
            'or' => trans('receipt_status.or'),
            'rt' => trans('receipt_status.rt'),
            'ma' => trans('receipt_status.ma'),
            'ir' => trans('receipt_status.ir'),
            'id' => trans('receipt_status.id'),
        ], $receiptStatus->toArray());
    }

    /** @test */
    public function retrieve_pod_receipt_statuses_list()
    {
        $receiptStatus = new Status;

        $this->assertEquals([
            'dl' => trans('receipt_status.dl'),
            'bd' => trans('receipt_status.bd'),
            'au' => trans('receipt_status.au'),
            'mr' => trans('receipt_status.mr'),
            'o1' => trans('receipt_status.o1'),
            'o2' => trans('receipt_status.o2'),
            'o3' => trans('receipt_status.o3'),
            'o4' => trans('receipt_status.o4'),
            'o5' => trans('receipt_status.o5'),
            'o6' => trans('receipt_status.o6'),
            'o7' => trans('receipt_status.o7'),
            'o8' => trans('receipt_status.o8'),
            'o9' => trans('receipt_status.o9'),
            'o0' => trans('receipt_status.o0'),
        ], $receiptStatus->podDropdown());
    }

    /** @test */
    public function retrieve_receipt_status_codes_by_id()
    {
        $receiptStatus = new Status;
        // Delivery
        $this->assertEquals('de', $receiptStatus->getById('de'));
        $this->assertEquals('mw', $receiptStatus->getById('mw'));
        $this->assertEquals('rw', $receiptStatus->getById('rw'));
        $this->assertEquals('mn', $receiptStatus->getById('mn'));
        $this->assertEquals('ot', $receiptStatus->getById('ot'));
        $this->assertEquals('rd', $receiptStatus->getById('rd'));
        $this->assertEquals('od', $receiptStatus->getById('od'));
        $this->assertEquals('no', $receiptStatus->getById('no'));

        // Prove of Delivery
        $this->assertEquals('dl', $receiptStatus->getById('dl'));
        $this->assertEquals('bd', $receiptStatus->getById('bd'));
        $this->assertEquals('au', $receiptStatus->getById('au'));
        $this->assertEquals('mr', $receiptStatus->getById('mr'));
        $this->assertEquals('o1', $receiptStatus->getById('o1'));
        $this->assertEquals('o2', $receiptStatus->getById('o2'));
        $this->assertEquals('o3', $receiptStatus->getById('o3'));
        $this->assertEquals('o4', $receiptStatus->getById('o4'));
        $this->assertEquals('o5', $receiptStatus->getById('o5'));
        $this->assertEquals('o6', $receiptStatus->getById('o6'));
        $this->assertEquals('o7', $receiptStatus->getById('o7'));
        $this->assertEquals('o8', $receiptStatus->getById('o8'));
        $this->assertEquals('o9', $receiptStatus->getById('o9'));
        $this->assertEquals('o0', $receiptStatus->getById('o0'));

        // After Delivery
        $this->assertEquals('or', $receiptStatus->getById('or'));
        $this->assertEquals('rt', $receiptStatus->getById('rt'));
        $this->assertEquals('ma', $receiptStatus->getById('ma'));
        $this->assertEquals('ir', $receiptStatus->getById('ir'));
        $this->assertEquals('id', $receiptStatus->getById('id'));
    }

    /** @test */
    public function retrieve_receipt_statuses_name_by_id()
    {
        $receiptStatus = new Status;
        // Delivery
        $this->assertEquals(trans('receipt_status.de'), $receiptStatus->getNameById('de'));
        $this->assertEquals(trans('receipt_status.mw'), $receiptStatus->getNameById('mw'));
        $this->assertEquals(trans('receipt_status.rw'), $receiptStatus->getNameById('rw'));
        $this->assertEquals(trans('receipt_status.mn'), $receiptStatus->getNameById('mn'));
        $this->assertEquals(trans('receipt_status.ot'), $receiptStatus->getNameById('ot'));
        $this->assertEquals(trans('receipt_status.rd'), $receiptStatus->getNameById('rd'));
        $this->assertEquals(trans('receipt_status.od'), $receiptStatus->getNameById('od'));
        $this->assertEquals(trans('receipt_status.no'), $receiptStatus->getNameById('no'));

        // Prove of Delivery
        $this->assertEquals(trans('receipt_status.dl'), $receiptStatus->getNameById('dl'));
        $this->assertEquals(trans('receipt_status.bd'), $receiptStatus->getNameById('bd'));
        $this->assertEquals(trans('receipt_status.au'), $receiptStatus->getNameById('au'));
        $this->assertEquals(trans('receipt_status.mr'), $receiptStatus->getNameById('mr'));
        $this->assertEquals(trans('receipt_status.o1'), $receiptStatus->getNameById('o1'));
        $this->assertEquals(trans('receipt_status.o2'), $receiptStatus->getNameById('o2'));
        $this->assertEquals(trans('receipt_status.o3'), $receiptStatus->getNameById('o3'));
        $this->assertEquals(trans('receipt_status.o4'), $receiptStatus->getNameById('o4'));
        $this->assertEquals(trans('receipt_status.o5'), $receiptStatus->getNameById('o5'));
        $this->assertEquals(trans('receipt_status.o6'), $receiptStatus->getNameById('o6'));
        $this->assertEquals(trans('receipt_status.o7'), $receiptStatus->getNameById('o7'));
        $this->assertEquals(trans('receipt_status.o8'), $receiptStatus->getNameById('o8'));
        $this->assertEquals(trans('receipt_status.o9'), $receiptStatus->getNameById('o9'));
        $this->assertEquals(trans('receipt_status.o0'), $receiptStatus->getNameById('o0'));

        // After Delivery
        $this->assertEquals(trans('receipt_status.or'), $receiptStatus->getNameById('or'));
        $this->assertEquals(trans('receipt_status.rt'), $receiptStatus->getNameById('rt'));
        $this->assertEquals(trans('receipt_status.ma'), $receiptStatus->getNameById('ma'));
        $this->assertEquals(trans('receipt_status.ir'), $receiptStatus->getNameById('ir'));
        $this->assertEquals(trans('receipt_status.id'), $receiptStatus->getNameById('id'));
    }

    /** @test */
    public function retrieve_public_receipt_statuses_list()
    {
        $receiptStatus = new Status;

        $this->assertEquals([
            'de' => trans('receipt_status.de'),
            'mw' => trans('receipt_status.mw'),
            'rw' => trans('receipt_status.rw'),
            'mn' => trans('receipt_status.mn'),
            'rd' => trans('receipt_status.rd'),
            'od' => trans('receipt_status.od'),
            'dl' => trans('receipt_status.dl'),
            'bd' => trans('receipt_status.bd'),
            'au' => trans('receipt_status.au'),
            'mr' => trans('receipt_status.mr'),
        ], $receiptStatus->publicList());
    }

    /** @test */
    public function retrieve_delivery_receipt_statuses_list()
    {
        $receiptStatus = new Status;

        $this->assertEquals([
            'de' => trans('receipt_status.de'),
            'mw' => trans('receipt_status.mw'),
            'rw' => trans('receipt_status.rw'),
            'mn' => trans('receipt_status.mn'),
            'rd' => trans('receipt_status.rd'),
            'od' => trans('receipt_status.od'),
        ], $receiptStatus->getList('delivery'));
    }

    /** @test */
    public function retrieve_invoiceable_receipt_statuses_list()
    {
        $receiptStatus = new Status;

        $this->assertEquals([
            'dl' => trans('receipt_status.dl'),
            'bd' => trans('receipt_status.bd'),
            'or' => trans('receipt_status.or'),
            'rt' => trans('receipt_status.rt'),
            'ma' => trans('receipt_status.ma'),
            'ir' => trans('receipt_status.ir'),
        ], $receiptStatus->getList('invoiceable'));
    }
}
