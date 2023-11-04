import SocialMediaImageType from "./fieldtypes/SocialMediaImageType.vue";

Statamic.booting(() => {
    Statamic.$components.register('social_media_image_type-fieldtype', SocialMediaImageType);
});