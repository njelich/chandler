<?php

namespace App\Domains\Settings\ManagePostTemplates\Services;

use App\Interfaces\ServiceInterface;
use App\Models\PostTemplate;
use App\Services\BaseService;

class DestroyPostTemplate extends BaseService implements ServiceInterface
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
            'post_template_id' => 'required|integer|exists:post_templates,id',
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
     * Destroy a post type.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->postTemplate = $this->account()->postTemplates()
            ->findOrFail($data['post_template_id']);

        $this->postTemplate->delete();
    }
}
