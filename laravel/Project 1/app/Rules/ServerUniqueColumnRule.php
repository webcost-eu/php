<?php

namespace App\Rules;

use App\Models\Base\AppAuth;
use App\Models\Base\AuthModel;
use App\Models\Enums\AppGuard;
use App\Repositories\Base\BaseModelRepository;
use App\Repositories\ClientRepository;
use App\Repositories\EmployerRepository;
use App\Repositories\Patterns\TableRepositoryFactory;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Foundation\Http\FormRequest;

class ServerUniqueColumnRule implements InvokableRule
{
    /** * @var FormRequest */
    private FormRequest $request;

    /** * @var string|null $guard */
    private ?string $guard;

    /** @var AuthModel|null $authModel */
    private ?AuthModel $authModel;

    /** @var BaseModelRepository $userRepository */
    private UserRepository $userRepository;

    /** @var BaseModelRepository $employerRepository */
    private EmployerRepository $employerRepository;

    /** @var BaseModelRepository $clientRepository */
    private ClientRepository $clientRepository;

    /**
     * @return void
     */
    public function __construct(FormRequest $request)
    {
        $this->request = $request;
        $this->guard = AppGuard::authGuard();
        $this->authModel = AppAuth::authModel();
        $this->userRepository = resolve(UserRepository::class);
        $this->employerRepository = resolve(EmployerRepository::class);
        $this->clientRepository = resolve(ClientRepository::class);
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail): void
    {
        if (!is_null($this->request->get('tables')))
        {
            /** @var array<int, string> */
            $tables = explode('.', $this->request->get('tables'));

            foreach (array_filter($tables) as $table) {
                $modelRepository = TableRepositoryFactory::make($table);
                /** @var BaseModelRepository $modelRepository */
                if ($modelRepository->existsBy($value, $attribute)) {
                    $fail("The {$value} has already been taken.");        
                    break;
                }
            }

        }
        else if (
            $this->userRepository->existsBy($value, $attribute, $this->authUserId(AppGuard::API))
            || $this->employerRepository->existsBy($value, $attribute, $this->authUserId(AppGuard::EMPLOYER))
            || $this->clientRepository->existsBy($value, $attribute, $this->authUserId(AppGuard::CLIENT))
        )
        {
            $fail("The {$value} has already been taken.");
        }
    }

    /**
     * @param AppGuard $guard
     * @return integer|null
     */
    private function authUserId(AppGuard $guard): ?int
    {
        return is_null($this->request->get('tables'))   ? $this->request->get('model_id') ?? $this->authModel->id
                                                        : null;
    }
}
