<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\DTOs\User\UserStoreRequestDTO;
use App\Http\DTOs\User\UserUpdateRequestDTO;
use App\Http\Requests\Api\User\UserStoreRequest;
use App\Http\Requests\Api\User\UserUpdateRequest;
use App\Http\Resources\Collection\UserCollectionResource;
use App\Http\Resources\UserResource;
use App\Models\Base\AppAuth;
use App\Models\Base\AuthModel;
use App\Models\User;
use App\Services\Api\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * @var AuthModel|null $authModel
     */
    private ?AuthModel $authModel;

    public function __construct(
        private UserService $service
    )
    {
        $this->authModel = AppAuth::authModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->service->index($this->authModel, $request->query->get('s'), $request->query->getInt('per_page'));

        return response()->json(UserCollectionResource::make($result));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $dataDTO = UserStoreRequestDTO::makeFromRequest($request);
        $result = $this->service->store($dataDTO);

        return response()->json(UserResource::make($result), Response::HTTP_ACCEPTED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->service->show($this->authModel, $id);

        return response()->json(UserResource::make($result));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        $dataDTO = UserUpdateRequestDTO::makeFromRequest($request);
        $result = $this->service->update($this->authModel, $dataDTO);

        return response()->json(UserResource::make($result), Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): Response
    {
        $this->service->destroy($id);

        return response()->noContent();
    }
}
