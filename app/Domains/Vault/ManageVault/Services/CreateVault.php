<?php

namespace App\Domains\Vault\ManageVault\Services;

use App\Domains\Vault\ManageVaultImportantDateTypes\Services\CreateContactImportantDateType;
use App\Interfaces\ServiceInterface;
use App\Models\Contact;
use App\Models\ContactImportantDate;
use App\Models\Vault;
use App\Services\BaseService;

class CreateVault extends BaseService implements ServiceInterface
{
    public Vault $vault;

    private array $data;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'template_id' => 'nullable|integer|exists:templates,id',
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
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
        ];
    }

    /**
     * Create a vault.
     *
     * @param  array  $data
     * @return Vault
     */
    public function execute(array $data): Vault
    {
        $this->validateRules($data);
        $this->data = $data;

        $this->createVault();
        $this->createUserContact();
        $this->populateDefaultContactImportantDateTypes();

        return $this->vault;
    }

    private function createVault(): void
    {
        // the vault default's template should be the first template in the
        // account, if it exists
        $template = $this->account()->templates()->first();

        $this->vault = Vault::create([
            'account_id' => $this->data['account_id'],
            'type' => $this->data['type'],
            'name' => $this->data['name'],
            'description' => $this->valueOrNull($this->data, 'description'),
            'default_template_id' => $template ? $template->id : null,
        ]);
    }

    private function createUserContact(): void
    {
        $contact = Contact::create([
            'vault_id' => $this->vault->id,
            'first_name' => $this->author->first_name,
            'last_name' => $this->author->last_name,
            'can_be_deleted' => false,
            'template_id' => $this->vault->default_template_id,
        ]);

        $this->vault->users()->save($this->author, [
            'permission' => Vault::PERMISSION_MANAGE,
            'contact_id' => $contact->id,
        ]);
    }

    private function populateDefaultContactImportantDateTypes(): void
    {
        (new CreateContactImportantDateType())->execute([
            'account_id' => $this->data['account_id'],
            'author_id' => $this->author->id,
            'vault_id' => $this->vault->id,
            'label' => trans('account.vault_contact_important_date_type_internal_type_birthdate'),
            'internal_type' => ContactImportantDate::TYPE_BIRTHDATE,
            'can_be_deleted' => false,
        ]);

        (new CreateContactImportantDateType())->execute([
            'account_id' => $this->data['account_id'],
            'author_id' => $this->author->id,
            'vault_id' => $this->vault->id,
            'label' => trans('account.vault_contact_important_date_type_internal_type_deceased_date'),
            'internal_type' => ContactImportantDate::TYPE_DECEASED_DATE,
            'can_be_deleted' => false,
        ]);
    }
}
