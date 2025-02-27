<?php

namespace App\Domains\Contact\ManageReminders\Services;

use App\Interfaces\ServiceInterface;
use App\Models\ContactReminder;
use App\Services\BaseService;
use Carbon\Carbon;

class DestroyReminder extends BaseService implements ServiceInterface
{
    private ContactReminder $reminder;

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
            'contact_reminder_id' => 'required|integer|exists:contact_reminders,id',
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
     * Destroy a reminder.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->reminder = $this->contact->reminders()
            ->findOrFail($data['contact_reminder_id']);

        $this->reminder->delete();

        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }
}
