<?php

namespace Tests\Unit\Services\Vault\ManageLabels;

use Tests\TestCase;
use App\Models\User;
use App\Models\Label;
use App\Models\Vault;
use App\Models\Account;
use App\Jobs\CreateAuditLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Exceptions\NotEnoughPermissionException;
use App\Services\Vault\ManageLabels\CreateLabel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateLabelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_label(): void
    {
        $ross = $this->createAdministrator();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $this->executeService($ross, $ross->account, $vault);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateLabel)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $this->executeService($ross, $account, $vault);
    }

    /** @test */
    public function it_fails_if_user_is_not_editor(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_VIEW, $vault);
        $this->executeService($ross, $ross->account, $vault);
    }

    /** @test */
    public function it_fails_if_vault_is_not_in_the_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $vault = $this->createVault(Account::factory()->create());
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_VIEW, $vault);
        $this->executeService($ross, $ross->account, $vault);
    }

    private function executeService(User $author, Account $account, Vault $vault): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'vault_id' => $vault->id,
            'name' => 'label name',
            'bg_color' => 'bg-zinc-700',
            'text_color' => 'bg-zinc-700',
        ];

        $label = (new CreateLabel)->execute($request);

        $this->assertDatabaseHas('labels', [
            'id' => $label->id,
            'vault_id' => $vault->id,
            'name' => 'label name',
            'bg_color' => 'bg-zinc-700',
            'text_color' => 'bg-zinc-700',
        ]);

        $this->assertInstanceOf(
            Label::class,
            $label
        );

        Queue::assertPushed(CreateAuditLog::class, function ($job) {
            return $job->auditLog['action_name'] === 'label_created';
        });
    }
}