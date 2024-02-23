<?php

namespace App\Command;


use App\Common\Structures\Address;
use App\Gpt\GptDialogueProcessor;
use App\Product\Availability\ProductAvailability;
use App\Product\Availability\ProductAvailabilityAnyDate;
use App\Product\Availability\ProductAvailabilitySpecificSlots;
use App\Reservation\SiteApi\SiteApiReservationProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:test')]
final class TestCommand extends Command
{
    public function __construct(
        private readonly GptDialogueProcessor       $dialogueProcessor,
        private readonly SiteApiReservationProvider $apiReservationProvider,
        ?string                                     $name = null
    )
    {
        parent::__construct($name);

    }

    protected function configure(): void
    {
        $this->addArgument('query', InputArgument::REQUIRED, 'Search query must be specified.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $parseResult = $this->dialogueProcessor->askAboutReservation($input->getArgument('query'));

            $searchResult = $this->apiReservationProvider->search(
                in: $parseResult->city,
                range: $parseResult->dateTimeRange,
                personsCount: $parseResult->personsCount,
                limit: 15
            );

            foreach ($searchResult as $product) {
                $productCities = \array_map(
                    static function (Address $address): string {
                        return $address->city;
                    },
                    $product->addresses
                );

                $output->writeln(
                    \sprintf('<info>%s</info> (%s) [%s]',
                        $product->name,
                        \join(', ', $productCities),
                        self::renderAvailability($product->availability)
                    )
                );
            }

            $output->writeln(\PHP_EOL . '<comment>Done</comment>');

            return Command::SUCCESS;
        } catch (\Throwable $throwable) {
            $output->writeln(\sprintf('<error>%s</error>', $throwable->getMessage()));

            return Command::FAILURE;
        }
    }

    private static function renderAvailability(?ProductAvailability $productAvailability): string
    {
        if ($productAvailability instanceof ProductAvailabilityAnyDate) {
            return 'any time';
        }

        if ($productAvailability instanceof ProductAvailabilitySpecificSlots) {
            return \join(
                ', ',
                \array_map(
                    static function (\DateTimeImmutable $datetime): string {
                        return $datetime->format('Y-m-d H:i:s');
                    },
                    $productAvailability->timeslots
                )
            );
        }

        return 'n/a';
    }
}
