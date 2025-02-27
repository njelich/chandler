<?php

namespace App\Domains\Settings\ManageActivityTypes\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Activity;
use App\Services\BaseService;

class UpdateActivity extends BaseService implements ServiceInterface
{
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
            'activity_type_id' => 'required|integer|exists:activity_types,id',
            'activity_id' => 'required|integer|exists:activities,id',
            'label' => 'required|string|max:255',
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
            'author_must_be_account_administrator',
        ];
    }

    /**
     * Update an activity.
     *
     * @param  array  $data
     * @return Activity
     */
    public function execute(array $data): Activity
    {
        $this->validateRules($data);

        $activityType = $this->account()->activityTypes()
            ->findOrFail($data['activity_type_id']);

        $activity = $activityType->activities()
            ->findOrFail($data['activity_id']);

        $activity->label = $data['label'];
        $activity->save();

        return $activity;
    }
}
