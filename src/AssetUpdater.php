<?php

namespace Stillat\SocialMediaImageKit;

use GuzzleHttp\Psr7\MimeType;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Statamic\Assets\ReplacementFile;
use Statamic\Facades\Asset;
use Statamic\Facades\Glide;
use Statamic\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetUpdater
{
    public static function updateAsset(AssetInfo $info, bool $cleanupFiles = true): ?\Statamic\Contracts\Assets\Asset
    {
        $localRoot = config('filesystems.disks.local.root');
        $imagePath = $info->imagePath;

        // Expect the local root and the incoming file to be locally accessible.
        if (! file_exists($localRoot) || ! file_exists($imagePath)) {
            return null;
        }

        $fileName = $info->fileName;

        if ($fileName == null || mb_strlen(trim($fileName)) == 0) {
            $fileName = basename($imagePath);
        }

        $asset = Asset::findById($info->assetId);

        $cleanUpPath = null;
        $replacementPath = $imagePath;

        if (Str::startsWith($replacementPath, $localRoot)) {
            $cleanUpPath = $replacementPath;
            // Our file is already inside the local disk.
            // We need to remove the local root from the path.
            $replacementPath = Str::after($replacementPath, $localRoot);
        } else {
            // Someone is being tricky! Let's copy the file to the local disk.
            $replacementPath = Storage::putFileAs('/', new File($imagePath), $fileName);
            $imagePath = Str::finish($localRoot, '/').$replacementPath;

            // Prevent removing the original file. Let's clean up our moved file.
            $cleanUpPath = Str::finish($localRoot, '/').$replacementPath;
        }

        if ($asset != null) {
            $asset->reupload(new ReplacementFile($replacementPath));
            Glide::clearAsset($asset);
        } else {
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $mimeType = MimeType::fromExtension($extension);

            $asset = Asset::make()->container($info->assetContainer)->path($info->assetId);
            $asset->save();

            $asset->upload(new UploadedFile($imagePath, $fileName, $mimeType));
        }

        if ($cleanupFiles && $cleanUpPath) {
            if (file_exists($cleanUpPath)) {
                @unlink($cleanUpPath);
            }
        }

        return $asset;
    }
}
