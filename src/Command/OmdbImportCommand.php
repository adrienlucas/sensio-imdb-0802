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
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
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

        $omdbResponse = $this->omdbGateway->getMoviesByTitle($input->getArgument('movieTitle'));

        if(count($omdbResponse) === 1) {
            $anwser = $io->askQuestion(new ConfirmationQuestion(sprintf(
                'Do you want to import "%s"', $omdbResponse[0]['Title']
            )));

            if(!$anwser) {
                $io->info('Import stopped.');
                return Command::FAILURE;
            } else {
                $importMovie = $omdbResponse[0];
            }
        } else {
            $moviesById = [];
            array_walk(
                $omdbResponse,
                function($movie) use (&$moviesById) {
                    $moviesById[$movie['Title'].' - '.$movie['imdbID']] = $movie;
                }
            );

            $anwser = $io->askQuestion(new ChoiceQuestion(
                'Which movie do you want to import ?', array_map(fn($movie) => $movie['Title'].' - '.$movie['imdbID'], $omdbResponse)
            ));

            if(!isset($moviesById[$anwser])) {
                $io->error('Selected movie is not in list.');
                return Command::FAILURE;
            } else {
                $importMovie = $moviesById[$anwser];
            }
        }

        $movie = new Movie();
        $movie->setTitle($importMovie['Title']);
        $movie->setDescription('Imported movie');
        $movie->setYear((int) $importMovie['Year']);
        $movie->setDirector('Imported movie');

        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        $io->success(sprintf('The movie "%s" has been imported.', $movie->getTitle()));

        return Command::SUCCESS;
    }
}
