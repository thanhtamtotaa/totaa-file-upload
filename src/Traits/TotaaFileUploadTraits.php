<?php

namespace Totaa\TotaaFileUpload\Traits;

use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Totaa\TotaaFileUpload\Models\FileUpload;
use Totaa\TotaaFileUpload\Jobs\GoogleDriveUpload;

trait TotaaFileUploadTraits
{
    protected $file_path, $temp_file, $file_name, $fileUpload, $local_path;

    public function save_to_drive(TemporaryUploadedFile $file, $path, $file_name)
    {
        $this->temp_file = $file;
        $this->file_path = $path;
        $this->file_name = $file_name;
        $this->saveAs();
        $this->put_to_db();
        $this->add_jobs();

        return $this->fileUpload->id;
    }

    protected function saveAs()
    {
        $this->local_path = $temp_filesss = $this->temp_file->storeAs($this->file_path, $this->file_name, ["disk" => "public"]);
    }

    protected function put_to_db()
    {
        $FileUpload = FileUpload::create([
            "drive" => "public",
            "local_path" => $this->local_path,
        ]);

        $this->fileUpload = $FileUpload;
    }

    protected function add_jobs()
    {
        GoogleDriveUpload::dispatch($this->fileUpload);
    }

    public function get_url($id)
    {
        $this->fileUpload = FileUpload::find($id);

        if ($this->fileUpload->drive == "public") {
            $path = $this->fileUpload->local_path;
        } else {
            $path = $this->fileUpload->google_drive_display_path;
        }
        return Storage::disk($this->fileUpload->drive)->url($path);
    }
}
