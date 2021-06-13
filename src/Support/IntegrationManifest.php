<?php


namespace Bddy\Integrations\Models;


use JetBrains\PhpStorm\ArrayShape;

abstract class IntegrationManifest
{
    public function __construct(
        protected string $title,
        protected string $key,
        protected bool $available = true,
        protected string $logoUrl = '',
        protected string $description = '')
    {
    }

    /**
     * @return array
     */
    #[ArrayShape(['title' => "string", 'key' => "string", 'logoUrl' => "string", 'description' => "string"])] public function getBasicManifest(): array
    {
        return [
            'title' => $this->title,
            'key' => $this->key,
            'logoUrl' => $this->logoUrl,
            'description' => $this->description
        ];
    }

    /**
     * Return null if manifest should not be used.
     *
     * @return array|null
     */
    public function toArray(): ?array
    {
        return $this->getBasicManifest();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return IntegrationManifest
     */
    public function setTitle(string $title): IntegrationManifest
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return IntegrationManifest
     */
    public function setKey(string $key): IntegrationManifest
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @param bool $available
     *
     * @return IntegrationManifest
     */
    public function setAvailable(bool $available): IntegrationManifest
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }

    /**
     * @param string $logoUrl
     *
     * @return IntegrationManifest
     */
    public function setLogoUrl(string $logoUrl): IntegrationManifest
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return IntegrationManifest
     */
    public function setDescription(string $description): IntegrationManifest
    {
        $this->description = $description;

        return $this;
    }

}