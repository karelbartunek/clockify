<?php

namespace KarelBartunek\Clockify\Command;

use DateTimeImmutable;
use JDecool\Clockify\Model\ProjectDtoImpl;
use KarelBartunek\Clockify\Domain\Clockify\Export;
use KarelBartunek\Clockify\Domain\Clockify\FetchApi\FetchProjects;
use KarelBartunek\Clockify\Domain\Clockify\FetchApi\FetchTimeEntries;
use KarelBartunek\Clockify\Domain\Clockify\FetchApi\FetchUsers;
use KarelBartunek\Clockify\Domain\Clockify\FetchApi\FetchWorkspaces;
use KarelBartunek\Clockify\Domain\Entity\Record;
use KarelBartunek\Clockify\Domain\Entity\User;
use KarelBartunek\Clockify\Domain\Entity\Workspace;
use KarelBartunek\Clockify\Infrastructure\Repository\RecordRepository;
use KarelBartunek\Clockify\Infrastructure\Repository\UserRepository;
use KarelBartunek\Clockify\Infrastructure\Repository\WorkspaceRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:clockify')]
class ClockifyCommand extends Command
{
    /**
     * @todo
     */
    private const KEYWORD_STAND_UP = 'Standup';

    private const FILENAME = 'results.txt';

    private FetchWorkspaces $fetchWorkspaces;
    private FetchUsers $fetchUsers;
    private FetchTimeEntries $fetchTimeEntries;
    private RecordRepository $recordRepository;
    private WorkspaceRepository $workspaceRepository;
    private UserRepository $userRepository;
    private Export $export;
    private FetchProjects $fetchProjects;

    public function __construct(
        FetchWorkspaces $fetchWorkspaces,
        FetchUsers $fetchUsers,
        FetchTimeEntries $fetchTimeEntries,
        FetchProjects $fetchProjects,
        RecordRepository $recordRepository,
        WorkspaceRepository $workspaceRepository,
        UserRepository $userRepository,
        Export $export
    ) {
        parent::__construct();
        $this->fetchWorkspaces = $fetchWorkspaces;
        $this->fetchUsers = $fetchUsers;
        $this->fetchTimeEntries = $fetchTimeEntries;
        $this->recordRepository = $recordRepository;
        $this->workspaceRepository = $workspaceRepository;
        $this->userRepository = $userRepository;
        $this->export = $export;
        $this->fetchProjects = $fetchProjects;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @todo spaghetti refactoring
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = new DateTimeImmutable('yesterday midnight');
        $to = new DateTimeImmutable('today midnight');

        $workspaces = $this->fetchWorkspaces->__invoke();

        foreach ($workspaces as $workspace) {
            $workspaceEntity = $this->workspaceRepository->findOneBy(['clockifyId' => $workspace->id()]);

            $projects = $this->fetchProjects->__invoke($workspace->id());

            $standUpProjectIds = array_map(function (ProjectDtoImpl $project) {
                return self::KEYWORD_STAND_UP === $project->name() ? $project->id() : null;
            }, $projects);

            if (is_null($workspaceEntity)) {
                $workspaceEntity = new Workspace($workspace);
                $this->workspaceRepository->save($workspaceEntity, true);
            }

            $users = $this->fetchUsers->__invoke($workspace->id());

            foreach ($users as $user) {
                $userEntity = $this->userRepository->findOneBy(['clockifyId' => $user->id()]);

                if (is_null($userEntity)) {
                    $userEntity = new User($user, $workspaceEntity);
                    $this->userRepository->save($userEntity, true);
                }

                $timeEntries = $this->fetchTimeEntries->__invoke($workspace->id(), $user->id(), $from, $to);

                foreach ($timeEntries as $timeEntry) {
                    $recordEntity = $this->recordRepository->findOneBy(['clockifyId' => $timeEntry->id()]);
                    if (is_null($recordEntity)) {
                        $recordEntity = new Record($timeEntry, $userEntity, $standUpProjectIds);
                        $this->recordRepository->save($recordEntity, true);
                    }
                }
            }
        }

        $fileContent = $this->export->__invoke($from, $to);

        file_put_contents(__DIR__ . '/../../var/' . self::FILENAME, $fileContent, FILE_APPEND);

        return Command::SUCCESS;
    }
}