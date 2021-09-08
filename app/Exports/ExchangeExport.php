<?php

namespace App\Exports;

use App\Domain\Convertor\Str;
use App\Models\Exchange;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Class ExchangeExport
 * @package App\Exports
 */
class ExchangeExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    /**
     * @var
     */
    private $collection;

    /**
     * @param $collection
     * @return $this
     */
    public function withCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            __('From Wallet'),
            __('From Currency'),
            __('Sent'),
            __('In USD'),
            __('From Rate'),
            __('To Wallet'),
            __('To Currency'),
            __('Received'),
            __('In USD'),
            __('To Rate'),
            __('Profit'),
            __('Note'),
            __('Date'),
        ];
    }

    /**
     * @return Builder
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @return array
     * @return array
     * @var Exchange $e
     */
    public function map($e): array
    {
        return [
            $e->exchange_id,
            $e->senderWallet->name,
            $e->senderRate->crypto->name,
            Str::trimZeroes($e->sender_amount),
            number_format($e->sender_usd_amount, 2),
            $e->senderRate->rate,
            $e->receiverWallet->name,
            $e->receiverRate->crypto->name,
            Str::trimZeroes($e->receiver_amount),
            number_format($e->receiver_usd_amount, 2),
            $e->receiverRate->rate,
            $e->profit,
            $e->note,
            $e->created_at,
        ];
    }

    /**
     * @return \Closure[]
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            },
        ];
    }
}
