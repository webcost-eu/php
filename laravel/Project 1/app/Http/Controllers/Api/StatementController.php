<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\DTOs\IndexRequestDTO;
use App\Http\DTOs\Statement\StatementStoreRequestDTO;
use App\Http\DTOs\Statement\StatementUpdateRequestDTO;
use App\Http\Requests\Api\StatementStoreRequest;
use App\Http\Requests\Api\StatementUpdateRequest;
use App\Http\Resources\Collection\StatementCollectionResource;
use App\Http\Resources\StatementResource;
use App\Models\Base\AppAuth;
use App\Models\Base\AuthModel;
use App\Services\Api\StatementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatementController extends Controller
{

    private readonly ?AuthModel $authModel;

    public function __construct(
        private readonly StatementService $service,
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
        $dataDTO = IndexRequestDTO::makeFromRequest($request);
        $result = $this->service->index($this->authModel, $dataDTO);

        return response()->json(StatementCollectionResource::make($result));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StatementStoreRequest $request): JsonResponse
    {
        $dataDTO = StatementStoreRequestDTO::makeFromRequest($request);
        $result = $this->service->store($this->authModel, $dataDTO);

        return response()->json(StatementResource::make($result), Response::HTTP_CREATED);
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

        return response()->json(StatementResource::make($result));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StatementUpdateRequest $request, int $id): JsonResponse
    {
        $dataDTO = StatementUpdateRequestDTO::makeFromRequest($request);
        $result = $this->service->update($this->authModel, $id, $dataDTO);

        return response()->json(StatementResource::make($result), Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): Response
    {
        $this->service->destroy($this->authModel, $id);

        return response()->noContent();
    }
}
