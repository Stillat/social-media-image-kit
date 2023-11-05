<?php

namespace Stillat\SocialMediaImageKit;

use Illuminate\Support\Str;
use Statamic\Contracts\Entries\Entry;
use Statamic\Facades\Entry as EntryApi;
use Stillat\SocialMediaImageKit\Contracts\ImageNameFormatter;
use Stillat\SocialMediaImageKit\Contracts\ProfileResolver;

class ImageManager
{
    protected GeneratorFieldConfiguration $fieldConfiguration;

    protected ImageNameFormatter $nameFormatter;

    protected ProfileResolver $profileResolver;

    public function __construct(GeneratorFieldConfiguration $fieldConfiguration, ImageNameFormatter $formatter, ProfileResolver $resolver)
    {
        $this->fieldConfiguration = $fieldConfiguration;
        $this->nameFormatter = $formatter;
        $this->profileResolver = $resolver;
    }

    /**
     * Determines if the provided entry has a social media profile image.
     *
     * @param  Entry|string  $entry The entry to check.
     * @param  string  $profile The social media profile to update.
     */
    public function entryHasProfile(Entry|string $entry, string $profile): bool
    {
        if (is_string($entry)) {
            $entry = EntryApi::find($entry);
        }

        if (! $entry) {
            return false;
        }

        $images = $entry->get($this->fieldConfiguration->imagesFieldName, null);

        if (! $images || ! is_array($images)) {
            return false;
        }

        foreach ($images as $image) {
            if (! is_array($image)) {
                break;
            }

            if (array_key_exists($this->fieldConfiguration->socialMediaPlatformType, $image)) {
                if ($image[$this->fieldConfiguration->socialMediaPlatformType] === $profile) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Creates or updates an existing social media profile image for the provided entry.
     *
     * The provided $filePath must be available on the local filesystem.
     * If the provided path is already within the application's local
     * storage directory, the existing file will be used. If the
     * file exists elsewhere, it will be copied to the local
     * storage directory. This method will not remove any
     * files from disk that were not originally in the
     * application's local storage directory.
     *
     * If the application's configured local storage directory or the
     * provided file path are not writable, this method will exit.
     *
     * This method does not resize images. Ensure input is the correct size.
     *
     * @param  Entry|string  $entry The entry to update.
     * @param  string  $profile The social media profile to update.
     * @param  string  $filePath The path to the image file to use.
     * @param  bool  $newPreserveSetting Whether to mark the updated image as "preserve".
     */
    public function setProfileImage(Entry|string $entry, string $profile, string $filePath, bool $newPreserveSetting = false): void
    {
        if (is_string($entry)) {
            $entry = EntryApi::find($entry);
        }

        if (! $entry) {
            return;
        }

        $entryId = $entry->id();
        $images = $entry->get($this->fieldConfiguration->imagesFieldName, []);

        if (! is_array($images)) {
            return;
        }

        $existingProfile = null;

        foreach ($images as $image) {
            if (! is_array($image)) {
                break;
            }

            if (array_key_exists($this->fieldConfiguration->socialMediaPlatformType, $image)) {
                if ($image[$this->fieldConfiguration->socialMediaPlatformType] === $profile) {
                    $existingProfile = $image;
                    break;
                }
            }
        }

        $size = $this->profileResolver->getSize($profile);

        if (! $size) {
            return;
        }

        $imageName = $this->nameFormatter->getImageName($entryId, $entry, $size).'.'.config('social_media_image_kit.generation.image_format.extension', 'png');

        $assetId = null;
        if ($existingProfile != null) {
            $assetId = $this->fieldConfiguration->assetContainer.'::'.$existingProfile[$this->fieldConfiguration->assetFieldName];
        }

        if (! $assetId) {
            $assetId = $this->fieldConfiguration->assetContainer.'::'.$imageName;
        }

        $asset = AssetUpdater::updateAsset(new AssetInfo(
            assetId: $assetId,
            imagePath: $filePath,
            assetContainer: $this->fieldConfiguration->assetContainer,
            fileName: $imageName
        ));

        if (! $asset) {
            return;
        }

        if (! $existingProfile) {
            $images[] = [
                'id' => 's'.Str::random(8),
                $this->fieldConfiguration->assetFieldName => $asset->path(),
                $this->fieldConfiguration->socialMediaPlatformType => $profile,
                $this->fieldConfiguration->preservedFieldName => $newPreserveSetting,
            ];
        }

        $entry->set($this->fieldConfiguration->imagesFieldName, $images);
        $entry->saveQuietly();
    }
}
