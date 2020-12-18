<?php

namespace Totaa\TotaaFileUpload\Traits;

use Totaa\TotaaFileUpload\Models\FileUpload;
use Livewire\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;

trait TotaaFileUploadTraits
{
    protected $file_path, $temp_file, $file_name, $fileUpload;

    public function save_to_drive(TemporaryUploadedFile $file, $path, $file_name)
    {
        $this->temp_file = $file;
        $this->file_path = $path;
        $this->file_name = $file_name;
        $this->saveAs();
        $this->put_to_db();
        //dd($this->temp_file);

        dd($this->fileUpload);
    }

    protected function saveAs()
    {
        $temp_filesss = $this->temp_file->storeAs($this->file_path, $this->file_name, ["disk" => "public"]);

        dd(Storage::disk('local')->get('public/'.$temp_filesss),  $temp_filesss);

        dd(public_path($temp_filesss));
    }

    protected function put_to_db()
    {
        $FileUpload = FileUpload::create([
            "drive" => "public",
            "local_path" => $this->file_path."/".$this->file_name,
        ]);

        $this->fileUpload = $FileUpload;
    }
}
