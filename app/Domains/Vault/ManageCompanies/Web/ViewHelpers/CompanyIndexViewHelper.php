<?php

namespace App\Domains\Vault\ManageCompanies\Web\ViewHelpers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Vault;

class CompanyIndexViewHelper
{
    public static function data(Vault $vault): array
    {
        $collection = $vault->companies()->orderBy('name', 'asc')
            ->with('contacts')
            ->get()
            ->map(function ($company) use ($vault) {
                return self::dto($vault, $company);
            });

        return [
            'companies' => $collection,
        ];
    }

    public static function dto(Vault $vault, Company $company): array
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'contacts' => $company->contacts()
                ->get()
                ->map(fn (Contact $contact) => [
                    'id' => $contact->id,
                    'avatar' => $contact->avatar,
                    'url' => [
                        'show' => route('contact.show', [
                            'vault' => $contact->vault_id,
                            'contact' => $contact->id,
                        ]),
                    ],
                ]),
            'url' => [
                'show' => route('vault.companies.show', [
                    'vault' => $vault->id,
                    'company' => $company->id,
                ]),
            ],
        ];
    }
}
