<?php
/**
 * @file
 * Contains \Drupal\Console\Command\Debug\EntityCommand.
 */

namespace Drupal\Console\Command\Debug;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Drupal\Core\Entity\EntityTypeRepository;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Console\Core\Command\Shared\CommandTrait;
use Drupal\Console\Core\Style\DrupalStyle;

class EntityCommand extends Command
{
    use CommandTrait;

    /**
     * @var EntityTypeRepository
     */
    protected $entityTypeRepository;

    /**
     * @var EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * EntityCommand constructor.
     *
     * @param EntityTypeRepository       $entityTypeRepository
     * @param EntityTypeManagerInterface $entityTypeManager
     */
    public function __construct(
        EntityTypeRepository $entityTypeRepository,
        EntityTypeManagerInterface $entityTypeManager
    ) {
        $this->entityTypeRepository = $entityTypeRepository;
        $this->entityTypeManager = $entityTypeManager;
        parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('debug:entity')
            ->setDescription($this->trans('commands.debug.entity.description'))
            ->addArgument(
                'entity-type',
                InputArgument::OPTIONAL,
                $this->trans('commands.debug.entity.arguments.entity-type')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new DrupalStyle($input, $output);

        $entityType = $input->getArgument('entity-type');

        $tableHeader = [
            $this->trans('commands.debug.entity.table-headers.entity-name'),
            $this->trans('commands.debug.entity.table-headers.entity-type')
        ];
        $tableRows = [];

        $entityTypesLabels = $this->entityTypeRepository->getEntityTypeLabels(true);

        if ($entityType) {
            $entityTypes = [$entityType => $entityType];
        } else {
            $entityTypes = array_keys($entityTypesLabels);
        }

        foreach ($entityTypes as $entityTypeId) {
            $entities = array_keys($entityTypesLabels[$entityTypeId]);
            foreach ($entities as $entity) {
                $tableRows[$entity] = [
                    $entity,
                    $entityTypeId
                ];
            }
        }

        $io->table($tableHeader, array_values($tableRows));
    }
}
