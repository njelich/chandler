<?php

namespace App\Domains\Contact\ManageContactInformation\Services;

use App\Interfaces\ServiceInterface;
use App\Models\ContactFeedItem;
use App\Models\ContactInformation;
use App\Models\ContactInformationType;
use App\Services\BaseService;
use Carbon\Carbon;

class CreateContactInformation extends BaseService implements ServiceInterface
{
    private ContactInformation $contactInformation;

    private ContactInformationType $contactInformationType;

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
            'vault_id' => 'required|integer|exists:vaults,id',
            'author_id' => 'required|integer|exists:users,id',
            'contact_id' => 'required|integer|exists:contacts,id',
            'contact_information_type_id' => 'required|integer|exists:contact_information_types,id',
            'data' => 'required|string|max:255',
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
     * Create a contact information.
     *
     * @param  array  $data
     * @return ContactInformation
     */
    public function execute(array $data): ContactInformation
    {
        $this->data = $data;
        $this->validate();
        $this->create();
        $this->updateLastEditedDate();
        $this->createFeedItem();

        return $this->contactInformation;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->contactInformationType = $this->account()->contactInformationTypes()
            ->findOrFail($this->data['contact_information_type_id']);
    }

    private function create(): void
    {
        $this->contactInformation = ContactInformation::create([
            'contact_id' => $this->contact->id,
            'type_id' => $this->contactInformationType->id,
            'data' => $this->data['data'],
        ]);
    }

    private function updateLastEditedDate(): void
    {
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }

    private function createFeedItem(): void
    {
        $feedItem = ContactFeedItem::create([
            'author_id' => $this->author->id,
            'contact_id' => $this->contact->id,
            'action' => ContactFeedItem::ACTION_CONTACT_INFORMATION_CREATED,
            'description' => $this->contactInformation->name,
        ]);
        $this->contactInformation->feedItem()->save($feedItem);
    }
}
