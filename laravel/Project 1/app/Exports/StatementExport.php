<?php

namespace App\Exports;

use App\DTOs\IndexDTO;
use App\Exports\Base\BaseExport;
use App\Models\Statement;
use App\Repositories\StatementRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatementExport extends BaseExport
{
    public function __construct(IndexDTO $dataDTO)
    {
        $this->repository = resolve(StatementRepository::class);
        $this->dataDTO = $dataDTO;

        $this->with = [
            'employer' => fn (BelongsTo $qb) => $qb->withTrashed(),
            'client' => fn (BelongsTo $qb) => $qb->withTrashed(),
            'status' => fn (BelongsTo $qb) => $qb->withTrashed(),
            'leadAuthority' => fn (BelongsTo $qb) => $qb->withTrashed(),
            'responsiblePerson' => fn (BelongsTo $qb) => $qb->withTrashed(),
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            // '#',
            'Pracodawca',
            'Imię',
            'Nazwisko',
            'Data zlecenia',
            'Data złożenia',
            'Data odbioru',
            'Ważne od',
            'ważne do',
            'Organ prowadzący',
            'Komentarz',
            'Status',
            'Osoba odpowiedzialna',
        ];
    }

    /**
     * @param Statement $statement
     * @return array
     */
    public function map($statement): array
    {

        return [
            // $statement->id,                                // id
            $statement->employer?->name,                   // Pracodawca
            $statement->client_first_name,                 // Imię
            $statement->client_last_name,                  // Nazwisko
            $statement->ordered_date,                      // Data zlecenia
            $statement->submission_date,                   // Data złożenia
            $statement->receipt_date,                      // Data odbioru
            $statement->valid_from_date,                   // Ważne od
            $statement->valid_until_date,                  // Ważne do
            $statement->leadAuthority?->name,              // Organ prowadzący
            $statement->comment,                           // Komentarz
            $statement->status?->name,                     // Status
            $statement->responsiblePerson?->full_name,     // Osoba odpowiedzialna
        ];
    }
}
