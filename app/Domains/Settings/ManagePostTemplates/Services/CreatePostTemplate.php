<?php

namespace App\Domains\Settings\ManagePostTemplates\Services;

use App\Interfaces\ServiceInterface;
use App\Models\PostTemplate;
use App\Services\BaseService;

class CreatePostTemplate extends BaseService implements ServiceInterface
{
    private PostTemplate $postTemplate;

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
            'label' => 'required|string|max:255',
            'can_be_deleted' => 'required|boolean',
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
     * Create a post type.
     *
     * @param  array  $data
     * @return PostTemplate
     */
    public function execute(array $data): PostTemplate
    {
        $this->validateRules($data);

        // determine the new position of the template page
        $newPosition = $this->account()->postTemplates()
            ->max('position');
        $newPosition++;

        $this->postTemplate = PostTemplate::create([
            'account_id' => $data['account_id'],
            'label' => $data['label'],
            'position' => $newPosition,
            'can_be_deleted' => $this->valueOrTrue($data, 'can_be_deleted'),
        ]);

        return $this->postTemplate;
    }
}
