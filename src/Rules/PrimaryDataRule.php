<?php

namespace Pz\LaravelDoctrine\JsonApi\Rules;

use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

/**
 * Validates if the `data` key contains an `id` of the entity with required `type`.
 * The target entity must implement JsonApiResource
 */
class PrimaryDataRule implements Rule
{
    protected string $type;
    protected string $resourceClass;

    protected mixed $rule = null;
    protected array $message = ['The :attribute field is invalid.'];
    private array $messages;

    public function __construct(string $resourceClass, mixed $rule = null, array $messages = [])
    {
        $this->resourceClass = $resourceClass;
        $this->type = ResourceRepository::classResourceKey($resourceClass);
        $this->rule = $rule;
        $this->messages = $messages;
    }

    public function passes($attribute, $value): bool
    {
        $valid = false;

        if (is_array($value) && isset($value['type']) && isset($value['id'])) {
            if ($value['type'] !== $this->type) {
                $this->message[] = sprintf(' Invalid type `%s` for the expected entity', $value['type']);
                return false;
            }

            if (null === ($resource = $this->em()->find($this->resourceClass, $value['id']))) {
                $this->message[] = sprintf(
                    'Entity with id `%s` and type `%s` doesn\'t exist',
                    $value['id'],
                    $value['type']
                );

                return false;
            }

            $valid = true;
        }

        if (!is_null($this->rule) && $valid) {
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = Validator::make(['data' => $resource], ['data' => $this->rule], $this->messages);

            if ($validator->fails()) {
                $this->message = $validator->getMessageBag()->get('data');
                return false;
            }

            $valid = true;
        }

        return $valid;
    }

    public function message(): array|string
    {
        return $this->message;
    }

    protected function em(): EntityManager
    {
        return app(EntityManager::class);
    }
}