<?php

namespace Totaa\TotaaFileUpload\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Totaa\TotaaFileUpload\Models\FileUpload;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Log;

class GoogleDriveUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fileUpload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(FileUpload $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = Storage::disk('google');

        $disk->put($this->fileUpload->local_path, fopen(Storage::disk('public')->path($this->fileUpload->local_path), 'r+'));

        Storage::disk('public')->delete($this->fileUpload->local_path);

        $adapter = $disk->getDriver()->getAdapter();

        $metadata = $adapter->getMetadata($this->fileUpload->local_path);

        $getFileObject = $adapter->getFileObject($metadata["virtual_path"]);

        $this->fileUpload->update([
            "drive" => "google",
            "google_drive_virtual_path" => $metadata["virtual_path"],
            "google_drive_display_path" => $metadata["display_path"],
            "google_drive_id" => $getFileObject->id,
        ]);
    }
}
