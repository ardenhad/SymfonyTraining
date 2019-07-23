<?php


namespace App\EventListener;


use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;
use MongoDB\BSON\Persistable;

class LikeNotificationSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }

    //Must match the event name!
    public function onFlush(OnFlushEventArgs $args)
    {
        $en = $args->getEntityManager();
        $uow = $en->getUnitOfWork();

        /** @var PersistentCollection $collectionUpdate */
        forEach ($uow->getScheduledCollectionUpdates() as $collectionUpdate) {
            if (!$collectionUpdate->getOwner() instanceof MicroPost) {
                continue;
            }

            if ('likedBy' !== $collectionUpdate->getMapping()['fieldName']) {
                continue;
            }

            $insertDiff = $collectionUpdate->getInsertDiff();

            if (!count($insertDiff)) { //basically less than 1
                return;
            }

            /** @var MicroPost $microPost */
            $microPost = $collectionUpdate->getOwner();

            $notification = new LikeNotification();
            $notification->setUser($microPost->getUser());
            $notification->setMicroPost($microPost);
            $notification->setLikedBy(reset($insertDiff));

            $en->persist($notification);

            $uow->computeChangeSet(
                $en->getClassMetadata(LikeNotification::class),
                $notification
            );
        }
    }
}