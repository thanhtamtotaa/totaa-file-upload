<?php

namespace Totaa\TotaaFileUpload\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;
use Illuminate\Support\Facades\Storage;

class FileUpload extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Userstamps;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'totaa_file_uploads';

    // Disable Laravel's mass assignment protection
    protected $guarded = ['id'];

    protected $disk, $adapter;

    /**
     * {@inheritdoc}
     */
    public function getMimetype()
    {
        if ($this->drive == "public") {
            $path = $this->local_path;
        } else {
            $path = $this->google_drive_display_path;
        }

        return $this->disk = Storage::disk($this->drive)->mimeType($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        if ($this->drive == "public") {
            $path = $this->local_path;
        } else {
            $path = $this->google_drive_display_path;
        }

        return $this->disk = Storage::disk($this->drive)->url($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnail()
    {
        if ($this->drive == "public") {
            return $this->disk = Storage::disk($this->drive)->url($this->local_path);
        } else {
            dd(Storage::disk($this->drive)->getDriver()->getAdapter()->getFileObject($this->google_drive_virtual_path));
            return $this->disk = Storage::disk($this->drive)->url($this->google_drive_display_path);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getGallery()
    {
        $gallery = [];

        if ($this->drive == "public") {
            $gallery['mimetype'] = Storage::disk($this->drive)->mimeType($this->local_path);
            $gallery['url'] = Storage::disk($this->drive)->url($this->local_path);
            $gallery['thumbnail'] = Storage::disk($this->drive)->url($this->local_path);
        } else {
            $FileObject = Storage::disk($this->drive)->getDriver()->getAdapter()->getFileObject($this->google_drive_virtual_path);

            $gallery['mimetype'] = $FileObject->getMimetype();

            if(($url = $FileObject->getWebContentLink())) {
                $gallery['url'] = str_replace('export=download', 'export=media', $url);
            } elseif (($url = $FileObject->getWebViewLink())) {
                $gallery['url'] = $url;
            }

            if(($thumbnail = $FileObject->getThumbnailLink())) {
                $gallery['thumbnail'] = $thumbnail;
            } elseif (($thumbnail = $FileObject->getWebContentLink())) {
                $gallery['thumbnail'] = str_replace('export=download', 'export=media', $thumbnail);
            } elseif (($thumbnail = $FileObject->getWebViewLink())) {
                $gallery['thumbnail'] = $thumbnail;
            }
        }

        return $gallery;
    }
}
