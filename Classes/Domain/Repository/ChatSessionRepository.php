<?php

namespace SvenLie\ChatbotRasa\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

class ChatSessionRepository extends Repository
{
    public function initializeObject()
    {
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    public function findBySenderToken(string $senderToken)
    {
        $query = $this->createQuery();

        return $query->matching($query->equals('sender_token', $senderToken))->execute()->getFirst();
    }
}
