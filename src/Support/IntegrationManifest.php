<?php


namespace Anny\Integrations\Support;


abstract class IntegrationManifest
{
    /**
     * Title of the integration.
     *
     * @var string
     */
    protected string $title;

    /**
     * Identifier key for the integration.
     *
     * @var string
     */
    protected string $key;

    /**
     * Url of the logo url for the integration.
     *
     * @var string
     */
    protected string $logoUrl = '';

    /**
     * Description of the integration.
     *
     * @var string
     */
    protected string $description = '';

    /**
     * Possible authentication strategies which can be used to authenticate the integration.
     *
     * @var array
     */
    protected array $authenticationStrategies = [];

    /**
     * Flag if the integration is available.
     *
     * @var bool
     */
    protected bool $available = true;

    /**
     * Message why integration is not available.
     * @var string
     */
    protected string $unavailableMessage = '';

    /**
     * Label for the action to make integration available.
     * @var string
     */
    protected string $availabilityAction = '';

    /**
     * Link to the action to make integration available.
     * @var string
     */
    protected string $availabilityLink = '';


    /**
     * @return array
     */
    public function getBasicManifest(): array
    {
        $manifestArray = [
            'title'       => $this->getTitle(),
            'key'         => $this->getKey(),
            'available'   => $this->isAvailable(),
            'logoUrl'     => $this->getLogoUrl(),
            'description' => $this->getDescription(),
            'credentials' => $this->getAuthenticationStrategies(),
        ];

        if (!$this->available)
        {
            $manifestArray['unavailable_message'] = $this->getUnavailableMessage();
            $manifestArray['availability_action'] = $this->getUnavailableMessage();
            $manifestArray['availability_link']   = $this->getUnavailableMessage();
        }

        return $manifestArray;
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
    public function setKey(string $key): static
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
    public function setAvailable(bool $available): static
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
    public function setLogoUrl(string $logoUrl): static
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
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array
     */
    public function getAuthenticationStrategies(): array
    {
        return $this->authenticationStrategies;
    }

    /**
     * @param array $authenticationStrategies
     *
     * @return IntegrationManifest
     */
    public function setAuthenticationStrategies(array $authenticationStrategies): static
    {
        $this->authenticationStrategies = $authenticationStrategies;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnavailableMessage(): string
    {
        return $this->unavailableMessage;
    }

    /**
     * @param string $unavailableMessage
     *
     * @return IntegrationManifest
     */
    public function setUnavailableMessage(string $unavailableMessage): static
    {
        $this->unavailableMessage = $unavailableMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvailabilityAction(): string
    {
        return $this->availabilityAction;
    }

    /**
     * @param string $availabilityAction
     *
     * @return IntegrationManifest
     */
    public function setAvailabilityAction(string $availabilityAction): static
    {
        $this->availabilityAction = $availabilityAction;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvailabilityLink(): string
    {
        return $this->availabilityLink;
    }

    /**
     * @param string $availabilityLink
     *
     * @return IntegrationManifest
     */
    public function setAvailabilityLink(string $availabilityLink): static
    {
        $this->availabilityLink = $availabilityLink;

        return $this;
    }
}