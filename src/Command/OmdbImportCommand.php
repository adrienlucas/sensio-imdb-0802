<?php

namespace App\Command;

use App\Entity\Movie;
use App\Omdb\OmdbGateway;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:omdb:import',
    description: 'Import a movie from the Open Movie Database.',
)]
class OmdbImportCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OmdbGateway $omdbGateway,
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('movieTitle', InputArgument::REQUIRED, 'Title of the movie you want to import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $omdbResponse = $this->omdbGateway->getFirstMovieByTitle($input->getArgument('movieTitle'));

        $movie = new Movie();
        $movie->setTitle($omdbResponse['Title']);
        $movie->setDescription('Imported movie');
        $movie->setYear($omdbResponse['Year']);
        $movie->setDirector('Imported movie');

        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        $io->success(sprintf('The movie "%s" has been imported.', $movie->getTitle()));
        
        return Command::SUCCESS;
    }
}
