<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Pz\Doctrine\Rest\RestRepository;
use Pz\LaravelDoctrine\Rest\RestController;
use Pz\LaravelDoctrine\Rest\Tests\App\Transformers\UserTransformer;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;

class UserController extends RestController
{
    /**
     * @param UserCreateRequest $request
     *
     * @return array
     */
    public function store(UserCreateRequest $request)
    {
        return $this->create($request);
    }

    /**
     * @param UserEditRequest $request
     *
     * @return array
     */
    public function edit(UserEditRequest $request)
    {
        return $this->update($request);
    }

    /**
     * @return RestRepository
     */
    public function repository()
    {
        return new RestRepository($this->em, $this->em->getClassMetadata(User::class));
    }

    /**
     * @return UserTransformer
     */
    public function transformer()
    {
        return new UserTransformer();
    }

    /**
     * @return string
     */
    protected function getQueryProperty()
    {
        return 'email';
    }

    /**
     * @return array
     */
    protected function getFilterable()
    {
        return ['id', 'email', 'name'];
    }
}
