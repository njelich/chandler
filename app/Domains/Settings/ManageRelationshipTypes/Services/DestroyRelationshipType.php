<?php

namespace App\Domains\Settings\ManageRelationshipTypes\Services;

use App\Interfaces\ServiceInterface;
use App\Services\BaseService;

class DestroyRelationshipType extends BaseService implements ServiceInterface
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
            'relationship_group_type_id' => 'required|integer|exists:relationship_group_types,id',
            'relationship_type_id' => 'required|integer|exists:relationship_types,id',
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
     * Destroy a relationship type.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $group = $this->account()->relationshipGroupTypes()
            ->findOrFail($data['relationship_group_type_id']);

        $type = $group->types()
            ->where('can_be_deleted', true)
            ->findOrFail($data['relationship_type_id']);

        $type->delete();
    }
}
