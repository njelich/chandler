<?php

namespace App\Domains\Contact\ManagePronouns\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Pronoun;
use App\Services\BaseService;
use Carbon\Carbon;

class SetPronoun extends BaseService implements ServiceInterface
{
    private Pronoun $pronoun;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'vault_id' => 'required|integer|exists:vaults,id',
            'author_id' => 'required|integer|exists:users,id',
            'contact_id' => 'required|integer|exists:contacts,id',
            'pronoun_id' => 'required|integer|exists:pronouns,id',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     *
     * @return array
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'vault_must_belong_to_account',
            'author_must_be_vault_editor',
            'contact_must_belong_to_vault',
        ];
    }

    /**
     * Set a contact's pronoun.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->pronoun = $this->account()->pronouns()
            ->findOrFail($data['pronoun_id']);

        $this->contact->pronoun_id = $this->pronoun->id;
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }
}
