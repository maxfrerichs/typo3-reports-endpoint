<?php
declare(strict_types=1);
namespace MFR\TYPO3ReportsEndpoint\Reaction;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Reactions\Reaction\ReactionInterface;
use TYPO3\CMS\Reactions\Model\ReactionInstruction;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Reports\Registry\ReportRegistry;
use TYPO3\CMS\Reports\Registry\StatusRegistry;

class ReportsEndpointReaction implements ReactionInterface 
{
    
    public function __construct(
        private readonly StatusRegistry $statusRegistry,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly ReportRegistry $reportRegistry
    ) {}

    /**
     * A meaningful description for the reaction
     * @return string
     */
    public static function getDescription(): string 
    {
        return 'Provides an endpoint for TYPO3 status reports';
    }
    
    /**
     * An icon identifier for the reaction
     * @return string
     */
    public static function getIconIdentifier(): string {
        return 'module-reports';
    }
    
    /**
     * The reaction type, used for the registry and stored in the database
     * @return string
     */
    public static function getType(): string {
        return 'typo3-reports-endpoint';
    }
    
    /**
     * Main method of the reaction, handling the incoming request
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array $payload
     * @param \TYPO3\CMS\Reactions\Model\ReactionInstruction $reaction
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function react(ServerRequestInterface $request, array $payload, ReactionInstruction $reaction): ResponseInterface 
    {
        $languageService = $this->getLanguageService();
        $statusProviders = $this->statusRegistry->getProviders();
        $statusData = [];

        foreach ($statusProviders as $statusProviderItem) {
            $status = $statusProviderItem->getStatus();

            /*
            $identifier = str_replace(" ", "-", strtolower($languageService->sL($statusProviderItem->getLabel())));
            if(!array_key_exists($identifier, $statusData)) {
                $statusData[$identifier] = [];
            }
            */
            foreach ($status as $statusItem) {
                $statusData[] = [
                    'title' => $statusItem->getTitle(),
                    'message' => strip_tags($statusItem->getMessage()),
                    'severity' => $this->convertSeverity($statusItem->getSeverity()),
                    'value' => $statusItem->getValue(),
                ];
            }
        }
    
        return $this->responseFactory->createResponse(200)->withBody($this->streamFactory->createStream(json_encode($statusData)));
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
    protected function convertSeverity(ContextualFeedbackSeverity $severity): string
    {
        return $severity->name;
    }
}