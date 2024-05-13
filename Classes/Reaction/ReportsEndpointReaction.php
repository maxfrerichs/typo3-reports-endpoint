<?php
declare(strict_types=1);
namespace MFR\TYPO3ReportsEndpoint\Reaction;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Reactions\Reaction\ReactionInterface;
use TYPO3\CMS\Reports\Registry\ReportRegistry;
use TYPO3\CMS\Reactions\Model\ReactionInstruction;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Reports\Registry\StatusRegistry;

class ReportsEndpointReaction implements ReactionInterface 
{
    
    private const REGISTRY_KEY = 'typo3-reports-endpoint';


    public function __construct(
        private readonly ReportRegistry $reportRegistry,
        private readonly StatusRegistry $statusRegistry,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
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
        $statusReport = $this->statusRegistry->getProviders();
        $exportData = [];

        foreach ($statusReport as $item) {
            $status = $item->getStatus();
            $identifier = str_replace(" ", "-", strtolower($languageService->sL($item->getLabel())));
            if(!array_key_exists($identifier, $exportData)) {
                $exportData[$identifier] = [];
            }
            foreach ($status as $index=>$statusItem) {
                $exportData[$identifier][$index] = [
                    'title' => $statusItem->getTitle(),
                    'severity' => $statusItem->getSeverity(),
                    'value' => $statusItem->getValue(),
                ];
            }
        }
        return $this->responseFactory->createResponse(200)->withBody($this->streamFactory->createStream(json_encode($exportData)));
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}