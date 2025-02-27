<?php

namespace App\Domains\Settings\ManageGenders\Services;

use App\Interfaces\ServiceInterface;
use App\Services\BaseService;

class DestroyGender extends BaseService implements ServiceInterface
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
            'gender_id' => 'required|integer|exists:genders,id',
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
     * Destroy a gender.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $gender = $this->account()->genders()
            ->findOrFail($data['gender_id']);

        $gender->delete();
    }
}
