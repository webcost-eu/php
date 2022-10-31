<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Facade\FileService;
use App\Http\DTOs\AffairsConversation\AffairsConversationStoreRequestDTO;
use App\Http\DTOs\IndexRequestDTO;
use App\Mail\Affairs\Coversation\AffairsConversationMail;
use App\Models\AffairsConversation;
use App\Models\Base\AuthModel;
use App\Models\User;
use App\Repositories\AffairsConversationRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class AffairsConversationService
{
    /**
     * @var array $with
     */
    private array $with = [
        'sender:id,first_name,last_name',
        'files',
    ];

    public function __construct(
        private readonly AffairsConversationRepository $repository,
    ) {

    }

    public function paginate(User $user, int $id, int $perPage = null): LengthAwarePaginator
    {
        return $this->repository->setWith($this->with)->paginateWhere('affairs_id', $id, $perPage);
    }

    public function store(User $authModel, int $id, AffairsConversationStoreRequestDTO $dataDTO): AffairsConversation
    {
        $storedAffairsConversation = $authModel->affairsConversations()->create(
            array_merge($dataDTO->except('id')->toArray(), ['affairs_id' => $id])
        )->loadMissing($this->with);

        /** @var AffairsConversation $affairsConversation */
        $affairsConversation = FileService::uploadModelFilesFromIterable($storedAffairsConversation, $dataDTO->files);
        
        return $this->sendConversation($affairsConversation);
    }

    /**
     * @param AffairsConversation $affairsConversation
     * @return AffairsConversation
     */
    private function sendConversation(AffairsConversation $affairsConversation): AffairsConversation
    {
        match($affairsConversation->type) {
            'email' => $this->sendEmail($affairsConversation->fresh(['files'])),
            'sms' => $this->sendSMS($affairsConversation),
        };

        $affairsConversation->markAsSent();

        return $affairsConversation;
    }
    
    /**
     * @param AffairsConversation $affairsConversation
     * @return void
     */
    private function sendEmail(AffairsConversation $affairsConversation): void
    {
        foreach ($affairsConversation->send_to as $key => $email) {
            Mail::to($email)->send(new AffairsConversationMail($affairsConversation));
        }
    }

    /**
     * @param AffairsConversation $affairsConversation
     * @return void
     */
    private function sendSMS(AffairsConversation $affairsConversation): void
    {
        foreach ($affairsConversation->send_to as $key => $phone) {
            SmsApiService::sendTo($phone, $affairsConversation->text);
        }
    }
}