<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        // Extracting invoice details
        $seriesFull = (string) $xml->xpath('//cbc:ID')[0]; // Full series and number (e.g., F011-604)
        list($series, $number) = explode('-', $seriesFull); // Separating series and number
        $invoiceTypeCode = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
        $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

        $voucher = new Voucher([
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
            # Set invoice details
            'voucher_series' => $series,
            'voucher_number' => $number,
            'voucher_type' => $invoiceTypeCode,
            'currency' => $currency,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }

    public function regularizeVouchers(): void
    {
        $vouchers = Voucher::whereNull('voucher_series')
            ->orWhereNull('voucher_number')
            ->orWhereNull('voucher_type')
            ->orWhereNull('currency')
            ->get();

        foreach ($vouchers as $voucher) {
            $xml = new SimpleXMLElement($voucher->xml_content);

            $seriesFull = (string) $xml->xpath('//cbc:ID')[0];
            list($series, $number) = explode('-', $seriesFull); 
            $invoiceTypeCode = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
            $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

            $voucher->voucher_series = $series;
            $voucher->voucher_number = $number;
            $voucher->voucher_type = $invoiceTypeCode;
            $voucher->currency = $currency;

            $voucher->save();
        }
    }
}
