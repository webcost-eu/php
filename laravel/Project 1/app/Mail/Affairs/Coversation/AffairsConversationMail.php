<?php

namespace App\Mail\Affairs\Coversation;

use App\Models\AffairsConversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AffairsConversationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public readonly AffairsConversation $affairsConversation,
    )
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        /** @var self $mail */
        $mail = $this->subject($this->affairsConversation->title);

        foreach ($this->files = $this->affairsConversation->files as $file) {
            $mail->attachFromStorageDisk($file['disk'], $file['path'], $file['name'], [
                    'mime' => $file['mime_type'],
                ],
            );
        }                    

        return $mail->view('mail.affairs.affairs_conversation.affairs_conversation');
    }
}
