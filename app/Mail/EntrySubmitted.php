<?php

namespace App\Mail;

use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Str;

class EntrySubmitted extends Mailable
{
    public function __construct(
        public array $entry,
        public array $categories,
        public array $files = [],
    ) {}

    public function envelope(): Envelope
    {
        $count = count($this->categories);

        return new Envelope(
            replyTo: [new Address($this->entry['email'], $this->entry['contact_name'])],
            subject: "New Flyoneers entry: {$this->entry['company']} ({$count} ".Str::plural('category', $count).')',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.entry-submitted',
            with: [
                'fileNames' => array_map(fn (UploadedFile $file) => $this->attachmentName($file), $this->files),
                'md' => fn (?string $value): string => addcslashes(str_replace(["\r", "\n"], ' ', (string) $value), '\\`*_{}[]()#+-.!|<>~'),
            ],
        );
    }

    public function attachments(): array
    {
        return array_map(
            fn (UploadedFile $file) => Attachment::fromPath($file->getRealPath())
                ->as($this->attachmentName($file))
                ->withMime($file->getMimeType() ?: 'application/octet-stream'),
            $this->files,
        );
    }

    private function attachmentName(UploadedFile $file): string
    {
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'evidence';

        return $name.'.'.strtolower($file->getClientOriginalExtension());
    }
}
