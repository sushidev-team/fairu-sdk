<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\Responses\Tenant;
use SushiDev\Fairu\Responses\TenantCreationResult;

class TenantMutations extends BaseMutation
{
    public function create(string $name): TenantCreationResult
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuTenant($name: String!) {
            createFairuTenant(name: $name) {
                id
                name
                api_key
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['name' => $name]);

        return new TenantCreationResult($result['createFairuTenant'] ?? []);
    }

    public function update(
        ?string $name = null,
        ?bool $forceLicense = null,
        ?bool $useAi = null,
        ?bool $useAiOnUpload = null,
        ?string $avatarId = null,
        ?string $aiLanguage = null,
        ?bool $aiNsfw = null,
        ?bool $aiBlurFaces = null,
        ?bool $forceFileAlt = null,
        ?bool $forceFileDescription = null,
        ?bool $forceFileCaption = null,
        ?bool $forceFileCopyright = null,
        ?bool $forceFilePolicy = null,
        ?bool $blockFilesWithError = null,
        ?string $customDomain = null,
        ?bool $hideDotfiles = null
    ): ?Tenant {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuTenant(
            $name: String,
            $force_license: Boolean,
            $use_ai: Boolean,
            $use_ai_onupload: Boolean,
            $avatar_id: ID,
            $ai_language: String,
            $ai_nsfw: Boolean,
            $ai_blur_faces: Boolean,
            $force_file_alt: Boolean,
            $force_file_description: Boolean,
            $force_file_caption: Boolean,
            $force_file_copyright: Boolean,
            $force_file_policy: Boolean,
            $block_files_with_error: Boolean,
            $custom_domain: String,
            $hide_dotfiles: Boolean
        ) {
            updateFairuTenant(
                name: $name,
                force_license: $force_license,
                use_ai: $use_ai,
                use_ai_onupload: $use_ai_onupload,
                avatar_id: $avatar_id,
                ai_language: $ai_language,
                ai_nsfw: $ai_nsfw,
                ai_blur_faces: $ai_blur_faces,
                force_file_alt: $force_file_alt,
                force_file_description: $force_file_description,
                force_file_caption: $force_file_caption,
                force_file_copyright: $force_file_copyright,
                force_file_policy: $force_file_policy,
                block_files_with_error: $block_files_with_error,
                custom_domain: $custom_domain,
                hide_dotfiles: $hide_dotfiles
            ) {
                id
                name
                use_ai
                use_ai_onupload
                custom_domain
                custom_domain_status
            }
        }
        GRAPHQL;

        $variables = array_filter([
            'name' => $name,
            'force_license' => $forceLicense,
            'use_ai' => $useAi,
            'use_ai_onupload' => $useAiOnUpload,
            'avatar_id' => $avatarId,
            'ai_language' => $aiLanguage,
            'ai_nsfw' => $aiNsfw,
            'ai_blur_faces' => $aiBlurFaces,
            'force_file_alt' => $forceFileAlt,
            'force_file_description' => $forceFileDescription,
            'force_file_caption' => $forceFileCaption,
            'force_file_copyright' => $forceFileCopyright,
            'force_file_policy' => $forceFilePolicy,
            'block_files_with_error' => $blockFilesWithError,
            'custom_domain' => $customDomain,
            'hide_dotfiles' => $hideDotfiles,
        ], fn ($v) => $v !== null);

        $result = $this->executeMutation($mutation, $variables);

        if (! isset($result['updateFairuTenant'])) {
            return null;
        }

        return new Tenant($result['updateFairuTenant']);
    }
}
