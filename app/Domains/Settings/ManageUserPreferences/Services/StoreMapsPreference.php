<?php

namespace App\Domains\Settings\ManageUserPreferences\Services;

use App\Interfaces\ServiceInterface;
use App\Models\User;
use App\Services\BaseService;

class StoreMapsPreference extends BaseService implements ServiceInterface
{
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
            'maps_site' => 'required|string|max:255',
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
     * Store maps preferences for the given user.
     *
     * @param  array  $data
     * @return User
     */
    public function execute(array $data): User
    {
        $this->data = $data;

        $this->validateRules($data);
        $this->updateUser();

        return $this->author;
    }

    private function updateUser(): void
    {
        $this->author->default_map_site = $this->data['maps_site'];
        $this->author->save();
    }
}
