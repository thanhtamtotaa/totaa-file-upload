<?php

namespace Totaa\TotaaFileUpload\Traits;

use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Totaa\TotaaFileUpload\Models\FileUpload;
use Totaa\TotaaFileUpload\Jobs\GoogleDriveUpload;

trait TotaaFileUploadTraits
{
    protected $file_path, $temp_file, $file_name, $fileUpload, $local_path;

    public $TotaaFileUploadStep = [], $TotaaFileUploadMethod = NULL, $TotaaFileId = NULL, $TotaaFileSubmit = false;

    public function Update_TotaaFileUploadStep($model, $step)
    {
        $this->TotaaFileUploadStep[$model] = $step;

        if ($this->TotaaFileSubmit && $step ==4) {
            $this->check_upload();
        }
    }

    public function TotaaFileUploadSubmit($method, $id = NULL)
    {
        $this->TotaaFileUploadMethod = $method;
        $this->TotaaFileId = $id;
        $this->TotaaFileSubmit = true;
        $this->check_upload();
    }

    protected function check_upload()
    {
        $array_count = array_count_values($this->TotaaFileUploadStep);
        $count = 0;

        if (array_key_exists(3, $array_count)) {
            $count += $array_count[3];
        }

        if (array_key_exists(4, $array_count)) {
            $count += $array_count[4];
        }

        if (count($this->TotaaFileUploadStep) == $count) {
            $this->emit($this->TotaaFileUploadMethod, $this->TotaaFileId);
            $this->TotaaFileUploadMethod = NULL;
            $this->TotaaFileId = NULL;
            $this->TotaaFileSubmit = false;
            $this->TotaaFileUploadStep = [];
        }
    }

    protected function save_to_drive(TemporaryUploadedFile $file, $path, $file_name)
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

    protected function delete(TemporaryUploadedFile $file)
    {
        $file->delete();
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
