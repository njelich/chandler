<?php

namespace App\Domains\Contact\ManageLifeContactEvents\Services;

use App\Interfaces\ServiceInterface;
use App\Models\ContactLifeEvent;
use App\Services\BaseService;
use Carbon\Carbon;

class DestroyContactLifeEvent extends BaseService implements ServiceInterface
{
    private ContactLifeEvent $contactLifeEvent;

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
            'contact_life_event_id' => 'required|integer|exists:contact_life_events,id',
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
            'contact_must_belong_to_vault',
            'author_must_be_vault_editor',
        ];
    }

    /**
     * Destroy a contact activity.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->data = $data;
        $this->validate();

        $this->contactLifeEvent->delete();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }

    private function validate(): void
    {
        $this->validateRules($this->data);

        $this->contactLifeEvent = $this->contact->contactLifeEvents()
            ->findOrFail($this->data['contact_life_event_id']);
    }
}
