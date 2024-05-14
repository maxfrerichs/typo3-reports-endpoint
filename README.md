# TYPO3 Extension 'typo3-reports-endpoint'

## About this extension:
This extension provides a simple, secure endpoint for external monitoring applications to get TYPO3 status report information in machine-readable JSON format.

## Requirements
* TYPO3 12.4 or higher
* System extensions "cms-reports" and "reactions" installed.
* PHP 8.2 or higher

## How to use:
* Install extension with ```composer req maxfrerichs/typo3-reports-endpoint``` in your TYPO3  distribution
* Create new Reaction type under Modules->Reaction and select `Provides an endpoint for TYPO3 status reports` (Attention: Keep the generated secret in a safe place)
* Now you can submit POST requests to the generated URL and retrieve the data (Example: ```curl -X 'POST' \
    'https://your-typo3.site/typo3/reaction/f73eb62e-ec6b-48de-a14f-b1ea39a22563' \
      -H 'accept: application/json' \
      -H 'x-api-key: ***your-secret***'```)


## Note: This extension is under development.